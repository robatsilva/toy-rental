ALTER TABLE `kiosks`
	DROP COLUMN `user_id`,
	DROP FOREIGN KEY `fk_users_kiosks`;

ALTER TABLE `kiosks`
	CHANGE COLUMN `default` `default2` TINYINT(1) UNSIGNED NULL DEFAULT NULL AFTER `postalcode`;


	Acima excutaods ///////////////////////


CREATE TABLE `cash_drawers` (
	`id` INT UNSIGNED NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci';

ALTER TABLE `cash_drawers`
	ADD COLUMN `kiosk_id` INT UNSIGNED NULL AFTER `name`,
	ADD INDEX `kiosk_id` (`kiosk_id`),
	ADD CONSTRAINT `fk_kiosk_cash_drawers` FOREIGN KEY (`kiosk_id`) REFERENCES `kiosks` (`id`);


ALTER TABLE `cash_drawers`
	ADD COLUMN `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `kiosk_id`;

insert into cash_drawers (name, kiosk_id)
select "Caixa 1", id from kiosks;

ALTER TABLE `cashes`
	ADD COLUMN `cash_drawer_id` INT(10) UNSIGNED NOT NULL DEFAULT '1' AFTER `kiosk_id`,
	ADD INDEX `cash_drawer_id` (`cash_drawer_id`),
	ADD CONSTRAINT `fk_cashes_cashe_drawer` FOREIGN KEY (`cash_drawer_id`) REFERENCES `cash_drawers` (`id`);



ALTER TABLE `cash_flows`
	ADD COLUMN `cash_drawer_id` INT(10) UNSIGNED NOT NULL DEFAULT '1' AFTER `kiosk_id`,
	ADD INDEX `cash_drawer_id` (`cash_drawer_id`),
	ADD CONSTRAINT `fk_cash_flows_cashe_drawer` FOREIGN KEY (`cash_drawer_id`) REFERENCES `cash_drawers` (`id`);



ALTER TABLE `rentals`
	ADD COLUMN `cash_drawer_id` INT(10) UNSIGNED NULL AFTER `kiosk_id`,
	ADD INDEX `cash_drawer_id` (`cash_drawer_id`),
	ADD CONSTRAINT `fk_rentals_cashe_drawer` FOREIGN KEY (`cash_drawer_id`) REFERENCES `cash_drawers` (`id`);

update rentals set cash_drawer_id = (select id from cash_drawers where cash_drawers.kiosk_id = rentals.kiosk_id order by id asc limit 1);
update cashes set cash_drawer_id = (select id from cash_drawers where cash_drawers.kiosk_id = cashes.kiosk_id order by id asc limit 1);
update cash_flows set cash_drawer_id = (select id from cash_drawers where cash_drawers.kiosk_id = cash_flows.kiosk_id order by id asc limit 1);