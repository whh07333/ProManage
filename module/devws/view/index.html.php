<?php
/**
 * 开发者工作台视图文件。
 * Developer workspace view file.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      whh
 * @package     devws
 * @version     $Id$
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->devws->common . ' - ' . $app->company->name;?></title>
    <?php js::set('webRoot', $webRoot);?>
    <?php js::set('appName', $app->getAppName());?>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: #f0f2f5; }
        
        .workspace { display: flex; height: 100vh; }
        
        /* 左侧菜单栏 */
        .sidebar { width: 220px; background: #001529; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; }
        
        .sidebar-header { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h1 { font-size: 18px; font-weight: 600; color: #fff; margin: 0; }
        .sidebar-header .subtitle { font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 4px; }
        
        .user-info { padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 12px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 16px; color: #fff; flex-shrink: 0; }
        .user-name { font-size: 14px; color: #fff; }
        .user-role { font-size: 12px; color: rgba(255,255,255,0.65); }
        
        .menu { flex: 1; padding: 12px 0; overflow-y: auto; }
        .menu-item { padding: 12px 20px; cursor: pointer; display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.85); transition: all 0.3s; border-left: 3px solid transparent; }
        .menu-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .menu-item.active { background: #1890ff; color: #fff; border-left-color: #fff; }
        .menu-item .icon { font-size: 16px; width: 20px; text-align: center; }
        .menu-item .text { font-size: 14px; }
        .menu-item .badge { margin-left: auto; background: #ff4d4f; color: #fff; font-size: 12px; padding: 2px 8px; border-radius: 10px; }
        
        .menu-group { margin-bottom: 8px; }
        .menu-group-title { padding: 12px 20px 8px; font-size: 12px; color: rgba(255,255,255,0.45); text-transform: uppercase; }
        
        .sidebar-footer { padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-footer a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .sidebar-footer a:hover { color: #fff; }
        
        /* 右侧内容区 */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        
        .header { height: 56px; background: #fff; border-bottom: 1px solid #e8e8e8; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; flex-shrink: 0; }
        .header-title { font-size: 16px; font-weight: 500; color: #333; }
        .header-actions { display: flex; gap: 12px; }
        .header-actions .btn { padding: 6px 16px; border: 1px solid #d9d9d9; background: #fff; border-radius: 4px; cursor: pointer; font-size: 14px; transition: all 0.3s; }
        .header-actions .btn:hover { border-color: #1890ff; color: #1890ff; }
        .header-actions .btn-primary { background: #1890ff; border-color: #1890ff; color: #fff; }
        .header-actions .btn-primary:hover { background: #40a9ff; }
        
        .content { flex: 1; padding: 24px; overflow-y: auto; }
        
        /* 面板样式 */
        .panel { background: #fff; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-bottom: 16px; }
        .panel-header { padding: 16px 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; }
        .panel-title { font-size: 15px; font-weight: 500; color: #333; display: flex; align-items: center; gap: 8px; }
        .panel-title .icon { color: #1890ff; }
        .panel-body { padding: 16px 20px; }
        
        /* 列表项 */
        .list-item { padding: 12px 0; border-bottom: 1px solid #f5f5f5; display: flex; align-items: center; gap: 12px; }
        .list-item:last-child { border-bottom: none; }
        .list-item .priority { width: 20px; height: 20px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #fff; flex-shrink: 0; }
        .list-item .priority.p1 { background: #ff4d4f; }
        .list-item .priority.p2 { background: #fa8c16; }
        .list-item .priority.p3 { background: #1890ff; }
        .list-item .title { flex: 1; color: #333; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .list-item .title:hover { color: #1890ff; }
        .list-item .status { font-size: 12px; padding: 2px 8px; border-radius: 4px; flex-shrink: 0; }
        .list-item .status.doing { background: #e6f7ff; color: #1890ff; }
        .list-item .status.wait { background: #fff7e6; color: #fa8c16; }
        .list-item .status.done { background: #f6ffed; color: #52c41a; }
        
        .empty-state { text-align: center; padding: 40px 20px; color: #999; }
        .empty-state .icon { font-size: 48px; margin-bottom: 16px; opacity: 0.5; }
        
        /* 项目卡片 */
        .project-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        .project-card { background: #fafafa; border-radius: 8px; padding: 16px; cursor: pointer; transition: all 0.3s; text-decoration: none; display: block; }
        .project-card:hover { background: #f0f0f0; transform: translateY(-2px); }
        .project-card .name { font-size: 14px; font-weight: 500; color: #333; margin-bottom: 8px; }
        .project-card .meta { font-size: 12px; color: #999; }
        .project-card .progress { margin-top: 12px; height: 4px; background: #e8e8e8; border-radius: 2px; overflow: hidden; }
        .project-card .progress-bar { height: 100%; background: #1890ff; border-radius: 2px; }
        
        /* 文档列表 */
        .doc-item { padding: 12px 0; border-bottom: 1px solid #f5f5f5; display: flex; align-items: center; gap: 12px; }
        .doc-item:last-child { border-bottom: none; }
        .doc-item .doc-icon { width: 32px; height: 32px; background: #e6f7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #1890ff; flex-shrink: 0; }
        .doc-item .doc-info { flex: 1; min-width: 0; }
        .doc-item .doc-title { font-size: 14px; color: #333; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .doc-item .doc-meta { font-size: 12px; color: #999; }
        
        .view-all { color: #1890ff; font-size: 14px; text-decoration: none; }
        .view-all:hover { text-decoration: underline; }
        
        /* 隐藏禅道默认的头部和菜单 */
        #header, #mainmenu, #modulemenu, .outer > .navbar, .navbar-fixed-top { display: none !important; }
        .outer { padding: 0 !important; }
        .container { max-width: 100% !important; padding: 0 !important; }
    </style>
</head>
<body>
    <div class="workspace">
        <!-- 左侧菜单栏 -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h1><?php echo $lang->devws->common;?></h1>
                <div class="subtitle">Developer Workspace</div>
            </div>
            
            <div class="user-info">
                <div class="user-avatar"><?php echo mb_substr($app->user->realname, 0, 1);?></div>
                <div>
                    <div class="user-name"><?php echo $app->user->realname;?></div>
                    <div class="user-role"><?php echo zget($lang->user->roleList, $app->user->role, '');?></div>
                </div>
            </div>
            
            <div class="menu">
                <div class="menu-group">
                    <div class="menu-group-title">工作台</div>
                    <div class="menu-item active" data-type="work">
                        <span class="icon">📋</span>
                        <span class="text"><?php echo $lang->devws->myWork;?></span>
                        <?php $workCount = count($tasks) + count($bugs) + count($todos);?>
                        <?php if($workCount > 0):?>
                        <span class="badge"><?php echo $workCount;?></span>
                        <?php endif;?>
                    </div>
                    <div class="menu-item" data-type="doc">
                        <span class="icon">📄</span>
                        <span class="text"><?php echo $lang->devws->myDocs;?></span>
                        <?php if(count($docs) > 0):?>
                        <span class="badge"><?php echo count($docs);?></span>
                        <?php endif;?>
                    </div>
                    <div class="menu-item" data-type="project">
                        <span class="icon">📁</span>
                        <span class="text"><?php echo $lang->devws->myProjects;?></span>
                    </div>
                </div>
                
                <div class="menu-group">
                    <div class="menu-group-title">快捷入口</div>
                    <?php if(common::hasPriv('bug', 'create')):?>
                    <div class="menu-item" onclick="location.href='<?php echo helper::createLink('bug', 'create');?>'">
                        <span class="icon">🐛</span>
                        <span class="text"><?php echo $lang->bug->create;?></span>
                    </div>
                    <?php endif;?>
                    <?php if(common::hasPriv('story', 'create')):?>
                    <div class="menu-item" onclick="location.href='<?php echo helper::createLink('story', 'create');?>'">
                        <span class="icon">📝</span>
                        <span class="text"><?php echo $lang->story->create;?></span>
                    </div>
                    <?php endif;?>
                </div>
            </div>
            
            <div class="sidebar-footer">
                <a href="<?php echo helper::createLink('user', 'logout');?>">🚪 <?php echo $lang->logout;?></a>
            </div>
        </div>
        
        <!-- 右侧内容区 -->
        <div class="main-content">
            <div class="header">
                <div class="header-title" id="pageTitle"><?php echo $lang->devws->myWork;?></div>
                <div class="header-actions">
                    <button class="btn" onclick="location.reload();">刷新</button>
                    <?php if(common::hasPriv('task', 'create')):?>
                    <button class="btn btn-primary" onclick="location.href='<?php echo helper::createLink('task', 'create');?>'">+ <?php echo $lang->task->create;?></button>
                    <?php endif;?>
                </div>
            </div>
            
            <div class="content">
                <!-- 我的工作内容 -->
                <div id="work-content" class="content-section">
                    <!-- 待处理任务 -->
                    <div class="panel">
                        <div class="panel-header">
                            <div class="panel-title">
                                <span class="icon">📋</span>
                                <?php echo $lang->devws->tasks;?> (<?php echo count($tasks);?>)
                            </div>
                            <?php if(common::hasPriv('my', 'work')):?>
                            <a href="<?php echo helper::createLink('my', 'work', 'type=task');?>" class="view-all">查看全部 →</a>
                            <?php endif;?>
                        </div>
                        <div class="panel-body">
                            <?php if(empty($tasks)): ?>
                            <div class="empty-state">
                                <div class="icon">📋</div>
                                <div><?php echo $lang->devws->noTasks;?></div>
                            </div>
                            <?php else: ?>
                                <?php foreach($tasks as $task): ?>
                                <div class="list-item">
                                    <span class="priority p<?php echo $task->pri;?>"><?php echo $task->pri;?></span>
                                    <a href="<?php echo helper::createLink('task', 'view', "taskID=$task->id");?>" class="title"><?php echo $task->name;?></a>
                                    <span class="status <?php echo $task->status == 'doing' ? 'doing' : ($task->status == 'done' ? 'done' : 'wait');?>"><?php echo zget($lang->task->statusList, $task->status);?></span>
                                </div>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
                    </div>
                    
                    <!-- 待处理Bug -->
                    <div class="panel">
                        <div class="panel-header">
                            <div class="panel-title">
                                <span class="icon">🐛</span>
                                <?php echo $lang->devws->bugs;?> (<?php echo count($bugs);?>)
                            </div>
                            <?php if(common::hasPriv('my', 'work')):?>
                            <a href="<?php echo helper::createLink('my', 'work', 'type=bug');?>" class="view-all">查看全部 →</a>
                            <?php endif;?>
                        </div>
                        <div class="panel-body">
                            <?php if(empty($bugs)): ?>
                            <div class="empty-state">
                                <div class="icon">🐛</div>
                                <div><?php echo $lang->devws->noBugs;?></div>
                            </div>
                            <?php else: ?>
                                <?php foreach($bugs as $bug): ?>
                                <div class="list-item">
                                    <span class="priority p<?php echo $bug->pri;?>"><?php echo $bug->pri;?></span>
                                    <a href="<?php echo helper::createLink('bug', 'view', "bugID=$bug->id");?>" class="title"><?php echo $bug->title;?></a>
                                    <span class="status <?php echo $bug->status == 'active' ? 'doing' : ($bug->status == 'resolved' ? 'done' : 'wait');?>"><?php echo zget($lang->bug->statusList, $bug->status);?></span>
                                </div>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
                    </div>
                    
                    <!-- 待办事项 -->
                    <div class="panel">
                        <div class="panel-header">
                            <div class="panel-title">
                                <span class="icon">📝</span>
                                <?php echo $lang->devws->todos;?> (<?php echo count($todos);?>)
                            </div>
                            <?php if(common::hasPriv('my', 'todo')):?>
                            <a href="<?php echo helper::createLink('my', 'todo');?>" class="view-all">查看全部 →</a>
                            <?php endif;?>
                        </div>
                        <div class="panel-body">
                            <?php if(empty($todos)): ?>
                            <div class="empty-state">
                                <div class="icon">📝</div>
                                <div><?php echo $lang->devws->noTodos;?></div>
                            </div>
                            <?php else: ?>
                                <?php foreach($todos as $todo): ?>
                                <div class="list-item">
                                    <span class="priority p2">!</span>
                                    <a href="<?php echo helper::createLink('todo', 'view', "todoID=$todo->id");?>" class="title"><?php echo $todo->name;?></a>
                                    <span class="status wait"><?php echo $todo->date;?></span>
                                </div>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                
                <!-- 我的文档内容 -->
                <div id="doc-content" class="content-section" style="display: none;">
                    <div class="panel">
                        <div class="panel-header">
                            <div class="panel-title">
                                <span class="icon">📄</span>
                                <?php echo $lang->devws->myDocs;?> (<?php echo count($docs);?>)
                            </div>
                            <?php if(common::hasPriv('doc', 'create')):?>
                            <button class="btn btn-primary" onclick="location.href='<?php echo helper::createLink('doc', 'create');?>'" style="padding: 4px 12px; font-size: 13px;">+ <?php echo $lang->devws->createDoc;?></button>
                            <?php endif;?>
                        </div>
                        <div class="panel-body">
                            <?php if(empty($docs)): ?>
                            <div class="empty-state">
                                <div class="icon">📄</div>
                                <div><?php echo $lang->devws->noDocs;?></div>
                            </div>
                            <?php else: ?>
                                <?php foreach($docs as $doc): ?>
                                <div class="doc-item">
                                    <div class="doc-icon">📄</div>
                                    <div class="doc-info">
                                        <a href="<?php echo helper::createLink('doc', 'view', "docID=$doc->id");?>" class="doc-title"><?php echo $doc->title;?></a>
                                        <div class="doc-meta">编辑于 <?php echo substr($doc->addedDate, 0, 10);?> · <?php echo zget($users, $doc->addedBy);?></div>
                                    </div>
                                </div>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                
                <!-- 我参与的项目内容 -->
                <div id="project-content" class="content-section" style="display: none;">
                    <div class="panel">
                        <div class="panel-header">
                            <div class="panel-title">
                                <span class="icon">📁</span>
                                <?php echo $lang->devws->myProjects;?> (<?php echo count($projects);?>)
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php if(empty($projects)): ?>
                            <div class="empty-state">
                                <div class="icon">📁</div>
                                <div><?php echo $lang->devws->noProjects;?></div>
                            </div>
                            <?php else: ?>
                            <div class="project-grid">
                                <?php foreach($projects as $project): ?>
                                <a href="<?php echo helper::createLink('project', 'index', "projectID=$project->id");?>" class="project-card">
                                    <div class="name"><?php echo $project->name;?></div>
                                    <div class="meta"><?php echo zget($lang->project->statusList, $project->status);?> · <?php echo $project->team ? $project->team . '人参与' : '';?></div>
                                    <?php if(!empty($project->hours) && $project->hours->totalEstimate > 0): ?>
                                    <?php $progress = round($project->hours->totalConsumed / $project->hours->totalEstimate * 100);?>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo min($progress, 100);?>%"></div>
                                    </div>
                                    <?php endif;?>
                                </a>
                                <?php endforeach;?>
                            </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function()
    {
        var menuItems = document.querySelectorAll('.menu-item[data-type]');
        var contentSections = document.querySelectorAll('.content-section');
        var pageTitle = document.getElementById('pageTitle');
        
        var titles = {
            'work': '<?php echo $lang->devws->myWork;?>',
            'doc': '<?php echo $lang->devws->myDocs;?>',
            'project': '<?php echo $lang->devws->myProjects;?>'
        };
        
        menuItems.forEach(function(item) {
            item.addEventListener('click', function() {
                var type = this.getAttribute('data-type');
                
                menuItems.forEach(function(mi) {
                    mi.classList.remove('active');
                });
                this.classList.add('active');
                
                contentSections.forEach(function(section) {
                    section.style.display = 'none';
                });
                document.getElementById(type + '-content').style.display = 'block';
                
                pageTitle.textContent = titles[type];
            });
        });
    });
    </script>
</body>
</html>
