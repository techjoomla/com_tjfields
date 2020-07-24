ALTER TABLE `#__tjfields_fields` MODIFY `description` TEXT NOT NULL;
ALTER TABLE `#__tjfields_fields_value` ADD INDEX(`field_id`);
ALTER TABLE `#__tjfields_fields_value` ADD INDEX(`content_id`);