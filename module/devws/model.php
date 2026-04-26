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
}