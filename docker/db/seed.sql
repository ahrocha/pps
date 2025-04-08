USE pps_write;

INSERT INTO users (name, cpf, email, password, type) VALUES
('Andrey da Silva', '123.456.789-01', 'andrey@example.com', 'senha123', 'user'),
('João Comprador', '987.654.321-00', 'joao@example.com', 'senha456', 'user'),
('Lojinha Legal', '00.000.000/0001-00', 'loja1@example.com', 'senha789', 'merchant'),
('Loja XPTO', '11.111.111/0001-11', 'loja2@example.com', 'senha321', 'merchant');

INSERT INTO wallets (user_id, balance) VALUES
(1, 1000.00),
(2, 500.00),
(3, 0.00),
(4, 0.00);
