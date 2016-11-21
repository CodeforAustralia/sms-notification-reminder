var selected_date = get_current_date();
var selected_menu = "";
var events = "";
$(function() {
	get_menu();
	init_calendar();
	click_calendars();
	send_messages();
});

function get_current_date() {
	var currentDate = new Date();
	var year  = currentDate.getFullYear();
	var month = currentDate.getMonth() + 1;
	var day   = currentDate.getDate() ;
	return  year + "-" + month + "-" + day;
}

function init_calendar() {	
  	// Handler for .ready() called.
	$('#calendar').datepicker({
	    daysOfWeekDisabled: "0,6",
	    //daysOfWeekHighlighted: "1,2,3,4,5",
		format: "yyyy-mm-dd",
		todayHighlight: true
	}).on("changeDate", function(e) {		        
        selected_date = e.format(0,"yyyy-mm-dd");   
		get_events();
    });
}

function show_loader() {
	$("#fade").show();
	$("#modal").show();
}

function hide_loader() {
	$("#fade").hide();
	$("#modal").hide();
}

function click_calendars(){
	$("#calendar_list").on('click', 'li', function() {
	    $("#current-calendar").text($(this).text());
	    $('#calendar_list li.active').removeClass('active');
	    $(this).addClass('active');
		selected_menu = $(this).attr("id");
		get_events();
	});
}

function get_events(){
	show_loader();  	
  	var data_url = "/services/get_events.php";
  	$.post( data_url, { date: selected_date, email: selected_menu })
	  .done(function( data ) {
	    var cards = JSON.parse(data);
	    if (cards.session_error === undefined) {
		    set_events(cards);
	    	render_cards(cards);
			hide_loader();
	    } else {
	    	window.location = "/";
	    }
	  });
}

function render_cards(cards) {
	var targetContainer = $(".events-container"),
    template = $("#messages-template").html();

	var html = Mustache.to_html(template, cards);

	$(targetContainer).html(html);
}

function get_menu(){
  	var data_url = "/include_files/emails_menu.txt";
  	$.post( data_url, {})
	  .done(function( data ) {
	    var items = JSON.parse(data);	    
    	render_menu(items);
		selected_menu = $("#calendar_list li")[0].id;
		$("#current-calendar").text($("#calendar_list li a")[0].text);
		$("#calendar_list li").first().addClass("active");
		get_events();
	  });
}

function render_menu(items){
	var targetContainer = $("#calendar_list"),
    template = $("#menu-template").html();

	var html = Mustache.to_html(template, items);

	$(targetContainer).html(html);
}

function set_events(obj) {
	events = obj.cards;
	if (events === undefined){
		$("#send_button").attr("disabled","disabled")
	} else {
		$("#send_button").removeAttr("disabled","disabled")
	}
}

function send_messages() {
	$("#send_button").on('click', function() {
		show_loader();
		$.post("/services/send_mails.php",
	    {
	        messages : events
	    },
	    function(data, status){
	        console.log("Data: " + data + "\nStatus: " + status);
	        hide_loader();
	        alert("The reminders were sent");
	    });
	});
}