$(document).ready(function () {

    $.validator.addMethod("trim",
            function (value, element) {
                return /^[a-zA-Z0-9]+(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?$/.test(value);
            },
          "Remove whitespace from start or end"
            );
    
    //custom email validation rule
    $.validator.addMethod("customemail",
            function (value, element) {
                return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/i.test(value);
            },
            "Invalid email format"
            );
    
    $.validator.addMethod("password",
            function (value, element) {
                return /^[a-zA-Z0-9]+$/i.test(value);
            },
            "Only letters and numbers allowed"
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
            workEmail: {
                minlength: 8,
                maxlength: 50,
                required: true,
                customemail: true,
            },
            username: {
                minlength: 2,
                maxlength: 20,
                required: true,
                trim: true,
            },
            password: {
                minlength: 8,
                maxlength: 32,
                required: true,
                password: true,
            },
            employeeId: {
                maxlength: 30,
            },
            firstname: {
                required: true,
                maxlength: 40
            },
            lastname: {
                required: true,
                maxlength: 40
            },
            phone: {
                maxlength: 20
            },
            privateEmail: {
                maxlength: 50
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
            phonePrivate: {
                maxlength: 20
            }
        },
        //Error messages
        messages: {
            workEmail: {
                minlength: "Min characters " + 8,
                maxlength: "Max characters " + 50,
                required: "Required field",
                customemail: "Invalid email format"
            },
            username: {
                minlength: "Min characters " + 2,
                maxlength: "Max characters " + 20,
                required: "Required field"
            },
            password: {
                minlength: "Min characters " + 8,
                maxlength: "Max characters " + 32,
                required: "Required field"
            },
            employeeId: {
                maxlength: "Max characters " + 20
            },
            firstname: {
                required: "Required field",
                maxlength: "Max characters " + 40
            },
            lastname: {
                required: "Required field",
                maxlength: "Max characters " + 40
            },
            phone: {
                maxlength: "Max characters " + 20
            },
            privateEmail: {
                maxlength: "Max characters " + 50
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
            phonePrivate: {
                maxlength: "Max characters " + 20
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



    /* Fetch pivotal tracker api token */
    $('.fetchToken').on('click', function (e) {
        e.preventDefault();
        $('#myModal').modal('toggle');
    });

    $('#confirmButton').on("click", function (e) {
        e.preventDefault();
        $('#myModal').modal('hide');
        var user = $('#myModal .modal-body').find('.user').val();
        var pass = $('#myModal .modal-body').find('.pass').val();

        $.ajax({
            type: "POST",
            url: "/fluxuser/fetchApiToken",
            data: {user: user, pass: pass},
            dataType: 'JSON',
            success: function (e) {
                if (e["apitoken"] !== null) {
                    $('#api').val(e["apitoken"]);
                }
            }
        });
    });

    $('.deleteToken').on('click', function (e) {
        e.preventDefault();
        var userid = $('#userid').val();

        $.ajax({
            type: "POST",
            url: "/fluxuser/deleteApiToken",
            data: {userid: userid},
            dataType: 'JSON',
            success: function (e) {
                $('#api').val("");
            }
        });
    });
});
