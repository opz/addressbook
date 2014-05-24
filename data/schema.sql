create table contacts (
  id int not null auto_increment,
  first varchar(255) not null,
  last varchar(255) not null,
  email varchar(255) not null,
  address varchar(255),
  phone varchar(255),
  notes text,
  primary key(id)
);
