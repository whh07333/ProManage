<?php
/**
 * The control file of jenkins module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      ZenTao Team
 * @package     jenkins
 * @link        https://www.zentao.net
 */
class jenkinsControl extends control
{
    /**
     * Index page.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->view->title = $this->lang->jenkins->common;
        $this->view->servers = $this->jenkins->getServers();
        $this->display();
    }
    
    /**
     * Create Jenkins server.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        if($this->post)
        {
            $server = new stdclass();
            $server->name     = $this->post->name;
            $server->url      = $this->post->url;
            $server->username = $this->post->username;
            $server->token    = $this->post->token;
            $server->createdBy = $this->app->user->account;
            $server->createdDate = helper::now();
            
            $this->jenkins->createServer($server);
            $this->redirect($this->createLink('jenkins', 'index'));
        }
        $this->display();
    }
    
    /**
     * Edit Jenkins server.
     * 
     * @param  int $id
     * @access public
     * @return void
     */
    public function edit($id)
    {
        if($this->post)
        {
            $server = new stdclass();
            $server->id       = $id;
            $server->name     = $this->post->name;
            $server->url      = $this->post->url;
            $server->username = $this->post->username;
            $server->token    = $this->post->token;
            $server->updatedBy = $this->app->user->account;
            $server->updatedDate = helper::now();
            
            $this->jenkins->updateServer($server);
            $this->redirect($this->createLink('jenkins', 'index'));
        }
        
        $this->view->server = $this->jenkins->getServer($id);
        $this->display();
    }
    
    /**
     * Delete Jenkins server.
     * 
     * @param  int $id
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $this->jenkins->deleteServer($id);
        $this->redirect($this->createLink('jenkins', 'index'));
    }
    
    /**
     * Handle Jenkins webhook.
     * 
     * @access public
     * @return void
     */
    public function webhook()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        
        if(empty($data)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Invalid webhook data';
            return;
        }
        
        $this->jenkins->handleWebhook($data);
        
        header('HTTP/1.1 200 OK');
        echo 'Webhook received';
    }
    
    /**
     * Get related builds for an object.
     * 
     * @access public
     * @return void
     */
    public function getRelatedBuilds()
    {
        if(!$this->get->objectType || !$this->get->objectID) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'objectType and objectID are required'));
            return;
        }
        
        $builds = $this->jenkins->getRelatedBuilds($this->get->objectType, $this->get->objectID);
        
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'success', 'data' => $builds));
    }
    
    /**
     * Test Jenkins server connection.
     * 
     * @access public
     * @return void
     */
    public function testConnection()
    {
        if(!$this->post->url || !$this->post->username || !$this->post->token) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'URL, username and token are required'));
            return;
        }
        
        $url = rtrim($this->post->url, '/') . '/api/json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->post->username}:{$this->post->token}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if($httpCode == 200 && $response) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'success', 'message' => 'Connection successful'));
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'Connection failed'));
        }
    }
}