<?php
/**
 * Test cases for git module.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      ZenTao Team
 * @package     git
 * @link        https://www.zentao.net
 */
class gitTest extends testbase
{
    /**
     * Test handle GitLab webhook.
     *
     * @access public
     * @return void
     */
    public function testHandleGitLabWebhook()
    {
        $this->resetModule();
        $this->app->user->account = 'admin';
        
        // Simulate GitLab webhook data
        $webhookData = array(
            'object_kind' => 'push',
            'event_name' => 'push',
            'project' => array(
                'name' => 'test-project',
                'web_url' => 'https://gitlab.example.com/test/test-project'
            ),
            'commits' => array(
                array(
                    'id' => '1234567890abcdef1234567890abcdef12345678',
                    'message' => 'Task #123: Implement login functionality',
                    'timestamp' => '2023-01-01T12:00:00Z',
                    'url' => 'https://gitlab.example.com/test/test-project/commit/1234567890abcdef1234567890abcdef12345678',
                    'author' => array(
                        'name' => 'John Doe',
                        'email' => 'john.doe@example.com'
                    )
                )
            ),
            'repository' => array(
                'name' => 'test-project',
                'url' => 'https://gitlab.example.com/test/test-project.git',
                'homepage' => 'https://gitlab.example.com/test/test-project'
            )
        );
        
        // Handle GitLab webhook
        $this->git->handleGitLabWebhook($webhookData);
        
        // Check if commit record is created
        $commit = $this->dao->select('*')->from(TABLE_GITCOMMIT)->where('commitID')->eq('1234567890abcdef1234567890abcdef12345678')->fetch();
        $this->assertNotEmpty($commit, 'Commit record not created');
        $this->assertEquals('Task #123: Implement login functionality', $commit->message, 'Commit message mismatch');
        $this->assertEquals('John Doe', $commit->author, 'Commit author mismatch');
    }
    
    /**
     * Test handle GitHub webhook.
     *
     * @access public
     * @return void
     */
    public function testHandleGitHubWebhook()
    {
        $this->resetModule();
        $this->app->user->account = 'admin';
        
        // Simulate GitHub webhook data
        $webhookData = array(
            'event_type' => 'push',
            'repository' => array(
                'name' => 'test-project',
                'html_url' => 'https://github.com/test/test-project'
            ),
            'commits' => array(
                array(
                    'id' => '1234567890abcdef1234567890abcdef12345678',
                    'message' => 'Task #123: Implement login functionality',
                    'timestamp' => '2023-01-01T12:00:00Z',
                    'url' => 'https://github.com/test/test-project/commit/1234567890abcdef1234567890abcdef12345678',
                    'author' => array(
                        'name' => 'John Doe',
                        'email' => 'john.doe@example.com'
                    )
                )
            )
        );
        
        // Handle GitHub webhook
        $this->git->handleGitHubWebhook($webhookData);
        
        // Check if commit record is created
        $commit = $this->dao->select('*')->from(TABLE_GITCOMMIT)->where('commitID')->eq('1234567890abcdef1234567890abcdef12345678')->fetch();
        $this->assertNotEmpty($commit, 'Commit record not created');
        $this->assertEquals('Task #123: Implement login functionality', $commit->message, 'Commit message mismatch');
        $this->assertEquals('John Doe', $commit->author, 'Commit author mismatch');
    }
    
    /**
     * Test get related commits.
     *
     * @access public
     * @return void
     */
    public function testGetRelatedCommits()
    {
        $this->resetModule();
        $this->app->user->account = 'admin';
        
        // Create a commit record
        $commit = new stdclass();
        $commit->commitID = '1234567890abcdef1234567890abcdef12345678';
        $commit->message = 'Task #123: Implement login functionality';
        $commit->author = 'John Doe';
        $commit->date = '2023-01-01 12:00:00';
        $commit->url = 'https://gitlab.example.com/test/test-project/commit/1234567890abcdef1234567890abcdef12345678';
        $commit->repository = 'test-project';
        $commit->createdDate = helper::now();
        
        $commitID = $this->dao->insert(TABLE_GITCOMMIT)->data($commit)->exec();
        
        // Create commit relation
        $relation = new stdclass();
        $relation->commitID = $commit->commitID;
        $relation->objectType = 'task';
        $relation->objectID = 123;
        $relation->createdDate = helper::now();
        
        $this->dao->insert(TABLE_GITCOMMITRELATION)->data($relation)->exec();
        
        // Get related commits
        $commits = $this->git->getRelatedCommits('task', 123);
        $this->assertNotEmpty($commits, 'No related commits found');
        $this->assertEquals(1, count($commits), 'Wrong number of related commits');
        $this->assertEquals('1234567890abcdef1234567890abcdef12345678', $commits[0]->commitID, 'Commit ID mismatch');
    }
}
