//Når document er klar valideres input
$(document).ready(function () {
    
    var form = $("form");
    
     $.validator.addMethod("trim",
            function (value, element) {
                  return /^[a-zA-Z0-9]+(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?$/.test(value);
            },
            "Remove whitespace from start or end"
            );
    
    $validator = $('form').validate({
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
            projectname: {
                maxlength: 50,
                required: true,
                trim: true
            },
            client: {
                maxlength: 50,
                required: false
            },
            fkProjectmanager: {
                required: false
            }
        },
        messages: {
            projectname: {
                maxlength: "Max characters " + 50,
                required: 'Required field',
            },
            client: {
                maxlength: "Max characters " + 50,
            },
        },
        //Hvis valideret korrekt kan postes
        submitHandler: function (form) {
            form.submit();
        }
    });

    

    $("#submitbutton").submit(function (e) {
        if ($(this).valid() === true) {
            form.submit();
        }
    });

    
});


