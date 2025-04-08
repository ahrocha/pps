USE pps_write;

-- Usuários comuns
INSERT INTO users (name, cpf, email, password, type) VALUES
('Andrey da Silva', '123.456.789-01', 'andrey@example.com', '$2y$10$wqWkDwRe43grEdmtDWYq7eM/3gOyMvO6phQ9WqByh3jU4eDDpU2cS', 'usuario'),
('João Comprador', '987.654.321-00', 'joao@example.com', '$2y$10$UB7b2W/fk7q8kK/BPx.C7O9kjqZzujLmvWWrIp4sQwMknVgwh2mUO', 'usuario'),
('Maria Teste', '321.654.987-10', 'maria@example.com', '$2y$10$XqYbg6xfEefjwdIoXTPgQOD9x4Xqnm2f0f7l7SSmkk46fXY/vZTrS', 'usuario'),
('Carlos Usuário', '159.357.258-11', 'carlos@example.com', '$2y$10$L4ufhLCxgS/PwRxJvvD5Z.yTR08DLFTyKp4aThyxLDxwVaGEMdMfu', 'usuario');

-- Lojistas
INSERT INTO users (name, cpf, email, password, type) VALUES
('Lojinha Legal', '00.000.000/0001-00', 'loja1@example.com', '$2y$10$3CcS43uF3x3BFtzTWlTxI.vFTaOkoA/gI5Qe.CzA0crO8b/kK5Bbi', 'lojista'),
('Loja XPTO', '11.111.111/0001-11', 'loja2@example.com', '$2y$10$k9jzvUMW5HqT1zN56giZG.mQMIj3U5YulIm8CgS7soKHLdUlFlIB6', 'lojista'),
('Mercado Fiel', '22.222.222/0001-22', 'mercado@example.com', '$2y$10$VtJoROhjqlOaESKr5RmzduJrfO6G9IksFMqxetQo8kXIPzmuYGiQW', 'lojista'),
('Padaria Boa Massa', '33.333.333/0001-33', 'padaria@example.com', '$2y$10$9qQZIzYDGfEakxfbnKsmieMpx3jZ89/s5xYjQSn5v9Sz8vklGcLde', 'lojista');

-- Carteiras
INSERT INTO wallets (user_id, balance) VALUES
(1, 1000.00),
(2, 500.00),
(3, 750.00),
(4, 300.00),
(5, 0.00),
(6, 0.00),
(7, 0.00),
(8, 0.00);
