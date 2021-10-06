-- Adding default value for all the columns
ALTER TABLE `#__tj_country`
	CHANGE `country_jtext` `country_jtext` varchar(255) NOT NULL DEFAULT '';

ALTER TABLE `#__tjfields_fields`
	CHANGE `label` `label` varchar(255) NOT NULL DEFAULT '',
	CHANGE `name` `name` varchar(255) NOT NULL DEFAULT '',
	CHANGE `title` `title` varchar(255) NOT NULL DEFAULT '',
	CHANGE `type` `type` varchar(255) NOT NULL DEFAULT '',
	CHANGE `state` `state` tinyint(1) NOT NULL DEFAULT '0',
	CHANGE `required` `required` varchar(255) NOT NULL DEFAULT '',
	CHANGE `created_by` `created_by` int(11) NOT NULL DEFAULT '0',
	CHANGE `description` `description` text NOT NULL DEFAULT '',
	CHANGE `js_function` `js_function` text NOT NULL DEFAULT '',
	CHANGE `validation_class` `validation_class` text NOT NULL DEFAULT '',
	CHANGE `ordering` `ordering` int(11) NOT NULL DEFAULT '0',
	CHANGE `client` `client` varchar(255) NOT NULL DEFAULT '',
	CHANGE `group_id` `group_id` int(11) NOT NULL DEFAULT '0',
	CHANGE `showonlist` `showonlist` tinyint(1) NOT NULL DEFAULT '0',
	CHANGE `params` `params` varchar(500) DEFAULT '';

ALTER TABLE `#__tjfields_fields_value`
	CHANGE `field_id` `field_id` int(11) NOT NULL COMMENT 'Field table ID' DEFAULT '0',
	CHANGE `content_id` `content_id` int(11) NOT NULL COMMENT 'client specific id' DEFAULT '0',
	CHANGE `value` `value` text NOT NULL DEFAULT '',
	CHANGE `option_id` `option_id` int(11) DEFAULT NULL DEFAULT '0',
	CHANGE `user_id` `user_id` int(11) NOT NULL DEFAULT '0',
	CHANGE `email_id` `email_id`  varchar(255) NOT NULL DEFAULT '',
	CHANGE `client` `client` varchar(255) NOT NULL COMMENT 'client(eg com_jticketing.event)' DEFAULT '';

ALTER TABLE `#__tjfields_groups`
	CHANGE `ordering` `ordering` int(11) NOT NULL DEFAULT '0',
	CHANGE `state` `state` tinyint(1) NOT NULL DEFAULT '0',
	CHANGE `created_by` `created_by` int(11) NOT NULL DEFAULT '0',
	CHANGE `name` `name` varchar(255) NOT NULL DEFAULT '',
	CHANGE `title` `title` varchar(255) NOT NULL DEFAULT '',
	CHANGE `client` `client` varchar(255) NOT NULL DEFAULT '';

ALTER TABLE `#__tjfields_options`
	CHANGE `field_id` `field_id` int(11) NOT NULL DEFAULT '0',
	CHANGE `options` `options` varchar(255) NOT NULL DEFAULT '',
	CHANGE `value` `value` varchar(255) NOT NULL DEFAULT '';

ALTER TABLE `#__tjfields_category_mapping`
	CHANGE `field_id` `field_id` INT(11) NOT NULL DEFAULT '0',
	CHANGE `category_id` `category_id` INT(11) NOT NULL COMMENT 'CATEGORY ID FROM JOOMLA CATEGORY TABLE FOR CLIENTS EG CLIENT=COM_QUICK2CART.PRODUCT' DEFAULT '0';

ALTER TABLE `#__tj_region`
	CHANGE `country_id` `country_id` int(11) NOT NULL DEFAULT '0',
	CHANGE `region_3_code` `region_3_code` varchar(3) NOT NULL DEFAULT '',
	CHANGE `region_code` `region_code` varchar(8) NOT NULL DEFAULT '',
	CHANGE `region` `region` varchar(64) NOT NULL DEFAULT '',
	CHANGE `region_jtext` `region_jtext` varchar(255) NOT NULL DEFAULT '';
