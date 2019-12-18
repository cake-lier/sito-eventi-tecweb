$(() => {
    let seatCategoryCount = 1;
    $("#categories_section > button").click(function(e) {
        e.preventDefault();
        $(this).before($("<section>").addClass("seat_category_section")
                                     .append($("<label>", {for: "sCatName_" + seatCategoryCount, text: "Tipologia posto: "})
                                                 .focus(),
                                             $("<input>", {type: "text", id: "sCatName_" + seatCategoryCount, name: "name"}),
                                             $("<label>", {
                                                   for: "sCatQuantity_" + seatCategoryCount,
                                                   text: "Quantità biglietti: "
                                               }),
                                             $("<input>", {
                                                   type: "number",
                                                   step: "1",
                                                   id: "sCatQuantity_" + seatCategoryCount,
                                                   name: "seats"
                                               }),
                                             $("<label>", {for: "sCatPrice_" + seatCategoryCount, text: "Prezzo biglietti: "}),
                                             $("<input>", {
                                                 type: "number",
                                                 step: "any",
                                                 id: "sCatPrice_" + seatCategoryCount,
                                                 name: "price"
                                               }),
                                             $("<button>", {type: "button", text: "Rimuovi categoria"})
                                                 .click(function() { 
                                                     $(this).parent().remove();
                                                     seatCategoryCount--;
                                                 })));
        seatCategoryCount++;
    });
    $("#categories").on("input", () => {
        let text = $("#categories").val();
        text = text.replace(/((^| )[\w])/gm, x => {
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
        $(".seat_category").each(function() {
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