function showRightForm() {
    if ($("input[name=registration_type]:checked").val() === "customer") {
        $("#email_register").after(
            $("<label>", {class: "customer", for: "name", text: "Nome: "}),
            $("<input>", {class: "customer", id: "name", name: "name", type: "text"}),
            $("<label>", {class: "customer", for: "surname", text: "Cognome: "}),
            $("<input>", {class: "customer", id: "surname", name: "surname", type: "text"}),
            $("<label>", {class: "customer", for: "username", text: "Username: "}),
            $("<input>", {class: "customer", id: "username", name: "username", type: "text"}),
            $("<label>", {class: "customer", for: "birthdate", text: "Data di nascita: "}),
            $("<input>", {class: "customer", id: "birthdate", name: "birthdate", type: "date"}),
            $("<label>", {class: "customer", for: "birthplace", text: "Luogo di nascita: "}),
            $("<input>", {class: "customer", id: "birthplace", name: "birthplace", type: "text"}),
            $("<label>", {class: "customer", for: "billing", text: "Indirizzo di fatturazione: "}),
            $("<input>", {class: "customer", id: "billing", name: "billing", type: "text"}),
            $("<label>", {class: "customer_non_req", for: "current", text: "Indirizzo corrente: "}),
            $("<input>", {class: "customer_non_req", id: "current", name: "current", type: "text"}),
            $("<label>", {class: "customer_non_req", for: "telephone", text: "Telefono: "}),
            $("<input>", {class: "customer_non_req", id: "telephone", name: "telephone", type: "text"})
        );
        $(".customer").prop("required", true);
        $(".promoter, .promoter_non_req").remove();
    } else if ($("input[name=registration_type]:checked").val() === "promoter") {
        $("#email_register").after(
            $("<label>", {class: "promoter", for: "organization_name", text: "Nome organizzazione: "}),
            $("<input>", {class: "promoter", id: "organization_name", name: "organization_name", type: "text"}),
            $("<label>", {class: "promoter", for: "vat_id", text: "VATid: "}),
            $("<input>", {class: "promoter", id: "vat_id", name: "vat_id", type: "text"}),
            $("<label>", {class: "promoter_non_req", for: "website", text: "Sito internet: "}),
            $("<input>", {class: "promoter_non_req", id: "website", name: "website", type: "text"})
        );
        $(".promoter").prop("required", true);
        $(".customer, .customer_non_req").remove();
    }
}

$(() => {
    $(".login").show();
    $(".customer, .customer_non_req").remove();
    $(".registration").hide();
    $("#login_button").click(() => {
        $(".login").show();
        $(".registration").hide();
        $(".login").prop("required", true);
        $("#login_button").addClass("selected");
        $("#registration_button").removeClass("selected");
    });
    $("#registration_button").click(() => {
        $(".login").hide();
        $(".registration").show();
        $("#registration_button").addClass("selected");
        $("#login_button").removeClass("selected");
        showRightForm();
    });
    $("#check_customer, #check_promoter").change(() => {
        showRightForm();
    });
    $("#registration_form").submit(e => {
        if ($("#profile_photo")[0].files.item(0).size > 12000000) {
            e.preventDefault();
            $("main").prepend($("<section>", {class: "alert"})
                                  .append($("<p>", {text: "Immagine troppo grande"}),
                                          $("<a>", {href: "#"})
                                              .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                              .click(function() {
                                                  $(this).parent().remove();
                                              })));
        }
        if ($("#telephone").length > 0
            && $("#telephone").val() !== ""
            && (isNaN($("#telephone").val()) || $("#telephone").val().includes(".") || $("#telephone").val().includes(","))) {
            e.preventDefault();
            $("main").prepend($("<section>", {class: "alert"})
                                  .append($("<p>", {text: "Numero di telefono non corretto"}),
                                          $("<a>", {href: "#"})
                                              .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                              .click(function() {
                                                  $(this).parent().remove();
                                              })));
        }
    });
});