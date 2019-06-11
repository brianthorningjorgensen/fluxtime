$(document).ready(function () {

    $("#labelList").tablesorter();
    $("#memberList").tablesorter();
    $("#contacsList").tablesorter();


    //Delete label
    $('#confirmDeleteButton').on("click", function (e) {
        e.preventDefault();
        $('#myModalDelete').modal('hide');
        var id = $('#myModalDelete .modal-body').find('#labelIdDelete').html();
        $.ajax({
            type: "POST",
            url: "/label/ajaxconfirmdeletelabel",
            data: {id: id},
            dataType: 'JSON',
            complete: function (e) {
                var ok = e['responseText'];
                if (ok.indexOf("true") >= 0) {
                    $('#labelList').find("[data-labelid='" + id + "']").closest('tr').hide();
                }
                if (ok.indexOf("false") >= 0) {
                    $('#myModalError .modal-body').find('#errortext').html('Sorry, the label cannot be deleted (task added).');
                    $('#myModalError').modal('toggle');
                }
            }
        });
    });

    //New label popup
    $('#newButton').on('click', function (e) {
        $('#confirmEditButton').hide();
        $('#confirmAddButton').show();
        $('#labeltext').val('');
        e.preventDefault();
        $('#myModalLabel').modal('toggle');
    });



    //Save button - add label
    $('#confirmAddButton').on('click', function (e) {
        e.preventDefault();
        $labelform = $('#label');
        $labelform.validate();
        if ($labelform.valid()) {
            $('#myModalLabel').modal('hide');
            var id = $('#projectId').val();
            var label = $('#labeltext').val();
            $.ajax({
                type: "POST",
                url: "/label/ajaxaddlabel",
                dataType: 'JSON',
                data: $('#label').serialize(),
                complete: function (data) {
                    var txt = data['responseText'];
                    if (txt.indexOf("newid") > -1) {
                        var newid = txt.match(/\d/g);
                        newid = newid.join("");
                        var newRow = '<tr>' + '<td hidden="true">' + newid + '</td>' + '<td hidden="true">' + id + '</td>' + '<td>' + label +
                                '</td>' + '<td>' + '</td>' + '<td>' + '<a id="editButton" class="fa fa-edit fa-2x editLabel" Style="padding-right: 20px" href="" data-labelid="' + newid + '" data-labeltext="' + label + '">' +
                                '</a>' +
                                '<a id="deleteButton" class="fa fa-trash-o fa-2x deleteLabel" href="" data-labelid="' + newid + '" data-labelname="' + label + '" >'
                                + '</a>' +
                                '</td>' +
                                '</tr>';
                        $('#labelList').prepend(newRow);
                    }
                    if (txt.indexOf("false") >= 0) {
                        $('#myModalError .modal-body').find('#errortext').html('Sorry, label already exists and cannot be created.');
                        $('#myModalError').modal('toggle');
                    }
                }
            });
        }
    });



    //Save label - edit
    $('#confirmEditButton').on('click', function (e) {
        e.preventDefault();
        $labelform = $('#label');
        $labelform.validate();
        if ($labelform.valid()) {
            $('#myModalLabel').modal('hide');
            var pid = $('#projectId').val();
            var label = $('#labeltext').val();
            var id = $('#labelId').val();
            $.ajax({
                type: "POST",
                url: "/label/ajaxeditlabel",
                dataType: 'JSON',
                data: $('#label').serialize(),
                complete: function (data) {
                    var ok = data['responseText'];
                    if (ok.indexOf("true") >= 0) {
                        var newRow = '<tr>' + '<td hidden="true">' + id + '</td>' + '<td hidden="true">' + pid + '</td>' + '<td>' + label +
                                '</td>' + '<td>' + '</td>' + '<td>' + '<a id="editButton" class="fa fa-edit fa-2x editLabel" Style="padding-right: 20px" href="" data-labelid="' + id + '" data-labeltext="' + label + '">' +
                                '</a>' +
                                '<a id="deleteButton" class="fa fa-trash-o fa-2x deleteLabel" href="" data-labelid="' + id + '"  data-labelname="' + label + '">'
                                + '</a>' +
                                '</td>' +
                                '</tr>';
                        $('#labelList').find("[data-labelid='" + id + "']").closest('tr').replaceWith(newRow);
                    }
                      if (ok.indexOf("false") >= 0) {
                        $('#myModalError .modal-body').find('#errortext').html('Sorry, cannot edit - label name already exists.');
                        $('#myModalError').modal('toggle');
                    }
                }
            });
        }
    });

    $.validator.addMethod("trim",
            function (value, element) {
                return /^[a-zA-Z0-9]+(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?(\s+[a-zA-Z0-9]+)?$/.test(value);
            },
             "Remove whitespace from start or end"
            );

    //Validate project
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
            },
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

    var form = $("form");

    $("#submitbutton").submit(function (e) {
        if ($(this).valid() === true) {
            form.submit();
        }
    });

    //Validate project
    $('#label').validate({
        //Validering
        rules: {
            labelname: {
                maxlength: 50,
                required: true,
                trim: true,
            },
            labelid: {
                required: false,
            },
            fkProjectid: {
                required: true,
            },
        },
        messages: {
            labelname: {
                maxlength: "Max characters " + 50,
                required: 'Required field',
            },
        },
    });


    //Add & remove project member

    //Add member popup
    $('#addmemberButton').on('click', function (e) {
        e.preventDefault();
        $('#myModalMember').modal('toggle');
    });
    
    //Add member popup
    $('#addclientButton').on('click', function (e) {
        e.preventDefault();
        $('#myModalcontact').modal('toggle');
    });

    //Save button - add member
    $('#confirmAddMemberButton').on('click', function (e) {
        e.preventDefault();
        $memberform = $('#projectmember');
        $memberform.validate();
        if ($memberform.valid()) {
            $('#myModalMember').modal('hide');
            var user = $('#selecteduser  option:selected').text()
            var userid = $('#selecteduser  option:selected').val()
            $.ajax({
                type: "POST",
                url: "/project/ajaxaddmember",
                dataType: 'JSON',
                data: $('#projectmember').serialize(),
                complete: function (data) {
                    var txt = data['responseText'];
                    var newid = txt.match(/\d/g);
                    newid = newid.join("");
                    if (txt.indexOf("newid") > -1) {
                        var newRow = '<tr>' + '<td hidden="true">' + newid + '</td>' + '<td>' + user +
                                '</td>' + '<td>' + '</a>' +
                                '<a id="removememberButton" class="fa fa-trash-o fa-2x deleteMember" href="" data-projectuserid="'
                                + newid + '" data-username="' + user + '" data-userid="' + userid + '" title="Remove member" >'
                                + '</a>' +
                                '</td>' +
                                '</tr>';
                        $('#memberList').prepend(newRow);
                        $("#myModalMember #selecteduser option[value='" + userid + "']").remove();
                    }
                }
            });
        }
    });
    
    
    //Save button - add contact
    $('#confirmAddContactButton').on('click', function (e) {
        e.preventDefault();
        $contactform = $('#projectclientcontact');
        $contactform.validate();
        if ($contactform.valid()) {
            $('#myModalContact').modal('hide');
            var name = $('#selectedclient  option:selected').text();
            var id = $('#selectedclient  option:selected').val();
            $.ajax({
                type: "POST",
                url: "/contact/ajaxaddtoproject",
                dataType: 'JSON',
                data: $('#projectclientcontact').serialize(),
                complete: function (data) {
                    var txt = data['responseText'];
                    var newid = txt.match(/\d/g);
                    newid = newid.join("");
                  
                    if (txt.indexOf("newid") > -1) {
                         
                        var newRow = '<tr>' + '<td hidden="true">' + newid + '</td>' + '<td>' + name +
                                '</td>' + '<td>' + '</a>' +
                                '<a id="removeclientButton" class="fa fa-trash-o fa-2x deleteClient" href="" title="Remove contact" data-projectcontactid="'
                                + newid + '" data-contactid="'+ id + '" data-name="' + name + '" >'
                                + '</a>' +                                 
                                '</td>' +
                                '</tr>';
                        $('#contactsList').prepend(newRow);
                       
                        $("#myModalcontact #selectedclient option[value='" + id + "']").remove();
                    }
                }
            });
        }
    });

    //Validate projectmember
    $('#projectmember').validate({
        //Validering
        rules: {
            member: {
                required: true,
            },
            fkProjectid: {
                required: true,
            },
        },
        messages: {
            member: {
                required: 'Required field',
            },
        },
    });


    //Remove member
    $('#confirmDeleteButtonMember').on("click", function (e) {
        e.preventDefault();
        $('#myModalDeleteMember').modal('hide');
        var id = $('#myModalDeleteMember .modal-body').find('#memberIdDelete').html();
        var userid = $('#myModalDeleteMember .modal-body').find('#userIdDelete').html();
        var username = $('#myModalDeleteMember .modal-body').find('#membernameDelete').html();
        $.ajax({
            type: "POST",
            url: "/project/ajaxremovemember/" + $('#myModalDeleteMember .modal-body').find('#memberIdDelete').html(),
            dataType: 'JSON',
            complete: function (data) {
                var ok = data['responseText'];
                if (ok.indexOf("true") >= 0) {
                    $('#memberList').find("[data-projectuserid='" + id + "']").closest('tr').hide();
                    $('#myModalMember').find('#selecteduser')
                            .append($('<option/>', {
                                value: userid,
                                text: username
                            })
                                    )
                }
            }
        });
    });
    
    //Remove contact
    $('#confirmDeleteButtonContact').on("click", function (e) {
        e.preventDefault();
        $('#myModalDeleteContact').modal('hide');
        var contactid = $('#myModalDeleteContact .modal-body').find('#contactIdDelete').html();
        var id = $('#myModalDeleteContact .modal-body').find('#projectcontactIdDelete').html();
        var name = $('#myModalDeleteContact .modal-body').find('#contactnameDelete').html();
        $.ajax({
            type: "POST",
            url: "/contact/ajaxremovefromproject/" + $('#myModalDeleteContact .modal-body').find('#projectcontactIdDelete').html(),
            dataType: 'JSON',
            complete: function (data) { 
                var ok = data['responseText'];
                if (ok.indexOf("true") >= 0) {
                   
                    $('#contactsList').find("[data-contactid='" + contactid + "']").closest('tr').hide();
                    $('#myModalcontact').find('#selectedclient')
                            .append($('<option/>', {
                                value: contactid,
                                text: name
                            })
                        )
                }
            }
        });
    });


});

//Show delete confirm popup
//  $('.deleteLabel').on('click', function (e) {
$(document).delegate('.deleteLabel', 'click', function (e) {
    e.preventDefault();
    $('#myModalDelete .modal-body').find('#deletetext').html($(this).data('labelname'));
    $('#myModalDelete .modal-body').find('#labelIdDelete').html($(this).data('labelid'));
    $('#myModalDelete').modal('toggle');
});

//Show delete confirm popup (member)
//  $('.deleteMember').on('click', function (e) {
$(document).delegate('.deleteMember', 'click', function (e) {
    e.preventDefault();
    $('#myModalDeleteMember .modal-body').find('#membernameDelete').html($(this).data('username'));
    $('#myModalDeleteMember .modal-body').find('#memberIdDelete').html($(this).data('projectuserid'));
    $('#myModalDeleteMember .modal-body').find('#userIdDelete').html($(this).data('userid'));
    $('#myModalDeleteMember').modal('toggle');
});

//Show delete confirm popup (contact)
$(document).delegate('#removeclientButton', 'click', function (e) {
    e.preventDefault();
    $('#myModalDeleteContact .modal-body').find('#contactnameDelete').html($(this).data('name'));
    $('#myModalDeleteContact .modal-body').find('#contactIdDelete').html($(this).data('contactid'));
    $('#myModalDeleteContact .modal-body').find('#projectcontactIdDelete').html($(this).data('projectcontactid'));
    $('#myModalDeleteContact').modal('toggle');
});

//Edit label popup
$(document).delegate('.editLabel', 'click', function (e) {
    // $('.editLabel').on('click', function (e) {
    $('#confirmAddButton').hide();
    $('#confirmEditButton').show();
    $('#labelId').val($(this).data('labelid'));
    $('#labeltext').val($(this).data('labeltext'));
    e.preventDefault();
    $('#myModalLabel').modal('toggle');
});
