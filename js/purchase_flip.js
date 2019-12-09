function toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent) {
    const searchParams = new URLSearchParams(window.location.search);
    if (!searchParams.has("id")) {
        alert("An error has occured while fetching for seats. Please reload the page");
        return;
    }
    const id = searchParams.get("id");
    $.getJSON("get_seat_categories.php?id=" + id, data => {
        const seatCategories = {};
        data.forEach((row, index) => {
            const tableRow = {
                "Categoria": row["name"],
                "Posti liberi su totali": (row["seats"] - row["occupiedSeats"]) + " su " + row["seats"],
                "Prezzo": row["price"].toFixed(2) + "€"
            };
            seatCategories[index] = tableRow;
        });
        const table = $(document.createElement("table"));
        const headerRow = $(document.createElement("tr"));
        const headerNames = Object.keys(seatCategories[0]);
        headerNames.concat("Quantità", "").forEach(headerName => {
            const header = $(document.createElement("th"));
            header.text(headerName);
            header.attr("id", headerName.replace(" ", "").toLowerCase());
            headerRow.append(header);
        });
        table.append(headerRow);
        Object.values(seatCategories).forEach((category, index) => {
            const row = $(document.createElement("tr"));
            Object.entries(category).forEach(([key, value]) => {
                const cell = $(document.createElement("td"));
                cell.text(value);
                cell.attr("headers", key.replace(" ", "").toLowerCase());
                row.append(cell);
            });
            const addTicketsCell = $(document.createElement("td"));
            const addTicketSpinner = $(document.createElement("input"));
            addTicketSpinner.attr({
                "value": 0,
                "type": "number",
                "name": "addTicketsCategory" + data[index]["id"],
                "min": 0,
                "max": data[index]["seats"] - data[index]["occupiedSeats"]
            });
            addTicketsCell.append(addTicketSpinner);
            row.append(addTicketsCell);
            const buyTicketsCell = $(document.createElement("td"));
            const buyTicketsButton = $(document.createElement("input"));
            buyTicketsButton.attr({
                "type": "button",
                "value": "Aggiungi al carrello"
            });
            buyTicketsButton.click(() => {
                const ticketsAmount = parseInt(addTicketSpinner.get(0).value);
                if (ticketsAmount > 0) {
                    $.getJSON("add_to_cart.php?seatId=" + data[index]["id"] + "&eventId=" + data[index]["eventId"] + "&amount="
                          + ticketsAmount,
                          result => {
                              if (result === "success") {
                                  alert("Operazione effettuata con successo");
                              } else {
                                  alert("C'è stato un errore nell'eseguire l'operazione. Si prega di riprovare");
                              }
                          });
                }
            });
            buyTicketsCell.append(buyTicketsButton);
            row.append(buyTicketsCell);
            table.append(row);
        });
        purchaseSection.html(table);
    });
    purchaseButton.off("click");
    purchaseButton.click(() => toEventDescription(purchaseSection, purchaseButton, purchaseSectionContent));
}

function toEventDescription(purchaseSection, purchaseButton, purchaseSectionContent) {
    purchaseSection.html(purchaseSectionContent);
    const freeSeatsPar = purchaseSection.children()[0];
    const searchParams = new URLSearchParams(window.location.search);
    if (!searchParams.has("id")) {
        alert("An error has occured while fetching for seats. Please reload the page");
        return;
    }
    $.getJSON("get_event_seats.php?eventId=" + searchParams.get("id"), seats => {
        if (seats["status"] === "failure") {
            alert("An error has occured while fetching for this event free sets. Please reload the page");
            return;
        }
        freeSeatsPar.text("Posti ancora disponibili: " + seats["freeSeats"] + " su " + seats["totalSeats"]);
    });
    purchaseButton.off("click");
    purchaseButton.click(() => toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent));
}

$(function() {
    const purchaseSection = $("#purchaseSection");
    const purchaseSectionContent = purchaseSection.html();
    const purchaseButton = $("#purchaseButton");
    purchaseButton.click(() => toSeatsTable(purchaseSection, purchaseButton, purchaseSectionContent));
});