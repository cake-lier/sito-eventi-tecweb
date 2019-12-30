function onNewTagInserted() {
    const searchTagsText = $("#tags").val();
    const lastTagBegin = searchTagsText.search(/[^\s#]+$/m);
    if (lastTagBegin !== -1) {
        const tag = searchTagsText.substr(lastTagBegin, searchTagsText.length);
        $("#tags").val(searchTagsText.substr(0, lastTagBegin) + "#" + tag);
    }
}

$(() => {
    const spacebarPressed = 32;
    const enterPressed = 13;
    $("#tags").keypress(e => {
        if (e.which === spacebarPressed || e.which === enterPressed) {
            onNewTagInserted();
        }
    }).focusout(() => onNewTagInserted());
    $("#keywords").keypress(e => {
        if (e.which === enterPressed) {
            onNewTagInserted();
        }
    });
    if ($(window).width() < 768) {
        collapseSearch();
    }

    $(window).resize(() => {
        if ($(window).width() < 768) {
            collapseSearch();
        }
    });
});

function collapseSearch() {
    $("#specific_search, #search_section > form > input[type=submit]").hide();
    $("#general_search").css("background", "transparent")
                        .css("padding", "0 0")
                        .css("margin", "0 0")
                        .css("width", "100%")
                        .css("display", "inline-block");
    $("#general_search input").css("margin-top", "0.25%")
                              .css("margin-bottom", "0%");
    $("#general_search label").css("margin-top", "1%");
    $("#search_section").css("padding", "0 0")
                        .css("box-sizing", "border-box")
                        .css("border-radius", "2em")
                        .css("width", "90%");
    $("#filters_button").unbind("click", collapseSearch)
                        .click(displaySearch);
}

function displaySearch() {
    $("#specific_search, #search_section > form > input[type=submit]").removeAttr("style");
    $("#general_search").removeAttr("style");
    $("#general_search input").removeAttr("style");
    $("#general_search label").removeAttr("style");
    $("#search_section").removeAttr("style");
    $("#filters_button").unbind("click", displaySearch)
                        .click(collapseSearch);
}