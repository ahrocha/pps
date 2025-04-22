CREATE DATABASE IF NOT EXISTS pps_write;
USE pps_write;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    type ENUM('usuario', 'lojista') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE wallets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    value DECIMAL(10,2) NOT NULL,
    payer_id INT NOT NULL,
    payee_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payer_id) REFERENCES users(id),
    FOREIGN KEY (payee_id) REFERENCES users(id)
);

CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
