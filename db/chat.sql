-- Chat module database tables

-- Chat rooms table
CREATE TABLE IF NOT EXISTS `zt_chatroom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL COMMENT 'team|task',
  `relatedID` int(11) DEFAULT 0 COMMENT 'Related task ID for task chat',
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_type_related` (`type`, `relatedID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chat messages table
CREATE TABLE IF NOT EXISTS `zt_chatmessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomID` int(11) NOT NULL,
  `sender` varchar(30) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'text' COMMENT 'text|file',
  `extra` text COMMENT 'Extra data for file messages',
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_room` (`roomID`),
  KEY `idx_sender` (`sender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chat room members table
CREATE TABLE IF NOT EXISTS `zt_chatroommember` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomID` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `joinedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_room_account` (`roomID`, `account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chat mentions table
CREATE TABLE IF NOT EXISTS `zt_chatmention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `messageID` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_message` (`messageID`),
  KEY `idx_account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
