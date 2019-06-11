//Når document er klar valideres input
$(document).ready(function () {
    
      //Validate account edit
    $('#mycontactform').validate({        
        //Validering
        rules: {
            firstname: {
                maxlength: 40,
                required: true,
                trim: true
            },
            lastname: {
                maxlength: 40,
                required: true,
                trim: true
            },
            phone: {
                maxlength: 20,
                required: true                
            },
            email: {
                maxlength: 50,
                required: true                
            }
            
        },
        //Error messages
        messages: {
            firstname: {
                required: "Required field",
                maxlength: "Max characters " + 40
            },
            lastname: {
                maxlength: "Max characters " + 40
            },
            phone: {
                maxlength: "Max characters " + 20
            },
            email: {
                maxlength: "Max characters " + 50
            }
        },
        //Hvis valideret korrekt kan postes
        submitHandler: function (form) {
            form.submit();
        }
    });
    
    
      //Confirm delete
    $('#confirmcontactdeleteButton').on("click", function (e) {
        e.preventDefault();
        $('#deletecontactModal').modal('hide');
        var id = $('#deletecontactModal .modal-body').find('#contactIdDelete').html();
        $.ajax({
            type: "POST",
            url: "/contact/ajaxdelete",
            data: {id: id},
            dataType: 'JSON',
            complete: function (data) {
                var ok = data['responseText'];
                if (ok.indexOf("true") >= 0) {
                    //Slet række i tabel
                    $("#contactrow"+id).closest('tr').hide();
                }
            }
        });
    });


    //Save contact - edit
    $('#confirmEditContactButton').on('click', function (e) {
        e.preventDefault();
        $contactform = $('#contactform');
        $contactform.validate();
        if ($contactform.valid()) {
          
            $('#myModalContactEdit').modal('hide');
            var firstname = $('#firstname').val();
            var lastname = $('#lastname').val();
            var phone = $('#phone').val();
            var email = $('#email').val();
            var description = $('#description').val();
            var id = $('#contactid').val();

            $.ajax({
                type: "POST",
                url: "/contact/ajaxedit",
                dataType: 'JSON',
                data: $('#contactform').serialize(),
                complete: function (data) {
                    var ok = data['responseText'];
                    if (ok.indexOf("true") >= 0) {
                        var newRow = $("#contactrow"+id);          
                       newRow.find("#rowfirst").html( firstname );
                       newRow.find("#rowlast").html( lastname );
                       newRow.find("#rowphone").html( phone );
                       newRow.find("#rowemail").html( email );
                       newRow.find("#rowdescription").html( description );
                       var editbutton = newRow.find("#editButton");
                       
                       editbutton.attr("data-contactfirstname", firstname);
                       editbutton.attr("data-contactlastname", lastname);
                       editbutton.attr("data-contactphone", phone);
                       editbutton.attr("data-contactemail", email);
                       editbutton.attr("data-contactdescription", description);
                       
                       var deletebutton = newRow.find("#deleteButton");
                       deletebutton.attr("data-contactname", firstname + ' ' + lastname);
                       
                       
                    }  if (ok.indexOf("false") >= 0) {
                          $('#myModalConfigError .modal-body').find('#errortext').html('Sorry, cannot edit contact already exists.');
                    $('#myModalConfigError').modal('toggle');
                    }
                }
            });
        } 
    });

    //Save contact - edit
    $('#confirmCreateContactButton').on('click', function (e) {
        e.preventDefault();
        $contactform = $('#contactform');
        $contactform.validate();
       
        if ($contactform.valid()) {
            $('#myModalContactEdit').modal('hide');
            var firstname = $('#firstname').val();
            var lastname = $('#lastname').val();
            var phone = $('#phone').val();
            var email = $('#email').val();
            var description = $('#description').val();
            var clientid = $('#clientid').val();           

            $.ajax({
                type: "POST",
                url: "/contact/ajaxadd",
                dataType: 'JSON',
              data: $('#contactform').serialize(),
                complete: function (data) {
                    var txt = data['responseText'];
                    var newid = txt.match(/\d/g);
                    newid = newid.join("");
                    if (txt.indexOf("newid") > -1) {
                        var contactid = newid;
                        var newRow = "";
                        newRow += '<tr id="contactrow' + contactid + '">';
                        newRow += '<td id="rowcid" hidden="true">' + clientid + '</td>';                     
                        newRow += '<td id="rowcoid" hidden="true">' + contactid + '</td>';                     
                        newRow += '<td id="rowfirst">' + firstname + '</td>';    
                        newRow += '<td id="rowlast">' + lastname + '</td>';    
                        newRow += '<td id="rowphone">' + phone + '</td>';    
                        newRow += '<td id="rowemail">' + email + '</td>';    
                        newRow += '<td id="rowdescription" hidden="true">' + description +'</td>';    
                        newRow += '<td>';
                        newRow += '<a id="editButton" class="fa fa-edit fa-2x editContact" Style="padding-right: 20px" href="" data-clientid="'+clientid+'" data-contactid="'+contactid+'" data-contactfirstname="'+firstname+'" data-contactlastname="'+lastname+'" data-contactphone="'+phone+'" data-contactemail="'+email+'" data-contactdescription="'+description+'" title="Edit contact"></a>';
                        newRow += '<a id="deleteButton" class="fa fa-trash-o fa-2x deleteContact" href="" data-contactname="'+firstname + ' ' + lastname +'" data-contactid="'+contactid+'" title="Delete contact"></a>';                             
                        newRow += '</td>';
                        newRow += '</tr>';
                        $('#contactList tbody').append( newRow );
                        $('#myModalContactEdit').modal("hide");
                    } if (ok.indexOf("false") >= 0) {
                            $('#myModalConfigError .modal-body').find('#errortext').html('Sorry, cannot edit contact already exists.');
                            $('#myModalConfigError').modal('toggle');
                    }
                }
            });
        } 
    });
    
  $.validator.addMethod("trim",
            function (value, element) {
             return /^[a-zA-Z0-9]+(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?$/.test(value);
            },
             "Remove whitespace from start or end"
            );

    //Validate account edit
    $('#accountEdit').validate({
        //Validering
        rules: {
            customer: {
                maxlength: 100,
                required: true,
                trim: true,
            },
            customerid: {
                maxlength: 50,
                required: false,
            },
        },
        //Error messages
        messages: {
            customer: {
                required: "Required field",
                maxlength: "Max characters " + 100
            },
            customerid: {
                maxlength: "Max characters " + 50
            },
        },
        //Hvis valideret korrekt kan postes
        submitHandler: function (form) {
            form.submit();
        }
    });
    
    
//Vise confirm-popup
$(document).delegate('.deleteContact', 'click', function (e) {
    e.preventDefault();
    $('#deletecontactModal .modal-body').find('#contactdeletetext').html($(this).data('contactname'));
    $('#deletecontactModal .modal-body').find('#contactIdDelete').html($(this).data('contactid'));
    $('#deletecontactModal').modal('toggle');
});

//Vise edit-popup
$(document).delegate('.editContact', 'click', function (e) {
    e.preventDefault();
    $('#contactid').val($(this).data('contactid'));
    $('#firstname').val($(this).data('contactfirstname'));
    $('#lastname').val($(this).data('contactlastname'));
    $('#phone').val($(this).data('contactphone'));
    $('#email').val($(this).data('contactemail'));
    $('#description').val($(this).data('contactdescription'));
    $('#clientid').val($(this).data('clientid'));
    $('#myModalContactEdit').modal('toggle');
    $('#myModalContactEdit #confirmEditContactButton').show();
    $('#myModalContactEdit #confirmCreateContactButton').hide();
});

//add 
$(document).delegate('.addContact', 'click', function (e) {
    e.preventDefault();
    $('#contactid').val('');
    $('#firstname').val('');
    $('#lastname').val('');
    $('#phone').val('');
    $('#email').val('');
    $('#description').val('');
    $('#clientid').val($(this).data('clientid'));
    $('#myModalContactEdit h4').text("New contact");
    $('#myModalContactEdit #confirmEditContactButton').hide();
    $('#myModalContactEdit #confirmCreateContactButton').show();
    
    $('#myModalContactEdit').modal('toggle');
});



/////////////////////////////////////////////////////////////
    
    
    
    
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
                maxlength: 20
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
