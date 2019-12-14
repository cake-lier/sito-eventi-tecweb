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
    }
}

$(() => {
    $("#login_form").show();
    $("#registration_form").hide();

    $("#login_button").click(e => {
        $("#login_form").show();
        $("#registration_form").hide();
        $(".login").prop("required", true);
    });
    $("#registration_button").click(e => {
        $("#login_form").hide();
        $("#registration_form").show();
        showRightForm();
    });
    $("#check_customer, #check_promoter").change(e => {
        showRightForm();
    });

    $("#registration_form").submit(e => {
        if ($("#profile_photo")[0].files.item(0).size > 10485760) { // 10MB
            e.preventDefault();
            alert("Immagine troppo grande");
        }
        if ($("#telephone").val() !== "" && (isNaN($("telephone").val()) || $("telephone").val().includes(".") || $("telephone").val().includes(","))) {
            e.preventDefault();
            alert("Numero di telefono non corretto!");
        }
    });
});