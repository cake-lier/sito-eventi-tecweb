$(() => {
    if ($(window).width() < 768) {
        $("#menu_icon").click(e => {
            e.preventDefault();
            e.stopPropagation();
            $("nav > ul").toggle();
        });
    
        $("nav").click(e => {
            e.stopPropagation();
        });
    
        $("body").click(e => {
            if (!$(e.target).is("img#menu_icon.icon") && !$(e.target).is("nav > ul > li > a") && $("nav > ul").is(":visible")) {
                e.preventDefault();
                $("nav > ul").hide();
            }
        });
    }

    $(window).resize(() => {
        if ($(window).width() < 768) {
            $("#menu_icon").click(e => {
                e.preventDefault();
                e.stopPropagation();
                $("nav > ul").toggle();
            });
        
            $("nav").click(e => {
                e.stopPropagation();
            });
        
            $("body").click(e => {
                if (!$(e.target).is("img#menu_icon.icon") && !$(e.target).is("nav > ul > li > a") && $("nav > ul").is(":visible")) {
                    e.preventDefault();
                    $("nav > ul").hide();
                }
            });
        } else {
            $("#menu_icon").unbind("click");
            $("nav").unbind("click");     
            $("body").unbind("click");
        }
    });
});

