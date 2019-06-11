
$(document).ready(function () {
    
    $("#userList").tablesorter();

    /* Delete button on table elements */
    $('.deleteUserButton').on('click', function (e) {
        e.preventDefault();
        $('#myModal .modal-body').find('#deleteuserid').html($(this).data('userid'));
        $('#myModal .modal-body').find('#info').html($(this).data('username'));
        $('#myModal').modal('toggle');
    });

    $('#confirmButton').on("click", function (e) {
        e.preventDefault();
        $('#myModal').modal('hide');
        var id = $('#myModal .modal-body').find('#deleteuserid').html();

        $.ajax({
            type: "POST",
            url: "/fluxuser/ajaxconfirmdelete",
            data: {id: id},
            dataType: 'JSON',
            complete: function (e) {
                var ok = e['responseText'];
                if (ok.indexOf("true") >= 0) {
                    $('#userList').find("[data-userid='" + id + "']").closest('tr').hide();
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
