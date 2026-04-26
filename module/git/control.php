<?php
/**
 * The control file of git module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      ZenTao Team
 * @package     git
 * @link        https://www.zentao.net
 */
class gitControl extends control
{
    /**
     * Handle GitLab webhook.
     * 
     * @access public
     * @return void
     */
    public function gitlabWebhook()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        
        if(empty($data)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Invalid webhook data';
            return;
        }
        
        $this->git->handleGitLabWebhook($data);
        
        header('HTTP/1.1 200 OK');
        echo 'Webhook received';
    }
    
    /**
     * Handle GitHub webhook.
     * 
     * @access public
     * @return void
     */
    public function githubWebhook()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        
        if(empty($data)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Invalid webhook data';
            return;
        }
        
        // Add event type if not present
        if(!isset($data['event_type'])) {
            $eventType = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? 'push';
            $data['event_type'] = $eventType;
        }
        
        $this->git->handleGitHubWebhook($data);
        
        header('HTTP/1.1 200 OK');
        echo 'Webhook received';
    }
    
    /**
     * Get related commits for an object.
     * 
     * @access public
     * @return void
     */
    public function getRelatedCommits()
    {
        if(!$this->get->objectType || !$this->get->objectID) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'objectType and objectID are required'));
            return;
        }
        
        $commits = $this->git->getRelatedCommits($this->get->objectType, $this->get->objectID);
        
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'success', 'data' => $commits));
    }
}