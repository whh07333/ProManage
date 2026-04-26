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
        error_log("[DEBUG] devws index START: module={$this->moduleName}, method={$this->methodName}");
        error_log("[DEBUG] devws index: loading lang...");
        $this->view->title = $this->lang->devws->common;
        $this->view->position[] = $this->lang->devws->common;
        error_log("[DEBUG] devws index: loading model...");
        
        try {
            error_log("[DEBUG] devws index: loading devws model...");
            $this->loadModel('devws');
            error_log("[DEBUG] devws index: getting my tasks...");
            $this->view->tasks = $this->devws->getMyTasks();
            error_log("[DEBUG] devws index: tasks count = " . count($this->view->tasks));
            
            error_log("[DEBUG] devws index: getting my bugs...");
            $this->view->bugs = $this->devws->getMyBugs();
            error_log("[DEBUG] devws index: bugs count = " . count($this->view->bugs));
            
            error_log("[DEBUG] devws index: getting my stories...");
            $this->view->stories = $this->devws->getMyStories();
            error_log("[DEBUG] devws index: stories count = " . count($this->view->stories));
            
            error_log("[DEBUG] devws index: getting my todos...");
            $this->view->todos = $this->devws->getMyTodos();
            error_log("[DEBUG] devws index: todos count = " . count($this->view->todos));
            
            error_log("[DEBUG] devws index: getting my projects...");
            $this->view->projects = $this->devws->getMyProjects();
            error_log("[DEBUG] devws index: projects count = " . count($this->view->projects));
            
            error_log("[DEBUG] devws index: getting my docs...");
            $this->view->docs = $this->devws->getMyDocs();
            error_log("[DEBUG] devws index: docs count = " . count($this->view->docs));
        } catch (Exception $e) {
            error_log("[ERROR] devws index: " . $e->getMessage());
        }
        
        error_log("[DEBUG] devws index: calling display...");
        $this->display();
        error_log("[DEBUG] devws index END");
    }
}