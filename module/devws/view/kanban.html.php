<?php
/**
 * Kanban board view for devws main content area.
 * Shows project tasks grouped by status columns.
 */
?>
<?php if(!$project):?>
<div class="kb-error" style="padding:60px 20px;text-align:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#333;">
    <div style="font-size:48px;color:#ff4d4f;margin-bottom:16px;">!</div>
    <div style="font-size:16px;color:#595959;margin-bottom:24px;">项目不存在</div>
    <a href="javascript:;" onclick="parent.cancelProjectView && parent.cancelProjectView()" style="display:inline-block;padding:8px 24px;border-radius:6px;font-size:14px;border:1px solid #d9d9d9;background:#fff;color:#595959;text-decoration:none;">返回项目列表</a>
</div>
<?php else:?>
<?php
$statusName = zget($lang->project->statusList, $project->status, $project->status);
$columnColors = array(
    'wait'       => '#faad14',
    'developing' => '#1890ff',
    'developed'  => '#52c41a',
    'pause'      => '#722ed1',
    'canceled'   => '#999',
    'closed'     => '#bbb',
);
$priColors = array(1 => '#ff4d4f', 2 => '#fa8c16', 3 => '#1890ff', 4 => '#999');
?>
<div class="kb-wrap" style="display:flex;flex-direction:column;height:100%;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;background:#f5f7fa;">

    <!-- ===== 顶部信息条 + 标签栏 ===== -->
    <div style="padding:16px 20px 0;flex-shrink:0;background:#f5f7fa;">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding-bottom:10px;">
            <span style="display:inline-block;padding:2px 10px;border-radius:3px;font-size:12px;font-weight:500;color:#fff;background:<?php echo $project->status == 'doing' ? '#1890ff' : ($project->status == 'closed' ? '#999' : '#faad14');?>"><?php echo $statusName;?></span>
            <h1 style="font-size:20px;font-weight:600;color:#1a1a2e;margin:0;"><?php echo $project->name;?></h1>
            <span style="font-size:12px;color:#bbb;">#<?php echo $project->id;?></span>
        </div>

        <!-- 标签导航 -->
        <div style="display:flex;gap:0;border-bottom:1px solid #e8e8e8;">
            <a href="javascript:;" onclick="parent.openProjectView(<?php echo $project->id;?>)" style="padding:10px 20px;font-size:14px;color:#666;text-decoration:none;border-bottom:2px solid transparent;transition:all 0.2s;"><?php echo $lang->devws->kanbanOverview;?></a>
            <a href="javascript:;" style="padding:10px 20px;font-size:14px;color:#1890ff;text-decoration:none;border-bottom:2px solid #1890ff;font-weight:500;"><?php echo $lang->devws->kanban;?></a>
        </div>
    </div>

    <!-- ===== 看板主体 ===== -->
    <div style="flex:1;overflow-x:auto;overflow-y:hidden;padding:16px 20px;">
        <div style="display:flex;gap:16px;height:100%;min-width:0;">

            <?php foreach($columnDefs as $colType => $taskStatus):
            $colTasks = isset($tasks[$colType]) ? $tasks[$colType] : array();
            $colColor = isset($columnColors[$colType]) ? $columnColors[$colType] : '#999';
            $statusLabel = zget($lang->task->statusList, $taskStatus, $taskStatus);
            ?>
            <div style="flex:0 0 280px;display:flex;flex-direction:column;background:#f0f2f5;border-radius:8px;overflow:hidden;max-height:100%;">
                <!-- 列头 -->
                <div style="padding:12px 14px 8px;flex-shrink:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                        <span style="font-size:14px;font-weight:600;color:#333;"><?php echo $statusLabel;?></span>
                        <span style="font-size:12px;color:#888;background:#e8e8e8;padding:0 8px;border-radius:10px;line-height:20px;"><?php echo count($colTasks);?></span>
                    </div>
                    <div style="height:3px;background:<?php echo $colColor;?>;border-radius:2px;"></div>
                </div>

                <!-- 列内容（可滚动，拖放目标） -->
                <div class="kb-column-body" data-col-type="<?php echo $colType;?>" data-task-status="<?php echo $taskStatus;?>" style="flex:1;overflow-y:auto;padding:0 10px 10px;">
                    <?php if(empty($colTasks)):?>
                    <div style="text-align:center;padding:24px 10px;color:#bbb;font-size:13px;" class="kb-empty-msg"><?php echo $lang->devws->kanbanNoTasks;?></div>
                    <?php else:?>
                        <?php foreach($colTasks as $task):
                        $priColor = isset($priColors[$task->pri]) ? $priColors[$task->pri] : '#999';
                        $isOverdue = $task->deadline && $task->deadline != '0000-00-00' && strtotime($task->deadline) < time() && !in_array($task->status, array('done', 'closed', 'cancel'));
                        ?>
                        <!-- 卡片 -->
                        <div class="kb-card" draggable="true" data-task-id="<?php echo $task->id;?>" data-last-edited="<?php echo $task->lastEditedDate;?>" style="background:#fff;border-radius:6px;padding:12px 14px;margin-top:10px;cursor:grab;border-left:3px solid <?php echo $priColor;?>;box-shadow:0 1px 2px rgba(0,0,0,0.05);transition:box-shadow 0.2s,transform 0.15s;position:relative;">
                            <?php if(!empty($task->executionName)):?>
                            <div style="font-size:11px;color:#999;margin-bottom:4px;"><?php echo $task->executionName;?></div>
                            <?php endif;?>
                            <div style="font-size:13px;font-weight:500;color:#333;line-height:1.4;margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?php echo $task->name;?></div>
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;">
                                <span style="color:#888;"><?php echo $task->assignedToRealName ?: ($task->assignedTo ?: '-');?></span>
                                <?php if($task->deadline && $task->deadline != '0000-00-00'):?>
                                <span style="color:<?php echo $isOverdue ? '#ff4d4f' : '#999';?>;font-weight:<?php echo $isOverdue ? '600' : '400';?>;"><?php echo $task->deadline;?></span>
                                <?php endif;?>
                            </div>
                            <?php if((float)$task->estimate > 0):?>
                            <?php $consumedPct = min(100, round((float)$task->consumed / max((float)$task->estimate, 1) * 100));?>
                            <div style="margin-top:8px;display:flex;align-items:center;gap:6px;">
                                <div style="flex:1;height:4px;background:#f0f0f0;border-radius:2px;overflow:hidden;">
                                    <div style="height:100%;width:<?php echo $consumedPct;?>%;background:<?php echo $consumedPct >= 100 ? '#52c41a' : '#1890ff';?>;border-radius:2px;"></div>
                                </div>
                                <span style="font-size:11px;color:#bbb;"><?php echo round((float)$task->consumed, 1);?>/<?php echo round((float)$task->estimate, 1);?></span>
                            </div>
                            <?php endif;?>
                        </div>
                        <?php endforeach;?>
                    <?php endif;?>
                </div>
            </div>
            <?php endforeach;?>

        </div>
    </div>
</div>

<style>
.kb-card:hover { box-shadow: 0 3px 8px rgba(0,0,0,0.1) !important; transform: translateY(-1px); }
.kb-card.dragging { opacity: 0.4 !important; transform: rotate(2deg) !important; cursor: grabbing !important; }
.kb-column-body.drag-over { background: #e6f7ff !important; border-radius: 6px; }
.kb-column-body.drag-over .kb-card { pointer-events: none; }
.kb-card.drag-origin { opacity: 0.3 !important; }
#header, #menu, #appsBar, .navbar, .breadcrumb, #footer { display: none !important; }
body { margin: 0; padding: 0; background: #f5f7fa !important; overflow: hidden; }
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-thumb { background: #d9d9d9; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #bbb; }
::-webkit-scrollbar-track { background: transparent; }
</style>

<script>
(function(){
    var columnStatusMap = <?php echo json_encode((array)$this->config->kanban->taskColumnStatusList ?: array('wait' => 'wait', 'developing' => 'doing', 'developed' => 'done', 'pause' => 'pause', 'canceled' => 'cancel', 'closed' => 'closed'));?>;
    var dragTaskID = null;

    /* Cards: drag events */
    document.addEventListener('dragstart', function(e) {
        var card = e.target.closest('.kb-card');
        if(!card) return;
        dragTaskID = card.getAttribute('data-task-id');
        card.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', dragTaskID);

        var colBody = card.closest('.kb-column-body');
        if(colBody) colBody.classList.add('drag-origin');
    });

    document.addEventListener('dragend', function(e) {
        var card = e.target.closest('.kb-card');
        if(card) card.classList.remove('dragging');
        dragTaskID = null;
        document.querySelectorAll('.kb-column-body').forEach(function(c) {
            c.classList.remove('drag-over', 'drag-origin');
        });
    });

    document.addEventListener('dragover', function(e) {
        var colBody = e.target.closest('.kb-column-body');
        if(!colBody) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    });

    document.addEventListener('dragenter', function(e) {
        var colBody = e.target.closest('.kb-column-body');
        if(!colBody || !dragTaskID) return;
        colBody.classList.add('drag-over');
    });

    document.addEventListener('dragleave', function(e) {
        var colBody = e.target.closest('.kb-column-body');
        if(!colBody) return;
        if(!colBody.contains(e.relatedTarget)) {
            colBody.classList.remove('drag-over');
        }
    });

    document.addEventListener('drop', function(e) {
        var colBody = e.target.closest('.kb-column-body');
        if(!colBody || !dragTaskID) return;
        e.preventDefault();

        colBody.classList.remove('drag-over');

        var taskID    = parseInt(dragTaskID);
        var colType   = colBody.getAttribute('data-col-type');
        var newStatus = colBody.getAttribute('data-task-status');

        if(!taskID || !colType || !newStatus) return;

        var sourceCol = document.querySelector('.kb-column-body.drag-origin');
        if(sourceCol && sourceCol.getAttribute('data-col-type') === colType) {
            document.querySelectorAll('.kb-column-body').forEach(function(c) {
                c.classList.remove('drag-over', 'drag-origin');
            });
            return;
        }

        /* Move card visually immediately (no clone — move actual element). */
        var card = document.querySelector('.kb-card[data-task-id="' + taskID + '"]');
        if(!card) return;

        var sourceBody = card.closest('.kb-column-body');
        card.classList.remove('dragging');
        sourceBody.removeChild(card);
        colBody.appendChild(card);
        updateEmptyStates();

        document.querySelectorAll('.kb-column-body').forEach(function(c) {
            c.classList.remove('drag-over', 'drag-origin');
        });

        /* Read lastEditedDate for optimistic concurrency check. */
        var lastEdited = card.getAttribute('data-last-edited') || '';

        /* Send AJAX to persist (async, optimistic). */
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo helper::createLink('devws', 'ajaxUpdateTaskStatus');?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if(xhr.status === 200) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if(resp.result === 'conflict') {
                        /* Another user modified this card — revert visual move. */
                        var movedCard = colBody.querySelector('.kb-card[data-task-id="' + taskID + '"]');
                        if(movedCard && sourceBody) {
                            sourceBody.appendChild(movedCard);
                            updateEmptyStates();
                        }
                        if(window.parent && window.parent.$.zui && window.parent.$.zui.messager) {
                            window.parent.$.zui.messager.danger('卡片已被其他人修改，请刷新后重试');
                        }
                    } else if(resp.result !== 'success') {
                        /* Unknown failure — revert. */
                        var movedCard = colBody.querySelector('.kb-card[data-task-id="' + taskID + '"]');
                        if(movedCard && sourceBody) {
                            sourceBody.appendChild(movedCard);
                            updateEmptyStates();
                        }
                    }
                } catch(err) {}
            }
        };
        xhr.send('taskID=' + encodeURIComponent(taskID) + '&status=' + encodeURIComponent(newStatus) + '&lastEdited=' + encodeURIComponent(lastEdited));
    });

    /* Click card to open drawer (browsers suppress click after drag). */
    document.addEventListener('click', function(e) {
        var card = e.target.closest('.kb-card');
        if(!card) return;
        var taskID = card.getAttribute('data-task-id');
        if(taskID && window.parent && window.parent.openDrawer) {
            window.parent.openDrawer('task', parseInt(taskID));
        }
    });

    function updateEmptyStates() {
        document.querySelectorAll('.kb-column-body').forEach(function(col) {
            var cards = col.querySelectorAll('.kb-card');
            var emptyMsg = col.querySelector('.kb-empty-msg');
            if(cards.length === 0) {
                if(!emptyMsg) {
                    var msg = document.createElement('div');
                    msg.className = 'kb-empty-msg';
                    msg.style.cssText = 'text-align:center;padding:24px 10px;color:#bbb;font-size:13px;';
                    msg.textContent = '<?php echo $lang->devws->kanbanNoTasks;?>';
                    col.appendChild(msg);
                }
            } else {
                if(emptyMsg) emptyMsg.parentNode.removeChild(emptyMsg);
            }
            updateColumnCounts();
        });
    }

    function updateColumnCounts() {
        document.querySelectorAll('.kb-column-body').forEach(function(col) {
            var count = col.querySelectorAll('.kb-card').length;
            var wrapper = col.parentNode;
            var badge = wrapper && wrapper.querySelector('[style*="border-radius:10px"]');
            if(badge) badge.textContent = count;
        });
    }
})();
</script>
<?php endif;?>
