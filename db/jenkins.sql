-- Jenkins module database tables

-- Jenkins servers table
CREATE TABLE IF NOT EXISTS `zt_jenkins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `username` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `updatedBy` varchar(30) DEFAULT NULL,
  `updatedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Jenkins builds table
CREATE TABLE IF NOT EXISTS `zt_jenkinsbuild` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serverID` int(11) NOT NULL,
  `jobName` varchar(255) NOT NULL,
  `buildNumber` int(11) NOT NULL,
  `result` varchar(20) DEFAULT 'PENDING',
  `timestamp` datetime NOT NULL,
  `duration` float DEFAULT 0,
  `url` varchar(500) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_server_job` (`serverID`, `jobName`),
  KEY `idx_build_number` (`jobName`, `buildNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Jenkins build relations table
CREATE TABLE IF NOT EXISTS `zt_jenkinsbuildrelation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serverID` int(11) NOT NULL,
  `jobName` varchar(255) NOT NULL,
  `buildNumber` int(11) NOT NULL,
  `objectType` varchar(50) NOT NULL,
  `objectID` int(11) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_object` (`objectType`, `objectID`),
  KEY `idx_build` (`serverID`, `jobName`, `buildNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
