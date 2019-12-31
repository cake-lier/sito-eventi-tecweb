function bodyHandlerMobile(e) {
    if (!$(e.target).is("img#menu_icon.icon") && !$(e.target).is("body > div > nav > ul > li > a") && $("body > div > nav > ul").is(":visible")) {
        e.preventDefault();
        $("body > div > nav > ul").slideUp(() => {
            $("body > div > nav").css("width", "50%")
                                 .css("left", "50%");
        });
    }
}

function mobileMenuBehavior() {
    cleanHandlers();
    $("#menu_icon").css("width", $("#logo").height());
    $("#menu_icon").click(e => {
        e.preventDefault();
        e.stopPropagation();
        if ($("body > div > nav > ul").is(":visible")) {
            $("body > div > nav > ul").slideUp(() => {
                $("body > div > nav").css("width", "50%")
                                     .css("left", "50%");
            });
        } else {
            $("body > div > nav").css("width", "100%")
                                 .css("left", "0%");
            $("body > div > nav > ul").slideDown();
        }
    });   
    $("body > div > nav > ul").click(e => {
        e.stopPropagation();
    });
    $("body").click(bodyHandlerMobile);
}

function cleanHandlers() {
    $("#menu_icon").off("click");
    $("body > div > nav > ul").off("click")
                              .removeAttr("style");     
    $("body").off("click", "body", bodyHandlerMobile);
}

$(window).on("load", () => {
    if ($(window).width() < 768) {
        mobileMenuBehavior();
    }
    $(window).resize(() => {
        if ($(window).width() < 768) {
            mobileMenuBehavior();
        } else {
            cleanHandlers();
            $("body > div > nav > ul").removeAttr("style");     
        }
    });
});