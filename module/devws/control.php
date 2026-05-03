<?php
/**
 * The control file of dev module.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      whh
 * @package     dev
 * @version     $Id$
 */
class devws extends control
{
    /**
     * Index page for dev workspace.
     *
     * @access public
     * @return void
     */
    public function index()
    {
        $this->view->title = $this->lang->devws->common;
        $this->view->position[] = $this->lang->devws->common;

        $this->loadModel('devws');
        $this->view->tasks    = $this->devws->getMyTasks();
        $this->view->bugs     = $this->devws->getMyBugs();
        $this->view->stories  = $this->devws->getMyStories();
        $this->view->todos    = $this->devws->getMyTodos();
        $this->view->projects = $this->devws->getMyProjects();
        $this->view->docs     = $this->devws->getMyDocs();
        $this->view->users    = $this->loadModel('user')->getPairs('nodeleted|noclosed');

        $this->display();
    }

    /**
     * Task detail page for drawer.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function task($taskID = 0)
    {
        $this->loadModel('devws');
        $task = $this->devws->getTaskById($taskID);

        if(!$task)
        {
            $this->view->task  = null;
            $this->view->title = '任务不存在';
            $this->display();
            return;
        }

        /* Load related lang for story category, bug severity, action history, etc. */
        $this->app->loadLang('story');
        $this->app->loadLang('bug');
        $this->app->loadLang('task');
        $this->app->loadLang('action');

        $this->view->task  = $task;
        $this->view->title = $task->name . ' - ' . $this->lang->devws->common;
        $this->display();
    }

    /**
     * Assign task to user (custom drawer page).
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    /**
     * Create task (custom drawer page).
     *
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function create($executionID = 0)
    {
        $this->loadModel('devws');
        $this->loadModel('task');
        $this->app->loadLang('task');
        $this->app->loadLang('action');

        $now = helper::now();
        $account = $this->app->user->account;

        if(!empty($_POST))
        {
            $response = null;
            try {
                $taskData = new stdclass();
                $taskData->execution      = $this->post->execution;
                $taskData->name           = $this->post->name;
                $taskData->type           = $this->post->type;
                $taskData->pri            = (int)$this->post->pri;
                $taskData->assignedTo     = $this->post->assignedTo;
                $taskData->estimate       = (float)$this->post->estimate;
                $taskData->estStarted     = !empty($this->post->estStarted) ? $this->post->estStarted : null;
                $taskData->deadline       = !empty($this->post->deadline) ? $this->post->deadline : null;
                $taskData->desc           = $this->post->desc;
                $taskData->version        = 1;
                $taskData->openedBy       = $account;
                $taskData->openedDate     = $now;
                $taskData->lastEditedBy   = $account;
                $taskData->lastEditedDate = $now;
                $taskData->vision         = $this->config->vision;
                $taskData->assignedDate   = !empty($this->post->assignedTo) ? $now : null;
                $taskData->status         = 'wait';
                $taskData->storyVersion   = 1;

                $taskID = $this->task->create($taskData);
                if(dao::isError())
                {
                    $response = array('result' => 'fail', 'message' => dao::getError());
                }
                else
                {
                    $response = array('result' => 'success');
                }
            } catch (Throwable $e) {
                $response = array('result' => 'fail', 'message' => $e->getMessage());
            }
            return $this->send($response);
        }

        /* Get accessible executions for current user. */
        $executions = $this->dao->select('t1.id, t1.name, t2.name as projectName')
            ->from(TABLE_EXECUTION)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.status')->ne('closed')
            ->beginIF($executionID)->andWhere('t1.id')->eq($executionID)->fi()
            ->orderBy('t1.id desc')
            ->fetchAll();

        /* Default execution */
        if(!$executionID && !empty($executions)) $executionID = $executions[0]->id;

        /* Get team members for default execution. */
        $users = array();
        if($executionID)
        {
            $members = $this->loadModel('user')->getTeamMemberPairs($executionID, 'execution', 'nodeleted');
            $users   = $this->loadModel('user')->getPairs('nodeleted|noclosed');
        }

        $this->view->executions   = $executions;
        $this->view->executionID  = $executionID;
        $this->view->users        = $users;
        $this->view->title        = $this->lang->task->create;
        $this->display();
    }

    /**
     * Create doc in main content area (not drawer).
     *
     * @param  int    $libID
     * @access public
     * @return void
     */
    public function createDoc($libID = 0)
    {
        $this->loadModel('devws');
        $this->loadModel('doc');
        $this->app->loadLang('doc');
        $this->app->loadLang('action');

        $now     = helper::now();
        $account = $this->app->user->account;

        if(!empty($_POST))
        {
            $response = null;
            try {
                $docData = new stdclass();
                $docData->lib           = (int)$this->post->lib;
                $docData->module        = (int)$this->post->module;
                $docData->title         = $this->post->title;
                $docData->type          = 'html';
                $docData->contentType   = 'html';
                $docData->content       = $this->post->content;
                $docData->status        = 'normal';
                $docData->acl           = $this->post->acl ?: 'open';
                $docData->keywords      = $this->post->keywords;
                $docData->mailto        = $this->post->mailto ? implode(',', (array)$this->post->mailto) : '';
                $docData->addedBy       = $account;
                $docData->addedDate     = $now;
                $docData->editedBy  = $account;
                $docData->editedDate = $now;

                $result = $this->doc->create($docData);
                if(dao::isError())
                {
                    $response = array('result' => 'fail', 'message' => dao::getError());
                }
                elseif(!$result)
                {
                    $response = array('result' => 'fail', 'message' => $this->lang->devws->createDocFail ?? '创建文档失败');
                }
                else
                {
                    $response = array('result' => 'success', 'id' => $result['id']);
                }
            } catch (Throwable $e) {
                $response = array('result' => 'fail', 'message' => $e->getMessage());
            }
            return $this->send($response);
        }

        /* Get available doc libs for the user. */
        $libs = $this->doc->getLibs('all');

        if(!$libID && !empty($libs)) $libID = key($libs);

        $this->view->libs   = $libs;
        $this->view->libID  = $libID;
        $this->view->title  = $this->lang->devws->createDoc;
        $this->view->users  = $this->loadModel('user')->getPairs('nodeleted|noclosed');
        $this->view->groups = $this->loadModel('group')->getPairs();
        $this->display();
    }

    /**
     * Project detail page for main content area.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function project($projectID = 0)
    {
        $this->loadModel('devws');
        $this->app->loadLang('project');
        $this->app->loadLang('execution');
        $this->app->loadLang('action');

        $project = $this->devws->getProjectById($projectID);
        if(!$project)
        {
            $this->view->project = null;
            $this->view->title   = '项目不存在';
            $this->display();
            return;
        }

        /* Fetch project data using core project model. */
        $projectModel = $this->loadModel('project');

        $project       = $projectModel->getById((int)$projectID);
        $statData      = $projectModel->getStatData($projectID);
        $teamMembers   = $projectModel->getTeamMembers($projectID);
        $actions       = $this->loadModel('action')->getList('project', $projectID);
        $dynamics      = $this->loadModel('action')->getDynamic('all', 'all', 'date_desc', 50, 'all', $projectID);
        $users         = $this->loadModel('user')->getPairs('nodeleted|noclosed');
        $programList   = $this->loadModel('program')->getPairsByList(explode(',', trim($project->path, ',')));

        /* Execution / sprint list. */
        $executions = $this->loadModel('execution')->getStatData($projectID, 'all', 0, 0, false, '', 'id_desc');

        /* Product data if project has products. */
        $products = array();
        if($project->hasProduct)
        {
            $products = $this->loadModel('product')->getProducts($projectID, 'all', '', true, '', false);
        }

        $this->view->title       = $project->name . ' - ' . $this->lang->devws->common;
        $this->view->project     = $project;
        $this->view->statData    = $statData;
        $this->view->teamMembers = $teamMembers;
        $this->view->actions     = $actions;
        $this->view->dynamics    = $dynamics;
        $this->view->users       = $users;
        $this->view->programList = $programList;
        $this->view->products    = $products;
        $this->view->executions  = $executions;
        $this->display();
    }

    /**
     * Kanban board view for project tasks.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function kanban($projectID = 0)
    {
        $this->loadModel('devws');
        $this->app->loadLang('task');
        $this->app->loadLang('execution');
        $this->app->loadLang('project');

        $project = $this->devws->getProjectById($projectID);
        if(!$project)
        {
            $this->view->project = null;
            $this->view->title   = '项目不存在';
            $this->display();
            return;
        }

        /* Fetch project model data. */
        $projectModel = $this->loadModel('project');
        $project      = $projectModel->getById((int)$projectID);

        /* Get all tasks in this project, grouped by kanban column status. */
        $tasks       = $this->devws->getProjectKanbanTasks($projectID);

        /* Default column definitions (fallback if kanban module config unavailable). */
        $defaultColumns = array('wait' => 'wait', 'developing' => 'doing', 'developed' => 'done', 'pause' => 'pause', 'canceled' => 'cancel', 'closed' => 'closed');
        $columnDefs  = !empty($this->config->kanban->taskColumnStatusList) ? (array)$this->config->kanban->taskColumnStatusList : $defaultColumns;
        $users       = $this->loadModel('user')->getPairs('nodeleted|noclosed');

        /* Build reverse map: task status -> column type. */
        $statusToColumn = array();
        foreach($columnDefs as $colType => $taskStatus) $statusToColumn[$taskStatus] = $colType;

        /* Group tasks into columns. */
        $groupedTasks = array();
        foreach($columnDefs as $colType => $taskStatus) $groupedTasks[$colType] = array();
        foreach($tasks as $task)
        {
            $col = isset($statusToColumn[$task->status]) ? $statusToColumn[$task->status] : 'wait';
            $groupedTasks[$col][] = $task;
        }

        $this->view->title       = $project->name . ' - ' . $this->lang->devws->common;
        $this->view->project     = $project;
        $this->view->tasks       = $groupedTasks;
        $this->view->users       = $users;
        $this->view->columnDefs  = $columnDefs;
        $this->display();
    }

    /**
     * Edit project page for main content area.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function editProject($projectID = 0)
    {
        $this->loadModel('devws');
        $this->app->loadLang('project');

        $project = $this->devws->getProjectById($projectID);
        if(!$project)
        {
            $this->view->project = null;
            $this->view->title   = '项目不存在';
            $this->display();
            return;
        }

        $this->view->project = $project;
        $this->view->title   = $project->name . ' - ' . $this->lang->devws->common;
        $this->view->editUrl = helper::createLink('project', 'edit', "projectID=$projectID", '', true);
        $this->display();
    }

    /**
     * Assign task to user (custom drawer page).
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function assignTo($taskID = 0)
    {
        $this->loadModel('devws');
        $this->loadModel('task');
        $this->app->loadLang('task');
        $this->app->loadLang('action');

        $task = $this->devws->getTaskById($taskID);
        if(!$task)
        {
            $this->view->error = '任务不存在';
            $this->display();
            return;
        }

        /* Build users list: team members + all users. */
        $projectModel = $this->dao->findById($task->project)->from(TABLE_PROJECT)->fetch('model');
        $memberType   = $projectModel == 'research' ? 'project' : 'execution';
        $objectID     = $projectModel == 'research' ? $task->project : $task->execution;
        $members      = $this->loadModel('user')->getTeamMemberPairs($objectID, $memberType, 'nodeleted');
        $users        = $this->loadModel('user')->getPairs('nodeleted|noclosed', $task->assignedTo);

        if(!empty($task->team) && $task->mode == 'multi' && !in_array($task->status, array('done', 'cancel', 'closed')))
        {
            $this->view->multiTeam = true;
        }

        if(!empty($_POST))
        {
            $assignedTo = $this->post->assignedTo;
            $left       = $this->post->left;
            $comment    = $this->post->comment;

            /* Build task data for assignment. */
            $taskData = new stdclass();
            $taskData->assignedTo     = $assignedTo;
            $taskData->assignedDate   = helper::now();
            $taskData->left           = $left;
            $taskData->lastEditedBy   = $this->app->user->account;
            $taskData->lastEditedDate = helper::now();
            $taskData->id             = $taskID;

            $changes = $this->task->assign($taskData);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            /* Record action log. */
            if(!empty($comment) || !empty($changes))
            {
                $actionID = $this->loadModel('action')->create('task', $taskID, 'Assigned', $comment);
                $this->action->logHistory($actionID, $changes);
            }

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true));
        }

        $this->view->task       = $task;
        $this->view->members    = $members;
        $this->view->users      = $users;
        $this->view->title      = '指派任务 - ' . $task->name;
        $this->display();
    }

    /**
     * AJAX: Update task status (via kanban drag-and-drop).
     *
     * @access public
     * @return void
     */
    public function ajaxUpdateTaskStatus()
    {
        $taskID = (int)$this->post->taskID;
        $status = $this->post->status;

        if(!$taskID || !$status) return $this->send(array('result' => 'fail', 'message' => '参数错误'));

        $this->loadModel('devws');
        $this->loadModel('task');
        $this->app->loadLang('action');

        $oldTask = $this->task->getById($taskID);
        if(!$oldTask) return $this->send(array('result' => 'fail', 'message' => '任务不存在'));

        /* Validate status is a valid task status. */
        $validStatuses = array_keys($this->lang->task->statusList);
        if(!in_array($status, $validStatuses)) return $this->send(array('result' => 'fail', 'message' => '无效的状态'));

        if($oldTask->status == $status) return $this->send(array('result' => 'success'));

        /* Optimistic concurrency check: reject if another user modified since page load. */
        $clientEdited = $this->post->lastEdited;
        if($clientEdited && $oldTask->lastEditedDate && $clientEdited !== $oldTask->lastEditedDate)
        {
            return $this->send(array('result' => 'conflict', 'message' => '卡片已被其他人修改'));
        }

        $now = helper::now();
        $account = $this->app->user->account;

        /* Prepare update data. */
        $taskData = new stdclass();
        $taskData->status         = $status;
        $taskData->lastEditedBy   = $account;
        $taskData->lastEditedDate = $now;
        if($status == 'doing' && empty($oldTask->realStarted)) $taskData->realStarted = $now;
        if($status == 'done')
        {
            if(empty($oldTask->finishedBy)) $taskData->finishedBy   = $account;
            if(empty($oldTask->finishedDate)) $taskData->finishedDate = $now;
            $taskData->assignedTo = 'closed';
            $taskData->assignedDate = $now;
        }
        if($status == 'cancel' && empty($oldTask->canceledBy))
        {
            $taskData->canceledBy   = $account;
            $taskData->canceledDate = $now;
        }

        $this->dao->update(TABLE_TASK)->data($taskData)->where('id')->eq($taskID)->exec();
        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        /* Refresh project stats. */
        $this->loadModel('program')->refreshProjectStats($oldTask->project);

        /* Update parent status. */
        if($oldTask->parent > 0) $this->task->updateParentStatus($taskID);

        /* Update linked story stage. */
        if($oldTask->story) $this->loadModel('story')->setStage($oldTask->story);

        /* Record action log. */
        $changes = common::createChanges($oldTask, (object)array_merge((array)$oldTask, (array)$taskData));
        $actionID = $this->loadModel('action')->create('task', $taskID, 'Edited', '', 'statuschanged');
        if($actionID) $this->action->logHistory($actionID, $changes);

        return $this->send(array('result' => 'success'));
    }
}