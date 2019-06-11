$(document).ready(function () {
    
 var form = $("form");

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
        rules: {
            privateEmail: {
                maxlength: 50
            },
            oldpassword: {
                minlength: 8,
                maxlength: 32,
                required: true,
            },
            password: {
                minlength: 8,
                maxlength: 32,
            },
            phonePrivate: {
                maxlength: 20
            }
        },
        messages: {
            oldpassword: {
                required: "Required field",
                minlength: "Min characters " + 8,
                maxlength: "Max characters " + 32,
            },
            password: {
                minlength: "Min characters " + 8,
                maxlength: "Max characters " + 32,
            },
            privateEmail: {
                maxlength: "Max characters " + 50,
            },
            phonePrivate: {
                maxlength: "Max characters " + 20
            }
        },
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
