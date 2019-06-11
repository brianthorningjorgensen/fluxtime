
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
             // måske skrive i error message???
                $input.val('');
            }
        }
    });
    
   
    
    
    

   
});