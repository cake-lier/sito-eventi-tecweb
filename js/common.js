$(() => {
    $("body").click(bodyHandler);
    if ($(window).width() < 768) {
        $("#menu_icon").click(e => {
            e.preventDefault();
            e.stopPropagation();
            if ($("nav > ul").is(":visible")) {
                $("nav").css("width", "50%")
                        .css("left", "50%");
            } else {
                $("nav").css("width", "100%")
                        .css("left", "0%");
            }
            $("nav > ul").toggle();
        });
    
        $("nav > ul").click(e => {
            e.stopPropagation();
        });
    
        $("body").click(bodyHandlerMobile);
    }

    $(window).resize(() => {
        if ($(window).width() < 768) {
            $("#menu_icon").click(e => {
                e.preventDefault();
                e.stopPropagation();
                if ($("nav > ul").is(":visible")) {
                    $("nav").css("width", "50%")
                            .css("left", "50%");
                } else {
                    $("nav").css("width", "100%")
                            .css("left", "0%");
                }
                $("nav > ul").toggle();
            });
        
            $("nav > ul").click(e => {
                e.stopPropagation();
            });
        
            $("body").click(bodyHandlerMobile);
        } else {
            $("#menu_icon").unbind("click");
            $("nav > ul").unbind("click");     
            $("body").unbind("click", bodyHandlerMobile);
        }
    });
});

function bodyHandlerMobile(e) {
    if (!$(e.target).is("img#menu_icon.icon") && !$(e.target).is("nav > ul > li > a") && $("nav > ul").is(":visible")) {
        e.preventDefault();
        $("nav").css("width", "50%")
                .css("left", "50%");
        $("nav > ul").hide();
    }
}

function bodyHandler(e) {
    if (!$(e.target).is(".alert") && $(".alert").is(":visible")) {
        $(".alert").hide();
    }
}