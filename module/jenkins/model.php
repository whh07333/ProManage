<?php
/**
 * The model file of jenkins module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      ZenTao Team
 * @package     jenkins
 * @link        https://www.zentao.net
 */
class jenkinsModel extends model
{
    /**
     * Get Jenkins servers.
     * 
     * @access public
     * @return array
     */
    public function getServers()
    {
        return $this->dao->select('*')->from(TABLE_JENKINS)->fetchAll();
    }
    
    /**
     * Get Jenkins server by ID.
     * 
     * @param  int $id
     * @access public
     * @return object
     */
    public function getServer($id)
    {
        return $this->dao->select('*')->from(TABLE_JENKINS)->where('id')->eq($id)->fetch();
    }
    
    /**
     * Create Jenkins server.
     * 
     * @param  object $server
     * @access public
     * @return int
     */
    public function createServer($server)
    {
        $this->dao->insert(TABLE_JENKINS)->data($server)->autoCheck()->exec();
        return $this->dao->lastInsertID();
    }
    
    /**
     * Update Jenkins server.
     * 
     * @param  object $server
     * @access public
     * @return bool
     */
    public function updateServer($server)
    {
        return $this->dao->update(TABLE_JENKINS)->data($server)->where('id')->eq($server->id)->exec();
    }
    
    /**
     * Delete Jenkins server.
     * 
     * @param  int $id
     * @access public
     * @return bool
     */
    public function deleteServer($id)
    {
        return $this->dao->delete()->from(TABLE_JENKINS)->where('id')->eq($id)->exec();
    }
    
    /**
     * Get Jenkins jobs for a server.
     * 
     * @param  object $server
     * @access public
     * @return array
     */
    public function getJobs($server)
    {
        $url = rtrim($server->url, '/') . '/api/json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$server->username}:{$server->token}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);
        
        if($response)
        {
            $data = json_decode($response);
            return $data->jobs ?? array();
        }
        return array();
    }
    
    /**
     * Get build status for a job.
     * 
     * @param  object $server
     * @param  string $jobName
     * @access public
     * @return object
     */
    public function getBuildStatus($server, $jobName)
    {
        $url = rtrim($server->url, '/') . "/job/{$jobName}/api/json";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$server->username}:{$server->token}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);
        
        if($response)
        {
            return json_decode($response);
        }
        return null;
    }
    
    /**
     * Handle Jenkins webhook event.
     * 
     * @param  array $data
     * @access public
     * @return void
     */
    public function handleWebhook($data)
    {
        // Get server by URL
        $serverURL = $data['serverUrl'];
        $server = $this->dao->select('*')->from(TABLE_JENKINS)->where('url')->eq($serverURL)->fetch();
        if(!$server) return;
        
        // Get job name and build information
        $jobName = $data['name'];
        $build = $data['build'];
        
        // Save build status
        $this->saveBuildStatus($server->id, $jobName, $build);
        
        // Parse commit messages for task/bug IDs
        if(isset($build['changeSet']['items']))
        {
            foreach($build['changeSet']['items'] as $change)
            {
                if(isset($change['msg']))
                {
                    $this->parseCommitMessage($server->id, $jobName, $build['number'], $change['msg']);
                }
            }
        }
        
        // Send notification for failed builds
        if($build['result'] == 'FAILURE')
        {
            $this->sendFailureNotification($server, $jobName, $build);
        }
    }
    
    /**
     * Save build status.
     * 
     * @param  int    $serverID
     * @param  string $jobName
     * @param  array  $build
     * @access public
     * @return int
     */
    public function saveBuildStatus($serverID, $jobName, $build)
    {
        $buildRecord = new stdclass();
        $buildRecord->serverID   = $serverID;
        $buildRecord->jobName    = $jobName;
        $buildRecord->buildNumber = $build['number'];
        $buildRecord->result     = $build['result'] ?? 'PENDING';
        $buildRecord->timestamp  = date('Y-m-d H:i:s', $build['timestamp'] / 1000);
        $buildRecord->duration   = $build['duration'] / 1000; // Convert to seconds
        $buildRecord->url        = $build['url'];
        $buildRecord->createdDate = helper::now();
        
        $this->dao->insert(TABLE_JENKINSBUILD)->data($buildRecord)->exec();
        return $this->dao->lastInsertID();
    }
    
    /**
     * Parse commit message for task/bug IDs.
     * 
     * @param  int    $serverID
     * @param  string $jobName
     * @param  int    $buildNumber
     * @param  string $message
     * @access public
     * @return void
     */
    public function parseCommitMessage($serverID, $jobName, $buildNumber, $message)
    {
        // Parse task IDs (e.g., #123)
        preg_match_all('/#(\d+)/', $message, $matches);
        if(!empty($matches[1]))
        {
            $taskIDs = array_unique($matches[1]);
            foreach($taskIDs as $taskID)
            {
                // Link build to task
                $link = new stdclass();
                $link->serverID   = $serverID;
                $link->jobName    = $jobName;
                $link->buildNumber = $buildNumber;
                $link->objectType = 'task';
                $link->objectID   = $taskID;
                $link->createdDate = helper::now();
                
                $this->dao->insert(TABLE_JENKINSBUILDRELATION)->data($link)->exec();
            }
        }
    }
    
    /**
     * Send failure notification.
     * 
     * @param  object $server
     * @param  string $jobName
     * @param  array  $build
     * @access public
     * @return void
     */
    public function sendFailureNotification($server, $jobName, $build)
    {
        // Get users associated with the job
        $users = $this->getJobUsers($jobName);
        if(empty($users)) return;
        
        $this->loadModel('message');
        $messageData = "Jenkins构建失败: {$jobName} #{$build['number']}";
        
        foreach($users as $user)
        {
            $notify = new stdclass();
            $notify->objectType  = 'message';
            $notify->action      = 0;
            $notify->toList      = ",{$user},";
            $notify->data        = $messageData;
            $notify->type        = 'task';
            $notify->status      = 'wait';
            $notify->createdBy   = 'system';
            $notify->createdDate = helper::now();
            $this->dao->insert(TABLE_NOTIFY)->data($notify)->exec();
        }
    }
    
    /**
     * Get users associated with a job.
     * 
     * @param  string $jobName
     * @access public
     * @return array
     */
    public function getJobUsers($jobName)
    {
        // This is a placeholder. In a real implementation, you would get users from job configuration or project association.
        return array();
    }
    
    /**
     * Get builds related to an object (task, bug).
     * 
     * @param  string $objectType
     * @param  int    $objectID
     * @access public
     * @return array
     */
    public function getRelatedBuilds($objectType, $objectID)
    {
        return $this->dao->select('*')->from(TABLE_JENKINSBUILDRELATION)
            ->leftJoin(TABLE_JENKINSBUILD)->alias('b')->on('b.id = relation.buildID')
            ->leftJoin(TABLE_JENKINS)->alias('s')->on('s.id = b.serverID')
            ->where('relation.objectType')->eq($objectType)
            ->andWhere('relation.objectID')->eq($objectID)
            ->fetchAll();
    }
}