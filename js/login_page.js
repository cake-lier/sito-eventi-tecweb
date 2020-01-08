function showRightForm() {
    if ($("input[name=registration_type]:checked").val() === "customer") {
        $(".customer").prop("required", true);
        $(".customer, .customer_non_req").show();
        $(".promoter").prop("required", false);
        $(".promoter, .promoter_non_req").hide();
    } else if ($("input[name=registration_type]:checked").val() === "promoter") {
        $(".promoter").prop("required", true);
        $(".promoter, .promoter_non_req").show();
        $(".customer").prop("required", false);
        $(".customer, .customer_non_req").hide();
    }
}

$(() => {
    $(".login").show();
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
        if ($("#telephone").val() !== ""
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