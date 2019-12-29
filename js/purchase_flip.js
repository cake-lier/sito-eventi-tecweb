function toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent) {
    const searchParams = new URLSearchParams(window.location.search);
    if (!searchParams.has("id")) {
        $("main").prepend($("<p>",
                            {
                                class: "alert",
                                text: "Si è verificato un errore. Per favore ricaricare la pagina"
                            }));
        return;
    }
    const id = searchParams.get("id");
    $.getJSON("get_seat_categories.php?id=" + id, data => {
        if (data["result"] === false) {
            $("main").prepend($("<p>",
                                {
                                    class: "alert",
                                    text: "Si è verificato un errore. Per favore ricaricare la pagina"
                                }));
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
        Object.values(seatCategories).forEach((category, index) => {const headerRow = $("<tr>");
            Object.keys(seatCategories[0]).concat("Quantità", "Acquista").forEach(headerName => {
                headerRow.append($("<th>", {text: headerName, id: headerName.replace(/ /g, "_").toLowerCase()}));
            });
            table.append(headerRow);
            const row = $("<tr>");
            Object.entries(category).forEach(([key, value]) => {
                row.append($("<td>", {text: value, headers: key.replace(/ /g, "_").toLowerCase()}));
            });
            row.append($("<td>").append($("<input>", {
                value: 0,
                type: "number",
                name: "addTicketsCategory" + seatCategoriesSent[index]["id"],
                min: 0,
                max: seatCategoriesSent[index]["seats"] - seatCategoriesSent[index]["occupiedSeats"],
                step: 1,
                headers: "quantità"
            })));
            row.append($("<td>").append($("<a>", {
                href: "#",
                click: function() {
                    const addTicketSpinner = $(this).parent().prev().children();
                    const ticketsAmount = parseInt(addTicketSpinner.val());
                    if (ticketsAmount > 0) {
                        $.getJSON("add_to_cart.php?seatId=" + seatCategoriesSent[index]["id"] + "&eventId="
                                  + seatCategoriesSent[index]["eventId"] + "&amount=" + ticketsAmount,
                                  data => {
                                      if (data["result"] === true) {
                                          $("main").prepend($("<p>",
                                                              {
                                                                  class: "alert",
                                                                  text: "Operazione effettuata con successo"
                                                              }));
                                          addTicketSpinner.attr("max", parseInt(addTicketSpinner.attr("max")) - ticketsAmount);
                                      } else {
                                          $("main").prepend($("<p>",
                                                              {
                                                                   class: "alert", 
                                                                   text: "C'è stato un errore nell'eseguire l'operazione. Si \
                                                                          prega di riprovare"}));
                                      }
                                  });
                    }
                }
                                          }).append($("<img>", {
                                                        src: "img/cart.png",
                                                        alt: "acquista"
                                                    }))
                ));
            table.append(row);
        });
        purchaseSection.html(table);
    });
    purchaseButton.off("click")
                  .click(() => toEventDescription(purchaseSection, purchaseButton, purchaseSectionContent))
                  .children()
                  .attr({
                      "src": "img/back.png",
                      "alt": "Torna alla descrizione"
                  });
}

function toEventDescription(purchaseSection, purchaseButton, purchaseSectionContent) {
    purchaseSection.html(purchaseSectionContent);
    const freeSeatsPar = purchaseSection.children()[0];
    const searchParams = new URLSearchParams(window.location.search);
    if (!searchParams.has("id")) {
        $("main").prepend($("<p>", {text: "Si è verificato un errore. Per favore ricaricare la pagina"}));
        return;
    }
    $.getJSON("get_event_seats.php?eventId=" + searchParams.get("id"), seats => {
        if (seats["status"] === false) {
            $("main").prepend($("<p>", {text: "Si è verificato un errore. Per favore ricaricare la pagina"}));
            return;
        }
        $(freeSeatsPar).text("Posti ancora disponibili: " + seats["freeSeats"] + " su " + seats["totalSeats"]);
    });
    purchaseButton.off("click")
                  .click(() => toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent))
                  .children()
                  .attr({
                      "src": "img/cart.png",
                      "alt": "Vai all'acquisto"
                  });
}

$(() => {
    const purchaseSection = $("#purchase_section");
    const purchaseSectionContent = purchaseSection.html();
    const purchaseButton = $("#purchase_button");
    purchaseButton.click(() => toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent));
});
