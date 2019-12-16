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
    const previousPage = $("#previousPage");
    const nextPage = $("#nextPage");
    const landingURLParams = new URLSearchParams(window.location.search);
    const minEventIndex = landingURLParams.has("min") ? parseInt(landingURLParams.get("min")) : 0;
    const resultsShown = landingURLParams.has("count") ? parseInt(landingURLParams.get("count")) : 5;
    $.getJSON("get_events_count.php", data => {
        if (data["result"] === false) {
            alert("Si è verificato un errore. Per favore ricaricare la pagina");
            return;
        }
        const eventCount = data["count"];
        if (minEventIndex - resultsShown < 0) {
            previousPage.attr("disabled", "");
        }
        if (minEventIndex + resultsShown >= eventCount) {
            nextPage.attr("disabled", "");
        }
        previousPage.click(() => {
            if (minEventIndex - resultsShown >= 0) {
                const queryParams = new URLSearchParams(window.location.search);
                queryParams.set("min", minEventIndex - resultsShown);
                queryParams.set("count", resultsShown);
                window.location.search = queryParams.toString();
            }
        });
        nextPage.click(() => {
            if (minEventIndex + resultsShown < eventCount) {
                const queryParams = new URLSearchParams(window.location.search);
                queryParams.set("min", minEventIndex + resultsShown);
                queryParams.set("count", resultsShown);
                window.location.search = queryParams.toString();
            }
        });
    });
});