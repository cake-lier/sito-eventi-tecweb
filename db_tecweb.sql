create database my_seatheat;
use my_seatheat;

create table eventCategories (
    id int not null auto_increment,
    name varchar(30) not null,
    constraint ID_EVENT_CATEGORY primary key (id)
);

create table seatCategories (
    eventId int not null,
    id int not null auto_increment,
    name varchar(30) not null,
    price decimal(13,2) not null,
    seats int not null,
    constraint ID_SEAT_CATEGORY_ID primary key (eventId, id)
);

create table events (
    id int not null auto_increment,
    name varchar(30) not null,
    place varchar(30) not null,
    dateTime datetime not null,
    description mediumtext not null,
    site varchar(30),
    promoterEmail varchar(30),
    constraint ID_EVENT primary key (id)
);

create table notifications (
    id int not null auto_increment,
    message mediumtext not null,
    constraint ID_NOTIFICATION primary key (id)
);

create table usersNotifications (
    notificationId int not null,
    email varchar(30) not null,
    dateTime datetime not null,
    visualized boolean not null,
    constraint ID_USER_NOTIFICATION primary key (notificationId, email, dateTime)
);

create table purchases (
    eventId int not null,
    seatId int not null,
    customerEmail varchar(30) not null,
    amount int not null,
    constraint ID_PURCHASE primary key (eventId, seatId, customerEmail)
);

create table carts (
    eventId int not null,
    seatId int not null,
    customerEmail varchar(30) not null,
    amount int not null,
    constraint ID_CART primary key (eventId, seatId, customerEmail)
);

create table eventsToCategories (
    categoryId int not null,
    eventId int not null,
    constraint ID_ASSOCIATION primary key (categoryId, eventId)
);

create table users (
    email varchar(30) not null,
    password varchar(255) not null,
    profilePhoto mediumblob not null,
    type enum('c', 'p', 'a') not null,
    constraint ID_USER primary key (email)
);

create table administrators (
    email varchar(30) not null,
    constraint FK_IS_ADMINISTRATOR_ID primary key (email)
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
    constraint FK_IS_CUSTOMER_ID primary key (email)
);

create table promoters (
    email varchar(30) not null,
    organizationName varchar(30) not null,
    VATid numeric(11) not null,
    website varchar(30),
    constraint ID_PROMOTER unique (VATid),
    constraint FK_IS_PROMOTER_ID primary key (email)
);

alter table seatCategories add constraint FK_OFFER
     foreign key (eventId)
     references eventId (id);

-- alter table events add constraint ID_EVENT_CHK
--     check(exists(select * from seatCategories
--                  where seatCategories.eventId = id)); 

alter table events add constraint FK_PROMOTER
    foreign key (promoterEmail)
    references promoters (email);

alter table usersNotifications add constraint FK_NOTIFICATION
    foreign key (notificationId)
    references notifications (id);

alter table usersNotifications add constraint FK_USER
    foreign key (email)
    references users (email);

alter table purchases add constraint FK_EVENT
     foreign key (eventId, seatId)
     references seatCategories (eventId, id);

alter table purchases add constraint FK_CUSTOMER
     foreign key (customerEmail)
     references customers (email);

alter table carts add constraint FK_EVENT
     foreign key (eventId, seatId)
     references seatCategories (eventId, id);

alter table carts add constraint FK_CUSTOMER
     foreign key (customerEmail)
     references customers (email);

alter table eventsToCategories add constraint FK_CATEGORY
    foreign key (categoryId)
    references eventCategories (id);

alter table eventsToCategories add constraint FK_EVENT
     foreign key (eventId)
     references events (id);

alter table administrators add constraint FK_IS_ADMINISTRATOR_FK
    foreign key (email)
    references users (email);

alter table customers add constraint FK_IS_CUSTOMER_FK
    foreign key (email)
    references users (email);

alter table promoters add constraint FK_IS_PROMOTER_FK
    foreign key (email)
    references users (email);
