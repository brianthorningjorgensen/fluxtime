$(document).ready(function () {

    $("#clientList").tablesorter();

    //Vise confirm-popup
    $('.deleteClient').on('click', function (e) {
        e.preventDefault();
        $('#myModal .modal-body').find('#deletetext').html($(this).data('clientname'));
        $('#myModal .modal-body').find('#clientIdDelete').html($(this).data('clientid'));
        $('#myModal').modal('toggle');
    });

    //Confirm delete
    $('#confirmButton').on("click", function (e) {
        e.preventDefault();
        $('#myModal').modal('hide');
        var id = $('#myModal .modal-body').find('#clientIdDelete').html();
        $.ajax({
            type: "POST",
            url: "/client/ajaxdelete",
            data: {id: id},
            dataType: 'JSON',
            complete: function (data) {
                var ok = data['responseText'];
                alert(ok);
                if (ok.indexOf("true") >= 0) {
                    //Slet række i tabel
                    $('.tablesorter').find("[data-clientid='" + id + "']").closest('tr').hide();
                }
                if (ok.indexOf("false") >= 0) {
                    $('#myModalErrorDelete').modal('toggle');
                }
            }
        });
    });


    $('#searchcheckbox-popover').mouseenter(function () {
        $('#searchcheckbox-popover').popover('show');
    });
    $('#searchcheckbox-popover').mouseleave(function () {
        $('#searchcheckbox-popover').popover('hide');
    });
});
