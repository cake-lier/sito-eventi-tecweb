$(() => {
    $("form").submit(e => {
        e.preventDefault();
        $.post("modify_event.php", $("form").serialize(), data => {
            $("form > footer > p").remove();
            $("form > footer").append($("<p>", {text: data.result}));
        });
    });
});