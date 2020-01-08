$(() => {
    $("form").submit(e => {
        e.preventDefault();
        $.post("modify_event.php", $("form").serialize(), data => {
            $("form > p:last-of-type").remove();
            $("form").append($("<p>", {text: data.result}));
        });
    });
});