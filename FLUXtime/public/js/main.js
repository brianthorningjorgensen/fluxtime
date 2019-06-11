
//Når document er klar valideres input
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $('#search-popover').mouseenter(function () {
        $('#search-popover').popover('show');
    });
    
    $('#search-popover').mouseleave(function () {
        $('#search-popover').popover('hide');
    });
    

    $('#img_wrap').on( 'mouseenter', function() {
         $(this).find("#animated").show();
         $(this).find("#static").hide();
    });
    
    $('#img_wrap').on( 'mouseleave', function() {
        $(this).find("#animated").hide();
        $(this).find("#static").show();
    });

});