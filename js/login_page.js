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
        if ($("input[name=registration_type]").val() === "customer") {
            $(".customer").prop("required", true);
            $(".customer").show();
            $(".promoter").hide();
        } else if ($("input[name=registration_type]").val() === "promoter") {
            $(".promoter").prop("required", true);
            $(".promoter").show();
            $(".customer").hide();
        }
    });
    $("#check_customer, #check_promoter").change(() => {
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
            //TODO: check what is happening here...
        }
    });

});