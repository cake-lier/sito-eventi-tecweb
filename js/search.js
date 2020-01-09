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
        if ($(window).width() > 768) {
            displaySearch();
        }
    });
});

function collapseSearch() {
    $("#specific_search, #search_section > form > input[type=submit]").hide();
    $("#general_search").css("padding", "0 0")
                        .css("margin", "0 0")
                        .css("border-radius", "2em")
                        .css("width", "100%")
                        .css("box-sizing", "border-box")
                        .css("height", "100%")
                        .css("display", "inline-block")
                        .css("vertical-align", "middle");
    $("#general_search input").css("margin-top", "0%")
                              .css("margin-bottom", "0%")
                              .css("box-sizing", "border-box")
                              .css("height", "100%")
                              .css("display", "inline-block")
                              .css("vertical-align", "middle");
    $("#general_search label").css("box-sizing", "border-box")
                              .css("height", "2em")
                              .css("margin-top", "0%")
                              .css("display", "inline-block")
                              .css("vertical-align", "middle");
    $("#general_search label img").css("box-sizing", "border-box")
                                  .css("height", "2em")
                                  .css("margin-top", "0%");
    $("#search_section").css("padding", "0 0")
                        .css("box-sizing", "border-box")
                        .css("border-radius", "2em")
                        .css("width", "90%")
                        .css("height", "2em")
                        .css("box-shadow", "0 0 0");
    $("#search_section > form").css("margin", "0 0")
                               .css("height", "100%");
    $("#filters_button").css("height", "100%")
                        .css("display", "inline-block")
                        .css("vertical-align", "middle")
                        .off("click", "#filters_button", collapseSearch)
                        .click(displaySearch);
}

function displaySearch() {
    $("#specific_search, #search_section > form > input[type=submit]").removeAttr("style");
    $("#general_search").removeAttr("style");
    $("#general_search input").removeAttr("style");
    $("#general_search label").removeAttr("style");
    $("#search_section").removeAttr("style");
    $("#filters_button").removeAttr("style")
                        .off("click", "#filters_button", displaySearch)
                        .click(collapseSearch);
}