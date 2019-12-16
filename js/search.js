function onNewTagInserted() {
    const searchTagsText = $("#tags").val();
    const lastTagBegin = searchTagsText.search(/[^\s#]+$/m);
    if (lastTagBegin !== -1) {
        const tag = searchTagsText.substr(lastTagBegin, searchTagsText.length);
        $("#tags").val(searchTagsText.substr(0, lastTagBegin) + "#" + tag);
    }
}

$(() => {
    const spacebarPressed = 32;
    const enterPressed = 13;
    $("#tags").keypress(e => {
        if (e.which === spacebarPressed || e.which === enterPressed) {
            onNewTagInserted();
        }
    }).focusout(() => onNewTagInserted());
    $("#keywords").keypress(e => {
        if (e.which === enterPressed) {
            onNewTagInserted();
        }
    });
});