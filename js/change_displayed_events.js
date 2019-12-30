$(() => {
    const previousPage = $("#previousPage");
    const nextPage = $("#nextPage");
    const landingURLParams = new URLSearchParams(window.location.search);
    const minEventIndex = landingURLParams.has("min") ? parseInt(landingURLParams.get("min")) : 0;
    const resultsShown = landingURLParams.has("count") ? parseInt(landingURLParams.get("count")) : 5;
    $.getJSON("get_events_count.php", data => {
        if (data["result"] === false) {
            $("main").prepend($("<section>", {class: "alert"})
                                  .append($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}),
                                          $("<a>", {href: "#"})
                                              .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                              .click(function() {
                                                  $(this).parent().remove();
                                              })));
            return;
        }
        const eventCount = data["count"];
        if (minEventIndex - resultsShown < 0) {
            previousPage.addClass("disabled");
        }
        if (minEventIndex + resultsShown >= eventCount) {
            nextPage.addClass("disabled");
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