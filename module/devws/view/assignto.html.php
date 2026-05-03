<?php
/**
 * Custom assignTo view for devws drawer.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      whh
 * @package     devws
 * @version     $Id$
 */
?>
<?php if(!empty($multiTeam)):?>
<div class="assign-container">
    <div class="assign-heading">
        <div class="assign-title"><?php echo $lang->task->tips;?></div>
    </div>
    <div class="assign-body">
        <p class="assign-notice"><?php echo sprintf($lang->task->deniedNotice, '<strong>' . $task->assignedToName . '</strong>', $lang->task->transfer);?></p>
    </div>
    <div class="assign-footer">
        <a href="javascript:;" onclick="parent.closeDrawer && parent.closeDrawer()" class="assign-btn assign-btn-secondary">返回</a>
    </div>
</div>
<?php else:?>
<div class="assign-container">
    <!-- Header -->
    <div class="assign-heading">
        <div class="assign-title"><?php echo $lang->task->assign;?></div>
        <div class="assign-task-info">#<?php echo $task->id;?> <?php echo $task->name;?></div>
    </div>

    <!-- Form -->
    <form method="post" class="assign-form" id="assignForm">
        <div class="assign-form-body">
            <div class="assign-field">
                <label class="assign-label"><?php echo $lang->task->assignedTo;?></label>
                <select name="assignedTo" class="assign-select">
                    <option value=""><?php echo $lang->devws->pleaseSelect ?? '请选择';?></option>
                    <?php foreach($users as $account => $realname):?>
                    <?php $selected = ($account == $task->assignedTo) ? 'selected' : '';?>
                    <option value="<?php echo $account;?>" <?php echo $selected;?>><?php echo $realname;?></option>
                    <?php endforeach;?>
                </select>
            </div>

            <div class="assign-field">
                <label class="assign-label"><?php echo $lang->task->left;?></label>
                <div class="assign-input-wrap">
                    <input type="number" name="left" step="0.01" min="0" value="<?php echo $task->left;?>" class="assign-input" placeholder="剩余工时">
                    <span class="assign-input-suffix">h</span>
                </div>
            </div>

            <div class="assign-field">
                <label class="assign-label"><?php echo $lang->comment;?></label>
                <textarea name="comment" rows="4" class="assign-textarea" placeholder="备注说明（可选）"></textarea>
            </div>
        </div>

        <div class="assign-footer">
            <button type="button" onclick="parent.closeDrawer && parent.closeDrawer()" class="assign-btn assign-btn-secondary">取消</button>
            <button type="submit" class="assign-btn assign-btn-primary"><?php echo $lang->save;?></button>
        </div>
    </form>
</div>
<?php endif;?>

<style>
/* ======= Container ======= */
.assign-container { padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #333; max-width: 520px; }

/* ======= Heading ======= */
.assign-heading { padding: 20px 24px 16px; border-bottom: 1px solid #f0f0f0; }
.assign-title { font-size: 18px; font-weight: 600; color: #1a1a2e; margin-bottom: 6px; }
.assign-task-info { font-size: 13px; color: #8c8c8c; }
.assign-notice { font-size: 14px; color: #595959; padding: 16px 24px; }

/* ======= Form ======= */
.assign-form-body { padding: 20px 24px; }
.assign-field { margin-bottom: 20px; }
.assign-field:last-child { margin-bottom: 0; }
.assign-label { display: block; font-size: 13px; font-weight: 500; color: #333; margin-bottom: 8px; }

.assign-select { width: 100%; height: 38px; border: 1px solid #d9d9d9; border-radius: 6px; padding: 0 12px; font-size: 14px; color: #333; background: #fff; appearance: auto; cursor: pointer; box-sizing: border-box; }
.assign-select:focus { border-color: #1890ff; outline: none; box-shadow: 0 0 0 2px rgba(24, 144, 255, 0.1); }

.assign-input-wrap { position: relative; display: flex; align-items: center; }
.assign-input { flex: 1; height: 38px; border: 1px solid #d9d9d9; border-radius: 6px; padding: 0 36px 0 12px; font-size: 14px; color: #333; box-sizing: border-box; }
.assign-input:focus { border-color: #1890ff; outline: none; box-shadow: 0 0 0 2px rgba(24, 144, 255, 0.1); }
.assign-input-suffix { position: absolute; right: 12px; font-size: 13px; color: #8c8c8c; pointer-events: none; }

.assign-textarea { width: 100%; min-height: 80px; border: 1px solid #d9d9d9; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #333; resize: vertical; font-family: inherit; box-sizing: border-box; line-height: 1.6; }
.assign-textarea:focus { border-color: #1890ff; outline: none; box-shadow: 0 0 0 2px rgba(24, 144, 255, 0.1); }

/* ======= Footer ======= */
.assign-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f0f0f0; }
.assign-btn { padding: 8px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; border: 1px solid #d9d9d9; transition: all 0.2s; }
.assign-btn:hover { opacity: 0.85; }
.assign-btn-primary { background: #1890ff; color: #fff; border-color: #1890ff; }
.assign-btn-primary:hover { background: #40a9ff; }
.assign-btn-secondary { background: #fff; color: #595959; }
.assign-btn-secondary:hover { border-color: #1890ff; color: #1890ff; }

/* ======= Responsive ======= */
@media (max-width: 600px) {
    .assign-container { max-width: 100%; }
    .assign-form-body { padding: 16px 18px; }
    .assign-heading { padding: 16px 18px; }
    .assign-footer { padding: 12px 18px; }
}
</style>
