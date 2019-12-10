function showEventsRange(minEventIndex, resultsToShow, keyword, place, date, free) {
    $.getJSON("get_event_data.php?min=" + minEventIndex + "&count=" + resultsToShow + "&keyword=" + keyword + "&place=" + place +
              "&date=" + date + "&free=" + free, data => {
        if (data["result"] === false) {
            alert("Si è verificato un errore. Per favore ricaricare la pagina");
            return;
        }
        const events = data["events"];
        Object.values(events).forEach(event => {
            const eventSection = $(document.createElement("section"));
            const eventHeader = $(document.createElement("header"));
            const eventTitle = $(document.createElement("h1"));
            const eventLink = $(document.createElement("a"));
            eventLink.attr("href", "event?id=" + event["id"]);
            eventLink.text(event["name"]);
            eventTitle.append(eventLink);
            eventHeader.append(eventTitle);
            const placeDateTimePar = $(document.createElement("p"));
            const date = new Date(event["dateTime"]);
            const dateString = date.toLocaleDateString("it-IT", {
                day: "numeric",
                month: "long",
                year: "numeric",
            }) + " ore " + date.toLocaleTimeString("it-IT", {
                hour: "2-digit",
                minute: "2-digit"
            });
            placeDateTimePar.text(event["place"] + ", " + dateString);
            eventHeader.append(placeDateTimePar);
            const promoterPar = $(document.createElement("p"));
            promoterPar.text("Organizzato da: " + event["organizationName"]);
            eventHeader.append(promoterPar);
            eventSection.append(eventHeader);
            const eventBody = $(document.createElement("section"));
            const seatsPar = $(document.createElement("p"));
            seatsPar.text("Posti ancora disponibili: " + event["freeSeats"] + " su " + event["totalSeats"]);
            eventBody.append(seatsPar);
            eventSection.append(eventBody);
            const eventFooter = $(document.createElement("footer"));
            Object.values(event["categories"]).forEach(category => {
                const categorySpan = $(document.createElement("span"));
                categorySpan.text("#" + category);
                eventFooter.append(categorySpan);
            });
            eventSection.append(eventFooter);
        });
    });
}

$(() => {
    const resultsShown = 5;
    const previousPage = $("#previousPage");
    const nextPage = $("#nextPage");
    let minEventIndex = 0;
    $.getJSON("get_events_count.php", data => {
        if (data["result"] === false) {
            alert("Si è verificato un errore. Per favore ricaricare la pagina");
            return;
        }
        const eventCount = data["count"];
        previousPage.attr("disabled", "");
        if (minEventIndex + resultsShown >= eventCount) {
            nextPage.attr("disabled", "");
        }
        previousPage.click(() => {
            if (minEventIndex > 0) {
                minEventIndex -= resultsShown;
                if (minEventIndex <= 0) {
                    previousPage.attr("disabled", "");
                }
                showEventsRange(minEventIndex, resultsShown);
            }
        });
        nextPage.click(() => {
            if (minEventIndex < eventCount) {
                minEventIndex += resultsShown;
                if (minEventIndex >= eventCount) {
                    nextPage.attr("disabled");
                }
                showEventsRange(minEventIndex, resultsShown);
            }
        });
    });
});
