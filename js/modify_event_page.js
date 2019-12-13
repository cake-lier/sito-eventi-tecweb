$(() => {
    $("form").submit(e => {
        e.preventDefault();
        $.post("modify_event.php", $("form").serialize(), data => {
            $("form > footer > p").remove();
            console.log("hi");
            $("form > footer").append($("<p>", {text: data.result}));
        });
    });
});