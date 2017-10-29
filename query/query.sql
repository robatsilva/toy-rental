ALTER TABLE `rentals`
	CHANGE COLUMN `period_id` `period_id` INT(10) UNSIGNED NULL DEFAULT '0' AFTER `customer_id`;

ALTER TABLE `rentals`
	CHANGE COLUMN `period_id` `period_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `customer_id`;

ALTER TABLE `rentals`
	ADD COLUMN `reason_tolerance` DATETIME NULL DEFAULT NULL AFTER `init`,
	CHANGE COLUMN `rason_cancel` `reason_cancel` VARCHAR(500) NULL DEFAULT NULL AFTER `discount`;

ALTER TABLE `rentals`
	CHANGE COLUMN `reason_tolerance` `reason_tolerance` VARCHAR(500) NULL DEFAULT NULL AFTER `tolerance`;

ALTER TABLE `rentals`
	CHANGE COLUMN `tolerance` `extra_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `extra_value`,
	CHANGE COLUMN `reason_tolerance` `reason_extra_time` VARCHAR(500) NULL DEFAULT NULL AFTER `extra_time`;