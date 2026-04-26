<?php
/**
 * Test cases for jenkins module.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      ZenTao Team
 * @package     jenkins
 * @link        https://www.zentao.net
 */
class jenkinsTest extends testbase
{
    /**
     * Test create server.
     *
     * @access public
     * @return void
     */
    public function testCreateServer()
    {
        $this->resetModule();
        $this->app->user->account = 'admin';
        
        $server = new stdclass();
        $server->name = 'Test Jenkins ' . time();
        $server->url = 'https://jenkins.example.com';
        $server->username = 'admin';
        $server->token = 'test-token';
        $server->createdBy = 'admin';
        $server->createdDate = helper::now();
        
        $serverID = $this->jenkins->createServer($server);
        $this->assertNotEmpty($serverID, 'Failed to create Jenkins server');
        
        // Check if server exists
        $createdServer = $this->jenkins->getServer($serverID);
        $this->assertNotEmpty($createdServer, 'Server not found');
        $this->assertEquals($server->name, $createdServer->name, 'Server name mismatch');
        $this->assertEquals($server->url, $createdServer->url, 'Server URL mismatch');
        $this->assertEquals($server->username, $createdServer->username, 'Server username mismatch');
    }
    
    /**
     * Test update server.
     *
     * @access public
     * @return void
     */
    public function testUpdateServer()
    {
        $this->resetModule();
        $this->app->user->account = 'admin';
        
        // Create a server first
        $server = new stdclass();
        $server->name = 'Test Jenkins ' . time();
        $server->url = 'https://jenkins.example.com';
        $server->username = 'admin';
        $server->token = 'test-token';
        $server->createdBy = 'admin';
        $server->createdDate = helper::now();
        
        $serverID = $this->jenkins->createServer($server);
        
        // Update the server
        $updatedServer = new stdclass();
        $updatedServer->id = $serverID;
        $updatedServer->name = 'Updated Jenkins ' . time();
        $updatedServer->url = 'https://jenkins-updated.example.com';
        $updatedServer->username = 'updated-admin';
        $updatedServer->token = 'updated-token';
        $updatedServer->updatedBy = 'admin';
        $updatedServer->updatedDate = helper::now();
        
        $this->jenkins->updateServer($updatedServer);
        
        // Check if server is updated
        $server = $this->jenkins->getServer($serverID);
        $this->assertEquals($updatedServer->name, $server->name, 'Server name not updated');
        $this->assertEquals($updatedServer->url, $server->url, 'Server URL not updated');
        $this->assertEquals($updatedServer->username, $server->username, 'Server username not updated');
    }
    
    /**
     * Test delete server.
     *
     * @access public
     * @return void
     */
    public function testDeleteServer()
    {
        $this->resetModule();
        $this->app->user->account = 'admin';
        
        // Create a server first
        $server = new stdclass();
        $server->name = 'Test Jenkins ' . time();
        $server->url = 'https://jenkins.example.com';
        $server->username = 'admin';
        $server->token = 'test-token';
        $server->createdBy = 'admin';
        $server->createdDate = helper::now();
        
        $serverID = $this->jenkins->createServer($server);
        
        // Delete the server
        $this->jenkins->deleteServer($serverID);
        
        // Check if server is deleted
        $server = $this->jenkins->getServer($serverID);
        $this->assertEmpty($server, 'Server not deleted');
    }
    
    /**
     * Test get servers.
     *
     * @access public
     * @return void
     */
    public function testGetServers()
    {
        $this->resetModule();
        $this->app->user->account = 'admin';
        
        // Create a server
        $server = new stdclass();
        $server->name = 'Test Jenkins ' . time();
        $server->url = 'https://jenkins.example.com';
        $server->username = 'admin';
        $server->token = 'test-token';
        $server->createdBy = 'admin';
        $server->createdDate = helper::now();
        
        $this->jenkins->createServer($server);
        
        // Get servers
        $servers = $this->jenkins->getServers();
        $this->assertNotEmpty($servers, 'No servers found');
    }
    
    /**
     * Test handle webhook.
     *
     * @access public
     * @return void
     */
    public function testHandleWebhook()
    {
        $this->resetModule();
        $this->app->user->account = 'admin';
        
        // Create a server first
        $server = new stdclass();
        $server->name = 'Test Jenkins ' . time();
        $server->url = 'https://jenkins.example.com';
        $server->username = 'admin';
        $server->token = 'test-token';
        $server->createdBy = 'admin';
        $server->createdDate = helper::now();
        
        $serverID = $this->jenkins->createServer($server);
        
        // Simulate webhook data
        $webhookData = array(
            'name' => 'test-job',
            'build' => array(
                'number' => 1,
                'status' => 'SUCCESS',
                'url' => 'https://jenkins.example.com/job/test-job/1/',
                'timestamp' => time() * 1000,
                'duration' => 10000,
                'builtOn' => 'jenkins-server'
            ),
            'cause' => array(
                array('shortDescription' => 'Started by user admin')
            )
        );
        
        // Handle webhook
        $this->jenkins->handleWebhook($webhookData);
        
        // Check if build record is created
        $build = $this->dao->select('*')->from(TABLE_JENKINSBUILD)->where('serverID')->eq($serverID)->fetch();
        $this->assertNotEmpty($build, 'Build record not created');
        $this->assertEquals('test-job', $build->jobName, 'Job name mismatch');
        $this->assertEquals(1, $build->buildNumber, 'Build number mismatch');
        $this->assertEquals('SUCCESS', $build->status, 'Build status mismatch');
    }
}
