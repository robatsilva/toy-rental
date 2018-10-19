ALTER TABLE `kiosks`
	DROP COLUMN `user_id`,
	DROP FOREIGN KEY `fk_users_kiosks`;

ALTER TABLE `kiosks`
	CHANGE COLUMN `default` `default2` TINYINT(1) UNSIGNED NULL DEFAULT NULL AFTER `postalcode`;
