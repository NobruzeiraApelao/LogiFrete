
<?php
require_once __DIR__ . '/includes/db.php';

$sql = "
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    peso DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    cep VARCHAR(10),
    rua VARCHAR(150),
    numero VARCHAR(20),
    cidade VARCHAR(100),
    uf CHAR(2),
    tipo ENUM('Origem','Destino') NOT NULL
);

CREATE TABLE IF NOT EXISTS transportadoras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    cnpj VARCHAR(18),
    telefone VARCHAR(30)
);

CREATE TABLE IF NOT EXISTS fretes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    origem_id INT NOT NULL,
    destino_id INT NOT NULL,
    transportadora_id INT NOT NULL,
    data_frete DATE NOT NULL,
    status VARCHAR(30) DEFAULT 'Pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (origem_id) REFERENCES enderecos(id),
    FOREIGN KEY (destino_id) REFERENCES enderecos(id),
    FOREIGN KEY (transportadora_id) REFERENCES transportadoras(id)
);
";

try {
    $pdo->exec($sql);
    echo "Tabelas do LogiFrete criadas com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao criar tabelas: " . $e->getMessage();
}
?>
