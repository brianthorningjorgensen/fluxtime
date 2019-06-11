
//Når document er klar valideres input
$(document).ready(function () {
    $(".fa-frown-o").rotate({
        bind:
                {
                    mouseover: function () {
                        $(this).rotate({animateTo: 180})
                    },
                    mouseout: function () {
                        $(this).rotate({animateTo: 0})
                    }
                }

    });

    $("#hideable").hide();

    $("#clickable").on("click", function () {
        $('#hideable').toggle();
    });
});
