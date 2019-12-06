<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $templateParams["title"]; ?></title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
    <?php
    if(isset($templateParams["js"])):
        foreach($templateParams["js"] as $script):
    ?>
        <script src="<?php echo $script; ?>"></script>
    <?php
        endforeach;
    endif;
    ?>
</head>
<body>
    <header><!-- TODO: mettere il nav dentro all'header? O raggruppare tutto in un div/section? -->
        <a href=""><img src="<?php echo IMG_DIR; ?>/SeatHeat.png" alt="SeatHeat logo"/></a><!-- TODO: il link -->
        <nav>
            <?php if (isset($_SESSION["email"])) {
                    $link_text = "./user_area.php";
                    $link_image = IMG_DIR."/login.png"; // TODO: get profile photo from db
            } else {
                    $link_text = "./register_login.php";
                    $link_image = IMG_DIR."/login.png"; 
            }?>
            <!-- TODO: put text in links for mobile version -->
            <a href="<?php echo $link_text; ?>"><img src="<?php echo $link_image; ?>" alt="login"/></a>
            <a href="./cart.php"><img src="<?php echo IMG_DIR; ?>/cart.png" alt="carrello"/></a>
            <?php if (isset($_SESSION["email"])) {
                    $_GET["user_info"] = "user_events";
                    echo '<a href="./user_area.php"><img src="'.IMG_DIR.'/calendar.png" alt="i miei eventi"/></a>';
                    echo '<a href="./logout.php"><img src="'.IMG_DIR.'/logout.png" alt="logout"/></a>';
            }?>
        </nav>
    </header>
    <main>
    <?php
    if(isset($templateParams["name"])){
        require($templateParams["name"]);
    }
    ?>
    </main>
    <footer>
        <div class="info_links"><!-- TODO: -->
            <a href="./info.php?type=chi_siamo">Chi siamo</a>
            <a href="./info.php?type=termini">Termini di servizio</a>
            <a href="./info.php?type=contatti">Contatti</a>
            <a href="./info.php?type=privacy">Privacy</a>
        </div>
        <div class="home_links">
            <a href="">SeatHeat s.r.l.s</a>
        </div>
        <div class="info_links">
            <a href="">Webmaster</a>
        </div>
    </footer>
</body>
</html>