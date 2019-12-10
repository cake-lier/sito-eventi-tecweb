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
            const nameHeader = $("<strong>", {text: "Nome:"});
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
            // it's a customer
            const generalPromoterSectionHeader = $("<h2>", {text: "Dati organizzazione"});
            const generalPromoterSection = $("<section>");
            generalPromoterSection.append(generalPromoterSectionHeader);
            mainSection.append(generalPromoterSection);
            // organization name
            const organizationNameHeader = $("<strong>", {text: "Nome organizzazione:"});
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

$(() => {
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
});