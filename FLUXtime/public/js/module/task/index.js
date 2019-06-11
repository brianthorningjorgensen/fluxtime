$(document).ready(function () {

    $("#taskList").tablesorter();

    //Vise confirm-popup
    $('.deleteTask').on('click', function (e) {
        e.preventDefault();
        $('#myModal .modal-body').find('#deletetext').html($(this).data('taskname'));
        $('#myModal .modal-body').find('#taskIdDelete').html($(this).data('taskid'));
        $('#myModal').modal('toggle');
    });


    //Confirm delete
    $('#confirmDeleteButton').on("click", function (e) {
        e.preventDefault();
        var id = $('#myModal .modal-body').find('#taskIdDelete').html();
        $.ajax({
            type: "POST",
            data: {id: id},
            url: "/task/ajaxdelete",
            dataType: 'JSON',
            complete: function (data) {
                //Slet række i tabel
                $('#myModal').modal('hide');
                var ok = data['responseText'];
                if (ok.indexOf("true") >= 0) {
                    $('.tablesorter').find("[data-taskid='" + id + "']").closest('tr').hide();
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
