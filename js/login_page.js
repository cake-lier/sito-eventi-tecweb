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
    } else {
        //TODO: What happens here?
    }
}

$(() => {
    $("#login_form").show();
    $("#registration_form").hide();
    $("#login_button").click(() => {
        $("#login_form").show();
        $("#registration_form").hide();
        $(".login").prop("required", true);
    });
    $("#registration_button").click(() => {
        $("#login_form").hide();
        $("#registration_form").show();
        showRightForm();
    });
    $("#check_customer, #check_promoter").change(() => {
        showRightForm();
    });
    $("#registration_form").submit(e => {
        if ($("#profile_photo")[0].files.item(0).size > 12000000) {
            e.preventDefault();
            $("main").prepend($("<p>", {text: "Immagine troppo grande"}));
        }
        if ($("#telephone").val() !== ""
            && (isNaN($("telephone").val()) || $("telephone").val().includes(".") || $("telephone").val().includes(","))) {
            e.preventDefault();
            $("main").prepend($("<p>", {text: "Numero di telefono non corretto!"}));
        }
    });
});