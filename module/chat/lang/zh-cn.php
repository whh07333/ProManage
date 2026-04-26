<?php
$lang->chat = new stdclass();
$lang->chat->common      = '职聊';
$lang->chat->messages    = '消息';
$lang->chat->createRoom  = '创建聊天室';
$lang->chat->noRooms     = '暂无聊天室，创建一个开始聊天吧！';
$lang->chat->enterRoom  = '选择一个聊天室开始聊天';
$lang->chat->selectRoom = '请从左侧选择一个聊天室';
$lang->chat->noMessages  = '暂无消息，开始聊天吧！';
$lang->chat->sendMessage = '输入消息...';
$lang->chat->send       = '发送';
$lang->chat->roomName    = '聊天室名称';
$lang->chat->roomType    = '聊天室类型';
$lang->chat->enterRoomName = '请输入聊天室名称';
$lang->chat->nameRequired  = '聊天室名称不能为空';
$lang->chat->nameTooShort  = '聊天室名称至少2个字符';
$lang->chat->creating      = '创建中';
$lang->chat->createFailed  = '创建失败，请重试';
$lang->chat->create      = '创建';
$lang->chat->members     = '成员';
$lang->chat->addMember   = '添加成员';
$lang->chat->addMemberPlaceholder = '输入账号...';
$lang->chat->searchUser   = '搜索用户...';
$lang->chat->selectUser   = '请先选择一个用户';
$lang->chat->cancel       = '取消';
$lang->chat->confirm      = '确认';
$lang->chat->rooms       = '聊天室';
$lang->chat->contacts    = '联系人';
$lang->chat->noContacts   = '暂无联系人';
$lang->chat->you         = '我';

$lang->chat->roomTypes = array();
$lang->chat->roomTypes['team'] = '团队聊天';
$lang->chat->roomTypes['task'] = '任务聊天';

$lang->chat->error = new stdclass();
$lang->chat->error->roomNameRequired = '聊天室名称不能为空';
$lang->chat->error->roomNameExists = '聊天室名称已存在，请使用其他名称';
$lang->chat->error->roomTypeRequired = '聊天室类型不能为空';
$lang->chat->error->messageRequired  = '消息内容不能为空';
$lang->chat->error->roomRequired     = '请先选择一个聊天室';
$lang->chat->error->memberRequired   = '成员账号不能为空';

$lang->chat->privileges = new stdclass();
$lang->chat->privileges->index = '访问职聊';