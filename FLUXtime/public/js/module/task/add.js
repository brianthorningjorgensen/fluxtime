//Når document er klar valideres input
$(document).ready(function () {
    

    
    $('form').validate({
        errorElement: "p",
        wrapper: "li",
        //place all errors in a <div id="error-div"> element
        errorPlacement: function (error, element) {
            $("div#error-div").append('<label class="control-label white-text">' + element.attr("name") + '</label>');
            error.appendTo("div#error-div").wrap('<strong>');
            $("#error-div p").addClass('white-text');
            $("#error-div").removeClass('hidden');
        },
        //Validering
        rules: {
            taskname: {
                maxlength: 255,
                required: true,
            },
            points: {
                maxlength: 30,
            },
            tasktype: {
                maxlength: 30,
            }
        },
        //Error messages
        messages: {
            taskname: {
                required: 'Required field',
                maxlength: "Max characters " + 255,
            },
            points: {
                maxlength: "Max characters " + 30,
            },
            tasktype: {
                maxlength: "Max characters " + 30,
            }
        },
        //Hvis valideret korrekt kan postes
        submitHandler: function (form) {
            form.submit();
        }
    });

    var form = $("form");

    $("#submitbutton").submit(function (e) {
        e.preventDefault();
        if ($(this).valid() === true) {
            form.submit();
        }
    });

});


