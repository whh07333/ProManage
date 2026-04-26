<?php
class chatModel extends model
{
    public function createRoom($name, $type, $relatedID = 0)
    {
        $exists = $this->dao->select('id')->from(TABLE_CHATROOM)
            ->where('name')->eq($name)
            ->andWhere('createdBy')->eq($this->app->user->account)
            ->fetch();
        if($exists) return array('result' => 'fail', 'message' => '聊天室名称已存在，请使用其他名称');

        $room = new stdclass();
        $room->name       = $name;
        $room->type       = $type;
        $room->relatedID  = (int)$relatedID;
        $room->createdBy  = $this->app->user->account;
        $room->createdDate = helper::now();

        $this->dao->insert(TABLE_CHATROOM)->data($room)->autoCheck()->exec();

        $roomID = 0;
        if(!dao::isError()) {
            $result = $this->dao->query("SELECT LAST_INSERT_ID() as id")->fetch();
            if($result) $roomID = $result->id;
        }

        if(empty($roomID)) {
            $foundRooms = $this->dao->select('*')->from(TABLE_CHATROOM)
                ->where('name')->eq($name)
                ->andWhere('createdBy')->eq($this->app->user->account)
                ->orderBy('id desc')->fetchAll();
            if(!empty($foundRooms)) $roomID = $foundRooms[0]->id;
        }

        if($roomID) {
            $this->addRoomMember($roomID, $this->app->user->account);
        }

        return $roomID;
    }

    public function getRooms($type = '', $relatedID = 0)
    {
        $account = $this->app->user->account;
        $query = $this->dao->select('DISTINCT r.*')->from(TABLE_CHATROOM)->alias('r')
            ->leftJoin(TABLE_CHATROOMMEMBER)->alias('m')->on('r.id=m.roomID')
            ->where('m.account')->eq($account)
            ->orWhere('r.createdBy')->eq($account);
        if(!empty($type)) $query->andWhere('r.type')->eq($type);
        if($relatedID > 0) $query->andWhere('r.relatedID')->eq($relatedID);
        return $query->fetchAll();
    }

    public function getRoom($roomID)
    {
        return $this->dao->select('*')->from(TABLE_CHATROOM)->where('id')->eq($roomID)->fetch();
    }

    public function getRoomByID($roomID)
    {
        return $this->dao->select('*')->from(TABLE_CHATROOM)->where('id')->eq($roomID)->fetch();
    }

    public function sendMessage($roomID, $content, $type = 'text', $extra = '')
    {
        $message = new stdclass();
        $message->roomID      = $roomID;
        $message->sender      = $this->app->user->account;
        $message->content     = $content;
        $message->type        = $type;
        $message->extra       = $extra;
        $message->createdDate = helper::now();

        $this->dao->insert(TABLE_CHATMESSAGE)->data($message)->autoCheck()->exec();

        $messageID = 0;
        if(!dao::isError()) {
            $result = $this->dao->query("SELECT LAST_INSERT_ID() as id")->fetch();
            if($result) $messageID = $result->id;
        }
        return $messageID;
    }

    public function getMessages($roomID, $limit = 50)
    {
        return $this->dao->select('*')->from(TABLE_CHATMESSAGE)
            ->where('roomID')->eq($roomID)
            ->orderBy('id asc')
            ->limit($limit)
            ->fetchAll();
    }

    public function addRoomMember($roomID, $account)
    {
        $member = new stdclass();
        $member->roomID      = $roomID;
        $member->account     = $account;
        $member->joinedDate  = helper::now();

        $this->dao->insert(TABLE_CHATROOMMEMBER)->data($member)->exec();
    }

    public function getRoomMembers($roomID)
    {
        return $this->dao->select('*')->from(TABLE_CHATROOMMEMBER)->where('roomID')->eq($roomID)->fetchAll();
    }

    public function getRoomMember($roomID, $account)
    {
        return $this->dao->select('*')->from(TABLE_CHATROOMMEMBER)
            ->where('roomID')->eq($roomID)
            ->andWhere('account')->eq($account)
            ->fetch();
    }

    public function getOrCreatePrivateRoom($otherAccount)
    {
        $myAccount = $this->app->user->account;

        $allPrivateRooms = $this->dao->select('*')->from(TABLE_CHATROOM)
            ->where('type')->eq('private')
            ->andWhere('relatedID')->eq(0)
            ->fetchAll();

        foreach($allPrivateRooms as $r) {
            $members = $this->getRoomMembers($r->id);
            if(count($members) === 2) {
                $accounts = array_column($members, 'account');
                if(in_array($myAccount, $accounts) && in_array($otherAccount, $accounts)) {
                    return $r->id;
                }
            }
        }

        $newRoom = new stdclass();
        $newRoom->name        = 'private_' . $myAccount . '_' . $otherAccount;
        $newRoom->type        = 'private';
        $newRoom->relatedID   = 0;
        $newRoom->createdBy   = $myAccount;
        $newRoom->createdDate = helper::now();

        $this->dao->insert(TABLE_CHATROOM)->data($newRoom)->autoCheck()->exec();

        $roomID = 0;
        if(!dao::isError()) {
            $result = $this->dao->query("SELECT LAST_INSERT_ID() as id")->fetch();
            if($result) $roomID = $result->id;
        }

        if($roomID) {
            $this->addRoomMember($roomID, $myAccount);
            $this->addRoomMember($roomID, $otherAccount);
        }

        return $roomID;
    }

    public function removeRoomMember($roomID, $account)
    {
        $this->dao->delete()->from(TABLE_CHATROOMMEMBER)
            ->where('roomID')->eq($roomID)
            ->andWhere('account')->eq($account)
            ->exec();
    }

    public function getUnreadCount()
    {
        $account = $this->app->user->account;
        $this->chatLog("[getUnreadCount] account: $account");
        $rooms = $this->getRoomsByAccount($account);
        $totalUnread = 0;

        foreach($rooms as $room)
        {
            $member = $this->getRoomMember($room->id, $account);
            $lastReadID = $member && $member->lastRead ? (int)$member->lastRead : 0;

            $unreadInRoom = $this->dao->query("SELECT COUNT(*) as count FROM " . TABLE_CHATMESSAGE . " WHERE roomID = " . (int)$room->id . " AND id > " . $lastReadID)->fetch();
            if($unreadInRoom) $totalUnread += (int)$unreadInRoom->count;
        }

        $this->chatLog("[getUnreadCount] totalUnread: $totalUnread");
        return $totalUnread;
    }

    private function chatLog($msg) {
        $logFile = '/data/zentao/tmp/log/chat_debug.log';
        @file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $msg\n", FILE_APPEND);
    }

    public function markAsRead($roomID, $account)
    {
        $member = $this->getRoomMember($roomID, $account);
        if($member)
        {
            $lastMessage = $this->dao->select('id')->from(TABLE_CHATMESSAGE)
                ->where('roomID')->eq($roomID)
                ->orderBy('id desc')
                ->limit(1)
                ->fetch();

            if($lastMessage)
            {
                $this->dao->update(TABLE_CHATROOMMEMBER)
                    ->set('lastRead')->eq($lastMessage->id)
                    ->where('roomID')->eq($roomID)
                    ->andWhere('account')->eq($account)
                    ->exec();
            }
        }
    }
}