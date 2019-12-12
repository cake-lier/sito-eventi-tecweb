function organizeUserNotifications(data) {
    const mainSection = $("main > section");
    if (data.length > 0) {
        for (let index in data) {
            const notification = data[index];
            const notSection = $("<section>", {id: notification.notificationId});
            if (notification.visualized === true) {
                notSection.addClass("visualized");
            }
            const dateTime = new Date(notification.dateTime);
            const dateString = dateTime.toLocaleDateString("it-IT", {
                day: "numeric",
                month: "long",
                year: "numeric",
            }) + " ore " + dateTime.toLocaleTimeString("it-IT", {
                hour: "2-digit",
                minute: "2-digit"
            });
            const date = $("<p>", {text: dateString});
            const message = $("<p>", {text: notification.message});
            notSection.append(date, message);
            mainSection.append(notSection);
        }
    } else {
        mainSection.append($("<p>", {text: "Non ci sono notifiche!"}));
    }
}

function organizeUserData(data) {
    if (Object.keys(data).length > 0) {
        // this part is common to every type of user
        const mainSection = $("main > section");
        const generalSection = $("<section>");
        mainSection.append(generalSection);
        const photo = $("<img>", {src: data.profilePhoto});
        generalSection.append(photo);
        const emailHeader = $("<strong>");
        emailHeader.text("Email: ");
        const emailLine = $("<p>");
        emailLine.text(data.email);
        emailLine.prepend(emailHeader);
        generalSection.append(emailLine);
        if ("username" in data) {
            // it's a customer
            const generalCustomerSection = $("<section>");
            const generalCustomerSectionHeader = $("<h2>", {text: "Dati personali"});
            generalCustomerSection.append(generalCustomerSectionHeader);
            mainSection.append(generalCustomerSection);
            // username
            const usernameLine = $("<p>", {class: "username", text: data.username});
            photo.after(usernameLine);
            // name
            const nameHeader = $("<strong>", {text: "Nome: "});
            const nameLine = $("<p>", {text: data.name});
            nameLine.prepend(nameHeader);
            generalCustomerSection.append(nameLine);
            // surname
            const surnameHeader = $("<strong>", {text: "Cognome: "});
            const surnameLine = $("<p>", {text: data.surname});
            surnameLine.prepend(surnameHeader);
            generalCustomerSection.append(surnameLine);
            // birthDate
            const birthDateHeader = $("<strong>", {text: "Data di nascita: "});
            const date = new Date(data.birthDate);
            const dateString = date.toLocaleDateString("it-IT", {
                day: "numeric",
                month: "long",
                year: "numeric",
            });
            const birthDateLine = $("<p>", {text: dateString});
            birthDateLine.prepend(birthDateHeader);
            generalCustomerSection.append(birthDateLine);
            // birthplace
            const birthplaceHeader = $("<strong>", {text: "Luogo di nascita: "});
            const birthplaceLine = $("<p>", {text: data.birthplace});
            birthplaceLine.prepend(birthplaceHeader);
            generalCustomerSection.append(birthplaceLine);
            // contacts
            const contactsSection = $("<section>");
            const contactsSectionHeader = $("<h2>", {text: "Contatti"});
            contactsSection.append(contactsSectionHeader);
            mainSection.append(contactsSection);
            // billing address
            const billingAddressHeader = $("<strong>", {text: "Indirizzo di fatturazione: "});
            const billingAddressLine = $("<p>", {text: data.billingAddress});
            billingAddressLine.prepend(billingAddressHeader);
            contactsSection.append(billingAddressLine);
            if (data.currentAddress !== null) {
                // current address
                const currentAddressHeader = $("<strong>", {text: "Indirizzo corrente: "});
                const currentAddressLine = $("<p>", {text: data.currentAddress});
                currentAddressLine.prepend(currentAddressHeader);
                contactsSection.append(currentAddressLine);
            }
            if (data.telephone !== null) {
                // telephone
                const telephoneHeader = $("<strong>", {text: "Telefono: "});
                const telephoneLine = $("<p>", {text: data.telephone});
                telephoneLine.prepend(telephoneHeader);
                contactsSection.append(telephoneLine);
            }
        } else if ("organizationName" in data) {
            // it's a promoter
            console.log("promoter");
            const generalPromoterSectionHeader = $("<h2>", {text: "Dati organizzazione"});
            const generalPromoterSection = $("<section>");
            generalPromoterSection.append(generalPromoterSectionHeader);
            mainSection.append(generalPromoterSection);
            // organization name
            const organizationNameHeader = $("<strong>", {text: "Nome organizzazione: "});
            const organizationNameLine = $("<p>", {text: data.organizationName});
            organizationNameLine.prepend(organizationNameHeader);
            generalPromoterSection.append(organizationNameLine);
            // VATid
            const vatHeader = $("<strong>", {text: "VATid: "});
            const vatLine = $("<p>", {text: data.VATid});
            vatLine.prepend(vatHeader);
            generalPromoterSection.append(vatLine);
            if (data.website !== null) {
                // website
                const websiteHeader = $("<strong>", {text: "Sito: "});
                const websiteLine = $("<p>", {text: data.website});
                websiteLine.prepend(websiteHeader);
                generalPromoterSection.append(websiteLine);
            }
        }
    }
}

function setChangePasswordForm() {
    const mainSection = $("main > section");
    const form = $("<form>");
    mainSection.append(form);
    const oldPwdLabel = $("<label>", {text: "Password attuale: ", for: "old_password"});
    const oldPwdField = $("<input>", {type: "password", name: "old_password", id: "old_password"});
    oldPwdField.prop("required", true);
    const newPwdLabel = $("<label>", {text: "Nuova password: ", for: "new_password"});
    const newPwdField = $("<input>", {type: "password", name: "new_password", id: "new_password"});
    newPwdField.prop("required", true);
    const newPwdRepeatLabel = $("<label>", {text: "Conferma password: ", for: "new_password_repeat"});
    const newPwdRepeatField = $("<input>", {type: "password", name: "new_password_repeat", id: "new_password_repeat"});
    newPwdRepeatField.prop("required", true);
    const submitButton = $("<input>", {type: "submit", value: "Cambia password"});
    form.append(oldPwdLabel, oldPwdField, newPwdLabel, newPwdField, newPwdRepeatLabel, newPwdRepeatField, submitButton);
    form.submit(e => {
        e.preventDefault();
        $.post("change_password.php", form.serialize(), data => {
            $("form > p").remove();
            form.prepend($("<p>", {text: data.resultMessage}));
        });
    })
}

function setChangeDataForm(data) {
    if (Object.keys(data).length > 0) {
        // this part is common to every type of user
        const form = $("<form>");
        $("main > section").append(form);
        const generalSection = $("<section>");
        form.append(generalSection);
        const photo = $("<img>", {src: data.profilePhoto});
        generalSection.append(photo);
        const photoLabel = $("<label>", {for: "profile_photo", text: "Nuova foto profilo: "});
        const photoChooser = $("<input>", {type: "file", name: "profile_photo", id: "profile_photo"});
        generalSection.append(photoLabel, photoChooser);
        if ("username" in data) {
            // it's a customer
            const generalCustomerSection = $("<section>");
            const generalCustomerSectionHeader = $("<h2>", {text: "Dati personali"});
            generalCustomerSection.append(generalCustomerSectionHeader);
            form.append(generalCustomerSection);
            // username
            const usernameLabel = $("<label>", {for: "username", text: "Username: "});
            const usernameField = $("<input>", {id: "username", name: "username", value: data.username});
            generalCustomerSection.append(usernameLabel, usernameField);
            // name
            const nameLabel = $("<label>", {for: "name", text: "Nome: "});
            const nameField = $("<input>", {id: "name", name: "name", value: data.name});
            generalCustomerSection.append(nameLabel, nameField);
            // surname
            const surnameLabel = $("<label>", {for: "surname", text: "Cognome: "});
            const surnameField = $("<input>", {id: "surname", surname: "surname", value: data.surname});
            generalCustomerSection.append(surnameLabel, surnameField);
            // birthDate
            const birthDateLabel = $("<label>", {for: "birthdate", text: "Data di nascita: "});
            const date = new Date(data.birthDate);
            const dateString = date.toLocaleDateString("it-IT", {
                day: "numeric",
                month: "long",
                year: "numeric",
            });
            const birthDateField = $("<input>", {id: "birthdate", value: data.birthDate, type: "date"}); // TODO:
            generalCustomerSection.append(birthDateLabel, birthDateField);
            // birthplace
            const birthplaceLabel = $("<label>", {for: "birthplace", text: "Luogo di nascita: "});
            const birthplaceField = $("<input>", {id: "birthplace", name: "birthplace", value: data.birthplace});
            generalCustomerSection.append(birthplaceLabel, birthplaceField);
            // contacts
            const contactsSection = $("<section>");
            const contactsSectionHeader = $("<h2>", {text: "Contatti"});
            contactsSection.append(contactsSectionHeader);
            form.append(contactsSection);
            // billing address
            const billingAddressLabel = $("<label>", {for: "billing_add", text: "Indirizzo di fatturazione: "});
            const billingAddressField = $("<input>", {id: "billing_add", name: "billingAddress", value: data.billingAddress});
            contactsSection.append(billingAddressLabel, billingAddressField);
            // current address
            const currentAddressLabel = $("<label>", {for: "current_add", text: "Indirizzo corrente: "});
            const currentAddressField = $("<input>", {id: "current_add", name: "currentAddress", value: data.currentAddress !== null ? data.currentAddress : ""});
            contactsSection.append(currentAddressLabel, currentAddressField);
            // telephone
            const telephoneLabel = $("<label>", {for: "telephone", text: "Indirizzo corrente: "});
            const telephoneField = $("<input>", {id: "telephone", name: "telephone", value: data.telephone !== null ? data.telephone : ""});
            contactsSection.append(telephoneLabel, telephoneField);
        } else if ("organizationName" in data) {
            // it's a customer
            const generalPromoterSectionHeader = $("<h2>", {text: "Dati organizzazione"});
            const generalPromoterSection = $("<section>");
            generalPromoterSection.append(generalPromoterSectionHeader);
            form.append(generalPromoterSection);
            // website
            const websiteLabel = $("<label>", {for: "website", text: "Sito internet: "});
            const websiteField = $("<input>", {id: "website", type: "text", name: "website", value: data.website !== null ? data.website : ""});
            generalPromoterSection.append(websiteLabel, websiteField);
        }
        const submit = $("<input>", {type: "submit", value: "Modifica dati"});
        form.append(submit);
        form.submit(e => {
            e.preventDefault();
            $.post("change_user_data.php", form.serialize(), data => {
                console.log(data.resultMessage);    
                $("form > p").remove();
                form.prepend($("<p>", {text: data.resultMessage}));
            });
        });
    }
}

$(() => {
    $("#notifications_button").click(e => {
        $(".selected").removeClass("selected");
        $("#notifications_button").addClass("selected");
        $("main > section").html("");
        $.get("get_user_notifications.php", organizeUserNotifications);
    });

    $("#user_area_button").click(e => {
        $(".selected").removeClass("selected");
        $("#user_area_button").addClass("selected");
        $("main > section").html("");
        $.get("get_user_data.php", organizeUserData);
    });

    $("#change_password_button").click(e => {
        $(".selected").removeClass("selected");
        $("#change_password_button").addClass("selected");
        $("main > section").html("");
        setChangePasswordForm();
    });

    $("#change_data_button").click(e => {
        $(".selected").removeClass("selected");
        $("#change_data_button").addClass("selected");
        $("main > section").html("");
        $.get("get_user_data.php", setChangeDataForm);
    });
});