create database my_seatheat;
use my_seatheat;

create table carts (
    eventId int not null,
    customerEmail varchar(30) not null,
    constraint ID_CART primary key (eventId, customerEmail)
);

create table events (
    id int not null auto_increment,
    name varchar(30) not null,
    place varchar(30) not null,
    dateTime datetime not null,
    seats int not null,
    description varchar(280) not null,
    site varchar(30),
    promoterEmail int,
    constraint ID_EVENT primary key (id)
);

create table subscriptions (
    eventId int not null,
    customerEmail varchar(30) not null,
    constraint ID_SUBSCRIPTION primary key (eventId, customerEmail)
);

create table usersNotifications (
    notificationId int not null,
    email varchar(30) not null,
    dateTime datetime not null,
    visualized boolean not null,
    constraint ID_USER_NOTIFICATION primary key (notificationId, email, dateTime)
);

create table notifications (
    id int not null auto_increment,
    message varchar(280) not null,
    constraint ID_NOTIFICATION primary key (id)
);

create table users (
    email varchar(30) not null,
    password varchar(30) not null,
    profilePhoto varchar(30) not null,
    type enum('c', 'p', 'a') not null,
    constraint ID_USER primary key (email)
);

create table administrators (
    email varchar(30) not null,
    constraint ID_ADMINISTRATOR primary key (email)
);

create table customers (
    email varchar(30) not null,
    username varchar(30) not null,
    name varchar(30) not null,
    surname varchar(30) not null,
    birthDate date not null,
    birthplace varchar(30) not null,
    currentAddress varchar(30),
    billingAddress varchar(30) not null,
    telephone numeric(11),
    constraint ID_CUSTOMER primary key (email)
);

create table promoters (
    email varchar(30) not null,
    organizationName varchar(30) not null,
    VATid numeric(11) not null,
    website varchar(30),
    constraint ID_PROMOTER unique (VATid),
    constraint ID_PROMOTER primary key (email)
);

alter table carts add constraint FKeventId
    foreign key (eventId)
    references events (id);

alter table carts add constraint FKcustomerEmail
    foreign key (customerEmail)
    references customers (email);

alter table events add constraint FKorganizes
    foreign key (promoterEmail)
    references promoters (email);

alter table subscriptions add constraint FKcustomerEmail
    foreign key (customerEmail)
    references customers (email);

alter table subscriptions add constraint FKeventId
    foreign key (eventId)
    references events (id);

alter table usersNotifications add constraint FKreceives
    foreign key (email)
    references users (email);

alter table usersNotifications add constraint FKowns
    foreign key (notificationId)
    references notifications (id);

alter table customers add constraint FKemail
    foreign key (email)
    references users (email);

alter table promoters add constraint FKemail
    foreign key (email)
    references users (email);

alter table administrators add constraint FKemail
    foreign key (email)
    references users (email);
