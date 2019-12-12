$(() => {
    let seatCategoryCount = 0;

    $("form > button").click(e => {
        e.preventDefault();
        const seatCategorySection = $("<section>");
        seatCategorySection.addClass("seat_category_section");
        $("form > button").before(seatCategorySection);
        const nameLabel = $("<label>", {for: "sCatName_" + seatCategoryCount, text: "Nome categoria: "});
        const nameField = $("<input>", {type: "text", id: "sCatName_" + seatCategoryCount, name: "name"});
        seatCategorySection.append(nameLabel, nameField);
        nameLabel.focus();
        const quantityLabel = $("<label>", {for: "sCatQuantity_" + seatCategoryCount, text: "Quantità biglietti categoria: "});
        const quantityField = $("<input>", {type: "number", id: "sCatQuantity_" + seatCategoryCount, name: "seats"});
        seatCategorySection.append(quantityLabel, quantityField);
        const priceLabel = $("<label>", {for: "sCatPrice_" + seatCategoryCount, text: "Prezzo biglietti categoria: "});
        const priceField = $("<input>", {type: "text", id: "sCatPrice_" + seatCategoryCount, name: "price"});
        seatCategorySection.append(priceLabel, priceField);
        const removeCatButton = $("<button>", {type: "button", text: "Rimuovi categoria"});
        seatCategorySection.append(removeCatButton);
        removeCatButton.click(e => {
            seatCategorySection.remove();
        });
        seatCategoryCount++;
    });

    $("form").submit(e => {
        e.preventDefault();
        const formObj = {};
        formObj["name"] = $("#name").val();
        formObj["place"] = $("#place").val();
        formObj["dateTime"] = $("#dateTime").val();
        formObj["description"] = $("#description").val();
        formObj["website"] = $("#website").val();
        // TODO: event categories
        if ($(".seat_category_section").length > 0) {
            const seatCatArray = [];
            $(".seat_category_section").each(function() {
                const catObj = {};
                $(this).children().each((index, element) => {
                    const el = $(element);
                    if (el.is("input")) {
                        catObj[el.attr("name")] = el.val();
                        // TODO: check if the price is a number
                    }
                });
                seatCatArray.push(catObj);
            });
            formObj["seatCategories"] = seatCatArray;
        } else {
            const pError = $("<p>", {text: "Occorre inserire almeno una categoria di biglietti!"});
            $("form > header").after(pError);
        }
        console.log(formObj);
        $.post("create_event.php", formObj, data => {
            $("main").html("");
            $("main").append($("<p>", {text: data.result}));
        });
    });
});