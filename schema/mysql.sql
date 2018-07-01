create table if not exists `page`
(
	`id` int(10) not null auto_increment,
	`alias` varchar(100) default null,
	`title` varchar(100) default null,
	`active` tinyint(1) default 1,
	`modifyDate` datetime,
	`content` text,
	primary key (`id`),
	key `alias` (`alias`)
) engine InnoDB;
