-- Chat 模块数据库表
-- 聊天室表
CREATE TABLE IF NOT EXISTS `zt_chatroom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT 'private',
  `relatedID` int(11) NOT NULL DEFAULT 0,
  `createdBy` varchar(30) NOT NULL DEFAULT '',
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 聊天室成员表
CREATE TABLE IF NOT EXISTS `zt_chatroommember` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomID` int(11) NOT NULL DEFAULT 0,
  `account` varchar(30) NOT NULL DEFAULT '',
  `joinedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `roomID` (`roomID`),
  KEY `account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 聊天消息表
CREATE TABLE IF NOT EXISTS `zt_chatmessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomID` int(11) NOT NULL DEFAULT 0,
  `sender` varchar(30) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `extra` varchar(255) NOT NULL DEFAULT '',
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `roomID` (`roomID`),
  KEY `sender` (`sender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
