CREATE TABLE `accounts` (
	`id` CHAR(36) NOT NULL DEFAULT uuid() COLLATE 'utf8_unicode_ci',
	`account_id` CHAR(36) NOT NULL DEFAULT uuid() COLLATE 'utf8_unicode_ci',
	`debit` DECIMAL(10,2) NULL DEFAULT NULL,
	`credit` DECIMAL(10,2) NULL DEFAULT NULL,
	`balance` DECIMAL(10,2) NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;
