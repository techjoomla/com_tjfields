ALTER TABLE `#__tjfields_fields_value` CHANGE `value` `value` mediumtext DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `#__tjfields_fields_conditions` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`state` tinyint(1) NOT NULL,
	`show` int(11) NOT NULL,
	`field_to_show` int(11) NOT NULL,
	`condition_match` int(11) NOT NULL,
	`field_on_show` int(11) NOT NULL,
	`condition` text CHARACTER NOT NULL,
	`type_id` int NOT NULL,
	`client` varchar(255) NOT NULL,
	`created_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`created_by` INT(10) NOT NULL DEFAULT '0' ,
	`modified_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`modified_by` INT(10) NOT NULL DEFAULT '0' ,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
