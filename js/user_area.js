function organizeUserNotifications(data) {
    const mainSection = $("main");
    if (data["result"] === false) {
        mainSection.append(
            $("<section>").append(
                $("<p>",
                  {
                       text: "Si è verificato un errore nel caricare le notifiche, si prega di riprovare più tardi"
                  })));
        return;
    }
    notifications = data.notifications;
    if (notifications.length > 0) {
        Object.values(notifications).forEach(notification => {
            const dateTime = new Date(notification.dateTime);
            const dateString = dateTime.toLocaleDateString("it-IT", {
                day: "numeric",
                month: "long",
                year: "numeric",
            }) + " ore " + dateTime.toLocaleTimeString("it-IT", {
                hour: "2-digit",
                minute: "2-digit"
            });
            mainSection.append(
                $("<section>", {id: notification.notificationId})
                    .addClass("notification")
                    .addClass((_i, c) => {
                        return notification.visualized === 1 ? c + " visualized" : c;
                    })
                    .append($("<p>", {text: dateString}), 
                            $("<p>", {text: notification.message}),
                            $("<label>", {for: "check_" + notification.notificationId, text: "Visualizzata"}),
                            $("<input>", {
                                id: "check_" + notification.notificationId, 
                                type: "checkbox",
                                change: () => toggleNotificationView(notification.notificationId, notification.dateTime)
                            })
                                .prop("checked", notification.visualized === 1),
                            $("<a>", {
                                class: "button_no_image",
                                text: "Elimina",
                                click: () => deleteNotification(notification.notificationId, notification.dateTime)
                            })));
        });
    } else {
        mainSection.append(
            $("<section>").append(
                $("<p>",
                  {
                      text: "Non ci sono notifiche!"
                  })));
    }
}

function toggleNotificationView(notificationId, notificationDateTime) {
    $.get("toggle_view_notification.php?id=" + notificationId + "&dateTime=" + notificationDateTime, data => {
        if (data.result !== true) {
            e.preventDefault();
        } else {
            $("#" + notificationId).toggleClass("visualized");
        }
    });
}

function deleteNotification(notificationId, notificationDateTime) {
    if (confirm("Sicuro di voler cancellare questa notifica?")) {
        $.get("delete_notification.php?id=" + notificationId + "&dateTime=" + notificationDateTime, data => {
            if (data.result === true) {
                $("#" + notificationId).remove();
            }
        });
    }
}

function organizeUserData(data) {
    const mainSection = $("main");
    if (data.result !== false) {
        userData = data.userData;
        // this part is common to every type of user
        const photo = $("<img>", {src: userData.profilePhoto, id: "profile_photo_img"});
        mainSection.append($("<section>").append(photo,
                                                 $("<p>", {text: userData.email})
                                                     .prepend($("<strong>", {text: "Email: "}))));
        // It's a customer
        if ("username" in userData) {
            const dateString = new Date(userData.birthDate).toLocaleDateString("it-IT", {
                day: "numeric",
                month: "long",
                year: "numeric",
            });
            mainSection.append($("<section>").append($("<h2>", {text: "Dati personali"}),
                                                     $("<p>", {text: userData.name})
                                                         .prepend($("<strong>", {text: "Nome: "})),
                                                     $("<p>", {text: userData.surname})
                                                         .prepend($("<strong>", {text: "Cognome: "})),
                                                     $("<p>", {text: dateString})
                                                         .prepend($("<strong>", {text: "Data di nascita: "})),
                                                     $("<p>", {text: userData.birthplace})
                                                         .prepend($("<strong>", {text: "Luogo di nascita: "}))),
                               $("<section>").append($("<h2>", {text: "Contatti"}),
                                                     $("<p>", {text: userData.billingAddress})
                                                         .prepend($("<strong>", {text: "Indirizzo di fatturazione: "})),
                                                     userData.currentAddress !== null
                                                         ? $("<p>", {text: userData.currentAddress})
                                                               .prepend($("<strong>", {text: "Indirizzo corrente: "}))
                                                         : null,
                                                     userData.telephone !== null
                                                         ? $("<p>", {text: userData.telephone})
                                                               .prepend($("<strong>", {text: "Telefono: "}))
                                                         : null));
            photo.after($("<p>", {class: "username", text: userData.username}));
        } else if ("organizationName" in userData) {
            // It's a promoter
            mainSection.append($("<section>").append($("<h2>", {text: "Dati organizzazione"}),
                                                     $("<p>", {text: userData.organizationName})
                                                         .prepend($("<strong>", {text: "Nome organizzazione: "})),
                                                     $("<p>", {text: userData.VATid})
                                                         .prepend($("<strong>", {text: "VATid: "})),
                                                     userData.website !== null
                                                        ? $("<p>", {text: userData.website})
                                                              .prepend($("<strong>", {text: "Sito: "}))
                                                        : null));
        }
    } else {
        mainSection.append($("<p>", {text: "Si è verificato un errore, impossibile visualizzare i dati dell'utente"}));
    }
}

function setChangePasswordForm() {
    $("main").append(
        $("<section>").append(
            $("<form>").append(
                $("<label>", {text: "Password attuale: ", for: "old_password"}),
                $("<input>", {type: "password", name: "old_password", id: "old_password"})
                    .prop("required", true),
                $("<label>", {text: "Nuova password: ", for: "new_password"}),
                $("<input>", {type: "password", name: "new_password", id: "new_password"})
                    .prop("required", true), 
                $("<label>", {text: "Conferma password: ", for: "new_password_repeat"}),
                $("<input>", {type: "password", name: "new_password_repeat", id: "new_password_repeat"})
                    .prop("required", true),
                $("<input>", {type: "submit", value: "Cambia password", class: "button_no_image"}))
                    .submit(function(e) {
                        e.preventDefault();
                        $.post("change_password.php", $(this).serialize(), data => {
                            $("form > p").remove();
                            $(this).prepend($("<p>", {text: data.resultMessage}));
                        });
                    })));
}

function setChangeDataForm(data) {
    const mainSection = $("<section>").appendTo($("main"));
    if (data.result !== false) {
        userData = data.userData;
        // this part is common to every type of user
        const form = $("<form>", {enctype: "multipart/form-data"})
                         .append($("<fieldset>")
                                     .append($("<img>", {src: userData.profilePhoto, id: "profile_photo_img"}),
                                             $("<label>", {for: "profile_photo", text: "Nuova foto profilo: "}),
                                             $("<input>", {type: "file", name: "profile_photo", id: "profile_photo"})));
        mainSection.append(form);
        // It's a customer
        if ("username" in userData) {
            form.append($("<fieldset>")
                            .append($("<h2>", {text: "Dati personali"}),
                                    $("<label>", {for: "username", text: "Username: "}),
                                    $("<input>", {id: "username", name: "username", value: userData.username}),
                                    $("<label>", {for: "name", text: "Nome: "}),
                                    $("<input>", {id: "name", name: "name", value: userData.name}),
                                    $("<label>", {for: "surname", text: "Cognome: "}),
                                    $("<input>", {id: "surname", name: "surname", value: userData.surname}),
                                    $("<label>", {for: "birthdate", text: "Data di nascita: "}),
                                    $("<input>", {id: "birthdate", name: "birthDate", value: userData.birthDate, type: "date"}),
                                    $("<label>", {for: "birthplace", text: "Luogo di nascita: "}),
                                    $("<input>", {id: "birthplace", name: "birthplace", value: userData.birthplace})),
                        $("<fieldset>")
                            .append($("<h2>", {text: "Contatti"}),
                                    $("<label>", {for: "billing_add", text: "Indirizzo di fatturazione: "}),
                                    $("<input>", {id: "billing_add", name: "billingAddress", value: userData.billingAddress}),
                                    $("<label>", {for: "current_add", text: "Indirizzo corrente: "}),
                                    $("<input>", {
                                        id: "current_add",
                                        name: "currentAddress",
                                        value: userData.currentAddress !== null ? userData.currentAddress : ""
                                    }),
                                    $("<label>", {for: "telephone", text: "Telefono: "}),
                                    $("<input>", {
                                        id: "telephone",
                                        name: "telephone",
                                        value: userData.telephone !== null ? userData.telephone : ""
                                    })));
        // It's a promoter
        } else if ("organizationName" in userData) {
            form.append($("<fieldset>")
                            .append($("<h2>", {text: "Dati organizzazione"}),
                                    $("<label>", {for: "website", text: "Sito internet: "}),
                                    $("<input>", {
                                        id: "website",
                                        type: "text",
                                        name: "website",
                                        value: userData.website !== null ? userData.website : ""
                                    })));
        }
        form.append($("<input>", {type: "submit", value: "Modifica dati", class: "button_no_image"}))
            .submit(function(e) {
                e.preventDefault();
                $.post({
                    url: "change_user_data.php",
                    data: new FormData($("form")[0]), 
                    processData: false,
                    contentType: false,
                    success: data => {
                        $("form > p").remove();
                        $(this).prepend($("<p>", {text: data.resultMessage}));
                        $.get("get_user_data.php", data => {
                            if (data.result !== false) {
                                userData = data.userData;
                                $("#profile_photo_img").attr("src", userData.profilePhoto);
                                $(".profile_icon").attr("src", userData.profilePhoto);
                            }
                        });
                    }
                });
            });
    } else {
        mainSection.append($("<p>", {text: "Si è verificato un errore, impossibile visualizzare i dati dell'utente"}));
    }
}

function showDeleteAccountForm() {
    $("<section>")
        .appendTo($("main"))
        .append($("<form>")
            .append($("<p>", {text: "Sei sicuro di voler cancellare il tuo account? Questa azione non é reversibile!"}),
                    $("<label>", {text: "Password attuale: ", for: "password"}),
                    $("<input>", {type: "password", name: "password", id: "password"})
                        .prop("required", true),
                    $("<input>",
                      {
                          value: "Elimina",
                          type: "submit",
                          class: "button_no_image",
                          submit: function(e) {
                              e.preventDefault();
                              $.post("delete_account.php", $(this).serialize(), data => {
                                  if (data.location !== "") {
                                      window.location.href = data.location;
                                  } else {
                                      $("form + p").remove();
                                      $("main").append($("<p>", {text : data.result}))
                                  }
                              });
                          }
                      })));
}

function hideMenu() {
    if ($(window).width() < 768) {
        $("#user_area_menu > ul").slideUp();
        $("#user_area_menu > a > img").attr("alt", "Apri menu area utente");
    }
}

$(() => {
    if ($(window).width() < 768) {
        $("#user_area_menu > ul").hide();
    }
    $.get("get_user_notifications.php", organizeUserNotifications);
    $("#notifications_button").click(() => {
        $(".selected").removeClass("selected");
        $("#notifications_button").addClass("selected");
        $("main > section:nth-child(n+2)").remove();
        $.get("get_user_notifications.php", organizeUserNotifications);
        hideMenu();
    });
    $("#user_area_button").click(() => {
        $(".selected").removeClass("selected");
        $("#user_area_button").addClass("selected");
        $("main > section:nth-child(n+2)").remove();
        $.get("get_user_data.php", organizeUserData);
        hideMenu();
    });
    $("#change_password_button").click(() => {
        $(".selected").removeClass("selected");
        $("#change_password_button").addClass("selected");
        $("main > section:nth-child(n+2)").remove();
        setChangePasswordForm();
        hideMenu();
    });
    $("#change_data_button").click(() => {
        $(".selected").removeClass("selected");
        $("#change_data_button").addClass("selected");
        $("main > section:nth-child(n+2)").remove();
        $.get("get_user_data.php", setChangeDataForm);
        hideMenu();
    });
    $("#events_button").click(() => {
        window.location.href = "my_events.php";
    });
    $("#delete_account_button").click(() => {
        $(".selected").removeClass("selected");
        $("#delete_account_button").addClass("selected");
        $("main > section:nth-child(n+2)").remove();
        showDeleteAccountForm();
        hideMenu();
    });
    $("#user_area_menu > a").click(() => {
        if ($("#user_area_menu > ul").is(":hidden")) {
            $("#user_area_menu > ul").slideDown();
            $("#user_area_menu > a > img").attr("alt", "Chiudi menu area utente");
        } else {
            hideMenu();
        }
    });
});