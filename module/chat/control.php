<?php
class chat extends control
{
    public function __construct()
    {
        parent::__construct();
    }

    private function chatLog($msg) {
        $logFile = '/data/zentao/tmp/log/chat_debug.log';
        @file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $msg\n", FILE_APPEND);
    }

    public function index()
    {
        $this->loadModel('chat');
        $this->view->rooms = $this->chat->getRooms();

        $viewFile = $this->app->getModulePath($this->appName, 'chat') . 'view' . DS . 'index.modal.php';
        if(!file_exists($viewFile)) $viewFile = $this->app->getModulePath($this->appName, 'chat') . 'view' . DS . 'index.html.php';

        extract((array)$this->view);
        ob_start();
        include $viewFile;
        $this->output = ob_get_clean();

        echo $this->output;
    }

    public function createRoom()
    {
        $this->chatLog("[createRoom] START - name: " . ($this->post->name ?? 'null') . ", type: " . ($this->post->type ?? 'null'));

        $this->loadModel('chat');
        if(!$this->post->name) $this->sendError('Name is required');
        if(!$this->post->type) $this->sendError('Type is required');

        $result = $this->chat->createRoom($this->post->name, $this->post->type, $this->post->relatedID);

        if(is_array($result)) {
            $this->chatLog("[createRoom] ERROR - " . $result['message']);
            $this->sendError($result['message']);
            return;
        }

        $this->chatLog("[createRoom] SUCCESS - roomID: $result");
        $this->sendSuccess(array('roomID' => $result));
    }

    public function sendMessage()
    {
        $this->chatLog("[sendMessage] START - roomID: " . ($this->post->roomID ?? 'null') . ", content: " . ($this->post->content ?? 'null') . ", user: " . $this->app->user->account);

        $this->loadModel('chat');
        if(!$this->post->roomID) {
            $this->chatLog("[sendMessage] ERROR - Room ID is required");
            $this->sendError('Room ID is required');
        }
        if(!$this->post->content) {
            $this->chatLog("[sendMessage] ERROR - Content is required");
            $this->sendError('Content is required');
        }

        $messageID = $this->chat->sendMessage($this->post->roomID, $this->post->content, $this->post->type, $this->post->extra);

        $this->chatLog("[sendMessage] messageID: $messageID");

        $message = $this->chat->dao->select('*')->from(TABLE_CHATMESSAGE)->where('id')->eq($messageID)->fetch();
        $user = $this->loadModel('user')->getByAccount($message->sender);
        $message->senderName = $user ? $user->realname : $message->sender;

        $this->chatLog("[sendMessage] SUCCESS - message: " . json_encode((array)$message));

        $this->sendSuccess((array)$message);
    }

    public function getRooms()
    {
        $this->chatLog("[getRooms] START");

        $this->loadModel('chat');
        $type = $this->get->type ? $this->get->type : '';
        $relatedID = $this->get->relatedID ? (int)$this->get->relatedID : 0;
        $rooms = $this->chat->getRooms($type, $relatedID);

        $this->chatLog("[getRooms] rooms count: " . count($rooms));

        $this->sendSuccess($rooms);
    }

    public function getMessages()
    {
        $roomID = isset($_POST['roomID']) ? (int)$_POST['roomID'] : (int)($this->get->roomID ?? 0);
        $this->chatLog("[getMessages] START - roomID: $roomID, user: " . $this->app->user->account);

        $this->loadModel('chat');
        if(!isset($_POST['roomID']) && !isset($_GET['roomID'])) {
            $this->chatLog("[getMessages] ERROR - Room ID is required");
            $this->sendError('Room ID is required');
        }

        $this->chatLog("[getMessages] roomID after parse: $roomID");

        $messages = $this->chat->getMessages($roomID);

        foreach($messages as $message)
        {
            $user = $this->loadModel('user')->getByAccount($message->sender);
            $message->senderName = $user ? $user->realname : $message->sender;
        }

        $this->chatLog("[getMessages] SUCCESS - messages count: " . count($messages) . ", data: " . json_encode((array)$messages));

        $this->sendSuccess($messages);
    }

    public function getUnreadCount()
    {
        $this->chatLog("[getUnreadCount] LINE 1");
        $account = $this->app->user->account;
        $this->chatLog("[getUnreadCount] LINE 2 - account: $account");

        $rooms = $this->dao->query("SELECT DISTINCT r.id FROM " . TABLE_CHATROOM . " r LEFT JOIN " . TABLE_CHATROOMMEMBER . " m ON r.id=m.roomID WHERE m.account = '$account' OR r.createdBy = '$account'")->fetchAll();
        $this->chatLog("[getUnreadCount] LINE 3 - rooms count: " . count($rooms));

        $totalUnread = 0;
        foreach($rooms as $room)
        {
            $this->chatLog("[getUnreadCount] LINE 4 - room: " . $room->id);
            $member = $this->dao->select('lastRead')->from(TABLE_CHATROOMMEMBER)
                ->where('roomID')->eq($room->id)
                ->andWhere('account')->eq($account)
                ->fetch();
            $this->chatLog("[getUnreadCount] LINE 5 - member: " . json_encode($member));
            $lastReadID = $member && $member->lastRead ? (int)$member->lastRead : 0;

            $unread = $this->dao->query("SELECT COUNT(*) as count FROM " . TABLE_CHATMESSAGE . " WHERE roomID = " . (int)$room->id . " AND id > " . $lastReadID)->fetch();
            $this->chatLog("[getUnreadCount] LINE 6 - unread: " . json_encode($unread));
            if($unread) $totalUnread += (int)$unread->count;
        }

        $this->chatLog("[getUnreadCount] LINE 7 - totalUnread: $totalUnread");
        $this->sendSuccess(array('unreadCount' => $totalUnread));
    }

    public function markAsRead()
    {
        $roomID = isset($_POST['roomID']) ? (int)$_POST['roomID'] : 0;
        $this->chatLog("[markAsRead] START - roomID: $roomID");
        $this->loadModel('chat');
        $this->chat->markAsRead($roomID, $this->app->user->account);
        $this->chatLog("[markAsRead] DONE");
        $this->sendSuccess(array('roomID' => $roomID));
    }

    public function addMember()
    {
        $roomID = (int)$this->post->roomID;
        $account = $this->post->account;

        $this->chatLog("[addMember] START - roomID: $roomID, account: $account");

        $this->loadModel('chat');
        if(!$this->post->roomID) $this->sendError('Room ID is required');
        if(!$this->post->account) $this->sendError('Account is required');

        $member = $this->chat->getRoomMember($roomID, $account);
        if($member) {
            $this->chatLog("[addMember] ERROR - Member already exists");
            $this->sendError('Member already exists');
        }

        $this->chat->addRoomMember($roomID, $account);

        $this->chatLog("[addMember] SUCCESS");
        $this->sendSuccess(array('roomID' => $roomID, 'account' => $account));
    }

    public function removeMember()
    {
        $roomID = (int)$this->post->roomID;
        $account = $this->post->account;

        $this->chatLog("[removeMember] START - roomID: $roomID, account: $account");

        $this->loadModel('chat');
        if(!$this->post->roomID) $this->sendError('Room ID is required');
        if(!$this->post->account) $this->sendError('Account is required');

        $this->chat->removeRoomMember($roomID, $account);

        $this->chatLog("[removeMember] SUCCESS");
        $this->sendSuccess(array('roomID' => $roomID, 'account' => $account));
    }

    public function getMembers()
    {
        $roomID = (int)($this->post->roomID || $_GET['roomID'] ?? 0);
        $this->chatLog("[getMembers] START - roomID: $roomID");

        $this->loadModel('chat');
        if(!$roomID) $this->sendError('Room ID is required');

        $members = $this->chat->getRoomMembers($roomID);

        $this->chatLog("[getMembers] members count: " . count($members));
        $this->sendSuccess($members);
    }

    public function getRoom()
    {
        $roomID = (int)($this->post->roomID || $_GET['roomID'] ?? 0);
        $this->chatLog("[getRoom] START - roomID: $roomID");

        $this->loadModel('chat');
        if(!$roomID) $this->sendError('Room ID is required');

        $room = $this->chat->getRoom($roomID);

        if(empty($room)) {
            $this->chatLog("[getRoom] ERROR - Room not found");
            $this->sendError('Room not found');
        }

        $this->chatLog("[getRoom] SUCCESS");
        $this->sendSuccess((array)$room);
    }

    public function startPrivateChat()
    {
        $this->chatLog("[startPrivateChat] START - target account: " . ($this->post->account ?? 'null') . ", current user: " . $this->app->user->account);

        $this->loadModel('chat');
        if(!$this->post->account) {
            $this->chatLog("[startPrivateChat] ERROR - Account is required");
            $this->sendError('Account is required');
        }

        $roomID = $this->chat->getOrCreatePrivateRoom($this->post->account);

        $this->chatLog("[startPrivateChat] roomID: $roomID");

        if(empty($roomID)) {
            $this->chatLog("[startPrivateChat] ERROR - Failed to create private room");
            $this->sendError('Failed to create private room');
        }

        $this->chatLog("[startPrivateChat] SUCCESS");
        $this->sendSuccess(array('roomID' => $roomID));
    }
}