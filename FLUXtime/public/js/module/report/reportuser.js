
$(document).ready(function () {
    
    $("#taskList").tablesorter(); 
    
    // hide fields on start
    $('#year, #quater, #month, #week, #to, #from').hide();
    
    // show fields on start
    var choicesselectbox = $('#choices').prop('selectedIndex');
    // make shure that select boxes shows correct on restart of page
    switch(choicesselectbox) {
        case 1:
            $('#year').show();
        break;
        case 2:
            $('#year, #month').show();
        break;
        case 3:
            $('#year, #week').show();
        break;
        case 4:
            $('#year, #quater').show();                
        break;
        case 5:
            $('#to, #from').show();                
        break;
    }   
    
    // if selector has been clicked
    $('#choices').on('change', function (e) {
        switch($(this).val()) {
            case '1':
                $('#quater, #month, #week, #to, #from').hide();
                $('#year').show();
            break;
            case '2':
                $('#quater, #week, #to, #from').hide();
                $('#year, #month').show();
            break;
            case '3':
                $('#quater, #month, #to, #from').hide();
                $('#year, #week').show();
            break;
            case '4':
                $('#month, #week, #to, #from').hide();
                $('#year, #quater').show();                
            break;
            case '5':
                $('#year, #quater, #month, #week').hide();
                $('#to, #from').show();                
            break;
        }
    });
    
    // checkall is clicked set the rest of the checkboxes
     $('.checkall').on('click', function (e) {
         var checkedvalue = $('.checkall').prop('checked');
         $('.checkvalue').prop('checked', checkedvalue);
    });
    
    $('.checkvalue').on('click', function (e) {       
        if (!$(this).checked) {
            $('.checkall').prop('checked', false);           
        }
        
        // remove / add all project checked 
        if ($(".checkvalue:checked").length == $(".checkvalue").length )
        {
            $('.checkall').prop('checked', true);
        } else {
            $('.checkall').prop('checked', false);
        }
    });
    
    // clicked on a projectlabel checkbox the project must be check too
     $('.checklabelvalue').on('click', function (e) {
         $(this).closest('tr').find('.checkvalue').prop('checked', true);
    });
        
    
    $('#from').datetimepicker({
        format:'Y.m.d H:i',
        lang:'da',
        theme:'dark'
    });

    $('#to').datetimepicker({
        format:'Y.m.d H:i',
        lang:'da',
        theme:'dark',
        onChangeDateTime:function(dp,$input){
            $fromdate = $('#from').val();
            $todate = $input.val();
            if ($todate<=$fromdate) {
             // måske skrive i error message???
                $input.val('');
            }
        }
    });
    
    
   
});