CREATE DATABASE cp_jardinagem;
USE cp_jardinagem;

CREATE TABLE orcamentos(
id INT AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(100),
whatsapp VARCHAR(30),
metros INT,
servico VARCHAR(30),
valor_estimado DECIMAL(10,2),
valor_final DECIMAL(10,2),
foto VARCHAR(255),
status VARCHAR(20),
data TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
