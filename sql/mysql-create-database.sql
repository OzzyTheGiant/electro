CREATE DATABASE IF NOT EXISTS Electro;

CREATE TABLE IF NOT EXISTS Bills (
	ID INT PRIMARY KEY AUTO_INCREMENT,
	PaymentAmount DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
	PaymentDate DATE NOT NULL
);