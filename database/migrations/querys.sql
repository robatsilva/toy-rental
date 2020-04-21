ALTER TABLE `kiosks`
DROP COLUMN `user_id`,
DROP FOREIGN KEY `fk_users_kiosks`;

ALTER TABLE `kiosks`
	CHANGE COLUMN `default` `default2` TINYINT
(1) UNSIGNED NULL DEFAULT NULL AFTER `postalcode`;


	Acima excutaods ///////////////////////


CREATE TABLE `cash_drawers`
(
	`id` INT UNSIGNED NULL AUTO_INCREMENT,
	`name` VARCHAR
(50) NOT NULL,
	PRIMARY KEY
(`id`)
)
COLLATE='latin1_swedish_ci';

ALTER TABLE `cash_drawers`
ADD COLUMN `kiosk_id` INT UNSIGNED NULL AFTER `name`,
ADD INDEX `kiosk_id`
(`kiosk_id`),
ADD CONSTRAINT `fk_kiosk_cash_drawers` FOREIGN KEY
(`kiosk_id`) REFERENCES `kiosks`
(`id`);


ALTER TABLE `cash_drawers`
ADD COLUMN `status` TINYINT
(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `kiosk_id`;

insert into cash_drawers
	(name, kiosk_id)
select "Caixa 1", id
from kiosks;

ALTER TABLE `cashes`
ADD COLUMN `cash_drawer_id` INT
(10) UNSIGNED NOT NULL DEFAULT '1' AFTER `kiosk_id`,
ADD INDEX `cash_drawer_id`
(`cash_drawer_id`),
ADD CONSTRAINT `fk_cashes_cashe_drawer` FOREIGN KEY
(`cash_drawer_id`) REFERENCES `cash_drawers`
(`id`);



ALTER TABLE `cash_flows`
ADD COLUMN `cash_drawer_id` INT
(10) UNSIGNED NOT NULL DEFAULT '1' AFTER `kiosk_id`,
ADD INDEX `cash_drawer_id`
(`cash_drawer_id`),
ADD CONSTRAINT `fk_cash_flows_cashe_drawer` FOREIGN KEY
(`cash_drawer_id`) REFERENCES `cash_drawers`
(`id`);



ALTER TABLE `rentals`
ADD COLUMN `cash_drawer_id` INT
(10) UNSIGNED NULL AFTER `kiosk_id`,
ADD INDEX `cash_drawer_id`
(`cash_drawer_id`),
ADD CONSTRAINT `fk_rentals_cashe_drawer` FOREIGN KEY
(`cash_drawer_id`) REFERENCES `cash_drawers`
(`id`);

update rentals set cash_drawer_id = (select id
from cash_drawers
where cash_drawers.kiosk_id = rentals.kiosk_id
order by id asc limit 1);
update cashes
set cash_drawer_id
=
(select id
from cash_drawers
where cash_drawers.kiosk_id = cashes.kiosk_id
order by id asc limit 1);
update cash_flows
set cash_drawer_id
=
(select id
from cash_drawers
where cash_drawers.kiosk_id = cash_flows.kiosk_id
order by id asc limit 1);

------------------------acima em produção

ALTER TABLE `kiosks
`
ADD COLUMN `timezone` VARCHAR
(50) NOT NULL DEFAULT 'America/Sao_Paulo' AFTER `payment_code`;


ALTER TABLE `rentals`
ADD COLUMN `employe_init_id` INT
(10) UNSIGNED NULL DEFAULT NULL AFTER `employe_id`;

UPDATE rentals SET employe_init_id = employe_id;
ALTER TABLE `users`
ADD COLUMN `type` TINYINT
(2) UNSIGNED NOT NULL DEFAULT '1' AFTER `status`;
ALTER TABLE `kiosks`
ADD COLUMN `credit_tax` DECIMAL
(4,2) UNSIGNED NULL DEFAULT '0.00' AFTER `extra_value`,
ADD COLUMN `debit_tax` DECIMAL
(4,2) UNSIGNED NULL DEFAULT '0.00' AFTER `credit_tax`;

ALTER TABLE `users`
	CHANGE COLUMN `type` `type` TINYINT
(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 = franqueador, 2 = funcionario, 3 = relatorio, 4 = franqueado,' AFTER `status`;

ALTER TABLE `rentals`
ADD COLUMN `created_by` VARCHAR
(500) NULL DEFAULT NULL AFTER `reason_cancel`,
ADD COLUMN `change_toy_by` VARCHAR
(500) NULL DEFAULT NULL AFTER `created_by`,
ADD COLUMN `finished_by` VARCHAR
(500) NULL DEFAULT NULL AFTER `change_toy_by`,
ADD COLUMN `canceled_by` VARCHAR
(500) NULL DEFAULT NULL AFTER `finished_by`,
ADD COLUMN `back_by` VARCHAR
(500) NULL DEFAULT NULL AFTER `canceled_by`,
ADD COLUMN `extra_time_by` VARCHAR
(500) NULL DEFAULT NULL AFTER `back_by`,
ADD COLUMN `next_period_by` VARCHAR
(500) NULL DEFAULT NULL AFTER `extra_time_by`,
ADD COLUMN `paused_by` VARCHAR
(500) NULL DEFAULT NULL AFTER `next_period_by`,
ADD COLUMN `started_by` VARCHAR
(500) NULL DEFAULT NULL AFTER `paused_by`;


ALTER TABLE `kiosks`
ADD COLUMN `royalty` DECIMAL
(4,2) UNSIGNED NULL DEFAULT '0.00' AFTER `debit_tax`;

ALTER TABLE `users`
ADD COLUMN `royalty` TINYINT
(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `status`;



ALTER TABLE `kiosk_user`
ADD CONSTRAINT `fk_kiosk_user` FOREIGN KEY
(`kiosk_id`) REFERENCES `kiosks`
(`id`),
ADD CONSTRAINT `fk_user_kiosk` FOREIGN KEY
(`user_id`) REFERENCES `users`
(`id`);

-- DELETE FROM kiosk_user WHERE kiosk_id = 31

CREATE TABLE `permissions`
(
	`id` INT
(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR
(50) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY
(`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

CREATE TABLE `permission_user`
(
	`permission_id` INT
(10) UNSIGNED NULL DEFAULT NULL,
	`user_id` INT
(10) UNSIGNED NULL DEFAULT NULL,
	INDEX `user_id`
(`user_id`),
	INDEX `permission_id`
(`permission_id`),
	CONSTRAINT `fk_permission_user` FOREIGN KEY
(`permission_id`) REFERENCES `permissions`
(`id`),
	CONSTRAINT `fk_user_permission` FOREIGN KEY
(`user_id`) REFERENCES `users`
(`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

INSERT INTO permission_user (
SELECT permissions.id, users.id
FROM permissions
	JOIN users 
WHERE users.`type` = 1 AND permissions.id = 1
);

INSERT INTO permission_user (
SELECT permissions.id, users.id
FROM permissions
	JOIN users 
WHERE users.`type` = 2 AND permissions.id = 2
);

INSERT INTO permission_user (
SELECT permissions.id, users.id
FROM permissions
	JOIN users 
WHERE users.`type` = 3 AND permissions.id = 3
);

--------------------acima executado

CREATE TABLE `types`
(
	`id` INT
(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`description` VARCHAR
(50) NOT NULL DEFAULT '0',
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY
(`id`)
)
ENGINE=InnoDB
AUTO_INCREMENT=1
;


CREATE TABLE `kiosk_type`
(
	`id` INT
(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`kiosk_id` INT
(10) UNSIGNED NOT NULL DEFAULT '0',
	`type_id` INT
(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY
(`id`),
	INDEX `fk_kiosk_type_kiosk_id`
(`kiosk_id`),
	INDEX `fk_kiosk_type_type_id`
(`type_id`),
	CONSTRAINT `fk_kiosk_type_kiosk_id` FOREIGN KEY
(`kiosk_id`) REFERENCES `kiosks`
(`id`),
	CONSTRAINT `fk_kiosk_type_type_id` FOREIGN KEY
(`type_id`) REFERENCES `types`
(`id`)
)
ENGINE=InnoDB
;

INSERT INTO `types` (`
id`,`description
`, `created_at`, `updated_at`) VALUES
(1, 'Carrinho elétrico', NULL, NULL);

INSERT INTO `types` (`
id`,`description
`, `created_at`, `updated_at`) VALUES
(2, 'Carrinho de bebê', NULL, NULL);

INSERT INTO kiosk_type
	(kiosk_id, type_id)
SELECT id, 1
FROM kiosks;

ALTER TABLE `periods`
ADD COLUMN `type_id` INT
(10) UNSIGNED NULL DEFAULT '1' AFTER `kiosk_id`,
ADD CONSTRAINT `fk_periods_types` FOREIGN KEY
(`type_id`) REFERENCES `types`
(`id`);

ALTER TABLE `toys`
ADD COLUMN `type_id` INT
(10) UNSIGNED NOT NULL DEFAULT '1' AFTER `kiosk_id`,
ADD CONSTRAINT `fk_toys_types` FOREIGN KEY
(`type_id`) REFERENCES `types`
(`id`);

--------------------acima executado
