var __calendars = {};
var __templates = {};
var __current_calendar = {};
var __current_template = {};

$(function() {
    /** Calendars Functions **/
    open_edit_calendar();
    add_calendar();
    get_calendars();
    open_calendars();
    delete_calendar();
    /** Tempalte Functions **/
    add_template();
    open_templates();
    open_edit_template();
    delete_template();
    /** Other functions **/
    enable_tooltips();
    hide_loader();
    shortcut_button();
});

function show_loader() {
	$("#fade").show();
	$("#modal").show();
}

function hide_loader() {
	$("#fade").hide();
	$("#modal").hide();
}

function add_calendar() {
	$("#add-calendar").on("click", function(){
		$("#fade").show();
		$("#forms").html($("#calendar-form-container").html());
		$("#forms").show();
		validate_calendar_form();
	});
}

function open_edit_calendar() {
	$("body").on("click", ".edit-calendar", function(){
		$("#fade").show();
		$("#forms").html($("#calendar-form-container").html());
		$("#forms").show();
		var calendar_info = __current_calendar = get_calendar_by_email($(this).attr("id"));
		fill_calendar_window(calendar_info);
		validate_calendar_form();
	});
}

function save_edit_calendar() {
	//$("body").on("click", ".save-calendar", function(){
		$("#fade").hide();
		$("#forms").hide();
		var calendar = {};
		calendar.name	= $("#forms #calendar-name").val();
		calendar.email	= $("#forms #calendar-email").val();
		update_calendar(calendar);
		$("#forms").html('');
		__current_calendar = {};
		store_data('calendar');
	//});
}

function add_template() {
	$("#add-template").on("click", function(){
		$("#fade").show();
		$("#forms").html($("#template-form-container").html());
		$("#forms").show();
		validate_template_form();
	});
}

function open_edit_template() {
	$("body").on("click", ".edit-template", function(){
		$("#fade").show();
		$("#forms").html($("#template-form-container").html());
		$("#forms").show();
		var template_info = __current_template = get_template_by_abbr($(this).attr("id"));
		fill_template_window(template_info);
		validate_template_form();
	});
}

function save_edit_template() {
	//$("body").on("click", ".save-template", function(){
		$("#fade").hide();
		$("#forms").hide();
		
		var template = {};
		template.name			= $("#forms #template-name").val();
	  	template.abbreviation	= $("#forms #template-abbreviation").val();
	  	template.office_number	= $("#forms #template-phone").val();
	  	template.content		= $("#forms #template-content").val();
	  	
		update_template(template);
		$("#forms").html('');
		__current_template = {};
		store_data('template');
	//});
}

function enable_tooltips() {
	$('[data-toggle="tooltip"]').tooltip(); 
}

function get_calendars(){
	show_loader();  	
  	var data_url = "/services/get_calendars.php";
  	$.post( data_url )
	  .done(function( data ) {
	  	__calendars = JSON.parse(data);
	    var calendars = {};	
	    calendars.calendar = JSON.parse(data);
	    console.log(calendars);
	    
		if(calendars.calendar.hasOwnProperty('errorNumber')) {
			switch(calendars.calendar.errorNumber) {
			case 401: //unauthorized
			case 500: //error accesing calendar
					window.location = "/logout.php";
				break;
			}
		}
		render_calendars(calendars);
		hide_loader();
	  });
}

function render_calendars(calendars) {
	var targetContainer = $(".calendar-table tbody"),
    template = $("#calendar-template").html();
	var html = Mustache.to_html(template, calendars);
	$(targetContainer).html(html);
}

function get_templates(){
	show_loader();  	
  	var data_url = "/services/get_templates.php";
  	$.post( data_url )
	  .done(function( data ) {
	  	__templates = JSON.parse(data);
	    var templates = {};	
	    templates.templates = JSON.parse(data);
	    console.log(templates);
	    
		if(templates.templates.hasOwnProperty('errorNumber')) {
			switch(templates.templates.errorNumber) {
			case 401: //unauthorized
			case 500: //error accesing calendar
					window.location = "/logout.php";
				break;
			}
		}
		render_templates(templates);
		hide_loader();
	  });
}

function render_templates(templates) {
	var targetContainer = $(".template-table tbody"),
    template = $("#template-template").html();
	var html = Mustache.to_html(template, templates);
	$(targetContainer).html(html);
}

function open_calendars(){
	$("#open-calendars").on("click", function() {
	    get_calendars();
	});
}

function open_templates(){
	$("#open-templates").on("click", function() {
	    get_templates();
	});
}

function get_calendar_by_email(email) {
  return __calendars.filter(
    function(__calendars) {
      return __calendars.email == email
    }
  );
}

function get_template_by_abbr(abbr) {
  return __templates.filter(
    function(__templates) {
      return __templates.abbreviation == abbr
    }
  );
}

function fill_calendar_window(information) {
	$("#forms #calendar-name").val(information[0].name);
	$("#forms #calendar-email").val(information[0].email);
}

function fill_template_window(information) {
	$("#forms #template-name").val(information[0].name);
	$("#forms #template-abbreviation").val(information[0].abbreviation);
	$("#forms #template-phone").val(information[0].office_number);
	$("#forms #template-content").val(information[0].content);
}

function shortcut_button(){
	$("body").on("click", "#forms .shortcut-tag", function(){	
	   var $txt = $("#forms #template-content");
       var caretPos = $txt[0].selectionStart;
       var textAreaTxt = $txt.val();
       var txtToAdd = $(this).text();
       $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
	});
}

function update_calendar(calendar){
	var calendar_found = false;
	var search_previous = get_calendar_by_email(calendar.email);
	for (pos in __calendars){
		if(__current_calendar.length > 0 && __calendars[pos].email == __current_calendar[0].email){//editing
			if(__current_calendar[0].email != calendar.email && search_previous.length < 1) {//if modifying email ensure that doesnt exist
			  	__calendars[pos].email	= calendar.email;
				__calendars[pos].name	= calendar.name;
			} else if(__current_calendar[0].email == calendar.email){
				__calendars[pos].name	= calendar.name;
			}
			calendar_found = true;
		}
	}
	
	if(search_previous.length > 0 && Object.keys(__current_calendar).length < 1){ //new but repeated email
		calendar_found = true;
	}
	if(!calendar_found) {
		__calendars.push(calendar);
	}
}

function update_template(template){
	var template_found = false;
	var search_previous = get_template_by_abbr(template.abbreviation);
	for (pos in __templates){
		if(__current_template.length > 0 && __templates[pos].abbreviation == __current_template[0].abbreviation){//editing
			if(__current_template[0].abbreviation != template.abbreviation && search_previous.length < 1) {//if modifying abbreviation ensure that doesnt exist
			  	__templates[pos].abbreviation = template.abbreviation;
				__templates[pos].name		= template.name;
				__templates[pos].phone		= template.phone;
				__templates[pos].content	= template.content;
			} else if(__current_template[0].abbreviation == template.abbreviation) {
				__templates[pos].name		= template.name;
				__templates[pos].phone		= template.phone;
				__templates[pos].content	= template.content;
			}
			template_found = true;
		}
	}
	
	if(search_previous.length > 0 && Object.keys(__current_template).length < 1){ //new but repeated abbreviation
		template_found = true;
	}
	if(!template_found) {
		__templates.push(template);
	}
}

function delete_calendar(){
	$("body").on("click", ".delete-calendar", function() {
	    var email = $(this).attr("id");
	    __calendars = remove_calendar(email);
	    store_data('calendar');
	})
}

function remove_calendar(email){
	return __calendars.filter(function(calendar) { 
	   return calendar.email !== email;  
	});
}

function delete_template(){
	$("body").on("click", ".delete-template", function() {
	    var abbreviation = $(this).attr("id");
	    console.log(abbreviation);
	    __templates = remove_template(abbreviation);
	    store_data('template');
	})
}

function remove_template(abbreviation){
	return __templates.filter(function(template) { 
	   return template.abbreviation !== abbreviation;  
	});
}

function validate_calendar_form(){
	// validate signup form on keyup and submit
	$("#forms #calendar-form").validate({
		submitHandler: function() { 
			save_edit_calendar();
		},
		rules: {
			"calendar-name": "required",
			"calendar-email": {
					required: true,
					email: true
			},
		}
	});
}

function validate_template_form(){
	// validate signup form on keyup and submit
	$("#forms #template-form").validate({
		submitHandler: function() { 
			save_edit_template();
		},
		rules: {
			"template-name": "required",
			"template-abbreviation": {
				required: true,
				minlength: 5
			},
			"template-phone": {
				required: true,
				minlength: 8,
    			number: true
			},
			"template-content": {
				required: true
			}
		},
		messages: {
			"template-name": "Please enter a name for this calendar",
			"template-abbreviation": {
				required: "Please enter an abbreviation for this calendar",
				minlength: "Your abbreviation must be at least 5 characters long"
			},
			"template-phone": {
				required: "Please enter a phone for this calendar",
				minlength: "Your phone must be at least 9 characters long",
				number: "Your phone must be a valid number"
			},
			"template-content": {
				required: "Please enter a description",
			}
		}
	});
}

function store_data(save_type){
	show_loader();
	var data_url = "/services/set_information.php";
	
	var info = __templates;
	if(save_type == 'calendar') {
		info = __calendars;
	} 
  	$.post( data_url, { info: info, save_type: save_type } )
	  .done(function( data ) {
		if(data.hasOwnProperty('errorNumber')) {
			switch(data.errorNumber) {
			case 401: //unauthorized
			case 500: //error accesing calendar
					window.location = "/logout.php";
				break;
			}
		} else {
			if (save_type == 'calendar') {
				$("#open-calendars").click();
			}else {
				$("#open-templates").click();
			}
		}
		hide_loader();
	  });
}