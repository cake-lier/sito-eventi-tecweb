function bodyHandlerMobile(e) {
    if (!$(e.target).is("img#menu_icon.icon") && !$(e.target).is("nav > ul > li > a") && $("nav > ul").is(":visible")) {
        e.preventDefault();
        $("nav > ul").slideUp(() => {
            $("nav").css("width", "50%")
                    .css("left", "50%");
        });
    }
}

function mobileMenuBehavior() {
    $("#menu_icon").css("width", $("#logo").height());
    $("#menu_icon").click(e => {
        e.preventDefault();
        e.stopPropagation();
        if ($("nav > ul").is(":visible")) {
            $("nav > ul").slideUp(() => {
                $("nav").css("width", "50%")
                        .css("left", "50%");
            });
        } else {
            $("nav").css("width", "100%")
                    .css("left", "0%");
            $("nav > ul").slideDown();
        }
    });   
    $("nav > ul").click(e => {
        e.stopPropagation();
    });
    $("body").click(bodyHandlerMobile);
}

$(window).on("load", () => {
    if ($(window).width() < 768) {
        mobileMenuBehavior();
    }
    $(window).resize(() => {
        if ($(window).width() < 768) {
            mobileMenuBehavior();
        } else {
            $("#menu_icon").unbind("click");
            $("nav > ul").unbind("click");     
            $("body").unbind("click", bodyHandlerMobile);
        }
    });
});
