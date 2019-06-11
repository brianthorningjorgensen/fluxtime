$(document).ready(function () {

    $("#taskList").tablesorter();

    $activetask = $('.activetask');
    $this = $(this);
    $tablerow = $this.closest('tr');
    var taskid = $activetask.attr('id');

    // opdatere den totale tid
    $.ajax({
        type: "POST",
        data: {id: taskid},
        url: "/mytask/ajaxcalculatesingletasktime",
        dataType: 'JSON',
        success: function (data) {
            $totaltime = data['totaltasktime'];
            if ($totaltime != '0:0:0') {
                // update 
                $tablerow.find('#duration').html(formatElapsedTime($totaltime));
            }
        }
    });

    setInterval(function () {
        var status = $('.activetask').closest('tr').find('#status').text();
        if (status == "started") {
            $('.activetask').closest('tr').find('#duration').html(formatElapsedTime($totaltime++));
        }
    }, 1000);

    function formatElapsedTime(time) {
        return Math.floor(time / 3600) + ':' + Math.floor((time / 60) % 60) + ':' + time % 60;
    }

    //trykke på playknap
    $('.playbutton').on('click', function (e) {
        e.preventDefault();
        $this = $(this);
        $tablerow = $this.closest('tr');
        $status = $tablerow.find('#status');
        var string = $this.attr('class');

        // stop
        if (string.indexOf('fa-pause') >= 0) {
            var id = $('#newid').val();
            $.ajax({
                type: "POST",
                data: {id: id},
                url: "/mytask/ajaxstoptimereg",
                dataType: 'JSON',
                complete: function (data) {
                    var result = data['responseText'];
                    if (result.indexOf("true") >= 0) {
                        $this.removeClass('fa-pause');
                        $this.addClass('fa-play');
                        $this.attr('data-original-title', "Start task");
                        $this.removeClass('activetask');

                        var taskid = $this.attr('id');
                        // opdatere den totale tid
                        $.ajax({
                            type: "POST",
                            data: {id: taskid},
                            url: "/mytask/ajaxcalculatesingletasktime",
                            dataType: 'JSON',
                            success: function (data) {
                                $totaltime = data['totaltasktime'];
                                if ($totaltime != '0:0:0') {
                                    // update 
                                    $tablerow.find('#duration').html(formatElapsedTime($totaltime));
                                }
                            }
                        });
                    }
                }
            });
        }
        // start
        else {

            // change all old unstarted button
            $('.fa-pause').each(function (index) {
                $(this).removeClass('fa-pause');
                $(this).addClass('fa-play');
                $(this).attr('data-original-title', "Start task");
            });

            $this.removeClass('fa-play');
            $this.addClass('fa-pause');
            $status.text('started');
            $this.attr('data-original-title', "Stop the task");

            // remove previous activetask
            $('.activetask').removeClass('activetask');

            $this.addClass('activetask');

            var id = $this.attr('id');

            $.ajax({
                type: "POST",
                data: {id: id},
                url: "/mytask/ajaxstarttimereg",
                dataType: 'JSON',
                complete: function (mydata) {
                    var txt = mydata['responseText'];
                    if (txt.indexOf("newid") > -1) {
                        var newid = txt.match(/\d/g);
                        newid = newid.join("");
                        $('#newid').val(newid);
                    }
                }
            });
        }
    });

    //trykke på Finish-knappen
    $('.finishbutton').on('click', function (e) {
        e.preventDefault();
        $this = $(this);
        $tablerow = $this.closest('tr');
        var taskid = $this.attr('id');
        $.ajax({
            type: "POST",
            data: {id: taskid},
            url: "/mytask/ajaxfinishtask",
            dataType: 'JSON',
            complete: function (data) {
                var result = data['responseText'];
                if (result.indexOf("true") >= 0) {
                    $tablerow.hide();
                }
            }
        });
    });
});
