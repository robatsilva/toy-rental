CREATE TABLE `kiosk_user` (
	`kiosk_id` INT(10) UNSIGNED NOT NULL,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`default` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`kiosk_id`, `user_id`),
	INDEX `kiosk_id` (`kiosk_id`),
	INDEX `user_id` (`user_id`)
)
ENGINE=InnoDB
;

insert into kiosk_user (kiosk_id, user_id, default)
select id, user_id, 1 from kiosks;

ALTER TABLE `kiosks`
	DROP COLUMN `user_id`,
	DROP FOREIGN KEY `fk_users_kiosks`;

ALTER TABLE `kiosks`
	CHANGE COLUMN `default` `default2` TINYINT(1) UNSIGNED NULL DEFAULT NULL AFTER `postalcode`;
