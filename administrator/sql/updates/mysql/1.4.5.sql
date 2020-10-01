ALTER TABLE `#__tjfields_fields` MODIFY `description` TEXT NOT NULL;
ALTER TABLE `#__tjfields_fields_value` ADD INDEX(`field_id`);
ALTER TABLE `#__tjfields_fields_value` ADD INDEX(`content_id`);
ALTER TABLE `#__tjfields_options` ADD `ordering` INT(11) NOT NULL AFTER `value`;
DROP TABLE IF EXISTS `#__tjfields_client_type`;
