drop table if exists contacts;
drop table if exists contact_groups;
drop table if exists contact_group_jct;
drop table if exists users;

create table contacts (
  id int not null auto_increment,
  uid int not null,
  first varchar(255) not null,
  last varchar(255) not null,
  email varchar(255) not null,
  address varchar(255),
  phone varchar(255),
  notes text,
  created timestamp not null default current_timestamp,
  primary key(id)
);

create table contact_groups (
  id int not null auto_increment,
  uid int not null,
  name varchar(255) not null,
  created timestamp not null default current_timestamp,
  primary key(id)
);

create table contact_group_jct (
  id int not null auto_increment,
  cid int not null,
  gid int not null,
  created timestamp not null default current_timestamp,
  primary key(id)
);

create table users (
  id int not null auto_increment,
  email varchar(255) not null,
  /* SHA512 hash */
  password char(128) not null,
  primary key(id)
);
