-- Αρχικοποίηση βάσης δεδομένων με την παρακάτω εντολή:
-- mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS vehicles_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vehicles_db;

CREATE TABLE IF NOT EXISTS vehicles (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model_name   VARCHAR(255) NOT NULL,
    type_id      INT UNSIGNED NOT NULL,
    vehicle_type VARCHAR(100),
    doors        TINYINT UNSIGNED,
    transmission ENUM('manual', 'automatic'),
    fuel         ENUM('petrol', 'diesel', 'hybrid', 'electric'),
    price        DECIMAL(10, 2),
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Δοκιμαστικά δεδομένα
INSERT INTO vehicles (model_name, type_id, vehicle_type, doors, transmission, fuel, price) VALUES
    ('Fiat Panda',        2, 'car',  4, 'manual',    'petrol',   90.00),
    ('Toyota Yaris',      2, 'car',  4, 'automatic', 'hybrid',  110.00),
    ('VW Golf',           2, 'car',  4, 'manual',    'petrol',  130.00),
    ('Ford Explorer',     3, 'suv',  5, 'automatic', 'diesel',  180.00),
    ('Tesla Model 3',     4, 'car',  4, 'automatic', 'electric',200.00),
    ('Renault Trafic',    5, 'van',  3, 'manual',    'diesel',  150.00),
    ('Hyundai i10',       1, 'car',  4, 'manual',    'petrol',   70.00),
    ('BMW X5',            4, 'suv',  5, 'automatic', 'diesel',  250.00);
