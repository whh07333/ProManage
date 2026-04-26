<?php
/**
 * The index view file of message module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      ZenTao Team
 * @package     message
 * @link        https://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/menu.html.php';?>

<div class="main-content" id="mainContent">
  <div class="main-header">
    <h2><?php echo $lang->message->common;?></h2>
    <div class="actions">
      <button class="btn btn-default" id="markAllAsReadBtn"><?php echo $lang->message->markAllAsRead;?></button>
      <button class="btn btn-default" data-toggle="modal" data-target="#notificationSettingsModal"><?php echo $lang->message->settings;?></button>
    </div>
  </div>
  
  <div class="notification-filters">
    <div class="btn-group" role="group">
      <a href="<?php echo $this->createLink('message', 'index', 'status=all');?>" class="btn <?php echo $status == 'all' ? 'btn-primary' : 'btn-default';?>"><?php echo $lang->message->all;?></a>
      <a href="<?php echo $this->createLink('message', 'index', 'status=wait');?>" class="btn <?php echo $status == 'wait' ? 'btn-primary' : 'btn-default';?>"><?php echo $lang->message->unread;?></a>
      <a href="<?php echo $this->createLink('message', 'index', 'status=read');?>" class="btn <?php echo $status == 'read' ? 'btn-primary' : 'btn-default';?>"><?php echo $lang->message->read;?></a>
    </div>
    
    <div class="btn-group" role="group" style="margin-left: 10px;">
      <a href="<?php echo $this->createLink('message', 'index', "status=$status&type=all");?>" class="btn <?php echo $type == 'all' ? 'btn-primary' : 'btn-default';?>"><?php echo $lang->message->allTypes;?></a>
      <a href="<?php echo $this->createLink('message', 'index', "status=$status&type=system");?>" class="btn <?php echo $type == 'system' ? 'btn-primary' : 'btn-default';?>"><?php echo $lang->message->system;?></a>
      <a href="<?php echo $this->createLink('message', 'index', "status=$status&type=chat");?>" class="btn <?php echo $type == 'chat' ? 'btn-primary' : 'btn-default';?>"><?php echo $lang->message->chat;?></a>
      <a href="<?php echo $this->createLink('message', 'index', "status=$status&type=mention");?>" class="btn <?php echo $type == 'mention' ? 'btn-primary' : 'btn-default';?>"><?php echo $lang->message->mention;?></a>
      <a href="<?php echo $this->createLink('message', 'index', "status=$status&type=task");?>" class="btn <?php echo $type == 'task' ? 'btn-primary' : 'btn-default';?>"><?php echo $lang->message->task;?></a>
    </div>
  </div>
  
  <div class="notification-list">
    <?php if(empty($messages)):?>
    <div class="empty-message">
      <?php echo $lang->message->noMessage;?>
    </div>
    <?php else:?>
    <?php foreach($messages as $message):?>
    <div class="notification-item <?php echo $message->status == 'wait' ? 'unread' : '';?>">
      <div class="notification-icon">
        <?php if($message->type == 'system'):?>
        <i class="icon-bell"></i>
        <?php elseif($message->type == 'chat'):?>
        <i class="icon-comment"></i>
        <?php elseif($message->type == 'mention'):?>
        <i class="icon-at"></i>
        <?php elseif($message->type == 'task'):?>
        <i class="icon-task"></i>
        <?php endif;?>
      </div>
      <div class="notification-content">
        <div class="notification-text"><?php echo $message->data;?></div>
        <div class="notification-meta">
          <span class="notification-time"><?php echo $message->createdDate;?></span>
          <?php if($message->status == 'wait'):?>
          <span class="notification-action">
            <a href="javascript:;" class="mark-as-read" data-id="<?php echo $message->id;?>"><?php echo $lang->message->markAsRead;?></a>
          </span>
          <?php endif;?>
        </div>
      </div>
    </div>
    <?php endforeach;?>
    <?php endif;?>
  </div>
</div>

<!-- Notification Settings Modal -->
<div class="modal fade" id="notificationSettingsModal" tabindex="-1" role="dialog" aria-labelledby="notificationSettingsModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="notificationSettingsModalLabel"><?php echo $lang->message->settings;?></h4>
      </div>
      <div class="modal-body">
        <form id="notificationSettingsForm">
          <div class="form-group">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="system" <?php echo $settings->system ? 'checked' : '';?>> <?php echo $lang->message->system;?> 通知
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="chat" <?php echo $settings->chat ? 'checked' : '';?>> <?php echo $lang->message->chat;?> 通知
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="mention" <?php echo $settings->mention ? 'checked' : '';?>> @<?php echo $lang->message->mention;?> 通知
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="task" <?php echo $settings->task ? 'checked' : '';?>> <?php echo $lang->message->task;?> 通知
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="email" <?php echo $settings->email ? 'checked' : '';?>> 邮件通知
              </label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" id="saveSettingsBtn">保存</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
  // Mark as read
  $('.mark-as-read').click(function() {
    var id = $(this).data('id');
    var $item = $(this).closest('.notification-item');
    
    $.ajax({
      url: '<?php echo $this->createLink('message', 'markAsRead');?>',
      type: 'POST',
      data: {id: id},
      dataType: 'json',
      success: function(response) {
        if(response.status == 'success') {
          $item.removeClass('unread');
          $item.find('.notification-action').remove();
        }
      }
    });
  });
  
  // Mark all as read
  $('#markAllAsReadBtn').click(function() {
    $.ajax({
      url: '<?php echo $this->createLink('message', 'markAllAsRead');?>',
      type: 'POST',
      data: {type: '<?php echo $type;?>'},
      dataType: 'json',
      success: function(response) {
        if(response.status == 'success') {
          location.reload();
        }
      }
    });
  });
  
  // Save settings
  $('#saveSettingsBtn').click(function() {
    var settings = {
      system: $('input[name="system"]').prop('checked'),
      chat: $('input[name="chat"]').prop('checked'),
      mention: $('input[name="mention"]').prop('checked'),
      task: $('input[name="task"]').prop('checked'),
      email: $('input[name="email"]').prop('checked')
    };
    
    $.ajax({
      url: '<?php echo $this->createLink('message', 'saveSettings');?>',
      type: 'POST',
      data: {settings: JSON.stringify(settings)},
      dataType: 'json',
      success: function(response) {
        if(response.status == 'success') {
          $('#notificationSettingsModal').modal('hide');
        }
      }
    });
  });
});
</script>

<style>
.notification-filters {
  margin: 20px 0;
}

.notification-list {
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
}

.notification-item {
  display: flex;
  padding: 15px;
  border-bottom: 1px solid #eee;
  background: #fff;
}

.notification-item:hover {
  background: #f5f5f5;
}

.notification-item.unread {
  background: #e6f7ff;
  border-left: 4px solid #1890ff;
}

.notification-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 15px;
  font-size: 18px;
  color: #666;
}

.notification-content {
  flex: 1;
}

.notification-text {
  margin-bottom: 10px;
  line-height: 1.4;
}

.notification-meta {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #999;
}

.notification-action a {
  color: #1890ff;
  text-decoration: none;
}

.notification-action a:hover {
  text-decoration: underline;
}

.empty-message {
  text-align: center;
  padding: 50px;
  color: #999;
  background: #fff;
}
</style>

<?php include '../../common/view/footer.html.php';?>
