$(document).ready(function () {

   var form = $("form");
   
    // datetime validation
    $.validator.addMethod("datetime", function (value, element) {
        return this.optional(element) || /\s*((31([-/ ])((0?[13578])|(1[02]))\3(\d\d)?\d\d)|((([012]?[1-9])|([123]0))([-/ ])((0?[13-9])|(1[0-2]))\12(\d\d)?\d\d)|(((2[0-8])|(1[0-9])|(0?[1-9]))([-/ ])0?2\22(\d\d)?\d\d)|(29([-/ ])0?2\25(((\d\d)?(([2468][048])|([13579][26])|(0[48])))|((([02468][048])|([13579][26]))00)))) (([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])\s*/i.test(value);
    }, "'Please enter a valid DateTime in this format [yyyy-mm-dd hh:mm]");


    $('form').validate({
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
            users: {
                 required: true,               
            },
            projects: {
                 required: true,               
            },
            labels: {
                 required: true,               
            },
            tasks: {
                 required: true,               
            },
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
            users: {
                 required: 'Required field',               
            },
            projects: {
                  required: 'Required field',               
            },
            labels: {
               required: 'Required field',            
            },
            tasks: {
                 required: 'Required field',              
            },
            from: {
                required: 'Required field',
                 datetime:  true
            },
            to: {
                 required: 'Required field',
                 datetime: true
            },
            
        }, 
        //Hvis valideret korrekt kan postes
        submitHandler: function (form) {
            form.submit();
        }
    });
    
     $("#submitbutton").submit(function (e) {
        e.preventDefault();
        if ($(this).valid() === true) {
            form.submit();
        }
    });

      // when start hide unwanted form elements
      $('#projects, #from, #to, #labels, #tasks, #submitbutton').hide();
    
      // when a project is choosen
      $("#users").change( function (e) {
          $selectedelement = $( "#users option:selected" ).val();
          if ($selectedelement > 0 ) {               
                var output = [];
                $.ajax({
                    type: "POST",
                    url: "/timereg/ajaxfetchuserprojects",
                    data: {id: $selectedelement},
                    dataType: 'JSON',
                    success: function (data) {
                            var selectValues = data['projects'];
                            output.push('<option value="0">Please select a project</option>');
                            $.each(selectValues, function(key, value)
                            {
                                output.push('<option value="'+ key +'">'+ value +'</option>');
                            });
                            $('#projects').find('option').remove();
                            $('#projects').html(output.join(''));
                            $('#projects').show();
                            $('#labels').find('option').remove().hide();
                            $('#labels').hide();
                            $('#tasks').find('option').remove().hide();
                            $('#tasks').hide();
                            $('#from, #to').hide();
                    }
                });
          }
      });
      
      // when a project is choosen
      $("#projects").change( function (e) {
                 $selectedelement = $( "#projects option:selected" ).val();
                 $('#pid').val($selectedelement);
                var output = [];
                $.ajax({
                    type: "POST",
                    url: "/timereg/ajaxfetchprojectlabels",
                    data: {id: $selectedelement},
                    dataType: 'JSON',
                    success: function (data) {  
                        var selectValues = data['labels'];
                      output.push('<option value="0">No label</option>');
                          $.each(selectValues, function(key, value)
                            {
                                output.push('<option value="'+ key +'">'+ value +'</option>');
                            }); 

                            $('#labels').find('option').remove();
                            $('#labels').html(output.join(''));
                            $('#labels').show();

                            $('#tasks').find('option').remove();
                            $('#tasks').hide();

                            $('#from, #to').hide();                        
                    }
                });
      });
    
      // when a label is choosen
      $("#labels").change( function (e) {
                 $selectedelement = $( "#labels option:selected" ).val();
                var pid = $('#pid').val();
                var output = [];
                $.ajax({
                    type: "POST",
                    url: "/timereg/ajaxfetchlabeltask",
                    data: {id: $selectedelement, pid: pid},
                    dataType: 'JSON',
                    success: function (data) {                        
                            var taskValues = data['tasks'];
                            output.push('<option value="0">Please select a task</option>');
                            $.each(taskValues, function(key, value)
                            {
                                output.push('<option value="' + key +'">'+ value +'</option>');
                            });    
                            $('#tasks').find('option').remove();
                            $('#tasks').html(output.join(''));
                            $('#tasks').show();
                            $('#from, #to').hide();                        
                    }
                });
      });

      // when a task is choosen
      $("#tasks").change( function (e) {
                $('#from, #to').show();  
                $('#submitbutton').show();
      });
      
    $('#from').datetimepicker({
                     format:'Y-m-d H:i',
                     lang:'en',
                     theme:'dark'
       });

     $('#to').datetimepicker({
                 format:'Y-m-d H:i',
                 lang:'en',
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


