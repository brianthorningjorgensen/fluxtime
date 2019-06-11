
$(document).ready(function () {

    $("#timeregList").tablesorter(); 
    
    $('#search-from').datetimepicker({
        format:'Y.m.d H:i',
        lang:'en',
        theme:'dark'
    });

    $('#search-to').datetimepicker({
        format:'Y.m.d H:i',
        lang:'en',
        theme:'dark',
        onChangeDateTime:function(dp,$input){
            $fromdate = $('#search-from').val();
            $todate = $input.val();
            if ($todate<=$fromdate) {
                $input.val('');
            }
        }
    });
    
    
    //Vise confirm-popup
    $('.deleteTimereg').on('click', function (e) {
        e.preventDefault();
        $('#myModal .modal-body').find('#deletetext').text($(this).data('name'));
        $('#myModal .modal-body').find('#timeregIdDelete').html($(this).attr('id'));
        $('#myModal').modal('toggle');
    });

    //Confirm delete
    $('#confirmDeleteButton').on("click", function (e) {
        e.preventDefault();
        var id = $('#myModal .modal-body').find('#timeregIdDelete').html();
        $.ajax({
            type: "POST",
            data: {id: id},
            url: "/timereg/ajaxdelete",
            dataType: 'JSON',
            complete: function (data) {
             var ok = data['responseText'];
                if (ok.indexOf("true") >= 0) {
                //Slet række i tabel
                $('#myModal').modal('hide');               
                $('.tablesorter').find("#" + id ).closest('tr').hide();
            }
            }
        });
    });
    
    $('[data-tooltip="tooltip"]').tooltip();
    
    $('[data-id="editTimereg"]').click(function() {
        var from = $('#start' + $(this).attr('id')).html();
        var to = $('#stop' + $(this).attr('id')).html();
        to = to.substring(0, to.lastIndexOf(" "));
        from = from.substring(0, from.lastIndexOf(" "));
        $('#editModal .modal-body').find('#from').val(from);
        $('#editModal .modal-body').find('#to').val(to);
        $('#editModal .modal-body').find('#timeregId').val($(this).attr('id'));
    });
    
     function editTimereg()  {
        var id = $('#editModal .modal-body').find('#timeregId').val();
        var from = $('#editModal .modal-body').find('#from').val();
        var to = $('#editModal .modal-body').find('#to').val();
        $.ajax({
            type: "POST",
            data: {id: id, from: from, to: to},
            url: "/timereg/ajaxedit",
            dataType: 'JSON',
            complete: function (data) {
                if (data['state']) {
                    $('#editModal').toggle();
                    location.reload();
                }
            }
        });
    }
        
    var form = $("#timeregform");

    // datetime validation
    $.validator.addMethod("datetime", function (value, element) {
        return this.optional(element) || /\s*((31([-/ ])((0?[13578])|(1[02]))\3(\d\d)?\d\d)|((([012]?[1-9])|([123]0))([-/ ])((0?[13-9])|(1[0-2]))\12(\d\d)?\d\d)|(((2[0-8])|(1[0-9])|(0?[1-9]))([-/ ])0?2\22(\d\d)?\d\d)|(29([-/ ])0?2\25(((\d\d)?(([2468][048])|([13579][26])|(0[48])))|((([02468][048])|([13579][26]))00)))) (([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])\s*/i.test(value);
    }, "'Please enter a valid DateTime in this format [yyyy-mm-dd hh:mm]");


    $('#timeregform').validate({
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
            from: {
                 required: true, 
                 datetime:  true
            },
            to: {
                 required: true,
                 datetime: true
            },
        }, 
        messages: {
            from: {
                 required: 'Required field'
            },
            to: {
                   required: 'Required field'
            },
        }, 
        //Hvis valideret korrekt kan postes
        submitHandler: function (form) {
            editTimereg();
        }
    });

     $('#from').datetimepicker({
                     format:'Y-m-d H:i',
                     lang:'da',
                     theme:'dark'
       });

     $('#to').datetimepicker({
                 format:'Y-m-d H:i',
                 lang:'da',
                 theme:'dark',                 
     });
    
});
