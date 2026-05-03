<?php
$config->devws->group['index']     = '';
$config->devws->group['task']      = '';
$config->devws->group['assignTo']  = '';
$config->devws->group['create']    = '';
$config->devws->group['createDoc'] = '';
$config->devws->group['project']   = '';
$config->devws->group['editProject']   = '';

/* Reuse 'task' privilege for 'assignTo', 'create', 'createDoc' and 'project' so existing users don't need DB changes. */
$config->devws->groupPrivs['assignto']     = 'task';
$config->devws->groupPrivs['create']       = 'task';
$config->devws->groupPrivs['createdoc']    = 'task';
$config->devws->groupPrivs['project']      = 'task';
$config->devws->groupPrivs['editproject']  = 'task';

/* Editor settings for create method. */
$config->devws->editor = new stdclass();
$config->devws->editor->create    = array('id' => 'desc', 'tools' => 'simpleTools');
$config->devws->editor->createDoc = array('id' => 'desc', 'tools' => 'simpleTools');