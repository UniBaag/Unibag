USE defaultdb;

-- TABELA: CLIENTE USUÁRIO
CREATE TABLE ClienteUsuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(200),
    cpf VARCHAR(14) UNIQUE NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABELA: CLIENTE MERCADO
CREATE TABLE ClienteMercado (
    id_mercado INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(18) UNIQUE NOT NULL,
    endereco VARCHAR(200),
    telefone VARCHAR(20) UNIQUE NOT NULL,
    tipo_mercado ENUM('atacado', 'varejo') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(100) NOT NULL
);

-- TABELA: ADMINISTRADOR MERCADO
CREATE TABLE AdministradorMercado (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(100) NOT NULL,
    id_mercado INT NOT NULL,
    FOREIGN KEY (id_mercado) REFERENCES ClienteMercado(id_mercado)
        ON DELETE CASCADE
);

-- TABELA: CLIENTE ENTREGADOR
CREATE TABLE ClienteEntregador (
    id_entregador INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    placa_veiculo VARCHAR(10),
    tipo_veiculo VARCHAR(50),
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(100) NOT NULL
);

-- TABELA: CORREDOR
CREATE TABLE Corredor (
    id_corredor INT AUTO_INCREMENT PRIMARY KEY,
    categoria ENUM(
        'hortifruti',
        'padaria',
        'açougue',
        'laticinios',
        'bebidas',
        'mercearia',
        'limpeza',
        'higienepessoal',
        'congelados'
    ) NOT NULL,
    id_mercado INT NOT NULL,
    FOREIGN KEY (id_mercado) REFERENCES ClienteMercado(id_mercado)
        ON DELETE CASCADE
);

-- TABELA: PRODUTO
CREATE TABLE Produto (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT DEFAULT 0,
    id_mercado INT NOT NULL,
    id_corredor INT NOT NULL,
    FOREIGN KEY (id_mercado) REFERENCES ClienteMercado(id_mercado)
        ON DELETE CASCADE,
    FOREIGN KEY (id_corredor) REFERENCES Corredor(id_corredor)
        ON DELETE CASCADE
);

-- TABELA: PEDIDO
CREATE TABLE Pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    valor_total DECIMAL(10,2),
    status_pedido ENUM('em preparo', 'pronto', 'a caminho', 'entregue') DEFAULT 'em preparo',
    metodo_pagamento ENUM('pix', 'cartão', 'dinheiro', 'outro') DEFAULT 'pix',
    FOREIGN KEY (id_usuario) REFERENCES ClienteUsuario(id_usuario)
        ON DELETE CASCADE
);

-- TABELA: SUBPEDIDO
CREATE TABLE SubPedido (
    id_subpedido INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_mercado INT NOT NULL,
    valor_subtotal DECIMAL(10,2),
    status_subpedido ENUM('em preparo', 'pronto', 'a caminho', 'entregue') DEFAULT 'em preparo',
    data_subpedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido)
        ON DELETE CASCADE,
    FOREIGN KEY (id_mercado) REFERENCES ClienteMercado(id_mercado)
        ON DELETE CASCADE
);

-- TABELA: ITEM PEDIDO
CREATE TABLE ItemPedido (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    id_subpedido INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2),
    FOREIGN KEY (id_subpedido) REFERENCES SubPedido(id_subpedido)
        ON DELETE CASCADE,
    FOREIGN KEY (id_produto) REFERENCES Produto(id_produto)
        ON DELETE CASCADE
);

-- TABELA: ENTREGA
CREATE TABLE Entrega (
    id_entrega INT AUTO_INCREMENT PRIMARY KEY,
    id_subpedido INT NOT NULL,
    id_entregador INT,
    status_entrega ENUM('aguardando retirada', 'em trânsito', 'entregue', 'cancelada') DEFAULT 'aguardando retirada',
    data_envio DATETIME,
    data_entrega DATETIME,
    FOREIGN KEY (id_subpedido) REFERENCES SubPedido(id_subpedido)
        ON DELETE CASCADE,
    FOREIGN KEY (id_entregador) REFERENCES ClienteEntregador(id_entregador)
        ON DELETE SET NULL
);

-- TABELA: RECLAMAÇÃO
CREATE TABLE Reclamacao (
    id_reclamacao INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_mercado INT NOT NULL,
    descricao TEXT NOT NULL,
    data_reclamacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    resposta TEXT,
    FOREIGN KEY (id_usuario) REFERENCES ClienteUsuario(id_usuario)
        ON DELETE CASCADE,
    FOREIGN KEY (id_mercado) REFERENCES ClienteMercado(id_mercado)
        ON DELETE CASCADE
);

-- TABELA: NOTIFICAÇÃO
CREATE TABLE Notificacao (
    id_notificacao INT AUTO_INCREMENT PRIMARY KEY,
    id_mercado INT NOT NULL,
    tipo ENUM('produto indisponível', 'pedido atualizado', 'outro') NOT NULL,
    mensagem TEXT NOT NULL,
    data_notificacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mercado) REFERENCES ClienteMercado(id_mercado)
        ON DELETE CASCADE
);

-- TABELA: PAGAMENTO (SIMULADO)
CREATE TABLE Pagamento (
    id_pagamento INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    metodo_pagamento ENUM('pix', 'cartão', 'dinheiro', 'outro') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    status_pagamento ENUM('pendente', 'confirmado', 'cancelado') DEFAULT 'pendente',
    data_pagamento DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido)
        ON DELETE CASCADE
);
