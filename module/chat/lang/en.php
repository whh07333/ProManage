<?php
$lang->chat = new stdclass();
$lang->chat->common      = 'Chat';
$lang->chat->messages    = 'Messages';
$lang->chat->createRoom  = 'Create Room';
$lang->chat->noRooms     = 'No chat rooms yet. Create one to start chatting!';
$lang->chat->enterRoom  = 'Select a room to start chatting';
$lang->chat->selectRoom = 'Please select a room from the left';
$lang->chat->noMessages  = 'No messages yet. Start the conversation!';
$lang->chat->sendMessage = 'Type a message...';
$lang->chat->send       = 'Send';
$lang->chat->roomName    = 'Room Name';
$lang->chat->roomType    = 'Room Type';
$lang->chat->create      = 'Create';
$lang->chat->members     = 'Members';
$lang->chat->addMember   = 'Add Member';
$lang->chat->addMemberPlaceholder = 'Enter account...';

$lang->chat->roomTypes = array();
$lang->chat->roomTypes['team'] = 'Team Chat';
$lang->chat->roomTypes['task'] = 'Task Chat';

$lang->chat->error = new stdclass();
$lang->chat->error->roomNameRequired = 'Room name is required';
$lang->chat->error->roomTypeRequired = 'Room type is required';
$lang->chat->error->messageRequired  = 'Message content is required';
$lang->chat->error->roomRequired     = 'Please select a room first';
$lang->chat->error->memberRequired   = 'Member account is required';