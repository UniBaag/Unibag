<?php
    define('NAV_PATH', 'C:\xampp\htdocs\UNIBAG\src\pages\nav-bar\navbar.php');
?>

<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniBag - Admin</title>
        <link rel="stylesheet" href="../mercado/style/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sen:wght@400..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/fill/style.css">
    <script src="/src/js/mercado.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body class="dashboard-content">
    <!-- <div class="mobile-warning">
        <div class="warning-box">
            <h1>Dashboard Indisponível</h1>
            <p>A experiência completa do Dashboard está disponível apenas em telas maiores (Desktop/Tablet em modo paisagem).</p>
            <p>Por favor, acesse o sistema em um dispositivo com tela maior para continuar.</p>
            <div class="warning-icon">💻</div>
        </div>
    </div> -->
    <!-- código para instaurar o vlibras nas páginas -->
    <div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
    <div class="vw-plugin-top-wrapper"></div>
    </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>new window.VLibras.Widget('https://vlibras.gov.br/app');</script>

    <?php include NAV_PATH; ?>
    <main class="main-content">
        <header class="dashboard-header">
            <div class="logo-area">
                <img src="/src/assets/icons/icons-dash/Logo.png" alt="Logo da UNIBAG" class="logo">
            </div>
            <div class="search-area">
                <input type="text" placeholder="Produtos, mercados, administradores....">
                <button>Pesquisar</button>
            </div>
        </header>
        <h1 id="boas-vindas">Olá, Administrador!<i class="ph ph-hand-waving"></i></h1>
        <p id="subtitulo">Monitore seus clientes</p>
        <section class="delivery-list-container">
            <h2 class="list-title">
                <div class="list-title-icon"><i class="ri-store-2-line"></i></div>
                Mercados Cadastrados
            </h2>
            <div class="data-table data-table-6-cols">
                <div class="table-header">
                    <span class="header-item">Nome do Mercado</span>
                    <span class="header-item">ID</span>
                    <span class="header-item">Phone Number</span>
                    <span class="header-item">Email - Admin</span>
                    <span class="header-item">ID - Admin</span>
                    <span class="header-item status-header">Status</span>
                </div>
                
                <!-- Linhas de Dados (Data Rows) -->
                <div class="table-row">
                    <span class="row-item">Rotidão Interlagos</span>
                    <span class="row-item">1142435362</span>
                    <span class="row-item">(225) 555-0118</span>
                    <span class="row-item">jane@microsoft.com</span>
                    <span class="row-item">1111111111</span>
                    <span class="row-item status-active">Ativo</span>
                </div>
                <div class="table-row">
                    <span class="row-item">Assaí Carapicuíba</span>
                    <span class="row-item">1142435362</span>
                    <span class="row-item">(205) 555-0100</span>
                    <span class="row-item">floyd@yahoo.com</span>
                    <span class="row-item">1111111111</span>
                    <span class="row-item status-inactive">Inativo</span>
                </div>
                <div class="table-row">
                    <span class="row-item">Carrefour Piratuba</span>
                    <span class="row-item">1142435362</span>
                    <span class="row-item">(302) 555-0107</span>
                    <span class="row-item">ronald@adobe.com</span>
                    <span class="row-item">1111111111</span>
                    <span class="row-item status-inactive">Inativo</span>
                </div>
                <div class="table-row">
                    <span class="row-item">Sacolão Brasilândia</span>
                    <span class="row-item">1142435362</span>
                    <span class="row-item">(252) 555-0126</span>
                    <span class="row-item">marvin@tesla.com</span>
                    <span class="row-item">1111111111</span>
                    <span class="row-item status-active">Ativo</span>
                </div>
                <div class="table-row">
                    <span class="row-item">Max Socorro</span>
                    <span class="row-item">1142435362</span>
                    <span class="row-item">(629) 555-0129</span>
                    <span class="row-item">jerome@google.com</span>
                    <span class="row-item">1111111111</span>
                    <span class="row-item status-active">Ativo</span>
                </div>
                <!-- Mais linhas adicionadas para preencher o exemplo da imagem -->
                <div class="table-row">
                    <span class="row-item">Dia Osasco</span>
                    <span class="row-item">1142435362</span>
                    <span class="row-item">(406) 555-0120</span>
                    <span class="row-item">kathryn@microsoft.com</span>
                    <span class="row-item">1111111111</span>
                    <span class="row-item status-active">Active</span>
                </div>
                <div class="table-row">
                    <span class="row-item">Extra Barueri</span>
                    <span class="row-item">1142435362</span>
                    <span class="row-item">(208) 555-0112</span>
                    <span class="row-item">jacob@yahoo.com</span>
                    <span class="row-item">1111111111</span>
                    <span class="row-item status-active">Active</span>
                </div>
                <div class="table-row">
                    <span class="row-item">Atacadão Diadema</span>
                    <span class="row-item">1142435362</span>
                    <span class="row-item">(704) 555-0127</span>
                    <span class="row-item">kristin@facebook.com</span>
                    <span class="row-item">1111111111</span>
                    <span class="row-item status-inactive">Inactive</span>
                </div>
            </div>
        </section>
    </main>
    </main>
</body>
</html>