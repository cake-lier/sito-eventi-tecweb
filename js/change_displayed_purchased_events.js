$(() => {
    const previousPage = $("#previous_page");
    const nextPage = $("#next_page");
    const landingURLParams = new URLSearchParams(window.location.search);
    const minEventIndex = landingURLParams.has("min") ? parseInt(landingURLParams.get("min")) : 0;
    const resultsShown = landingURLParams.has("count") ? parseInt(landingURLParams.get("count")) : 5;
    const eventCountSelect = $("#event_count");
    eventCountSelect.val(resultsShown).on("change", () => {
        const queryParams = new URLSearchParams(window.location.search);
        queryParams.set("min", 0);
        queryParams.set("count", eventCountSelect.val() === null ? 5 : eventCountSelect.val());
        window.location.search = queryParams.toString();
    });
    $.getJSON("get_events_count.php?type=2", data => {
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
                queryParams.set("count", eventCountSelect.val() === null ? 5 : eventCountSelect.val());
                window.location.search = queryParams.toString();
            }
        });
        nextPage.click(() => {
            if (minEventIndex + resultsShown < eventCount) {
                const queryParams = new URLSearchParams(window.location.search);
                queryParams.set("min", minEventIndex + resultsShown);
                queryParams.set("count", eventCountSelect.val() === null ? 5 : eventCountSelect.val());
                window.location.search = queryParams.toString();
            }
        });
    });
    let formUpdated = false;
    $("#search_section > form").submit(function() {
        if (!formUpdated) {
            const queryParams = new URLSearchParams(window.location.search);
            if (queryParams.has("min") && queryParams.has("count")) {
                $("#search_section > form").append($("<input>",
                                                     {
                                                         type: "hidden",
                                                         name: "min",
                                                         value: queryParams.get("min")
                                                     }),
                                                   $("<input>", 
                                                     {
                                                         type: "hidden",
                                                         name: "count",
                                                         value: queryParams.get("count")
                                                     }));
            }
            formUpdated = true;
            $(this).submit();
        }
    });
});