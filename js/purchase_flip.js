function toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent) {
    const searchParams = new URLSearchParams(window.location.search);
    if (!searchParams.has("id")) {
        alert("Si è verificato un errore. Per favore ricaricare la pagina");
        return;
    }
    const id = searchParams.get("id");
    $.getJSON("get_seat_categories.php?id=" + id, data => {
        if (data["result"] === false) {
            alert("Si è verificato un errore. Per favore ricaricare la pagina");
            return;
        }
        const seatCategories = {};
        const seatCategoriesSent = data["seatCategories"];
        seatCategoriesSent.forEach((row, index) => {
            const tableRow = {
                "Categoria": row["name"],
                "Posti liberi su totali": (row["seats"] - row["occupiedSeats"]) + " su " + row["seats"],
                "Prezzo": row["price"].toFixed(2) + "€"
            };
            seatCategories[index] = tableRow;
        });
        const table = $("<table>");
        const headerRow = $("<tr>");
        Object.keys(seatCategories[0]).concat("Quantità", "").forEach(headerName => {
            headerRow.append($("<th>", {text: headerName, id: headerName.replace(" ", "").toLowerCase()}));
        });
        table.append(headerRow);
        Object.values(seatCategories).forEach((category, index) => {
            const row = $("<tr>");
            Object.entries(category).forEach(([key, value]) => {
                row.append($("<td>", {text: value, headers: key.replace(" ", "").toLowerCase()}));
            });
            row.append($("<td>").append($("<input>", {
                value: 0,
                type: "number",
                name: "addTicketsCategory" + seatCategoriesSent[index]["id"],
                min: 0,
                max: seatCategoriesSent[index]["seats"] - seatCategoriesSent[index]["occupiedSeats"]
            })));
            row.append($("<td>").append($("<input>", {
                type: "button", 
                value: "Aggiungi al carrello",
                click: function() {
                    const addTicketSpinner = $(this).parent().prev().children();
                    const ticketsAmount = parseInt(addTicketSpinner.val());
                    if (ticketsAmount > 0) {
                        $.getJSON("add_to_cart.php?seatId=" + seatCategoriesSent[index]["id"] + "&eventId="
                                  + seatCategoriesSent[index]["eventId"] + "&amount=" + ticketsAmount,
                                  data => {
                                      if (data["result"] === true) {
                                          alert("Operazione effettuata con successo");
                                          addTicketSpinner.attr("max", parseInt(addTicketSpinner.attr("max")) - ticketsAmount);
                                      } else {
                                          alert("C'è stato un errore nell'eseguire l'operazione. Si prega di riprovare");
                                      }
                                  });
                    }
                }
            })));
            table.append(row);
        });
        purchaseSection.html(table);
    });
    purchaseButton.off("click");
    purchaseButton.click(() => toEventDescription(purchaseSection, purchaseButton, purchaseSectionContent));
    purchaseButton.children().attr({
        "src": "img/back.png",
        "alt": "Torna alla descrizione"
    });
}

function toEventDescription(purchaseSection, purchaseButton, purchaseSectionContent) {
    purchaseSection.html(purchaseSectionContent);
    const freeSeatsPar = purchaseSection.children()[0];
    const searchParams = new URLSearchParams(window.location.search);
    if (!searchParams.has("id")) {
        alert("Si è verificato un errore. Per favore ricaricare la pagina");
        return;
    }
    $.getJSON("get_event_seats.php?eventId=" + searchParams.get("id"), seats => {
        if (seats["status"] === false) {
            alert("Si è verificato un errore. Per favore ricaricare la pagina");
            return;
        }
        freeSeatsPar.text("Posti ancora disponibili: " + seats["freeSeats"] + " su " + seats["totalSeats"]);
    });
    purchaseButton.off("click");
    purchaseButton.click(() => toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent));
    purchaseButton.children().attr({
        "src": "img/cart.png",
        "alt": "Vai all'acquisto"
    });
}

$(() => {
    const purchaseSection = $("#purchaseSection");
    const purchaseSectionContent = purchaseSection.html();
    const purchaseButton = $("#purchaseButton");
    purchaseButton.click(() => toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent));
});
