$(document).ready(function () {

    $("#accountList").tablesorter();
    
   


    //Confirm delete
    $('#confirmButton').on("click", function (e) {
        e.preventDefault();
        $('#myModal').modal('hide');
        var id = $('#myModal .modal-body').find('#accountIdDelete').html();
        $.ajax({
            type: "POST",
            url: "/account/ajaxdelete",
            data: {id: id},
            dataType: 'JSON',
            complete: function (data) {
                var ok = data['responseText'];
                if (ok.indexOf("true") >= 0) {
                    //Slet række i tabel
                    $('.tablesorter').find("[data-accountid='" + id + "']").closest('tr').hide();
                }
            }
        });
    });



    //Save account - edit
    $('#confirmEditButton').on('click', function (e) {
        e.preventDefault();
        $editform = $('#accountEdit');
        $editform.validate();
        if ($editform.valid()) {
            $('#myModalEdit').modal('hide');
            var comment = $('#comment').val();
            var activeTxt = '';
            if ($("#active").is(":checked")) {
                activeTxt = 'Active';
                var active = '1';
            }else{
                activeTxt = 'Inactive';
                var active = '0';
            }
            var accountid = $('#accId').val();
            var client = $('#client').val();
             var clientid = $('#clientId').val();
             if(clientid !== ''){
                var clientbutton = '<a class="fa fa-briefcase fa-2x " href="/client/edit/'+clientid+'"  data-toggle="tooltip" title="View client"></a>';
             } else{
                 var clientbutton = '<a></a>';
             }
            var id = $('#compId').val();
            var name = $('#companyname').val();
            $.ajax({
                type: "POST",
                url: "/account/ajaxedit",
                dataType: 'JSON',
                data: $('#accountEdit').serialize(),
                complete: function (data) {
                    var ok = data['responseText'];
                    if (ok.indexOf("true") >= 0) {
                        var newRow = '<tr>' + '<td>' + accountid + '</td>' + '<td >' + name + '</td>' + '<td>' + id + '<td >' + activeTxt + '</td>'+ '<td >' + client + '</td>' + '<td hidden="true">' + comment + '</td>' +
                                '</td>' + '<td>' + 
                                '<a id="editButton" class="fa fa-edit fa-2x editAccount" Style="padding-right: 20px" href="" data-accountid="' + accountid + '" data-customer="' + name +
                                '" data-customerid="' + id + '" data-description="' + comment + '" data-active="' + active + '"' +
                                '" data-clientid="' + clientid + '"' + '" data-client="' + client + '">' + '</a>' +
                                '<a id="deleteButton" class="fa fa-trash-o fa-2x deleteAccount" href="" data-accountid="' + accountid + '" data-customer="' + name + '" >'
                                + '</a>' +
                                 clientbutton + 
                                '</td>' +
                                '</tr>';
                        $('#accountList').find("[data-accountid='" + accountid + "']").closest('tr').replaceWith(newRow);
                    }  if (ok.indexOf("false") >= 0) {
                          $('#myModalError .modal-body').find('#errortext').html('Sorry, cannot edit - accountname already exists.');
                    $('#myModalError').modal('toggle');
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
    
  

    $('#searchcheckbox-popover').mouseenter(function () {
        $('#searchcheckbox-popover').popover('show');
    });
    $('#searchcheckbox-popover').mouseleave(function () {
        $('#searchcheckbox-popover').popover('hide');
    });
    
    
});

//Vise confirm-popup
$(document).delegate('.deleteAccount', 'click', function (e) {
    e.preventDefault();
    $('#myModal .modal-body').find('#deletetext').html($(this).data('customer'));
    $('#myModal .modal-body').find('#accountIdDelete').html($(this).data('accountid'));
    $('#myModal').modal('toggle');
});

//Vise edit-popup
$(document).delegate('.editAccount', 'click', function (e) {
    e.preventDefault();
    $('#accId').val($(this).data('accountid'));
    if ($(this).data('active') === 1) {
        $("#active").prop("checked", true)
    } else{
        $("#active").prop("checked", false);
    }
    $('#comment').val($(this).data('description'));
    $('#companyname').val($(this).data('customer'));
    $('#name').val($(this).data('customer'));
    $('#compId').val($(this).data('customerid'));
    $('#client').val($(this).data('client'));
     $('#clientId').val($(this).data('clientid'));
    $('#myModalEdit').modal('toggle');
});