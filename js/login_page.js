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
        if ($("input[name=registration_type]").val() === "customer") {
            $(".customer").prop("required", true);
            $(".customer").show();
            $(".customer_non_req").show();
            $(".promoter").hide();
        } else if ($("input[name=registration_type]").val() === "promoter") {
            $(".promoter").prop("required", true);
            $(".promoter").show();
            $(".promoter_non_req").show();
            $(".customer").hide();
        }
    });
    $("#check_customer, #check_promoter").change(e => {
        if ($("input[name=registration_type]:checked").val() === "customer") {
            $(".customer").prop("required", true);
            $(".customer").show();
            $(".promoter").prop("required", false);
            $(".promoter").hide();
        } else if ($("input[name=registration_type]:checked").val() === "promoter") {
            $(".promoter").prop("required", true);
            $(".promoter").show();
            $(".customer").prop("required", false);
            $(".customer").hide();
        } else {
        }
    });

});