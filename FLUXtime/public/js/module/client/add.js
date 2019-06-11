//Når document er klar valideres input
$(document).ready(function () {
    
    var form = $("form");

     $.validator.addMethod("trim",
            function (value, element) {
              return /^[a-zA-Z0-9]+(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?$/.test(value);
            },
           "Remove whitespace from start or end"
            );

    var $validator = $('form').validate({
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
            email: {
                maxlength: 50               
            },
            clientname: {
                maxlength: 50,
                required: true,
                trim: true,
            },
            cvrid: {
                maxlength: 30,
            },
            phone: {
                maxlength: 20,
              
            },
            street: {
                maxlength: 50
            },
            houseNumber: {
                maxlength: 10
            },
            city: {
                maxlength: 50
            },
            zipCode: {
                maxlength: 10
            },
            country: {
                maxlength: 50
            },
        },
        //Error messages
        messages: {
            email: {
                maxlength: "Max characters " + 50
            },
            clientname: {
                maxlength: "Max characters " + 100,
                required: "Required field"
            },
            cvrid: {
                maxlength: "Max characters " + 30
            },
            phone: {
                maxlength: "Max characters " + 20
            },
            street: {
                maxlength: "Max characters " + 50
            },
            houseNumber: {
                maxlength: "Max characters " + 10
            },
            city: {
                maxlength: "Max characters " + 50
            },
            zipCode: {
                maxlength: "Max characters " + 10
            },
            country: {
                maxlength: "Max characters " + 50
            },
        },
        //Hvis valideret korrekt kan postes
        submitHandler: function (form) {
            form.submit();
        }
    });

    

    $("#submitbutton").submit(function (e) {
        e.preventDefault();
        if ($(this).valid() === true) {
            form.submit();
        }
    });

   



});
