<?php
/**
 * Project dashboard view for devws main content area.
 * Shows project overview data styled like the original ZenTao project dashboard.
 */
?>
<?php if(!$project):?>
<div class="project-error" style="padding:60px 20px;text-align:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#333;">
    <div style="font-size:48px;color:#ff4d4f;margin-bottom:16px;">!</div>
    <div style="font-size:16px;color:#595959;margin-bottom:24px;">项目不存在</div>
    <a href="javascript:;" onclick="parent.cancelProjectView && parent.cancelProjectView()" style="display:inline-block;padding:8px 24px;border-radius:6px;font-size:14px;border:1px solid #d9d9d9;background:#fff;color:#595959;text-decoration:none;">返回项目列表</a>
</div>
<?php else:?>
<?php
$statusName = zget($lang->project->statusList, $project->status, $project->status);
$aclName    = zget($lang->project->aclList, $project->acl, $project->acl);
$modelName  = zget($lang->project->modelList, $project->model, $project->model);

/* Show delay label. */
$isDelayed = false;
$delayDays = 0;
if($project->status != 'closed' && $project->end && $project->end != '0000-00-00')
{
    $endTime = strtotime($project->end);
    $nowTime = time();
    if($endTime && $endTime < $nowTime)
    {
        $delayDays = floor(($nowTime - $endTime) / 86400);
        $isDelayed = true;
    }
}
$progress = (int)$project->progress;
if(!$progress && $project->status == 'closed') $progress = 100;
?>
<div class="pd-wrap" style="padding:20px 24px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#333;max-width:1000px;margin:0 auto;">

    <!-- ===== 顶部信息条 ===== -->
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #e8e8e8;">
        <span class="pd-status-badge" style="display:inline-block;padding:2px 10px;border-radius:3px;font-size:12px;font-weight:500;color:#fff;background:<?php echo $project->status == 'doing' ? '#1890ff' : ($project->status == 'closed' ? '#999' : ($isDelayed ? '#ff4d4f' : '#faad14'));?>"><?php echo $statusName;?></span>
        <?php if($isDelayed):?>
        <span class="pd-status-badge" style="display:inline-block;padding:2px 10px;border-radius:3px;font-size:12px;font-weight:500;color:#fff;background:#ff4d4f;">延期 <?php echo $delayDays;?> 天</span>
        <?php endif;?>
        <h1 style="font-size:20px;font-weight:600;color:#1a1a2e;margin:0;"><?php echo $project->name;?></h1>
        <span style="font-size:12px;color:#999;background:#f5f5f5;padding:1px 8px;border-radius:3px;"><?php echo $project->code;?></span>
        <span style="font-size:12px;color:#bbb;">#<?php echo $project->id;?></span>
        <?php if($modelName):?><span style="font-size:12px;color:#666;background:#e6f7ff;padding:1px 8px;border-radius:3px;"><?php echo $modelName;?></span><?php endif;?>
        <span style="font-size:12px;color:#666;background:#f0f5ff;padding:1px 8px;border-radius:3px;"><?php echo $aclName;?></span>
    </div>

    <!-- ===== 标签导航 ===== -->
    <div style="display:flex;gap:0;margin-bottom:12px;border-bottom:1px solid #e8e8e8;">
        <a href="javascript:;" style="padding:10px 20px;font-size:14px;color:#1890ff;text-decoration:none;border-bottom:2px solid #1890ff;font-weight:500;"><?php echo $lang->devws->kanbanOverview;?></a>
        <a href="javascript:;" onclick="parent.openKanbanView(<?php echo $project->id;?>)" style="padding:10px 20px;font-size:14px;color:#666;text-decoration:none;border-bottom:2px solid transparent;transition:all 0.2s;"><?php echo $lang->devws->kanban;?></a>
    </div>

    <!-- ===== 统计卡片 ===== -->
    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
        <div class="pd-stat-card" style="flex:1;min-width:120px;background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;text-align:center;">
            <div style="font-size:26px;font-weight:700;color:#1890ff;"><?php echo (int)$statData->taskCount;?></div>
            <div style="font-size:12px;color:#888;margin-top:2px;"><?php echo $lang->project->tasks ?? '任务';?></div>
        </div>
        <div class="pd-stat-card" style="flex:1;min-width:120px;background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;text-align:center;">
            <div style="font-size:26px;font-weight:700;color:#52c41a;"><?php echo (int)$statData->storyCount;?></div>
            <div style="font-size:12px;color:#888;margin-top:2px;"><?php echo $lang->project->storyCount ?? '需求';?></div>
        </div>
        <div class="pd-stat-card" style="flex:1;min-width:120px;background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;text-align:center;">
            <div style="font-size:26px;font-weight:700;color:#ff4d4f;"><?php echo (int)$statData->bugCount;?></div>
            <div style="font-size:12px;color:#888;margin-top:2px;"><?php echo $lang->project->bugs ?? 'Bug';?></div>
        </div>
        <div class="pd-stat-card" style="flex:1;min-width:120px;background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;text-align:center;">
            <div style="font-size:26px;font-weight:700;color:#722ed1;"><?php echo count($executions);?></div>
            <div style="font-size:12px;color:#888;margin-top:2px;"><?php echo $lang->project->executions ?? '执行';?></div>
        </div>
        <div class="pd-stat-card" style="flex:1;min-width:120px;background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;text-align:center;">
            <div style="font-size:26px;font-weight:700;color:#fa8c16;"><?php echo count($teamMembers);?></div>
            <div style="font-size:12px;color:#888;margin-top:2px;"><?php echo $lang->project->team ?? '团队';?></div>
        </div>
    </div>

    <!-- ===== 进度条 ===== -->
    <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-size:13px;color:#555;font-weight:500;">项目进度</span>
            <span style="font-size:14px;font-weight:600;color:#1a1a2e;"><?php echo $progress;?>%</span>
        </div>
        <div style="height:8px;background:#f0f0f0;border-radius:4px;overflow:hidden;">
            <div style="height:100%;width:<?php echo min($progress, 100);?>%;background:<?php echo $progress >= 100 ? '#52c41a' : '#1890ff';?>;border-radius:4px;transition:width 0.3s;"></div>
        </div>
    </div>

    <!-- ===== 双栏布局 ===== -->
    <div style="display:flex;gap:16px;margin-bottom:16px;flex-wrap:wrap;">

        <!-- 左栏：基本信息 + 描述 -->
        <div style="flex:1.2;min-width:300px;display:flex;flex-direction:column;gap:16px;">

            <!-- 基本信息 -->
            <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;">
                <h3 style="font-size:14px;font-weight:600;color:#1a1a2e;margin:0 0 10px 0;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">基本信息</h3>
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <tr><td style="padding:5px 0;color:#888;width:85px;">项目负责人</td><td style="padding:5px 0;"><?php echo isset($users[$project->PM]) ? $users[$project->PM] : ($project->PM ?: '-');?></td></tr>
                    <?php if($project->PO):?><tr><td style="padding:5px 0;color:#888;">产品负责人</td><td style="padding:5px 0;"><?php echo isset($users[$project->PO]) ? $users[$project->PO] : $project->PO;?></td></tr><?php endif;?>
                    <?php if($project->QD):?><tr><td style="padding:5px 0;color:#888;">测试负责人</td><td style="padding:5px 0;"><?php echo isset($users[$project->QD]) ? $users[$project->QD] : $project->QD;?></td></tr><?php endif;?>
                    <?php if($project->RD):?><tr><td style="padding:5px 0;color:#888;">发布负责人</td><td style="padding:5px 0;"><?php echo isset($users[$project->RD]) ? $users[$project->RD] : $project->RD;?></td></tr><?php endif;?>
                    <tr><td style="padding:5px 0;color:#888;">计划开始</td><td style="padding:5px 0;"><?php echo $project->begin && $project->begin != '0000-00-00' ? $project->begin : '-';?></td></tr>
                    <tr><td style="padding:5px 0;color:#888;">计划完成</td><td style="padding:5px 0;"><?php echo $project->end && $project->end != '0000-00-00' ? $project->end : '-';?></td></tr>
                    <?php if($project->realBegan && $project->realBegan != '0000-00-00'):?><tr><td style="padding:5px 0;color:#888;">实际开始</td><td style="padding:5px 0;"><?php echo $project->realBegan;?></td></tr><?php endif;?>
                    <?php if($project->realEnd && $project->realEnd != '0000-00-00'):?><tr><td style="padding:5px 0;color:#888;">实际完成</td><td style="padding:5px 0;"><?php echo $project->realEnd;?></td></tr><?php endif;?>
                    <tr><td style="padding:5px 0;color:#888;">预算</td><td style="padding:5px 0;"><?php echo $project->budget ? $project->budget . ($project->budgetUnit == 'CNY' ? ' 元' : ($project->budgetUnit ? ' ' . $project->budgetUnit : '')) : '-';?></td></tr>
                </table>
            </div>

            <!-- 项目描述 -->
            <?php if(!empty($project->desc)):?>
            <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;">
                <h3 style="font-size:14px;font-weight:600;color:#1a1a2e;margin:0 0 8px 0;">项目描述</h3>
                <div style="font-size:13px;color:#555;line-height:1.6;"><?php echo htmlspecialchars_decode($project->desc);?></div>
            </div>
            <?php endif;?>

            <!-- 关联产品 -->
            <?php if($project->hasProduct && !empty($products)):?>
            <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;">
                <h3 style="font-size:14px;font-weight:600;color:#1a1a2e;margin:0 0 8px 0;">关联产品</h3>
                <?php foreach($products as $product):?>
                <div style="font-size:13px;padding:6px 0;border-bottom:1px solid #f5f5f5;display:flex;justify-content:space-between;">
                    <span><?php echo $product->name;?></span>
                    <span style="color:#888;"><?php echo !empty($product->branchName) ? $product->branchName : '';?></span>
                </div>
                <?php endforeach;?>
            </div>
            <?php endif;?>
        </div>

        <!-- 右栏：任务状态 + 最近动态 -->
        <div style="flex:0.8;min-width:250px;display:flex;flex-direction:column;gap:16px;">

            <!-- 任务状态分布 -->
            <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;">
                <h3 style="font-size:14px;font-weight:600;color:#1a1a2e;margin:0 0 10px 0;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">任务状态</h3>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
                        <span style="color:#888;">待处理</span>
                        <span style="font-weight:600;"><?php echo (int)$statData->waitCount;?></span>
                    </div>
                    <div style="height:4px;background:#f0f0f0;border-radius:2px;overflow:hidden;">
                        <?php $totalTasks = max((int)$statData->taskCount, 1);?>
                        <div style="height:100%;width:<?php echo round((int)$statData->waitCount / $totalTasks * 100);?>%;background:#faad14;border-radius:2px;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
                        <span style="color:#1890ff;">进行中</span>
                        <span style="font-weight:600;"><?php echo (int)$statData->doingCount;?></span>
                    </div>
                    <div style="height:4px;background:#f0f0f0;border-radius:2px;overflow:hidden;">
                        <div style="height:100%;width:<?php echo round((int)$statData->doingCount / $totalTasks * 100);?>%;background:#1890ff;border-radius:2px;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
                        <span style="color:#52c41a;">已完成</span>
                        <span style="font-weight:600;"><?php echo (int)$statData->finishedCount;?></span>
                    </div>
                    <div style="height:4px;background:#f0f0f0;border-radius:2px;overflow:hidden;">
                        <div style="height:100%;width:<?php echo round((int)$statData->finishedCount / $totalTasks * 100);?>%;background:#52c41a;border-radius:2px;"></div>
                    </div>
                    <?php if(!empty($statData->delayedCount) && $statData->delayedCount > 0):?>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
                        <span style="color:#ff4d4f;">已延期</span>
                        <span style="font-weight:600;color:#ff4d4f;"><?php echo (int)$statData->delayedCount;?></span>
                    </div>
                    <?php endif;?>
                </div>
            </div>

            <!-- 工时统计 -->
            <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;">
                <h3 style="font-size:14px;font-weight:600;color:#1a1a2e;margin:0 0 10px 0;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">工时统计</h3>
                <div style="display:flex;flex-direction:column;gap:6px;font-size:13px;">
                    <div style="display:flex;justify-content:space-between;"><span style="color:#888;">预计</span><span><strong><?php echo round($project->estimate, 1);?></strong> 小时</span></div>
                    <div style="display:flex;justify-content:space-between;"><span style="color:#888;">消耗</span><span><strong><?php echo round($project->consumed, 1);?></strong> 小时</span></div>
                    <div style="display:flex;justify-content:space-between;"><span style="color:#888;">剩余</span><span><strong><?php echo round($project->left, 1);?></strong> 小时</span></div>
                </div>
                <?php if($project->estimate > 0):?>
                <?php $consumePct = round($project->consumed / max($project->estimate, 1) * 100, 1);?>
                <div style="margin-top:8px;padding-top:8px;border-top:1px solid #f0f0f0;">
                    <div style="display:flex;justify-content:space-between;font-size:12px;color:#888;margin-bottom:3px;">
                        <span>消耗进度</span><span><?php echo $consumePct;?>%</span>
                    </div>
                    <div style="height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:<?php echo min($consumePct, 100);?>%;background:<?php echo $consumePct > 100 ? '#ff4d4f' : '#52c41a';?>;border-radius:3px;"></div>
                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>
    </div>

    <!-- ===== 执行/迭代列表 ===== -->
    <?php if(!empty($executions)):?>
    <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;margin-bottom:16px;">
        <h3 style="font-size:14px;font-weight:600;color:#1a1a2e;margin:0 0 10px 0;padding-bottom:8px;border-bottom:1px solid #f0f0f0;"><?php echo $lang->project->executions ?? '执行';?> / <?php echo $lang->project->sprints ?? '迭代';?></h3>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#fafafa;">
                    <th style="padding:8px 10px;text-align:left;border-bottom:1px solid #e8e8e8;">名称</th>
                    <th style="padding:8px 10px;text-align:center;border-bottom:1px solid #e8e8e8;">状态</th>
                    <th style="padding:8px 10px;text-align:center;border-bottom:1px solid #e8e8e8;">任务</th>
                    <th style="padding:8px 10px;text-align:center;border-bottom:1px solid #e8e8e8;">消耗</th>
                    <th style="padding:8px 10px;text-align:center;border-bottom:1px solid #e8e8e8;">剩余</th>
                    <th style="padding:8px 10px;text-align:center;border-bottom:1px solid #e8e8e8;">进度</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($executions as $execution):?>
                <tr style="border-bottom:1px solid #f5f5f5;">
                    <td style="padding:8px 10px;">
                        <a href="<?php echo helper::createLink('execution', 'task', "executionID=$execution->id");?>" target="_blank" style="color:#1890ff;text-decoration:none;"><?php echo $execution->name;?></a>
                    </td>
                    <td style="padding:8px 10px;text-align:center;">
                        <span style="display:inline-block;padding:1px 8px;border-radius:3px;font-size:12px;background:<?php echo $execution->status == 'doing' ? '#e6f7ff' : ($execution->status == 'closed' ? '#f5f5f5' : '#fff7e6');?>;color:<?php echo $execution->status == 'doing' ? '#1890ff' : ($execution->status == 'closed' ? '#999' : '#faad14');?>"><?php echo zget($lang->execution->statusList, $execution->status, $execution->status);?></span>
                    </td>
                    <td style="padding:8px 10px;text-align:center;"><?php echo (int)$execution->tasks;?></td>
                    <td style="padding:8px 10px;text-align:center;"><?php echo round($execution->consumed, 1);?></td>
                    <td style="padding:8px 10px;text-align:center;"><?php echo round($execution->left, 1);?></td>
                    <td style="padding:8px 10px;text-align:center;">
                        <?php $execProgress = $execution->estimate > 0 ? round($execution->consumed / $execution->estimate * 100) : 0;?>
                        <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                            <div style="width:50px;height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:<?php echo min($execProgress, 100);?>%;background:#1890ff;border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;color:#888;"><?php echo $execProgress;?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <?php endif;?>

    <!-- ===== 团队成员 ===== -->
    <?php if(!empty($teamMembers)):?>
    <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;margin-bottom:16px;">
        <h3 style="font-size:14px;font-weight:600;color:#1a1a2e;margin:0 0 10px 0;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">团队成员 <span style="font-weight:400;color:#888;font-size:12px;">(<?php echo count($teamMembers);?>人)</span></h3>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#fafafa;">
                    <th style="padding:7px 10px;text-align:left;border-bottom:1px solid #e8e8e8;">账号</th>
                    <th style="padding:7px 10px;text-align:left;border-bottom:1px solid #e8e8e8;">姓名</th>
                    <th style="padding:7px 10px;text-align:left;border-bottom:1px solid #e8e8e8;">角色</th>
                    <th style="padding:7px 10px;text-align:center;border-bottom:1px solid #e8e8e8;">工时/天</th>
                    <th style="padding:7px 10px;text-align:center;border-bottom:1px solid #e8e8e8;">可用天数</th>
                    <th style="padding:7px 10px;text-align:center;border-bottom:1px solid #e8e8e8;">总计(h)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($teamMembers as $member):?>
                <tr style="border-bottom:1px solid #f5f5f5;">
                    <td style="padding:7px 10px;"><?php echo $member->account;?></td>
                    <td style="padding:7px 10px;"><strong><?php echo $member->realname;?></strong></td>
                    <td style="padding:7px 10px;"><?php echo zget($lang->user->roleList, $member->role, $member->role ?? '-');?></td>
                    <td style="padding:7px 10px;text-align:center;"><?php echo $member->hours;?></td>
                    <td style="padding:7px 10px;text-align:center;"><?php echo $member->days;?></td>
                    <td style="padding:7px 10px;text-align:center;"><?php echo $member->totalHours ?? ($member->hours * $member->days);?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <?php endif;?>

    <!-- ===== 最近动态 ===== -->
    <?php if(!empty($dynamics)):?>
    <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;margin-bottom:16px;">
        <h3 style="font-size:14px;font-weight:600;color:#1a1a2e;margin:0 0 10px 0;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">最近动态</h3>
        <?php foreach(array_slice($dynamics, 0, 15) as $action):?>
        <div style="padding:7px 0;border-bottom:1px solid #f5f5f5;display:flex;gap:8px;font-size:13px;">
            <span style="color:#1890ff;white-space:nowrap;font-weight:500;"><?php echo isset($users[$action->actor]) ? $users[$action->actor] : $action->actor;?></span>
            <span style="color:#555;flex:1;"><?php echo $action->actionLabel ?? $action->action;?></span>
            <span style="color:#bbb;font-size:12px;white-space:nowrap;"><?php echo substr($action->date, 0, 16);?></span>
        </div>
        <?php endforeach;?>
    </div>
    <?php endif;?>

    <!-- ===== 操作历史 ===== -->
    <?php if(!empty($actions)):?>
    <div style="background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;margin-bottom:16px;">
        <h3 style="font-size:14px;font-weight:600;color:#1a1a2e;margin:0 0 10px 0;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">操作历史</h3>
        <div style="max-height:360px;overflow-y:auto;">
            <?php foreach($actions as $action):?>
            <div style="padding:7px 0;border-bottom:1px solid #f5f5f5;font-size:13px;">
                <div style="display:flex;justify-content:space-between;">
                    <span><strong><?php echo isset($users[$action->actor]) ? $users[$action->actor] : $action->actor;?></strong>
                    <span style="color:#888;margin-left:4px;"><?php echo $action->actionLabel ?? $action->action;?></span></span>
                    <span style="color:#bbb;font-size:12px;"><?php echo substr($action->date, 0, 16);?></span>
                </div>
                <?php if(!empty($action->comment)):?>
                <div style="color:#888;margin-top:4px;padding-left:10px;border-left:2px solid #e8e8e8;font-size:12px;"><?php echo $action->comment;?></div>
                <?php endif;?>
            </div>
            <?php endforeach;?>
        </div>
    </div>
    <?php endif;?>

    <!-- ===== 操作按钮 ===== -->
    <div style="display:flex;gap:10px;flex-wrap:wrap;padding:8px 0 20px;">
        <?php if($project->status == 'wait'):?>
        <a href="<?php echo helper::createLink('project', 'start', "projectID=$project->id", '', true);?>" class="pd-btn pd-btn-primary">开始项目</a>
        <?php endif;?>
        <?php if($project->status == 'doing' || $project->status == 'wait'):?>
        <a href="<?php echo helper::createLink('project', 'suspend', "projectID=$project->id", '', true);?>" class="pd-btn pd-btn-default">挂起项目</a>
        <?php endif;?>
        <?php if($project->status != 'closed' && $project->status != 'cancel' && $project->status != 'done'):?>
        <a href="<?php echo helper::createLink('project', 'close', "projectID=$project->id", '', true);?>" class="pd-btn pd-btn-default">关闭项目</a>
        <?php endif;?>
        <?php if($project->status == 'suspended' || $project->status == 'closed'):?>
        <a href="<?php echo helper::createLink('project', 'activate', "projectID=$project->id", '', true);?>" class="pd-btn pd-btn-success">激活项目</a>
        <?php endif;?>
        <a href="<?php echo helper::createLink('devws', 'editProject', "projectID=$project->id");?>" class="pd-btn pd-btn-default">编辑</a>
        <a href="<?php echo helper::createLink('project', 'view', "projectID=$project->id");?>" target="_blank" class="pd-btn pd-btn-default">在新标签页打开</a>
    </div>
</div>
<?php endif;?>

<style>
#header, #menu, #appsBar, .navbar, .breadcrumb, #footer { display: none !important; }
body { background: #f5f7fa !important; margin: 0; padding: 0; }
.pd-btn { display:inline-block; padding:7px 18px; border-radius:4px; font-size:13px; cursor:pointer; text-decoration:none; transition:opacity 0.2s; }
.pd-btn:hover { opacity:0.85; }
.pd-btn-primary { border:none; background:#1890ff; color:#fff; }
.pd-btn-success { border:none; background:#52c41a; color:#fff; }
.pd-btn-default { border:1px solid #d9d9d9; background:#fff; color:#555; }
.pd-status-badge { white-space:nowrap; }
.pd-stat-card { transition:box-shadow 0.2s; }
.pd-stat-card:hover { box-shadow:0 2px 8px rgba(0,0,0,0.06); }
table tr:hover td { background:#fafafa; }
a { transition:color 0.2s; }
</style>