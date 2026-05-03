<?php
/**
 * The model file of dev module.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      whh
 * @package     dev
 * @version     $Id$
 */
class devwsModel extends model
{
    /**
     * Get my tasks.
     *
     * @access public
     * @return array
     */
    public function getMyTasks()
    {
        $account = $this->app->user->account;

        return $this->dao->select('*')->from(TABLE_TASK)
            ->where('assignedTo')->eq($account)
            ->andWhere('status')->in('wait,doing,pause')
            ->orderBy('pri_desc, id_desc')
            ->limit(20)
            ->fetchAll();
    }

    /**
     * Get my bugs.
     *
     * @access public
     * @return array
     */
    public function getMyBugs()
    {
        $account = $this->app->user->account;

        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('assignedTo')->eq($account)
            ->andWhere('status')->eq('active')
            ->orderBy('pri_desc, id_desc')
            ->limit(20)
            ->fetchAll();
    }

    /**
     * Get my stories.
     *
     * @access public
     * @return array
     */
    public function getMyStories()
    {
        $account = $this->app->user->account;

        return $this->dao->select('*')->from(TABLE_STORY)
            ->where('assignedTo')->eq($account)
            ->andWhere('status')->eq('active')
            ->orderBy('pri_desc, id_desc')
            ->limit(20)
            ->fetchAll();
    }

    /**
     * Get my todos.
     *
     * @access public
     * @return array
     */
    public function getMyTodos()
    {
        $account = $this->app->user->account;

        return $this->dao->select('*')->from(TABLE_TODO)
            ->where('account')->eq($account)
            ->andWhere('status')->eq('wait')
            ->orderBy('date, id_desc')
            ->limit(20)
            ->fetchAll();
    }

    /**
     * Get my projects.
     *
     * @access public
     * @return array
     */
    public function getMyProjects()
    {
        $account = $this->app->user->account;

        $projectMembers = $this->dao->select('root')->from(TABLE_TEAM)
            ->where('account')->eq($account)
            ->andWhere('type')->eq('project')
            ->fetchAll();

        $projectIDs = array();
        foreach($projectMembers as $member) $projectIDs[] = $member->root;

        if(empty($projectIDs)) return array();

        return $this->dao->select('*')->from(TABLE_PROJECT)
            ->where('id')->in($projectIDs)
            ->andWhere('status')->ne('closed')
            ->orderBy('id_desc')
            ->fetchAll();
    }

    /**
     * Get my docs.
     *
     * @access public
     * @return array
     */
    public function getMyDocs()
    {
        $account = $this->app->user->account;

        $createdDocs = $this->dao->select('*')->from(TABLE_DOC)
            ->where('addedBy')->eq($account)
            ->orderBy('id_desc')
            ->limit(20)
            ->fetchAll();

        $aclDocs = $this->dao->select('*')->from(TABLE_DOC)
            ->where('acl')->ne('private')
            ->orderBy('id_desc')
            ->limit(20)
            ->fetchAll();

        $allDocs = array_merge($createdDocs, $aclDocs);
        $docIds = array();
        $uniqueDocs = array();
        foreach($allDocs as $doc)
        {
            if(!in_array($doc->id, $docIds))
            {
                $docIds[] = $doc->id;
                $uniqueDocs[] = $doc;
            }
        }

        usort($uniqueDocs, function($a, $b) {
            return strcmp($b->id, $a->id);
        });

        return array_slice($uniqueDocs, 0, 20);
    }

    /**
     * Get task by ID with related info.
     *
     * @param  int    $taskID
     * @access public
     * @return object|null
     */
    public function getTaskById($taskID)
    {
        $task = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();
        if(!$task) return null;

        /* Related names. */
        $task->projectName    = '';
        $task->executionName  = '';
        $task->storyTitle     = '';
        $task->storyCategory  = '';
        $task->moduleName     = '';
        $task->openedByName   = $task->openedBy;
        $task->finishedByName = $task->finishedBy;
        $task->assignedToName = $task->assignedTo;

        if($task->project)   $project   = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($task->project)->fetch();
        if($task->execution) $execution = $this->dao->select('name, begin, end')->from(TABLE_EXECUTION)->where('id')->eq($task->execution)->fetch();
        if($task->story)     $story     = $this->dao->select('id, title, category')->from(TABLE_STORY)->where('id')->eq($task->story)->fetch();
        if($task->module)    $module    = $this->dao->select('id, name')->from(TABLE_MODULE)->where('id')->eq($task->module)->fetch();

        if(!empty($project))   $task->projectName   = $project->name;
        if(!empty($execution))
        {
            $task->executionName = $execution->name;
            $task->executionBegin = $execution->begin;
            $task->executionEnd   = $execution->end;
        }
        if(!empty($story))
        {
            $task->storyTitle    = $story->title;
            $task->storyCategory = $story->category;
        }
        if(!empty($module)) $task->moduleName = $module->name;

        $openedByUser   = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($task->openedBy)->fetch();
        $assignedToUser = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($task->assignedTo)->fetch();
        if($task->finishedBy) $finishedByUser = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($task->finishedBy)->fetch();

        if(!empty($openedByUser))     $task->openedByName   = $openedByUser->realname;
        if(!empty($assignedToUser))   $task->assignedToName = $assignedToUser->realname;
        if(!empty($finishedByUser))   $task->finishedByName = $finishedByUser->realname;

        /* Subtasks (children tasks). */
        $task->subtasks = $this->dao->select('id, name, status, pri, assignedTo, estimate, consumed, `left`, deadline')
            ->from(TABLE_TASK)->where('parent')->eq($taskID)
            ->andWhere('deleted')->eq(0)
            ->orderBy('id asc')
            ->fetchAll();

        /* Related bugs. */
        $task->bugs = $this->dao->select('id, title, status, severity, pri, openedBy, assignedTo, resolvedBy, resolvedDate')
            ->from(TABLE_BUG)->where('task')->eq($taskID)
            ->andWhere('deleted')->eq(0)
            ->orderBy('id desc')
            ->fetchAll();

        /* Action history. */
        $task->actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('task')
            ->andWhere('objectID')->eq($taskID)
            ->orderBy('date desc')
            ->limit(30)
            ->fetchAll();

        return $task;
    }

    /**
     * Get project by ID.
     *
     * @param  int    $projectID
     * @access public
     * @return object|null
     */
    public function getProjectById($projectID)
    {
        $project = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch();
        if(!$project) return null;

        $project->pmName = '';
        $project->poName = '';
        $project->qdName = '';
        $project->rdName = '';

        if($project->PM)
        {
            $pmUser = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($project->PM)->fetch();
            $project->pmName = $pmUser ? $pmUser->realname : $project->PM;
        }
        if($project->PO)
        {
            $poUser = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($project->PO)->fetch();
            $project->poName = $poUser ? $poUser->realname : $project->PO;
        }
        if($project->QD)
        {
            $qdUser = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($project->QD)->fetch();
            $project->qdName = $qdUser ? $qdUser->realname : $project->QD;
        }
        if($project->RD)
        {
            $rdUser = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($project->RD)->fetch();
            $project->rdName = $rdUser ? $rdUser->realname : $project->RD;
        }

        return $project;
    }
}