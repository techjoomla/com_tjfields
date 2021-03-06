CREATE TABLE IF NOT EXISTS `#__tjfields_fields` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`core` int(11) NOT NULL DEFAULT '0',
	`asset_id` int(11) NOT NULL DEFAULT '0',
	`label` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`title` varchar(255) NOT NULL,
	`type` varchar(255) NOT NULL,
	`state` tinyint(1) NOT NULL,
	`required` varchar(255) NOT NULL,
	`readonly` int(11) NOT NULL DEFAULT '0',
	`created_by` int(11) NOT NULL,
	`description` text NOT NULL,
	`js_function` text NOT NULL,
	`validation_class` text NOT NULL,
	`ordering` int(11) NOT NULL,
	`filterable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - For not filterable field. 1 for filterable field',
	`client` varchar(255) NOT NULL,
	`group_id` int(11) NOT NULL,
	`showonlist` tinyint(1) NOT NULL,
	`params` varchar(500),
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__tjfields_fields_value` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`field_id` int(11) NOT NULL COMMENT 'Field table ID',
	`content_id` int(11) NOT NULL COMMENT 'client specific id',
	`value` text NOT NULL,
	`option_id` int(11) DEFAULT NULL,
	`user_id` int(11) NOT NULL,
	`email_id` varchar(255) NOT NULL,
	`client` varchar(255) NOT NULL COMMENT 'client(eg com_jticketing.event)',
	PRIMARY KEY (`id`),
	KEY `field_id` (`field_id`),
	KEY `content_id` (`content_id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__tjfields_groups` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`ordering` int(11) NOT NULL,
	`asset_id` int(11) NOT NULL DEFAULT '0',
	`state` tinyint(1) NOT NULL,
	`created_by` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`title` varchar(255) NOT NULL,
	`client` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__tjfields_options` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`field_id` int(11) NOT NULL,
	`options` varchar(255) NOT NULL,
	`value` varchar(255) NOT NULL,
	`ordering` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__tjfields_category_mapping` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `field_id` INT(11) NOT NULL,
  `category_id` INT(11) NOT NULL COMMENT 'CATEGORY ID FROM JOOMLA CATEGORY TABLE FOR CLIENTS EG CLIENT=COM_QUICK2CART.PRODUCT',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
