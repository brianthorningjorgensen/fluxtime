$(document).ready(function () {

    $("#projectList").tablesorter();

    //Vise confirm-popup
    $('.deleteProject').on('click', function (e) {
        e.preventDefault();
        $('#myModal .modal-body').find('#deletetext').html($(this).data('projectname'));
        $('#myModal .modal-body').find('#projectIdDelete').html($(this).data('projectid'));
        $('#myModal').modal('toggle');
    });

    //Confirm delete
    $('#confirmButton').on("click", function (e) {
        e.preventDefault();
        $('#myModal').modal('hide');
        var id = $('#myModal .modal-body').find('#projectIdDelete').html();
        $.ajax({
            type: "POST",
            url: "/project/ajaxconfirmdelete",
            data: {id: id},
            dataType: 'JSON',
            complete: function (data) {
                var ok = data['responseText'];
                if (ok.indexOf("true") >= 0) {
                    //Slet række i tabel
                    $('.tablesorter').find("[data-projectid='" + id + "']").closest('tr').hide();
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
