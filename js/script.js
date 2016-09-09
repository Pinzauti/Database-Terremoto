$(function () {
    var a = $("#database"), b = $("#result");
    $(a).submit(function (c) {
        $("#gif").css("visibility", "visible"), c.preventDefault();
        var d = $(a).serialize();
        $.ajax({type: "POST", url: "php/database.php", data: d}).done(function (a) {
            $("#gif").css("visibility", "hidden"), $(b).text("Database Aggiornati!")
        }).fail(function (a) {
            $("#gif").css("visibility", "hidden"), "" !== a.responseText ? $(b).text(a.responseText) : $(b).text("Errore")
        })
    })
});