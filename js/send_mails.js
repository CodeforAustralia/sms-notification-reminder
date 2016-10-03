$(function() {
    send_sms();
});

function send_sms() {
    $(".send_sms").on("click", function(){
      var selected_calendar = $(this).closest("div").prop("id");
      //console.log(selected_calendar);
      create_reminder_object(calendar_obj, selected_calendar);
    });
}

function create_reminder_object(calendar_obj, selected_calendar) {
    var calendars = JSON.parse(calendar_obj);
    
    $.post("services/send_mails.php",
    {
        reminders : calendars[selected_calendar]
    },
    function(data, status){
        console.log("Data: " + data + "\nStatus: " + status);
    });
    
}