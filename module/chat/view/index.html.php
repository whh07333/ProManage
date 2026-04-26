<?php
/**
 * The index view file of chat module.
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/menu.html.php';?>

<div class="main-content" id="mainContent">
  <div class="chat-container">
    <div class="chat-sidebar">
      <div class="chat-sidebar-header">
        <h3><?php echo $lang->chat->messages;?></h3>
        <button class="btn btn-primary btn-sm" style="margin-top:10px;width:100%" data-toggle="modal" data-target="#createRoomModal">
          <i class="icon icon-plus"></i> <?php echo $lang->chat->createRoom;?>
        </button>
      </div>
      <div class="chat-sidebar-body">
        <?php if(empty($rooms)):?>
        <div class="no-rooms">
          <p><?php echo $lang->chat->noRooms;?></p>
        </div>
        <?php else:?>
        <ul class="room-list">
          <?php foreach($rooms as $room):?>
          <li class="room-item" data-room-id="<?php echo $room->id;?>">
            <a href="javascript:enterRoom(<?php echo $room->id;?>);">
              <div class="room-name"><?php echo $room->name;?></div>
              <div class="room-type"><?php echo isset($lang->chat->roomTypes[$room->type]) ? $lang->chat->roomTypes[$room->type] : $room->type;?></div>
            </a>
          </li>
          <?php endforeach;?>
        </ul>
        <?php endif;?>
      </div>
    </div>

    <div class="chat-main">
      <div class="chat-header">
        <h3 id="currentRoomName"><?php echo $lang->chat->enterRoom;?></h3>
        <div class="chat-actions" id="chatActions" style="display:none">
          <button class="btn btn-sm" id="roomMembersBtn" data-toggle="modal" data-target="#roomMembersModal">
            <i class="icon icon-users"></i> <?php echo $lang->chat->members;?>
          </button>
        </div>
      </div>
      <div class="chat-body" id="chatMessages">
        <div class="empty-room"><?php echo $lang->chat->selectRoom;?></div>
      </div>
      <div class="chat-footer">
        <form id="messageForm">
          <input type="hidden" id="roomID" name="roomID" value="">
          <div class="form-group">
            <textarea id="messageContent" name="content" class="form-control" placeholder="<?php echo $lang->chat->sendMessage;?>" disabled></textarea>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="sendBtn" disabled>
              <i class="icon icon-send"></i> <?php echo $lang->chat->send;?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="createRoomModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $lang->chat->createRoom;?></h4>
      </div>
      <div class="modal-body">
        <form id="createRoomForm">
          <div class="form-group">
            <label><?php echo $lang->chat->roomName;?></label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label><?php echo $lang->chat->roomType;?></label>
            <select name="type" class="form-control" required>
              <?php foreach($lang->chat->roomTypes as $key => $value):?>
              <option value="<?php echo $key;?>"><?php echo $value;?></option>
              <?php endforeach;?>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang->cancel;?></button>
        <button type="button" class="btn btn-primary" id="createRoomBtn"><?php echo $lang->chat->create;?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="roomMembersModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $lang->chat->members;?></h4>
      </div>
      <div class="modal-body">
        <div id="memberList"></div>
        <form id="addMemberForm" style="margin-top:15px;">
          <div class="input-group">
            <input type="text" name="memberAccount" class="form-control" placeholder="<?php echo $lang->chat->addMemberPlaceholder;?>">
            <span class="input-group-btn">
              <button type="submit" class="btn btn-primary"><?php echo $lang->chat->addMember;?></button>
            </span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.chat-container {
  display: flex;
  height: calc(100vh - 100px);
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.chat-sidebar {
  width: 280px;
  border-right: 1px solid #e5e5e5;
  display: flex;
  flex-direction: column;
  background: #fafafa;
}
.chat-sidebar-header {
  padding: 15px;
  border-bottom: 1px solid #e5e5e5;
}
.chat-sidebar-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #333;
}
.chat-sidebar-body {
  flex: 1;
  overflow-y: auto;
}
.no-rooms {
  padding: 30px 15px;
  text-align: center;
  color: #999;
}
.room-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.room-item {
  border-bottom: 1px solid #f0f0f0;
}
.room-item a {
  display: block;
  padding: 12px 15px;
  text-decoration: none;
  color: #333;
}
.room-item a:hover {
  background: #f0f0f0;
}
.room-item.active a {
  background: #e6f7ff;
}
.room-name {
  font-weight: 500;
  margin-bottom: 4px;
}
.room-type {
  font-size: 12px;
  color: #999;
}
.chat-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.chat-header {
  padding: 15px;
  border-bottom: 1px solid #e5e5e5;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.chat-header h3 {
  margin: 0;
  font-size: 16px;
}
.chat-body {
  flex: 1;
  overflow-y: auto;
  padding: 15px;
  background: #fff;
}
.empty-room {
  text-align: center;
  color: #999;
  padding: 50px;
}
.chat-footer {
  padding: 15px;
  border-top: 1px solid #e5e5e5;
  background: #fafafa;
}
.chat-footer textarea {
  resize: none;
  height: 60px;
}
.message-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.message-item {
  margin-bottom: 15px;
  display: flex;
}
.message-item.sent {
  flex-direction: row-reverse;
}
.message-content {
  max-width: 70%;
  padding: 10px 15px;
  border-radius: 10px;
  background: #f0f0f0;
}
.message-item.sent .message-content {
  background: #1890ff;
  color: #fff;
}
.message-sender {
  font-size: 12px;
  color: #999;
  margin-bottom: 4px;
}
.message-time {
  font-size: 11px;
  color: #999;
  margin-top: 4px;
}
</style>

<script>
function enterRoom(roomID) {
    $('#roomID').val(roomID);
    $('#messageContent').prop('disabled', false);
    $('#sendBtn').prop('disabled', false);
    $('#chatActions').show();

    $.get(createLink('chat', 'getMessages', 'roomID=' + roomID), function(data) {
        if (data.status === 'success') {
            renderMessages(data.messages);
        }
    });

    $.get(createLink('chat', 'getRoom', 'roomID=' + roomID), function(data) {
        if (data.status === 'success') {
            $('#currentRoomName').text(data.room.name);
        }
    });
}

function renderMessages(messages) {
    var html = '';
    if (messages.length === 0) {
        html = '<div class="empty-room"><?php echo $lang->chat->noMessages;?></div>';
    } else {
        html = '<ul class="message-list">';
        for (var i = 0; i < messages.length; i++) {
            var msg = messages[i];
            html += '<li class="message-item' + (msg.isMine ? ' sent' : '') + '">';
            html += '<div class="message-content">';
            if (!msg.isMine) {
                html += '<div class="message-sender">' + msg.senderName + '</div>';
            }
            html += '<div class="message-text">' + msg.content + '</div>';
            html += '<div class="message-time">' + msg.createdDate + '</div>';
            html += '</div></li>';
        }
        html += '</ul>';
    }
    $('#chatMessages').html(html);
    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
}

$(document).ready(function() {
    $('#createRoomBtn').on('click', function() {
        var formData = $('#createRoomForm').serialize();
        $.post(createLink('chat', 'createRoom'), formData, function(data) {
            if (data.status === 'success') {
                $('#createRoomModal').modal('hide');
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });

    $('#messageForm').on('submit', function(e) {
        e.preventDefault();
        var roomID = $('#roomID').val();
        var content = $('#messageContent').val();
        if (!roomID || !content) return;

        $.post(createLink('chat', 'sendMessage'), {roomID: roomID, content: content}, function(data) {
            if (data.status === 'success') {
                $('#messageContent').val('');
                enterRoom(roomID);
            } else {
                alert(data.message);
            }
        });
    });

    $('#addMemberForm').on('submit', function(e) {
        e.preventDefault();
        var roomID = $('#roomID').val();
        var account = $('input[name="memberAccount"]').val();
        if (!roomID || !account) return;

        $.post(createLink('chat', 'addMember'), {roomID: roomID, account: account}, function(data) {
            if (data.status === 'success') {
                $('input[name="memberAccount"]').val('');
                alert('成员添加成功');
            } else {
                alert(data.message);
            }
        });
    });
});
</script>

<?php include '../../common/view/footer.html.php';?>