$(document).ready(function () {
    var form = $("form");
    
     //custom email validation rule
    $.validator.addMethod("customemail",
            function (value, element) {
                return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/i.test(value);
            },
            "Invalid email format"
            );
    
    $.validator.addMethod("password",
            function (value, element) {
                return /^[a-z0-9]+$/i.test(value);
            },
            "Only letters and numbers allowed"
            );

   var $validator = $('form').validate({
        //Validering
        rules: {
           workEmail: {
                minlength: 8,
                maxlength: 50,
                required: true,
                customemail: true
            },
            newpassword: {
                minlength: 8,
                maxlength: 32,
                required: true,
                password: true,
            },
            repeatnewpassword: {
                equalTo: "#newpassword",
                minlength: 8,
                maxlength: 32,
                required: true,
                password: true,
            },
        },
        //Error messages
        messages: {
              workEmail: {
                minlength: "Min characters " + 8,
                maxlength: "Max characters " + 50,
                required: "Required field",
                customemail: "Invalid email format"
            },
            newpassword: {
                minlength: "Min characters " + 8,
                maxlength: "Max characters " + 32,
                required: "Required field"
            },
            repeatnewpassword: {
                equalTo: "Passwords must match",
                required: "Required field",
                minlength: "Min characters " + 8,
                maxlength: "Max characters " + 32,
            }

        },
        submitHandler: function (form) {
            form.submit();
        }
    });

});
