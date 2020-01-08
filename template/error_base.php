<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <title><?php echo $templateParams["title"]; ?></title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
    <div id="wrapper">
        <header>
            <a href="index.php">
                <img id="home" src="<?php echo IMG_DIR; ?>home-icon.png" alt="home"/>
                <img id="logo" src="<?php echo IMG_DIR; ?>SeatHeat.png" alt="SeatHeat logo"/>
            </a>
        </header>
        <main>
        <?php
            if (isset($templateParams["name"])) {
                require $templateParams["name"];
            }
        ?>
        </main>
    </div>
    <footer>
            <div class="info_links">
                <a href="info.php?type=chi_siamo">Chi siamo</a>
                <a href="info.php?type=termini">Termini di servizio</a>
                <a href="info.php?type=contatti">Contatti</a>
                <a href="info.php?type=privacy">Privacy</a>
            </div>
            <div class="home_links">
                <a href="index.php">SeatHeat S.r.l.s</a>
            </div>
            <div class="info_links">
                <a href="message_admin.php">Contatta gli admin</a>
            </div>
        </footer>
</body>
</html>