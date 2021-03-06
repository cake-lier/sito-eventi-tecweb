function toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent) {
    const searchParams = new URLSearchParams(window.location.search);
    if (!searchParams.has("id")) {
        $("main").prepend(($("section.alert").length > 0 ? $("section.alert").html("") : $("<section>", {class: "alert"}))
                              .append($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}),
                                      $("<a>", {href: "#"})
                                          .append($("<img>", {src: "img/close.png", alt: "Chiudi"}))));
        return;
    }
    const id = searchParams.get("id");
    $.getJSON("get_seat_categories.php?id=" + id, data => {
        if (data["result"] === false) {
            $("main").prepend(($("section.alert").length > 0 ? $("section.alert").html("") : $("<section>", {class: "alert"}))
                                  .append($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}),
                                          $("<a>", {href: "#"})
                                              .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                              .click(function() {
                                                  $(this).parent().remove();
                                              })));
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
                                          $("main").prepend(($("section.alert").length > 0 ? $("section.alert").html("") : $("<section>", {class: "alert"}))
                                                                .append($("<p>", 
                                                                          {
                                                                              text: "Operazione effettuata con successo"
                                                                          }),
                                                                        $("<a>", {href: "#"})
                                                                          .append($("<img/>",
                                                                                    {
                                                                                         src: "img/close.png",
                                                                                         alt: "Chiudi"
                                                                                    }))
                                                                          .click(function() {
                                                                              $(this).parent().remove();
                                                                          })));
                                          addTicketSpinner.attr("max", parseInt(addTicketSpinner.attr("max")) - ticketsAmount);
                                      } else {
                                          $("main").prepend(($("section.alert").length > 0 ? $("section.alert").html("") : $("<section>", {class: "alert"}))
                                                                .append($("<p>",
                                                                          {
                                                                               text: "Si è verificato un errore. Si prega di \
                                                                                      riprovare"
                                                                          }),
                                                                        $("<a>", {href: "#"})
                                                                            .append($("<img/>",
                                                                                      {
                                                                                           src: "img/close.png",
                                                                                           alt: "Chiudi"
                                                                                      }))
                                                                            .click(function() {
                                                                                $(this).parent().remove();
                                                                            })));
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
        $("main").prepend(($("section.alert").length > 0 ? $("section.alert").html("") : $("<section>", {class: "alert"}))
                              .append($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}),
                                      $("<a>", {href: "#"})
                                          .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                          .click(function() {
                                              $(this).parent().remove();
                                          })));
        return;
    }
    $.getJSON("get_event_seats.php?eventId=" + searchParams.get("id"), seats => {
        if (seats["status"] === false) {
            $("main").prepend(($("section.alert").length > 0 ? $("section.alert").html("") : $("<section>", {class: "alert"}))
                                  .append($("<p>", {text: "Si è verificato un errore. Si prega di ricaricare la pagina"}),
                                          $("<a>", {href: "#"})
                                              .append($("<img/>", {src: "img/close.png", alt: "Chiudi"}))
                                              .click(function() {
                                                  $(this).parent().remove();
                                              })));
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
