function getDataFromId(id) {
    return [id.split("_")[2], id.split("_")[3]];
}

function removeSectionIfEmpty(section) {
    const parentSection = section.parent();
    section.remove();
    if ($.trim(parentSection.html()) === "") {
        window.location.reload();
    }
}

$(() => {
    $("a[id^='remove_button_']").click(function() {
        const [seatId, eventId] = getDataFromId($(this).attr("id"));
        $.getJSON("manage_tickets.php?seatId=" + seatId + "&eventId=" + eventId + "&actionType=0", data => {
            if (data["result"] === true) {
                removeSectionIfEmpty($(this).parent().parent());
            } else {
                $("main").prepend($("<p>",
                                    {
                                        class: "alert",
                                        text: "Si è verificato un errore. Si prega di ricaricare la pagina"
                                    }));
            }
        });
    });
    $("a[id^='dec_button_']").click(function() {
        const [seatId, eventId] = getDataFromId($(this).attr("id"));
        $.getJSON("manage_tickets.php?seatId=" + seatId + "&eventId=" + eventId + "&actionType=1", data => {
            if (data["result"] === true) {
                const amountLabel = $(this).next();
                const amount = parseInt(amountLabel.text()) - 1;
                if (amount === 0) {
                    removeSectionIfEmpty($(this).parent().parent());
                } else {
                    amountLabel.text(amount + " bigliett" + (amount > 1 ? "i" : "o"));
                }
            } else {
                $("main").prepend($("<p>",
                                    {
                                        class: "alert",
                                        text: "Si è verificato un errore. Si prega di ricaricare la pagina"
                                    }));
            }
        });
    });
    $("a[id^='inc_button_']").click(function() {
        const [seatId, eventId] = getDataFromId($(this).attr("id"));
        $.getJSON("manage_tickets.php?seatId=" + seatId + "&eventId=" + eventId + "&actionType=2", data => {
            if (data["result"] === true) {
                const amountLabel = $(this).prev();
                const amount = parseInt(amountLabel.text()) + 1;
                amountLabel.text(amount + " biglietti");
            } else {
                $("main").prepend($("<p>", 
                                    {
                                        class: "alert",
                                        text: "Si è verificato un errore. Si prega di ricaricare la pagina"
                                    }));
            }
        });
    });
    $("section#payment_types > ul > li").click(function() {
        $("section#payment_types > ul > li").filter((_i, e) => $(e).hasClass("selected")).removeClass("selected");
        $(this).addClass("selected");
    });
    $("#buy_button").click(() => {
        if ($("section#payment_types > ul > li").filter((_i, e) => $(e).hasClass("selected")).length == 0) {
            $("main").prepend($("<p>",
                                {
                                    class: "alert",
                                    text: "Selezionare una modalità di pagamento"
                                }));
        } else {
            window.location.href = "buy_tickets.php";
        }
    });
}); 
