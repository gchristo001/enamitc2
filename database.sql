

CREATE TABLE items (
  itemid int unsigned not null auto_increment primary key,
  name char (50) not null,
  supplier char (30) not null,
  category char (30) not null,
  color char (30) not null,
  size char (30) not null,
  weight float unsigned not null,
  code float unsigned not null,
  price float unsigned not null,
  image blob
)


CREATE TABLE goldprice (
  goldprice float unsigned not null
)



CREATE TABLE orders (
  orderid int unsigned not null auto_increment primary key,
  userid int unsigned not null,
  itemid varchar(20),
  date date not null
)


CREATE TABLE `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `address` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1


CREATE TABLE ticket_redeem (
  id int unsigned not null auto_increment primary key,
  date datetime,
  userid int unsigned not null,
  prize varchar (10) not null,
  status varchar (10) not null
)