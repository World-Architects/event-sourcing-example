CREATE TABLE `accounts` (
	`id` CHAR(36) NOT NULL DEFAULT uuid() COLLATE 'utf8_unicode_ci',
	`name` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`balance` DECIMAL(10,2) NULL DEFAULT NULL,
	`description` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;
