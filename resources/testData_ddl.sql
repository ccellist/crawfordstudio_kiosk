/* drop database if exists admin;
 create database admin;
 use admin;

 CREATE TABLE `sessiondata` (
   `id` varchar(150) NOT NULL DEFAULT '',
   `data` text,
   `access` datetime DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/
drop database if exists crawfordphoto;
create database crawfordphoto;
use crawfordphoto;

create table links (
    uid integer auto_increment primary key, 
    text text, 
    url text, 
    target varchar(100)
);

create table errors (
    uid integer auto_increment primary key, 
    message text
);

CREATE TABLE `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `password` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `must_chg_pwd` tinyint(1) DEFAULT '0',
  `statusid` int(10) unsigned DEFAULT NULL,
  `admin_user` enum('0','1') DEFAULT '0',
  `expires_on` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;


CREATE TABLE `active_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `instance_id` text,
  `last_login` datetime DEFAULT NULL,
  `expire` datetime DEFAULT NULL,
  `ip_addr` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `userid` (`userid`),
  CONSTRAINT `active_users_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

create table photos(
    uid int auto_increment not null primary key,
    event_id int default 0,
    photo_name varchar(50) not null,
    photo_thumbnail_name varchar(50),
    photo_uri text,
    photo_price decimal(10,4),
    orient_portrait tinyint(1) default 0
) Engine=InnoDB;

create table gym_meets (
    uid int auto_increment not null primary key,
    meet_name varchar(100) not null
) Engine=InnoDB;

create table event_lookup (
    uid int auto_increment not null primary key,
    event_name varchar(40) not null
) Engine=InnoDB;

create table rotations (
    uid int auto_increment not null primary key,
    rotation_name varchar(40)
) Engine=InnoDB;

create table sessions (
    uid int auto_increment not null primary key,
    session_name varchar(40)
) Engine=InnoDB;

create table meet_events (
    uid int auto_increment not null primary key,
    meet_id int not null,
    session_id int not null,
    event_id int not null,
    rotation_id int not null
) Engine=InnoDB;

create table orders(
    uid int auto_increment not null primary key,
    customer_id int not null,
    order_subtotal decimal(10,4),
    order_discount decimal(10,4) default 0,
    order_taxamt decimal (10,4),
    order_saletotal decimal (10,4),
    is_pending tinyint default 0,
    date_created timestamp default now()
) Engine=InnoDB;


create table orders_archive (
    uid int auto_increment not null primary key,
    customer_id int not null,
    customer_fname varchar(100),
    customer_lname varchar(100),
    customer_email_address varchar(250),
    customer_primary_phone varchar(50),
    order_subtotal decimal(10,4),
    order_id int,
    order_discount decimal(10,4) default 0,
    order_taxamt decimal (10,4),
    order_saletotal decimal (10,4),
    order_num_items int default 0,
    date_archived timestamp default now()
);

create table order_items(
    order_id int not null,
    photo_id int not null
) Engine=InnoDB;

create table customers(
    uid int auto_increment not null primary key,
    first_name varchar(50) not null,
    last_name varchar(100) not null,
    email_address varchar(100) not null,
    primary_phone varchar(25)
) Engine=InnoDB;

create table pricing_rules ( 
    uid integer auto_increment primary key, 
    qty_threshold integer not null, 
    discount_rate decimal(10,4) not null
);


alter table photos
add constraint fk_evtId foreign key (event_id)
references meet_events(uid);

alter table orders
add constraint fk_custId foreign key (customer_id)
references customers(uid);

alter table order_items
add constraint fk_orderId foreign key (order_id)
references orders(uid);

alter table order_items
add constraint fk_photoId foreign key (photo_id)
references photos(uid);

alter table meet_events
add constraint fk_sessionId foreign key (session_id)
references sessions(uid);

alter table meet_events
add constraint fk_meetId foreign key (meet_id)
references gym_meets(uid);

alter table meet_events
add constraint fk_evtLookupId foreign key (event_id)
references event_lookup(uid);

alter table meet_events
add constraint fk_rotationId foreign key (rotation_id)
references rotations(uid);

create VIEW `events_by_meet_view` AS 
select 
`me`.`uid` AS `uid`,`me`.`meet_id` AS `meet_id`,`me`.`event_id` AS `event_id`, `rot`.`uid` AS `rotation_id`, `rot`.`rotation_name` as `rotation_name`,
`gm`.`meet_name` AS `meet_name`,`el`.`event_name` AS `event_name`, `ses`.`uid` AS `session_id`, `ses`.`session_name` AS `session_name` 
from ((`gym_meets` `gm` join `meet_events` `me` on((`me`.`meet_id` = `gm`.`uid`))) 
join `event_lookup` `el` on((`el`.`uid` = `me`.`event_id`))
join `rotations` `rot` on((`rot`.`uid` = `me`.`rotation_id`))
join `sessions` `ses` on((`ses`.`uid` = `me`.`session_id`))) 
order by `me`.`meet_id`,`me`.`event_id`;

create view orders_by_customer_view as 
select 
o.uid as order_uid, o.order_subtotal, o.order_discount, 
o.order_taxamt, o.order_saletotal, o.is_pending, o.date_created,
c.uid as customer_uid, c.first_name, c.last_name, 
c.email_address, c.primary_phone 
from orders o inner join customers c on o.customer_id = c.uid order by o.uid;

insert into gym_meets (meet_name) values ('Test Meet 1');
insert into gym_meets (meet_name) values ('Test Meet 2');
insert into gym_meets (meet_name) values ('Test Meet 3');

insert into sessions (session_name) values ('Session 1'), ('Session 2');

insert into event_lookup (event_name) values ('Vault');
insert into event_lookup (event_name) values ('Bars');
insert into event_lookup (event_name) values ('Floor');
insert into event_lookup (event_name) values ('Beam');

insert into rotations (rotation_name) values ('Morning A'),
('Morning B'), ('Morning C');

insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Vault'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Vault'),
(select uid from rotations where rotation_name = 'Morning B')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Vault'),
(select uid from rotations where rotation_name = 'Morning C')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Floor'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Floor'),
(select uid from rotations where rotation_name = 'Morning B')
;

insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Floor'),
(select uid from rotations where rotation_name = 'Morning C')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Beam'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Beam'),
(select uid from rotations where rotation_name = 'Morning B')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Beam'),
(select uid from rotations where rotation_name = 'Morning C')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Bars'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 1'),
(select uid from sessions where session_name = 'Session 2'),
(select uid from event_lookup where event_name = 'Bars'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 2'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Vault'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 2'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Floor'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 2'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Beam'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 2'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Bars'),
(select uid from rotations where rotation_name = 'Morning A')
;

insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 3'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Vault'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 3'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Floor'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 3'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Beam'),
(select uid from rotations where rotation_name = 'Morning A')
;
insert into meet_events (meet_id, session_id, event_id, rotation_id)
select 
(select uid from gym_meets where meet_name = 'Test Meet 3'),
(select uid from sessions where session_name = 'Session 1'),
(select uid from event_lookup where event_name = 'Bars'),
(select uid from rotations where rotation_name = 'Morning A')
;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and s.session_name = 'Session 1' and l.event_name = 'Vault' and m.rotation_id = 1),
'DSC00001.JPG','DSC00001_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and s.session_name = 'Session 1' and l.event_name = 'Vault' and m.rotation_id = 1),
'DSC00002.JPG','DSC00002_tn.JPG','c:\\testpics',3.00,1;
insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and s.session_name = 'Session 1' and l.event_name = 'Vault' and m.rotation_id = 2),
'DSC00017.JPG','DSC00017_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and s.session_name = 'Session 1' and l.event_name = 'Vault' and m.rotation_id = 2),
'DSC00018.JPG','DSC00018_tn.JPG','c:\\testpics',3.00,1;
insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and s.session_name = 'Session 1' and l.event_name = 'Vault' and m.rotation_id = 3),
'DSC00019.JPG','DSC00019_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and s.session_name = 'Session 1' and l.event_name = 'Vault' and m.rotation_id = 3),
'DSC00020.JPG','DSC00020_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name, photo_uri, photo_price)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Bars' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00003.JPG','DSC00003_tn.JPG','c:\\testpics',3.00;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Bars' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00004.JPG','DSC00004_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Beam' and m.rotation_id = 2 and s.session_name = 'Session 1'),
'DSC00021.JPG','DSC00021_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name, photo_uri, photo_price)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Beam' and m.rotation_id = 2 and s.session_name = 'Session 1'),
'DSC00022.JPG','DSC00022_tn.JPG','c:\\testpics',3.00;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Beam' and m.rotation_id = 3 and s.session_name = 'Session 1'),
'DSC00023.JPG','DSC00023_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name, photo_uri, photo_price)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Beam' and m.rotation_id = 3 and s.session_name = 'Session 1'),
'DSC00024.JPG','DSC00024_tn.JPG','c:\\testpics',3.00;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Floor' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00005.JPG','DSC00005_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Floor' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00006.JPG','DSC00006_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where g.uid = m.meet_id and l.uid = m.event_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Beam' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00007.JPG','DSC00007_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where g.uid = m.meet_id and l.uid = m.event_id and g.meet_name = 'Test Meet 1' and l.event_name = 'Beam' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00008.JPG','DSC00008_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name,photo_thumbnail_name,  photo_uri, photo_price,orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 2' and l.event_name = 'Vault' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00009.JPG','DSC00009_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 2' and l.event_name = 'Vault' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00010.JPG','DSC00010_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 2' and l.event_name = 'Bars' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00016.JPG','DSC00016_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 2' and l.event_name = 'Bars' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00015.JPG','DSC00015_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 2' and l.event_name = 'Beam' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00014.JPG','DSC00014_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 2' and l.event_name = 'Beam' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00013.JPG','DSC00013_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 2' and l.event_name = 'Floor' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00012.JPG','DSC00012_tn.JPG','c:\\testpics',3.00,1;

insert into photos (event_id, photo_name, photo_thumbnail_name,  photo_uri, photo_price, orient_portrait)
select
(select m.uid from meet_events m, gym_meets g, sessions s, event_lookup l where s.uid = m.session_id and l.uid = m.event_id and g.uid = m.meet_id and g.meet_name = 'Test Meet 2' and l.event_name = 'Floor' and m.rotation_id = 1 and s.session_name = 'Session 1'),
'DSC00011.JPG','DSC00011_tn.JPG','c:\\testpics',3.00,1;

insert into customers (first_name, last_name, email_address, primary_phone) values ('Test', 'Customer', 'DSC000@email.com', '');
insert into customers (first_name, last_name, email_address, primary_phone) values ('Test2', 'Customer2', 'DSC0002@email.com', '');
insert into customers (first_name, last_name, email_address, primary_phone) values ('Test3', 'Customer3', 'DSC0003@email.com', '');
insert into customers (first_name, last_name, email_address, primary_phone) values ('Test4', 'Customer4', 'DSC0004@email.com', '');
insert into customers (first_name, last_name, email_address, primary_phone) values ('Test5', 'Customer5', 'DSC0005@email.com', '');
insert into customers (first_name, last_name, email_address, primary_phone) values ('Test6', 'Customer6', 'DSC0006@email.com', '');
insert into customers (first_name, last_name, email_address, primary_phone) values ('Test7', 'Customer7', 'DSC0007@email.com', '');

insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal, is_pending) values (1, 3.0000, 0.0000, 0.1500, 3.1500, 1);
insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal, is_pending) values (1, 6.0000, 1.0000, 0.2500, 5.2500, 0);
insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal, is_pending) values (2, 3.0000, 0.0000, 0.1500, 3.1500, 0);
insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal, is_pending) values (3, 6.0000, 1.0000, 0.2500, 5.2500, 1);
insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal, is_pending) values (3, 9.0000, 1.0000, 0.4000, 8.4000, 1);
insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal, is_pending) values (3, 12.0000, 2.0000, 0.5000, 10.5000, 0);
insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal, is_pending) values (3, 15.0000, 5.0000, 0.5000, 10.5000, 0);
insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal, is_pending) values (3, 18.0000, 5.0000, 0.6500, 13.6500, 0);

insert into orders_archive (customer_id, customer_fname, customer_lname, customer_email_address, customer_primary_phone, order_id, order_subtotal, order_discount, order_taxamt, order_saletotal, order_num_items) values (1, 'Test', 'Customer', 'DSC000@email.com', '', 99, 6.0000, 1.0000, 0.2500, 5.2500, 2);
insert into orders_archive (customer_id, customer_fname, customer_lname, customer_email_address, customer_primary_phone, order_id, order_subtotal, order_discount, order_taxamt, order_saletotal, order_num_items) values (1, 'Test', 'Customer', 'DSC000@email.com', '', 100, 9.0000, 1.0000, 0.4000, 8.4000, 3);
insert into orders_archive (customer_id, customer_fname, customer_lname, customer_email_address, customer_primary_phone, order_id, order_subtotal, order_discount, order_taxamt, order_saletotal, order_num_items) values (3, 'Test3', 'Customer3', 'DSC0003@email.com', '', 101, 3.0000, 0.0000, 0.1500, 3.1500, 1);

insert into order_items (order_id, photo_id) values (1,1);
insert into order_items (order_id, photo_id) values (2,2);
insert into order_items (order_id, photo_id) values (2,3);
insert into order_items (order_id, photo_id) values (3,1);
insert into order_items (order_id, photo_id) values (4,5);
insert into order_items (order_id, photo_id) values (4,6);
insert into order_items (order_id, photo_id) values (5,5);
insert into order_items (order_id, photo_id) values (5,1);
insert into order_items (order_id, photo_id) values (5,2);
insert into order_items (order_id, photo_id) values (6,1);
insert into order_items (order_id, photo_id) values (6,2);
insert into order_items (order_id, photo_id) values (6,3);
insert into order_items (order_id, photo_id) values (6,4);
insert into order_items (order_id, photo_id) values (7,1);
insert into order_items (order_id, photo_id) values (7,2);
insert into order_items (order_id, photo_id) values (7,3);
insert into order_items (order_id, photo_id) values (7,4);
insert into order_items (order_id, photo_id) values (7,5);
insert into order_items (order_id, photo_id) values (8,1);
insert into order_items (order_id, photo_id) values (8,2);
insert into order_items (order_id, photo_id) values (8,3);
insert into order_items (order_id, photo_id) values (8,4);
insert into order_items (order_id, photo_id) values (8,5);
insert into order_items (order_id, photo_id) values (8,6);

insert into pricing_rules (qty_threshold, discount_rate) values (2, 0.1667);
insert into pricing_rules (qty_threshold, discount_rate) values (5, 0.3333);
insert into pricing_rules (qty_threshold,discount_rate) values (12, 25.0000);

create table kiosk_jobs( 
    uid int auto_increment primary key,
    create_time timestamp default now(), 
    is_pending tinyint default 1, 
    command text
);

insert into kiosk_jobs (command) values ('cp /tmp/DSC000.JPG /home/user/DSC000.JPG');

delimiter %
create event `purge_completed_jobs` ON SCHEDULE every 1 minute ON COMPLETION PRESERVE DO BEGIN delete from kiosk_jobs where is_pending = 0; end %
delimiter ;
SET GLOBAL event_scheduler = ON;

-- Stored procedures
delimiter %

CREATE PROCEDURE GetMeetId(
    IN meetName varchar(100),
    OUT meetId int)

BEGIN
    DECLARE meetCount int;
    select count(uid) into @meetCount from gym_meets where meet_name=meetName;

    IF @meetCount>0 THEN
        select uid into meetId from gym_meets where meet_name=meetName;
        ELSE
        select 0 into meetId from gym_meets where 1=1 limit 1;
        END IF;
END%

CREATE PROCEDURE GetSessionId(
    IN sessionName varchar(40),
    OUT sessionId int)

BEGIN
    DECLARE sessionCount int;
    select count(uid) into @sessioncount from sessions where session_name=sessionName;

    IF @sessionCount>0 THEN
        select uid into sessionId from sessions where session_name=sessionName;
        ELSE
        select 0 into sessionId from sessions where 1=1 limit 1;
        END IF;
END%

CREATE PROCEDURE GetEventId(
    IN eventName varchar(40),
    OUT eventId int)

BEGIN
    DECLARE eventCount int;
    select count(uid) into @eventCount from event_lookup where event_name=eventName;

    IF @eventCount>0 THEN
        select uid into eventId from event_lookup where event_name=eventName;
        ELSE
        select 0 into eventId from event_lookup where 1=1 limit 1;
        END IF;
END%

CREATE PROCEDURE GetRotationId(
    IN rotationName varchar(40),
    OUT rotationId int
)

BEGIN
    DECLARE rotationCount int;
    select count(uid) into @rotationCount from rotations where rotation_name = rotationName;
   
    IF @rotationCount>0 then
        select uid into rotationId from rotations where rotation_name = rotationName;
        ELSE
        select 0 into rotationId from rotations where 1=1 limit 1;
        END IF;
END%    
    
CREATE PROCEDURE GetMeetEventId(
    IN meetId int,
    IN sessionId int,
    IN eventId int,
    IN rotationId int,
    OUT meetEventId int)

BEGIN
    DECLARE eventCount int;
    select count(uid) into @eventCount from meet_events where session_id=sessionId and event_id=eventId and meet_id=meetId and rotation_id=rotationId;

    IF @eventCount>0 THEN
        select uid into meetEventId from meet_events where session_id=sessionId and event_id=eventId and meet_id=meetId and rotation_id=rotationId;
        ELSE
        select 0 into meetEventId from meet_events where 1=1 limit 1;
        END IF;
END%

CREATE PROCEDURE InsertNewMeet(
    IN meetName varchar(100),
    OUT meetId int)

BEGIN
    INSERT INTO gym_meets (meet_name) values (meetName);
    select LAST_INSERT_ID() into meetId from gym_meets LIMIT 1;
END%

CREATE PROCEDURE InsertNewSession(
    IN sessionName varchar(100),
    OUT sessionId int)

BEGIN
    INSERT INTO sessions (session_name) values (sessionName);
    SELECT LAST_INSERT_ID() into sessionId from sessions LIMIT 1;
END%

CREATE PROCEDURE InsertNewEvent(
    IN eventName varchar(40),
    OUT eventId int)

BEGIN
    INSERT INTO event_lookup (event_name) values (eventName);
    select LAST_INSERT_ID() into eventId from event_lookup LIMIT 1;
END%

CREATE PROCEDURE InsertNewRotation(
    IN rotationName varchar(40),
    OUT rotationId int
)

BEGIN
    INSERT INTO rotations (rotation_name) values (rotationName);
    select LAST_INSERT_ID() into rotationId from rotations limit 1;
END%

CREATE PROCEDURE InsertNewMeetEvent(
    IN meetId int,
    IN sessionId int,
    IN eventId int,
    IN rotationId int,
    OUT meetEventId int)

BEGIN
    INSERT INTO meet_events (meet_id, session_id, event_id, rotation_id) values (meetId, sessionId, eventId, rotationId);
    select LAST_INSERT_ID() into meetEventId from meet_events LIMIT 1;
END%


CREATE PROCEDURE InsertNewPhoto(
    IN meetEventId int,
    IN photoName varchar(50),
    IN photoThumbnail varchar(50),
    IN photoUri text,
    IN photoPrice decimal(10,4),
    IN orientation tinyint)

BEGIN
    INSERT INTO photos (event_id,photo_name,photo_thumbnail_name,photo_uri,photo_price,orient_portrait) VALUES (meetEventId,photoName,photoThumbnail,photoUri,photoPrice,orientation);
END%

CREATE PROCEDURE SavePhoto(
    IN meetName varchar(100),
    IN sessionName varchar(40),
    IN eventName varchar(40),
    IN rotationName varchar(40),
    IN photoName varchar(50),
    IN photoThumbnail varchar(50),
    IN photoUri text,
    IN photoPrice decimal(10,4),
    IN orientPortrait tinyint)

BEGIN
    DECLARE meetId int default 0;
    DECLARE sessionId int default 0;
    DECLARE eventId int default 0;
    DECLARE rotationId int default 0;
    DECLARE meetEventId int default 0;

    CALL GetMeetId(meetName, @meetId);
    IF @meetId=0 or @meetId is null THEN
            CALL InsertNewMeet(meetName, @meetId);
    END IF;

    CALL GetSessionId(sessionName, @sessionId);
    IF @sessionId=0 or @sessionId is null THEN
            CALL InsertNewSession(sessionName, @sessionId);
    END IF;

    CALL GetEventId(eventName, @eventId);
    IF @eventId=0 or @eventId is null THEN
            CALL InsertNewEvent(eventName, @eventId);
    END IF;

    CALL GetRotationId(rotationName, @rotationId);
    IF @rotationId=0 or @rotationId is null THEN
            CALL InsertNewRotation(rotationName, @rotationId);
    END IF;

    CALL GetMeetEventId(@meetId, @sessionId, @eventId, @rotationId, @meetEventId);
    IF @meetEventId=0 or @meetEventId is null THEN
            CALL InsertNewMeetEvent(@meetId, @sessionId, @eventId, @rotationId, @meetEventId);
    END IF;

    CALL InsertNewPhoto(@meetEventId,photoName,photoThumbnail,photoUri,photoPrice,orientPortrait);
END%

CREATE PROCEDURE DeleteRotation (
    IN meetName varchar(100),
    IN sessionName varchar(40),
    IN eventName varchar(40),
    IN rotationName varchar(40)
)
BEGIN
    DECLARE meetId int default 0;
    DECLARE sessionId int default 0;
    DECLARE eventId int default 0;
    DECLARE meetEventId int default 0;
    DECLARE rotationId int default 0;

    CALL GetMeetId(meetName, @meetId);
    CALL GetSessionId(sessionName, @sessionId);
    CALL GetEventId(eventName, @eventId);
    CALL GetRotationId(rotationName, @rotationId);

    DELETE FROM photos WHERE event_id in (
        SELECT uid FROM meet_events WHERE rotation_id = @rotationId 
            AND meet_id = @meetId AND event_id = @eventId AND session_id = @sessionId
    );
    DELETE FROM meet_events WHERE rotation_id = @rotationId 
            AND meet_id = @meetId AND event_id = @eventId AND session_id = @sessionId;
END%

CREATE PROCEDURE DeleteAllMeetData()

BEGIN
    DELETE FROM order_items;
    DELETE FROM orders;
    DELETE FROM photos;
    DELETE FROM meet_events;
    DELETE FROM event_lookup;
    DELETE FROM rotations;
    DELETE FROM sessions;
    DELETE FROM gym_meets;
END%
delimiter ;