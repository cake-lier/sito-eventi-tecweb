<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <title><?php echo $templateParams["title"]; ?></title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
    <?php
    if (isset($templateParams["js"])):
        foreach($templateParams["js"] as $script):
    ?>
        <script src="<?php echo $script; ?>"></script>
    <?php
        endforeach;
    endif;
    ?>
</head>
<body>
    <header>
        <a href="index.php"><img id="home" src="<?php echo IMG_DIR; ?>home-icon.png" alt="home"/><img id="logo" src="<?php echo IMG_DIR; ?>SeatHeat.png" alt="SeatHeat logo"/></a>
        <img id="menu_icon" class="icon" src="<?php echo IMG_DIR; ?>menu.png" alt="menu" />
        <nav>
            <?php if (isset($_SESSION["email"])) {
                    $link_text = "./user_area.php";
                    $link_alt = "Area personale";
                    $imgTag = "<img class=\"icon\" src=\"".getProfileImage($dbh, $_SESSION["email"])."\" alt=\"Area personale\"/>";
            } else {
                    $link_text = "./login_page.php";
                    $link_alt = "Login";
                    $imgTag = "<img class=\"icon\" src=\"".IMG_DIR."/login.png\" alt=\"Login\"/>";
            }?>
            <ul>
                <li><a href="<?php echo $link_text; ?>"><?php echo $imgTag; ?><p class="menu_links"><?php echo $link_alt; ?></p></a></li>
                <?php
                    if (!isset($_SESSION["email"]) || $dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
                        echo "<li><a href=\"./cart.php\"><img class=\"icon\" src=\"".IMG_DIR."cart.png\" alt=\"carrello\"/><p class=\"menu_links\">Carrello</p></a></li>";
                    }
                ?>
                <?php if (isset($_SESSION["email"])) {
                        if (!$dbh->getUsersManager()->isAdmin($_SESSION["email"])) {
                            $_GET["user_info"] = "user_events";
                            echo '<li><a href="./user_area.php"><img class="icon" src="'.IMG_DIR.'calendar.png" alt="i miei eventi"/><p class="menu_links">I miei eventi</p></a></li>';
                        }
                        echo '<li><a href="./logout.php"><img class="icon" src="'.IMG_DIR.'logout.png" alt="logout"/><p class="menu_links">Logout</p></a></li>';
                }?>
            </ul>
        </nav>
    </header>
    <main>
    <?php
        if (isset($templateParams["name"])) {
            require $templateParams["name"];
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
            <a href="index.php">SeatHeat s.r.l.s</a>
        </div>
        <div class="info_links">
            <a href="message_admin.php">Contatta gli amministratori</a>
        </div>
    </footer>
</body>
</html>
