<nav>
    <ul>
        <li class="selected" id="notifications_button">Notifiche</li>
        <li id="user_area_button">Dati personali</li>
        <li id="change_password_button">Cambia password</li>
        <li id="change_data_button">Modifica dati</li>
        <?php if (!$dbh->getUsersManager()->isAdmin($_SESSION["email"])): ?>
            <li id="events_button">I miei eventi</li>
        <?php endif; ?>
        <li id="delete_account_button">Chiudi account</li>
    </ul>
</nav>
<section>
    <?php
        require_once "utils/functions.php";
        try {
            $notifications = $dbh->getNotificationsManager()->getLoggedUserNotifications();
            if (count($notifications) > 0) {
                foreach ($notifications as $not) {
                    if ($not["visualized"]) {
                        $html = "<section class=\"visualized\" id=\"not_".$not["notificationId"]."\">";
                    } else {
                        $html = "<section id=\"not_".$not["notificationId"]."\">";
                    }
                    $html = $html."<p>".convertDateTimeToLocale($not["dateTime"])."</p><p>".$not["message"]."</p></section>";
                    echo $html;
                }
            } else {
                echo "<p>Non ci sono notifiche!</p>";
            }
        } catch (\Exception $e) {
            echo '<p>Qualcosa é andato storto! Riprova!';
        }
    ?>
</section>