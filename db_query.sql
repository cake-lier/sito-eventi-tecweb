-- add customer user
INSERT INTO users(email, password, profilePhoto, type) 
VALUES (?, ?, ?, ?)
;
INSERT INTO customers(email, billingAddress, birthDate, birthplace, name, surname, username) 
VALUES (?, ?, ?, ?, ?, ?, ?)
;
UPDATE customers
SET telephone = ?
WHERE email = ?
;
UPDATE customers
SET currentAddress = ?
WHERE email = ?

-- add promoter user
INSERT INTO users(email, password, profilePhoto, type) 
VALUES (?, ?, ?, ?)
;
INSERT INTO promoters(email, organizationName, VATid)
VALUES (?, ?, ?)
;
UPDATE promoters
SET website = ?
WHERE email = ?

-- login
SELECT * FROM users WHERE email = ? AND password = ?

-- show short customer profile
SELECT username, name, surname, birthDate, birthplace, profilePhoto
FROM users u, customers c 
WHERE u.email = ? 
    AND u.email = c.email

-- show long customer profile
SELECT username, name, surname, birthDate, birthplace, profilePhoto, currentAddress, billingAddress, telephone, email
FROM users u, customers c
WHERE u.email = ?
    AND u.email = c.email

-- show user type
SELECT type
FROM users
WHERE email = ?

-- show short promoter profile
SELECT email, profilePhoto, organizationName, website
FROM users u, promoters p 
WHERE u.email = ? 
    AND u.email = p.email

-- show long promoter profile
SELECT email, profilePhoto, organizationName, website, VATid
FROM users u, promoters p 
WHERE u.email = ? 
    AND u.email = p.email

-- modify password
UPDATE users SET password = ? WHERE email = ?

-- modify profile photo
UPDATE users SET profilePhoto = ? WHERE email = ?

-- modify user data
UPDATE users
SET profilePhoto = ?, password = ?
WHERE email = ?

-- modify customer data
UPDATE customers
SET username = ?, name = ?, surname = ?, birthDate = ?, birthplace = ?, currentAddress = ?, billingAddress = ?, telephone = ?
WHERE email = ?

-- modify promoter data
UPDATE promoters
SET website = ?
WHERE email = ?

-- show events
SELECT eventId
FROM events 
WHERE dateTime >= ?

-- select possible event places
SELECT DISTINCT place 
FROM events

-- show events filtered by place
SELECT eventId
FROM events 
WHERE place = ?

-- show events filtered by date
SELECT eventId
FROM events 
WHERE dateTime = ?

-- show still free events 
SELECT eventId 
FROM events e 
WHERE seats > (SELECT COUNT(customerEmail) 
                FROM subscriptions 
                WHERE eventId = e.id)

-- create event
INSERT INTO events(name, place, dateTime, seats, description, site, promoterEmail)
VALUES (?, ?, ?, ?, ?, ?, ?)

-- show subscribers
SELECT username, name, surname, profilePhoto
FROM subscriptions s, customers c, users u
WHERE s.eventId = ?
    AND s.customerEmail = c.email
    AND c.email = u.email

-- show event
SELECT e.id, e.name, e.place, e.dateTime, e.description, e.site, p.organizationName, e.seats - COUNT(*) as freeSeats
FROM events e, subscriptions s, promoters p
WHERE e.id = ?
    AND e.id = s.eventId
    AND e.promoterEmail = p.email
GROUP BY e.id

-- show my events
SELECT eventId
FROM subscriptions
WHERE customerEmail = ?

-- put a ticket in the cart
INSERT INTO carts(eventId, customerEmail)
SELECT ?, ?
FROM events
WHERE id IN (SELECT eventId 
                FROM events e 
                WHERE seats > (SELECT COUNT(customerEmail) 
                                FROM subscriptions 
                                WHERE eventId = e.id))

-- buy a ticket
INSERT INTO subscriptions(eventId, customerEmail)
SELECT ?, ?
FROM events
WHERE id IN (SELECT eventId 
                FROM events e 
                WHERE seats > (SELECT COUNT(customerEmail) 
                                FROM subscriptions 
                                WHERE eventId = e.id))
;
DELETE FROM carts
WHERE eventId = ?
    AND customerEmail = ?

-- new type of notification
INSERT INTO notifications(message)
VALUES (?)

-- send notification
INSERT INTO usersNotifications(email, dateTime, notificationId, visualized)
SELECT customerEmail, ?, ?, false
FROM subscriptions
WHERE eventId = ?

-- get user notifications
SELECT dateTime, visualized, message
FROM usersNotifications, notifications
WHERE notificationId = id
    AND email = ?

-- delete type of notification
DELETE FROM notifications
WHERE id NOT IN (SELECT notificationId
                 FROM usersNotifications)

-- delete user notification
DELETE FROM usersNotifications
WHERE notificationId = ? AND email = ? AND dateTime = ?

-- toggle view notification
UPDATE usersNotifications
SET visualized = not visualized
WHERE email = ?
    AND notificationId = ?
    AND dateTime = ?

-- remove user
DELETE FROM users
WHERE email = ?
