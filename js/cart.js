function getDataFromId(id) {
    return [id.split("_")[1], id.split("_")[2]];
}

function removeSectionIfEmpty(section) {
    const parentSection = section.parent();
    section.remove();
    if ($.trim(parentSection.html()) === "") {
        window.location.reload();
    }
}

$(() => {
    $("button[id^='removeButton_']").click(function() {
        const [seatId, eventId] = getDataFromId($(this).attr("id"));
        $.getJSON("manage_tickets.php?seatId=" + seatId + "&eventId=" + eventId + "&actionType=0", data => {
            if (data["result"] === true) {
                removeSectionIfEmpty($(this).parent().parent());
            } else {
                $("main").prepend($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}));
            }
        });
    });
    $("button[id^='decButton_']").click(function() {
        const [seatId, eventId] = getDataFromId($(this).attr("id"));
        $.getJSON("manage_tickets.php?seatId=" + seatId + "&eventId=" + eventId + "&actionType=1", data => {
            if (data["result"] === true) {
                const amountLabel = $(this).next();
                const amount = parseInt(amountLabel.text()) - 1;
                if (amount === 0) {
                    removeSectionIfEmpty($(this).parent().parent().parent());
                } else {
                    amountLabel.text(amount + " bigliett" + (amount > 1 ? "i" : "o"));
                }
            } else {
                $("main").prepend($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}));
            }
        });
    });
    $("button[id^='incButton_']").click(function() {
        const [seatId, eventId] = getDataFromId($(this).attr("id"));
        $.getJSON("manage_tickets.php?seatId=" + seatId + "&eventId=" + eventId + "&actionType=2", data => {
            if (data["result"] === true) {
                const amountLabel = $(this).prev();
                const amount = parseInt(amountLabel.text()) + 1;
                amountLabel.text(amount + " biglietti");
            } else {
                $("main").prepend($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}));
            }
        });
    });
    $("section#paymentTypes > ul > li").click(function() {
        $("section#paymentTypes > ul > li").filter((_i, e) => $(e).hasClass("selected")).removeClass("selected");
        $(this).addClass("selected");
    });
    $("#buyButton").click(() => {
        window.location.href = "buy_tickets.php";
    });
}); 
