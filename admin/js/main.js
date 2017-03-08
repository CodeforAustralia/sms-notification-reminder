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
    close_pop_up();
    character_count();
});

//Display loader and fade background
function show_loader() {
	$("#fade").show();
	$("#modal").show();
}

//Hide loader and fade background
function hide_loader() {
	$("#fade").hide();
	$("#modal").hide();
}

//Display Add calendar form from template and add validation to form
function add_calendar() {
	$("#add-calendar").on("click", function(){
		$("#fade").show();
		$("#forms").html($("#calendar-form-container").html());
		$("#forms").show();
		validate_calendar_form();
	});
}

//Display edit calendar form from template, fill it and add validation to form
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

//Save calendar and hide modal
function save_edit_calendar() {
		$("#fade").hide();
		$("#forms").hide();
		var calendar = {};
		calendar.name	= $("#forms #calendar-name").val();
		calendar.email	= $("#forms #calendar-email").val();
		update_calendar(calendar);
		$("#forms").html('');
		__current_calendar = {};
		store_data('calendar');
}

//Display Add template form from and add validation to form
function add_template() {
	$("#add-template").on("click", function(){
		$("#fade").show();
		$("#forms").html($("#template-form-container").html());
		$("#forms").show();
		validate_template_form();
		enable_tooltips();
		var content = $("#forms #template-content").val();
		$("#forms #char-count").text(400 - content.length + substract_tags(content));
	});
}

//Display edit template form from , fill it and add validation to form
function open_edit_template() {
	$("body").on("click", ".edit-template", function(){
		$("#fade").show();
		$("#forms").html($("#template-form-container").html());
		$("#forms").show();
		var template_info = __current_template = get_template_by_abbr($(this).attr("id"));
		fill_template_window(template_info);
		validate_template_form();
		enable_tooltips();
	});
}

//Save template and hide modal
function save_edit_template() {
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
}

//Enable bootstrap tooltips
function enable_tooltips() {
	$('[data-toggle="tooltip"]').tooltip(); 
}

//Get calendars from service or logout if lost session
function get_calendars(){
	show_loader();  	
  	var data_url = "/services/get_calendars.php";
  	$.post( data_url )
	  .done(function( data ) {
	  	__calendars = JSON.parse(data);
	    var calendars = {};	
	    calendars.calendar = JSON.parse(data);
	    
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

//Set calendar information using MustacheJs template 
function render_calendars(calendars) {
	var targetContainer = $(".calendar-table tbody"),
    template = $("#calendar-template").html();
	var html = Mustache.to_html(template, calendars);
	$(targetContainer).html(html);
}

//Get templates from service or logout if lost session
function get_templates(){
	show_loader();  	
  	var data_url = "/services/get_templates.php";
  	$.post( data_url )
	  .done(function( data ) {
	  	__templates = JSON.parse(data);
	    var templates = {};	
	    templates.templates = JSON.parse(data);
	    
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

//Set template information using MustacheJs template 
function render_templates(templates) {
	var targetContainer = $(".template-table tbody"),
    template = $("#template-template").html();
	var html = Mustache.to_html(template, templates);
	$(targetContainer).html(html);
}

//Open calendar tab on left menu
function open_calendars(){
	$("#open-calendars").on("click", function() {
	    get_calendars();
	});
}

//Open template tab on left menu
function open_templates(){
	$("#open-templates").on("click", function() {
	    get_templates();
	});
}

//Get information from an specific email inside the calendars object
function get_calendar_by_email(email) {
  return __calendars.filter(
    function(__calendars) {
      return __calendars.email == email
    }
  );
}

//Get information from an specific abbreviation inside the templates object
function get_template_by_abbr(abbr) {
  return __templates.filter(
    function(__templates) {
      return __templates.abbreviation == abbr
    }
  );
}

//Fill calendar information in pop-up window
function fill_calendar_window(information) {
	$("#forms #calendar-name").val(information[0].name);
	$("#forms #calendar-email").val(information[0].email);
}

//Fill template information in pop-up window
function fill_template_window(information) {
	$("#forms #template-name").val(information[0].name);
	$("#forms #template-abbreviation").val(information[0].abbreviation);
	$("#forms #template-phone").val(information[0].office_number);
	$("#forms #template-content").val(information[0].content);
	$("#forms #char-count").text(400 - information[0].content.length + substract_tags(information[0].content));
}

//Put tag text inside template content after clicking tag button
function shortcut_button(){
	$("body").on("click", "#forms .shortcut-tag", function(){	
	   var $txt = $("#forms #template-content");
       var caretPos = $txt[0].selectionStart;
       var textAreaTxt = $txt.val();
       var txtToAdd = $(this).text();
       $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
	});
}

//Update calendars object checking if its a delition, an edition or a creation
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

//Update templates object checking if its a delition, an edition or a creation
function update_template(template){
	var template_found = false;
	var search_previous = get_template_by_abbr(template.abbreviation);
	for (pos in __templates){
		if(__current_template.length > 0 && __templates[pos].abbreviation == __current_template[0].abbreviation){//editing
			if(__current_template[0].abbreviation != template.abbreviation && search_previous.length < 1) {//if modifying abbreviation ensure that doesnt exist
			  	__templates[pos].abbreviation = template.abbreviation;
				__templates[pos].name		= template.name;
				__templates[pos].office_number		= template.office_number;
				__templates[pos].content	= template.content;
			} else if(__current_template[0].abbreviation == template.abbreviation) {
				__templates[pos].name		= template.name;
				__templates[pos].office_number		= template.office_number;
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

//On click button functionality for dynamic elements
function delete_calendar(){
	$("body").on("click", ".delete-calendar", function() {
	    var email = $(this).attr("id");
	    __calendars = remove_calendar(email);
	    store_data('calendar');
	})
}

//Delete calendar by email
function remove_calendar(email){
	return __calendars.filter(function(calendar) { 
	   return calendar.email !== email;  
	});
}

//On click button functionality for dynamic elements
function delete_template(){
	$("body").on("click", ".delete-template", function() {
	    var abbreviation = $(this).attr("id");
	    __templates = remove_template(abbreviation);
	    store_data('template');
	})
}

//Delete template by abbreviation
function remove_template(abbreviation){
	return __templates.filter(function(template) { 
	   return template.abbreviation !== abbreviation;  
	});
}

//Jquery validation for calendar form
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

//Jquery validation for template form
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

//Save calendars or templates object
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

//Close when adding or editting calendars or templates
function close_pop_up(){
	$("body").on("click", ".close-icon, .cancel", function() {
	    $("#fade").hide();
		$("#forms").hide();
		__current_calendar = {};
		__current_template = {};
	});
}

//Character counter text and event
function character_count(){
	var max_amount = 400;
	$("body").on("keyup", "#template-content", function(){
		limit_text(this , max_amount + substract_tags($(this).val()));
		$("body #char-count").text((max_amount - $(this).val().length) + substract_tags($(this).val()));
	});
}

//Regec function to avoid counting tags from character count function
function substract_tags(text) {
    var mts = text.match(/\(([^()]+)\)/g );
    var total_char = 0;
    mts.forEach(function(entry) {
    	total_char += entry.length;
	});
	return total_char;
}

//Avoid more than the allowed text in the template content
function limit_text(limitField, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}