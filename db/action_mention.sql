-- Action mention table
CREATE TABLE IF NOT EXISTS `zt_actionmention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actionID` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `objectType` varchar(50) NOT NULL,
  `objectID` int(11) NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_action` (`actionID`),
  KEY `idx_account` (`account`),
  KEY `idx_object` (`objectType`, `objectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
