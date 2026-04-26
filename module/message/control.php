<?php
/**
 * The control file of message module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      ZenTao Team
 * @package     message
 * @link        https://www.zentao.net
 */
class messageControl extends control
{
    /**
     * Index page.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->view->title = $this->lang->message->common;
        $this->view->status = $this->get->status ? $this->get->status : 'all';
        $this->view->type = $this->get->type ? $this->get->type : 'all';
        $this->view->messages = $this->message->getMessages($this->view->status, $this->view->type);
        $this->view->settings = $this->message->getNotificationSettings();
        $this->display();
    }
    
    /**
     * Get notification count.
     * 
     * @access public
     * @return void
     */
    public function getCount()
    {
        $type = $this->get->type ? $this->get->type : 'all';
        $count = $this->message->getNotificationCount('wait', $type);
        header('Content-Type: application/json');
        echo json_encode(array('count' => $count));
    }
    
    /**
     * Mark notification as read.
     * 
     * @access public
     * @return void
     */
    public function markAsRead()
    {
        if(!$this->post->id) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'Notification ID is required'));
            return;
        }
        
        $result = $this->message->markAsRead($this->post->id);
        header('Content-Type: application/json');
        echo json_encode(array('status' => $result ? 'success' : 'error'));
    }
    
    /**
     * Mark all notifications as read.
     * 
     * @access public
     * @return void
     */
    public function markAllAsRead()
    {
        $type = $this->post->type ? $this->post->type : 'all';
        $result = $this->message->markAllAsRead($type);
        header('Content-Type: application/json');
        echo json_encode(array('status' => $result ? 'success' : 'error'));
    }
    
    /**
     * Get notification settings.
     * 
     * @access public
     * @return void
     */
    public function getSettings()
    {
        $settings = $this->message->getNotificationSettings();
        header('Content-Type: application/json');
        echo json_encode($settings);
    }
    
    /**
     * Save notification settings.
     * 
     * @access public
     * @return void
     */
    public function saveSettings()
    {
        if(!$this->post->settings) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'Settings are required'));
            return;
        }
        
        $settings = json_decode($this->post->settings);
        $result = $this->message->saveNotificationSettings($settings);
        header('Content-Type: application/json');
        echo json_encode(array('status' => $result ? 'success' : 'error'));
    }
}