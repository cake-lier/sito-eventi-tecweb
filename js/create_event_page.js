$(() => {
    let seatCategoryCount = 1;

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
        const quantityField = $("<input>", {type: "number", step: "1", id: "sCatQuantity_" + seatCategoryCount, name: "seats"});
        seatCategorySection.append(quantityLabel, quantityField);
        const priceLabel = $("<label>", {for: "sCatPrice_" + seatCategoryCount, text: "Prezzo biglietti categoria: "});
        const priceField = $("<input>", {type: "number", step: "any", id: "sCatPrice_" + seatCategoryCount, name: "price"});
        seatCategorySection.append(priceLabel, priceField);
        const removeCatButton = $("<button>", {type: "button", text: "Rimuovi categoria"});
        seatCategorySection.append(removeCatButton);
        removeCatButton.click(_e => {
            seatCategorySection.remove();
        });
        seatCategoryCount++;
    });

    $("#categories").on("input", () => {
        let text = $("#categories").val();
        const regex = /((^| )[\w])/gm;
        text = text.replace(regex, x => {
            if (x.charAt(0) === " ") {
                return " #" + x.substr(1, x.length);
            } else {
                return "#" + x;
            }
        });
        $("#categories").val(text.replace(/[@$%^&()=\[\]{};':"\\|,<>\/]/gm, ""));
    });

    $("form").submit(e => {
        e.preventDefault();
        const formObj = {};
        formObj["name"] = $("#name").val();
        formObj["place"] = $("#place").val();
        formObj["dateTime"] = $("#dateTime").val();
        formObj["description"] = $("#description").val();
        formObj["website"] = $("#website").val();
        formObj["eventCategories"] = $("#categories").val().replace(/(#)/g, "").split(" ");
        const seatCatArray = [];
        $(".seat_category_section").each(function() {
            const catObj = {};
            $(this).children().each((_index, element) => {
                const el = $(element);
                if (el.is("input")) {
                    catObj[el.attr("name")] = el.val();
                }
            });
            seatCatArray.push(catObj);
        });
        formObj["seatCategories"] = seatCatArray;
        $.post("create_event.php", formObj, data => {
            $("#result").remove();
            $("form").append($("<p>", {id: "result", text: data.result}));
        });
    });
});