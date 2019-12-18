<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <title><?php echo $templateParams["title"]; ?></title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
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
        <a href="index.php">
            <img id="home" src="<?php echo IMG_DIR; ?>home-icon.png" alt="home"/>
            <img id="logo" src="<?php echo IMG_DIR; ?>SeatHeat.png" alt="SeatHeat logo"/>
        </a>
        <img id="menu_icon" class="icon" src="<?php echo IMG_DIR; ?>menu.png" alt="menu" />
        <nav>
            <ul>
                <li>
                    <a href="<?php echo $templateParams["user_area_link"]; ?>">
                        <img class="<?php echo $templateParams["user_area_class"];?>"
                             src="<?php echo $templateParams["user_area_img"]?>"
                             alt="<?php echo $templateParams["user_area_alt"]; ?>"/>
                        <p class="menu_links"><?php echo $link_alt; ?></p>
                    </a>
                </li>
                <?php
                    if ($templateParams["showCart"]):
                ?>
                    <li>
                        <a href="cart.php">
                            <img class="icon" src="<?php echo IMG_DIR . "cart.png"; ?>" alt="carrello"/>
                            <p class="menu_links">Carrello</p>
                        </a>
                    </li>
                <?php
                    endif;
                    if ($templateParams["showMyEvents"]):
                ?>
                    <li>
                        <a href="my_events.php">
                            <img class="icon" src="<?php echo IMG_DIR . "calendar.png"; ?>" alt="I miei eventi"/>
                            <p class="menu_links">I miei eventi</p>
                        </a>
                    </li>
                <?php
                    endif;
                    if ($templateParams["showCreateEvent"]):
                ?>
                    <li>
                        <a href="create_event_page.php">
                            <img class="icon" src="<?php echo IMG_DIR . "new.png"; ?>" alt="Crea evento"/>
                            <p class="menu_links">Crea evento</p>
                        </a>
                    </li>
                <?php
                    endif;
                    if ($templateParams["showLogout"]):
                ?>
                    <li>
                        <a href="logout.php">
                            <img class="icon" src="<?php echo IMG_DIR . "logout.png"; ?>" alt="Logout"/>
                            <p class="menu_links">Logout</p>
                        </a>
                    </li>
                <?php
                    endif;
                ?>
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
