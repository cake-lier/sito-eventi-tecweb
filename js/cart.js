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
                $("main").prepend($("<section>", {class: "alert"})
                                      .append($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}),
                                              $("<a>", {href: "#"})
                                                  .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                                  .click(function() {
                                                      $(this).parent().remove();
                                                  })));
            }
        });
    });
    $("a[id^='dec_button_']").click(function(e) {
        e.preventDefault();
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
                $("main").prepend($("<section>", {class: "alert"})
                                      .append($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}),
                                              $("<a>", {href: "#"})
                                                  .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                                  .click(function() {
                                                      $(this).parent().remove();
                                                  })));
            }
        });
    });
    $("a[id^='inc_button_']").click(function(e) {
        e.preventDefault();
        const [seatId, eventId] = getDataFromId($(this).attr("id"));
        $.getJSON("manage_tickets.php?seatId=" + seatId + "&eventId=" + eventId + "&actionType=2", data => {
            if (data["result"] === true) {
                const amountLabel = $(this).prev();
                const amount = parseInt(amountLabel.text()) + 1;
                amountLabel.text(amount + " biglietti");
            } else {
                $("main").prepend($("<section>", {class: "alert"})
                                      .append($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}),
                                              $("<a>", {href: "#"})
                                                  .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                                  .click(function() {
                                                      $(this).parent().remove();
                                                  })));
            }
        });
    });
    $("section#payment_types > ul > li").click(function() {
        $("section#payment_types > ul > li").filter((_i, e) => $(e).hasClass("selected")).removeClass("selected");
        $(this).addClass("selected");
    });
    let finalizePurchase = false;
    $("#cart_payment_section > section > section:first-child").hide();
    $("#buy_button").click(() => {
        const paymentSection = $("#cart_payment_section");
        if (!finalizePurchase) {
            if ($("section#payment_types > ul > li").filter((_i, e) => $(e).hasClass("selected")).length == 0) {
                $("main").prepend($("<section>", {class: "alert"})
                                      .append($("<p>", {text: "Selezionare una modalità di pagamento"}),
                                              $("<a>", {href: "#"})
                                                  .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                                  .click(function() {
                                                      $(this).parent().remove();
                                                  })));
            } else {
                const header = paymentSection.children("header");
                header.children("p:first-child").css("font-weight", "normal");
                header.children("p:last-of-type").css("font-weight", "bold");
                header.children("svg").children("g").children("circle").animate({"cx": "429"});
                const body = paymentSection.children("section");
                body.children("section:first-child").show();
                const paymentTypesSection = body.children("section:last-of-type");
                paymentTypesSection.children("h1").hide();
                paymentTypesSection.children("ul").children("li").filter((_i, e) => !$(e).hasClass("selected")).hide();
                paymentSection.children("section").children("a").text("Compra e paga");
                const tickets = $("#tickets > section");
                tickets.children("section").children("a").hide();
                tickets.children("footer").hide();
                finalizePurchase = true;
            }
        } else {
            window.location.href = "buy_tickets.php";
        }
    });
}); 
