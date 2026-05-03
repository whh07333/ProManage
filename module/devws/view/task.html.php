<?php
/**
 * Task detail view for devws drawer - two-column layout.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      whh
 * @package     devws
 * @version     $Id$
 */
?>
<?php if(!$task):?>
<div class="task-error">
    <div class="error-icon">!</div>
    <div class="error-text"><?php echo $lang->devws->taskNotFound ?? '任务不存在';?></div>
    <a href="javascript:;" onclick="parent.closeDrawer && parent.closeDrawer()" class="back-btn">返回工作台</a>
</div>
<?php else:?>
<div class="task-detail">
    <!-- ===== Title & Actions Bar ===== -->
    <div class="task-topbar">
        <div class="task-topbar-left">
            <?php
            $statusClass = $task->status == 'done' ? 'status-done' : ($task->status == 'doing' ? 'status-doing' : ($task->status == 'pause' ? 'status-pause' : 'status-wait'));
            $statusLabel = zget($lang->task->statusList, $task->status, $task->status);
            ?>
            <span class="task-status <?php echo $statusClass;?>"><?php echo $statusLabel;?></span>
            <h1 class="task-name"><?php echo $task->name;?></h1>
        </div>
        <div class="task-topbar-right">
            <span class="pri-tag pri-<?php echo $task->pri;?>">P<?php echo $task->pri;?></span>
            <span class="type-tag"><?php echo zget($lang->task->typeList, $task->type, $task->type);?></span>
        </div>
    </div>

    <!-- ===== Action Buttons ===== -->
    <div class="task-actions">
        <?php if($task->status == 'wait'):?>
        <a href="<?php echo helper::createLink('task', 'start', "taskID=$task->id", '', true);?>" class="action-btn start">▶ <?php echo $lang->task->start;?></a>
        <a href="<?php echo helper::createLink('devws', 'assignTo', "taskID=$task->id", '', true);?>" class="action-btn assign">👤 <?php echo $lang->task->assignTo;?></a>
        <?php elseif($task->status == 'doing'):?>
        <a href="<?php echo helper::createLink('task', 'finish', "taskID=$task->id", '', true);?>" class="action-btn finish">✅ <?php echo $lang->task->finish;?></a>
        <a href="<?php echo helper::createLink('task', 'pause', "taskID=$task->id", '', true);?>" class="action-btn pause">⏸ <?php echo $lang->task->pause;?></a>
        <a href="<?php echo helper::createLink('devws', 'assignTo', "taskID=$task->id", '', true);?>" class="action-btn assign">👤 <?php echo $lang->task->assignTo;?></a>
        <?php elseif(in_array($task->status, array('done', 'cancel', 'pause'))):?>
        <a href="<?php echo helper::createLink('task', 'activate', "taskID=$task->id", '', true);?>" class="action-btn activate">🔄 <?php echo $lang->task->activate;?></a>
        <?php endif;?>
        <?php if(!in_array($task->status, array('cancel', 'closed'))):?>
        <a href="<?php echo helper::createLink('task', 'cancel', "taskID=$task->id", '', true);?>" class="action-btn cancel">✕ <?php echo $lang->task->cancel;?></a>
        <?php endif;?>
    </div>

    <!-- ===== Two-Column Layout ===== -->
    <div class="task-two-col">
        <!-- Left Column: Tabs (Details, Subtasks, Bugs, History) -->
        <div class="task-col-left">

            <!-- Tabs Header -->
            <div class="task-tabs">
                <button class="tab-btn active" onclick="switchTab('detail')"><?php echo $lang->devws->detail ?? '详细信息';?></button>
                <button class="tab-btn" onclick="switchTab('subtask')"><?php echo $lang->task->children;?> <span class="tab-count"><?php echo count($task->subtasks);?></span></button>
                <button class="tab-btn" onclick="switchTab('bug')"><?php echo $lang->devws->bugs;?> <span class="tab-count"><?php echo count($task->bugs);?></span></button>
                <button class="tab-btn" onclick="switchTab('history')"><?php echo $lang->task->legendLife;?></button>
            </div>

            <!-- Tab: Detail -->
            <div class="tab-content active" id="tab-detail">
                <?php if(!empty($task->desc)):?>
                <div class="card-section">
                    <div class="card-section-title"><?php echo $lang->task->desc;?></div>
                    <div class="card-body task-desc-body"><?php echo htmlspecialchars_decode($task->desc);?></div>
                </div>
                <?php endif;?>

                <div class="card-section">
                    <div class="hours-bar">
                        <div class="hours-item">
                            <div class="hours-value"><?php echo $task->estimate;?></div>
                            <div class="hours-label"><?php echo $lang->task->estimate;?></div>
                        </div>
                        <div class="hours-arrow">→</div>
                        <div class="hours-item consumed">
                            <div class="hours-value"><?php echo $task->consumed;?></div>
                            <div class="hours-label"><?php echo $lang->task->consumed;?></div>
                        </div>
                        <div class="hours-arrow">→</div>
                        <div class="hours-item left">
                            <div class="hours-value"><?php echo $task->left;?></div>
                            <div class="hours-label"><?php echo $lang->task->left;?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Subtask -->
            <div class="tab-content" id="tab-subtask">
                <?php if(empty($task->subtasks)):?>
                <div class="empty-state"><?php echo $lang->devws->noTasks;?></div>
                <?php else:?>
                <table class="subtask-table">
                    <thead>
                        <tr>
                            <th class="w-50">ID</th>
                            <th><?php echo $lang->task->name;?></th>
                            <th class="w-70"><?php echo $lang->task->status;?></th>
                            <th class="w-50">P</th>
                            <th class="w-60"><?php echo $lang->task->assignedTo;?></th>
                            <th class="w-60"><?php echo $lang->task->estimate;?></th>
                            <th class="w-60"><?php echo $lang->task->consumed;?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($task->subtasks as $sub):?>
                        <?php $subStatusClass = $sub->status == 'done' ? 'status-done' : ($sub->status == 'doing' ? 'status-doing' : ($sub->status == 'pause' ? 'status-pause' : 'status-wait'));?>
                        <tr>
                            <td class="text-center"><?php echo $sub->id;?></td>
                            <td><a href="<?php echo helper::createLink('task', 'view', "taskID=$sub->id");?>" target="_blank" class="subtask-link"><?php echo $sub->name;?></a></td>
                            <td class="text-center"><span class="mini-status <?php echo $subStatusClass;?>"><?php echo zget($lang->task->statusList, $sub->status, $sub->status);?></span></td>
                            <td class="text-center"><span class="pri-dot pri-<?php echo $sub->pri;?>"><?php echo $sub->pri;?></span></td>
                            <td class="text-center"><?php echo $sub->assignedTo;?></td>
                            <td class="text-center"><?php echo $sub->estimate;?></td>
                            <td class="text-center"><?php echo $sub->consumed;?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php endif;?>
            </div>

            <!-- Tab: Bug -->
            <div class="tab-content" id="tab-bug">
                <?php if(empty($task->bugs)):?>
                <div class="empty-state"><?php echo $lang->devws->noBugs;?></div>
                <?php else:?>
                <table class="bug-table">
                    <thead>
                        <tr>
                            <th class="w-50">ID</th>
                            <th><?php echo $lang->task->name;?></th>
                            <th class="w-70"><?php echo $lang->bug->status;?></th>
                            <th class="w-60"><?php echo $lang->bug->severity;?></th>
                            <th class="w-50">P</th>
                            <th class="w-70"><?php echo $lang->bug->assignedTo;?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($task->bugs as $bug):?>
                        <?php
                        $sevClass = 'sev-' . $bug->severity;
                        $bugStatusClass = $bug->status == 'resolved' ? 'status-done' : ($bug->status == 'active' ? 'status-doing' : 'status-pause');
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $bug->id;?></td>
                            <td><a href="<?php echo helper::createLink('bug', 'view', "bugID=$bug->id");?>" target="_blank" class="subtask-link"><?php echo $bug->title;?></a></td>
                            <td class="text-center"><span class="mini-status <?php echo $bugStatusClass;?>"><?php echo zget($lang->bug->statusList, $bug->status, $bug->status);?></span></td>
                            <td class="text-center"><span class="sev-tag <?php echo $sevClass;?>"><?php echo $bug->severity;?></span></td>
                            <td class="text-center"><span class="pri-dot pri-<?php echo $bug->pri;?>"><?php echo $bug->pri;?></span></td>
                            <td class="text-center"><?php echo $bug->assignedTo;?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php endif;?>
            </div>

            <!-- Tab: History -->
            <div class="tab-content" id="tab-history">
                <?php if(empty($task->actions)):?>
                <div class="empty-state"><?php echo $lang->devws->noTasks;?></div>
                <?php else:?>
                <div class="history-timeline">
                    <?php foreach($task->actions as $action):?>
                    <?php
                    $date = date('Y-m-d', strtotime($action->date));
                    $time = date('H:i', strtotime($action->date));
                    $actionKey  = $action->action;
                    $actionName = isset($lang->action->label->$actionKey) ? $lang->action->label->$actionKey : $actionKey;
                    ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="timeline-action"><?php echo $actionName;?></span>
                                <span class="timeline-user"><?php echo $action->actor;?></span>
                            </div>
                            <?php if(!empty($action->comment)):?>
                            <div class="timeline-comment"><?php echo htmlspecialchars_decode($action->comment);?></div>
                            <?php endif;?>
                            <div class="timeline-time"><?php echo $date;?> <?php echo $time;?></div>
                        </div>
                    </div>
                    <?php endforeach;?>
                </div>
                <?php endif;?>
            </div>
        </div>

        <!-- Right Column: Basic Info Panel -->
        <div class="task-col-right">
            <div class="info-panel">
                <div class="info-panel-title"><?php echo $lang->devws->myWork;?></div>

                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->status;?></span>
                    <span class="info-row-value"><span class="task-status <?php echo $statusClass;?>"><?php echo $statusLabel;?></span></span>
                </div>

                <?php if($task->story):?>
                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->story;?></span>
                    <span class="info-row-value">
                        <a href="<?php echo helper::createLink('story', 'view', "storyID=$task->story");?>" target="_blank" class="info-link">#<?php echo $task->story;?> <?php echo $task->storyTitle;?></a>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->story->category;?></span>
                    <span class="info-row-value"><?php echo zget($lang->story->categoryList, $task->storyCategory, $task->storyCategory ?: '-');?></span>
                </div>
                <?php endif;?>

                <?php if($task->module):?>
                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->module;?></span>
                    <span class="info-row-value"><?php echo $task->moduleName ?: '-';?></span>
                </div>
                <?php endif;?>

                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->execution;?></span>
                    <span class="info-row-value"><?php echo $task->executionName ?: '-';?></span>
                </div>

                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->pri;?></span>
                    <span class="info-row-value"><span class="pri-tag pri-<?php echo $task->pri;?>">P<?php echo $task->pri;?></span></span>
                </div>

                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->assignedTo;?></span>
                    <span class="info-row-value"><?php echo $task->assignedToName;?></span>
                </div>

                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->openedBy;?></span>
                    <span class="info-row-value"><?php echo $task->openedByName;?></span>
                </div>

                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->estStarted;?></span>
                    <span class="info-row-value"><?php echo $task->estStarted ?: '-';?></span>
                </div>

                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->deadline;?></span>
                    <span class="info-row-value <?php echo ($task->deadline && $task->deadline < date('Y-m-d') && $task->status != 'done') ? 'text-overdue' : '';?>"><?php echo $task->deadline ?: '-';?></span>
                </div>

                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->openedDate;?></span>
                    <span class="info-row-value"><?php echo $task->openedDate ? date('Y-m-d H:i', strtotime($task->openedDate)) : '-';?></span>
                </div>

                <?php if($task->finishedDate):?>
                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->finishedDate;?></span>
                    <span class="info-row-value"><?php echo date('Y-m-d H:i', strtotime($task->finishedDate));?></span>
                </div>
                <?php endif;?>

                <?php if($task->project):?>
                <div class="info-row">
                    <span class="info-row-label"><?php echo $lang->task->project;?></span>
                    <span class="info-row-value"><?php echo $task->projectName;?></span>
                </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>

<style>
/* ======= Base ======= */
.task-detail { padding: 20px 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #333; }
.task-error { padding: 80px 24px; text-align: center; color: #999; }
.task-error .error-icon { width: 64px; height: 64px; border-radius: 50%; background: #ff4d4f; color: #fff; font-size: 28px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.task-error .error-text { font-size: 16px; margin-bottom: 24px; }
.task-error .back-btn { display: inline-block; padding: 8px 24px; background: #1890ff; color: #fff; text-decoration: none; border-radius: 4px; font-size: 14px; }
.task-error .back-btn:hover { background: #40a9ff; }

/* ======= Top Bar ======= */
.task-topbar { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; gap: 16px; }
.task-topbar-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.task-topbar-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.task-name { font-size: 20px; font-weight: 600; color: #1a1a2e; margin: 0; line-height: 1.4; word-break: break-word; }
.task-status { font-size: 12px; padding: 4px 12px; border-radius: 12px; font-weight: 500; flex-shrink: 0; white-space: nowrap; }
.status-wait { background: #fff7e6; color: #d46b08; }
.status-doing { background: #e6f7ff; color: #096dd9; }
.status-done { background: #f6ffed; color: #389e0d; }
.status-pause { background: #fff2e8; color: #d4380d; }

/* ======= Action Buttons ======= */
.task-actions { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
.action-btn { padding: 8px 18px; border-radius: 6px; font-size: 13px; font-weight: 500; text-decoration: none; border: 1px solid #d9d9d9; background: #fff; color: #333; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
.action-btn:hover { opacity: 0.85; transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.action-btn.start { background: #1890ff; color: #fff; border-color: #1890ff; }
.action-btn.finish { background: #52c41a; color: #fff; border-color: #52c41a; }
.action-btn.pause { background: #fa8c16; color: #fff; border-color: #fa8c16; }
.action-btn.assign { background: #722ed1; color: #fff; border-color: #722ed1; }
.action-btn.activate { background: #eb2f96; color: #fff; border-color: #eb2f96; }
.action-btn.cancel { border-color: #d9d9d9; color: #999; }
.action-btn.cancel:hover { border-color: #ff4d4f; color: #ff4d4f; }

/* ======= Two-Column Layout ======= */
.task-two-col { display: flex; gap: 24px; align-items: flex-start; }
.task-col-left { flex: 1; min-width: 0; }
.task-col-right { width: 300px; flex-shrink: 0; }

/* ======= Card Section (Left) ======= */
.card-section { background: #fff; border: 1px solid #e8e8e8; border-radius: 8px; margin-bottom: 16px; overflow: hidden; }
.card-section-title { font-size: 14px; font-weight: 600; color: #1a1a2e; padding: 14px 18px; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
.card-body { padding: 16px 18px; }
.empty-state { padding: 24px 18px; text-align: center; color: #bfbfbf; font-size: 13px; }

/* ======= Hours Bar ======= */
.hours-bar { display: flex; align-items: center; gap: 16px; padding: 16px 24px; }
.hours-item { text-align: center; flex: 1; }
.hours-value { font-size: 24px; font-weight: 700; color: #1890ff; }
.hours-item.consumed .hours-value { color: #722ed1; }
.hours-item.left .hours-value { color: #fa8c16; }
.hours-label { font-size: 12px; color: #8c8c8c; margin-top: 4px; }
.hours-arrow { color: #d9d9d9; font-size: 18px; }

/* ======= Description ======= */
.task-desc-body { font-size: 14px; line-height: 1.8; color: #333; }
.task-desc-body img { max-width: 100%; }
.task-desc-body table { border-collapse: collapse; width: 100%; }
.task-desc-body td, .task-desc-body th { border: 1px solid #e8e8e8; padding: 8px 12px; }

/* ======= Subtask / Bug Tables ======= */
.subtask-table, .bug-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.subtask-table th, .bug-table th { background: #fafafa; color: #595959; font-weight: 600; padding: 10px 8px; text-align: left; border-bottom: 1px solid #e8e8e8; }
.subtask-table td, .bug-table td { padding: 10px 8px; border-bottom: 1px solid #f5f5f5; color: #333; }
.subtask-table tr:hover, .bug-table tr:hover { background: #fafafa; }
.subtask-table .w-50, .bug-table .w-50 { width: 50px; }
.subtask-table .w-60, .bug-table .w-60 { width: 60px; }
.subtask-table .w-70, .bug-table .w-70 { width: 70px; }
.text-center { text-align: center; }
.mini-status { font-size: 11px; padding: 2px 8px; border-radius: 8px; font-weight: 500; white-space: nowrap; }
.subtask-link { color: #1890ff; text-decoration: none; }
.subtask-link:hover { text-decoration: underline; }
.pri-dot { font-size: 11px; padding: 1px 6px; border-radius: 3px; font-weight: 600; color: #fff; }
.pri-1 { background: #ff4d4f; }
.pri-2 { background: #fa8c16; }
.pri-3 { background: #1890ff; }
.pri-4 { background: #8c8c8c; }
.type-tag { font-size: 11px; padding: 2px 10px; border-radius: 4px; background: #f0f0f0; color: #595959; font-weight: 500; white-space: nowrap; }
.sev-tag { font-size: 11px; padding: 1px 8px; border-radius: 3px; font-weight: 600; color: #fff; }
.sev-1 { background: #ff4d4f; }
.sev-2 { background: #fa8c16; }
.sev-3 { background: #1890ff; }
.sev-4 { background: #8c8c8c; }

/* ======= History Timeline ======= */
.history-timeline { padding: 12px 18px; }
.timeline-item { position: relative; padding-left: 24px; padding-bottom: 20px; border-left: 2px solid #e8e8e8; margin-left: 6px; }
.timeline-item:last-child { border-left-color: transparent; padding-bottom: 0; }
.timeline-dot { position: absolute; left: -7px; top: 4px; width: 12px; height: 12px; border-radius: 50%; background: #1890ff; border: 2px solid #fff; box-shadow: 0 0 0 2px #1890ff; }
.timeline-content { }
.timeline-header { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
.timeline-action { font-size: 13px; font-weight: 600; color: #262626; }
.timeline-user { font-size: 12px; color: #8c8c8c; }
.timeline-comment { font-size: 13px; color: #595959; margin: 4px 0; padding: 8px 12px; background: #fafafa; border-radius: 4px; line-height: 1.6; }
.timeline-time { font-size: 11px; color: #bfbfbf; margin-top: 2px; }

/* ======= Info Panel (Right) ======= */
.info-panel { background: #fff; border: 1px solid #e8e8e8; border-radius: 8px; overflow: hidden; position: sticky; top: 0; }
.info-panel-title { font-size: 14px; font-weight: 600; color: #1a1a2e; padding: 14px 18px; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
.info-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; border-bottom: 1px solid #f5f5f5; gap: 12px; }
.info-row:last-child { border-bottom: none; }
.info-row-label { font-size: 12px; color: #8c8c8c; flex-shrink: 0; }
.info-row-value { font-size: 13px; color: #262626; font-weight: 500; text-align: right; word-break: break-all; }
.info-row-value .task-status { font-size: 11px; padding: 2px 10px; }
.info-row-value .pri-tag { font-size: 11px; padding: 1px 8px; }
.info-link { color: #1890ff; text-decoration: none; }
.info-link:hover { text-decoration: underline; }
.text-overdue { color: #cf1322 !important; font-weight: 600 !important; }

/* ======= Pri Tags Override for Right Panel ======= */
.info-row-value .pri-1, .info-row-value .pri-2, .info-row-value .pri-3, .info-row-value .pri-4 { display: inline-block; }

/* ======= Hide ZenTao chrome ======= */
#header, #menu, #appsBar, .navbar, .breadcrumb, #footer { display: none !important; }

/* ======= Tabs ======= */
.task-tabs { display: flex; gap: 0; border-bottom: 2px solid #e8e8e8; margin-bottom: 16px; }
.tab-btn { padding: 10px 20px; font-size: 13px; font-weight: 500; color: #8c8c8c; background: none; border: none; cursor: pointer; position: relative; transition: color 0.2s; }
.tab-btn:hover { color: #1890ff; }
.tab-btn.active { color: #1890ff; }
.tab-btn.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: #1890ff; }
.tab-count { display: inline-block; background: #f0f0f0; color: #8c8c8c; font-size: 11px; padding: 0 6px; border-radius: 8px; margin-left: 4px; }
.tab-btn.active .tab-count { background: #e6f7ff; color: #1890ff; }

.tab-content { display: none; }
.tab-content.active { display: block; }

/* ======= Responsive ======= */
@media (max-width: 900px) {
    .task-two-col { flex-direction: column; }
    .task-col-right { width: 100%; }
    .info-panel { position: static; }
    .tab-btn { padding: 8px 12px; font-size: 12px; }
}
</style>

<script>
function switchTab(tabName)
{
    var container = document.querySelector('.task-tabs');
    if(!container) return;
    var btns = container.querySelectorAll('.tab-btn');
    for(var i = 0; i < btns.length; i++) btns[i].classList.remove('active');
    var activeBtn = container.querySelector('.tab-btn[onclick*="' + tabName + '"]');
    if(activeBtn) activeBtn.classList.add('active');

    var contents = document.querySelectorAll('.tab-content');
    for(var j = 0; j < contents.length; j++) contents[j].classList.remove('active');
    var activeContent = document.getElementById('tab-' + tabName);
    if(activeContent) activeContent.classList.add('active');
}
</script>
<?php endif;?>
