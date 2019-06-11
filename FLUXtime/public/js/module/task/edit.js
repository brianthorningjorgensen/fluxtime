$(document).ready(function () {
    

   
    
    $("#ownerList").tablesorter();

     $('#projecttask').validate({
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
 
    var form = $("#projecttask");
    
      $("#submitbutton").submit(function (e) {
        e.preventDefault();
        if ($(this).valid() === true) {
            form.submit();
        }
    });


    //delete owner
    $('#confirmDeleteButton').on('click', function (e) {
        e.preventDefault();
        var id = $('#myModalDelete .modal-body').find('#ownerIdDelete').html();
        var userid = $('#myModalDelete .modal-body').find('#userIdDelete').html();
        var username = $('#myModalDelete .modal-body').find('#usernameDelete').html();
        $.ajax({
            type: "POST",
            url: "/task/ajaxremoveowner",
            data: {id: id},
            dataType: 'JSON',
            complete: function (data) {
                $('#myModalDelete').modal('hide');
                var ok = data['responseText'];
                if (ok.indexOf("true") >= 0) {
                    // remove deleted owner
                    $('#ownerList').find("[data-ownerid='" + id + "']").closest('tr').hide();
                    // one to modal selectbox
                    $('#myModalOwner').find('#ownerid')
                            .append($('<option/>', {
                                value: userid,
                                text: username
                            })
                                    )
                }
            }
        });
    });

    //Add owner popup
    $('#newButton').on('click', function (e) {
        e.preventDefault();
        $count = $('#myModalOwner').find('#ownerid option').size();
        if ($count === 0) {
            $('#ownermessage').html('Sorry, no further project members added to project...');
            $('#confirmButton').hide();
        } else {
            $('#ownermessage').html('');
            $('#confirmButton').show();
        }
        $('#myModalOwner').modal('toggle');
    });

    //Save owner
    $('#confirmButton').on('click', function (e) {
        e.preventDefault();
        $form = $('#owner');
        $form.validate();
        $data = $form.serialize();
        //      if ($form.valid()) {
        var user = $('#ownerid  option:selected').text();
        var userid = $('#ownerid  option:selected').val();
        $.ajax({
            type: "POST",
            url: "/task/ajaxaddowner",
            dataType: 'JSON',
            data: $data,
            complete: function (data) {
                $('#myModalOwner').modal('hide');
                var txt = data['responseText'];
                var newid = txt.match(/\d/g);
                newid = newid.join("");
                if (txt.indexOf("true") >= 0) {
                    // get table
                    $table = $('#ownerList');
                    // get template row and change data
                    $newRow = $table.find("tr.templaterow").first().clone();
                    $newRow.removeClass('templaterow');
                    $newRow.addClass('rowNotHidden');
                    $newRow.find('#template_username').text(user);
                    $newRow.find('template_ownerid').text(newid);
                    $newRow.find('#template_userid').text(userid);
                    $newRow.find('.deleteOwner').attr("id", newid);
                    $newRow.find('.deleteOwner').attr("data-ownerid", newid);
                    $newRow.find('.deleteOwner').attr("data-userid", userid);
                    $newRow.find('.deleteOwner').attr("data-username", user);
                    // apply to the table
                    $table.append($newRow);
                    // remove from modal selectbox ownerid
                    $("#myModalOwner #ownerid option[value='" + userid + "']").remove();
                }
            }
        });
        // }
    });
    
    
});

$(document).delegate('.deleteOwner', 'click', function (e) {
    //Show delete confirm popup
    e.preventDefault();
    $('#myModalDelete .modal-body').find('#ownerIdDelete').html($(this).data('ownerid'));
    $('#myModalDelete .modal-body').find('#userIdDelete').html($(this).data('userid'));
    $('#myModalDelete .modal-body').find('#usernameDelete').html($(this).data('username'));
    $('#myModalDelete').modal('toggle');
});
