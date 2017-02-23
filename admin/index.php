<!DOCTYPE html>
<html lang="en">
	<head>
		<title>SMS Reminder Admin</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/modal.css">
		<link rel="stylesheet" href="css/admin-styles.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.7.2/mustache.min.js"></script>
		<script src="js/jquery-validation/jquery.validate.js"></script>
		<script src="js/main.js"></script>
		
		<script id="calendar-template" type="text/template">
			{{#calendar}}
			<tr>
				<td>{{name}}</td>
				<td>{{email}}</td>
				<td>
					<button id="{{email}}" type="button" class="btn btn-grey btn-xs delete-calendar">Delete</button>
					<button id="{{email}}" type="button" class="btn btn-primary btn-xs edit-calendar">Edit</button>
				</td>
			</tr>
			{{/calendar}}
		</script>
		
		<script id="template-template" type="text/template">
			{{#templates}}
		      <tr>
		        <td>{{name}}</td>
		        <td>{{abbreviation}}</td>
		        <td>{{office_number}}</td>
		        <td>{{content}}</td>
		        <td>
		        	<button id="{{abbreviation}}" type="button" class="btn  btn-grey  btn-xs delete-template">Delete</button>
		        	<button id="{{abbreviation}}" type="button" class="btn btn-primary btn-xs edit-template">Edit</button>
		        </td>
		      </tr>
			{{/templates}}
		</script>
	</head>

	<body>
		<header>
			<ul>
				<li><span><b>SMS</b> Reminder Admin</span></li>
			    <li class="logout"><a class="active" href="/logout.php">Logout</a></li>
			    <li class="admin-menu"><a href="/admin">Admin</a></li>
			</ul>
		</header>
		<div class="container-fluid">
		  <div class="row content"> 	
				<div class="col-sm-3 sidenav">
		  			<h3>Dashboard</h3>
		      		<hr>
					<ul class="nav nav-pills nav-stacked">
						<li id="open-calendars" class="active"><a  data-toggle="tab" href="#home">Calendars</a></li>
						<li id="open-templates"><a data-toggle="tab"  href="#menu1">Templates</a></li>
					</ul>
				</div>

			    <div class="col-sm-9 main-section">
		  			<div class="tab-content">
					  <div id="home" class="tab-pane fade in active">
						<div class="col-md-3 title">
						    <h3>Calendars <span data-toggle="tooltip" title="Add Calendar" class="add-plus" data-placement="right" id="add-calendar"><i class="fa fa-plus-circle" aria-hidden="true"></i></span></h3>
						</div>
		      			<hr class="col-xs-11">
		      			
						    <div class="extracontainer">
					    <table class="table calendar-table">
						    <thead>
						      <tr>
						        <th class="col-xs-4">Name</th>
						        <th class="col-xs-6">Email</th>
						        <th class="col-xs-2">Actions</th>
						      </tr>
						    </thead>
						    <tbody>
						    	
						    </tbody>
					  	</table>
						    </div>
					  	<div id="calendar-form-container">
						    <form class="form-horizontal" id="calendar-form">
								<fieldset>
								
								<!-- Form Name -->
								<legend></legend>
								
									<!-- Text input-->
									<div class="form-group">
									  <label class="col-md-4 control-label" for="">Calendar Name</label>  
									  <div class="col-md-7">
										  <input id="calendar-name" name="calendar-name" type="text" placeholder="" class="form-control input-md" required="">
										  <span class="help-block">Calendar name to be displayed</span>  
									  </div>
									</div>
									
									<!-- Text input-->
									<div class="form-group">
									  <label class="col-md-4 control-label" for="email">Calendar email</label>  
									  <div class="col-md-7">
										  <input id="calendar-email" name="calendar-email" type="text" placeholder="" class="form-control input-md" required="">
										  <span class="help-block">Calendar email to get information from</span>  
									  </div>
									</div>
								
									<div class="form-group">
										<div class="col-md-3 pull-right">
											<button type="submit" class="btn btn-success save-calendar">Save</button>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					  </div>
					  <div id="menu1" class="tab-pane fade">
					  	<div class="col-md-3 title">
					    	<h3>Templates <span data-toggle="tooltip" title="Add Template" class="add-plus" data-placement="right" id="add-template"><i class="fa fa-plus-circle" aria-hidden="true"></i></span></h3>
				    	</div>
		      			<hr class="col-xs-11">
					    <table class="table template-table">
						    <thead>
						      <tr>
						        <th class="col-xs-2">Name</th>
						        <th class="col-xs-2">Abbreviation</th>
						        <th class="col-xs-2">Office Number</th>
						        <th class="col-xs-4">Content</th>
						        <th class="col-xs-2">Actions</th>
						      </tr>
						    </thead>
						    <tbody>
						    	
						    </tbody>
					  	</table>
					  	<div id="template-form-container">
						    <form class="form-horizontal" id="template-form">
								<fieldset>
								
									<!-- Form Name -->
									<legend></legend>
									
									<!-- Text input-->
									<div class="form-group">
									  <label class="col-md-4 control-label" for="template-name">Template Name</label>  
									  <div class="col-md-7">
									  	<input id="template-name" name="template-name" type="text" placeholder="Office Appointment Reminder" class="form-control input-md" required="">
									  </div>
									</div>
									
									<!-- Text input-->
									<div class="form-group">
									  <label class="col-md-4 control-label" for="template-abbreviation">Abbreviation</label>  
									  <div class="col-md-4">
									  	<input id="template-abbreviation" name="template-abbreviation" type="text" placeholder="(XXX)" class="form-control input-md" required="">
									  </div>
									</div>
									
									<!-- Text input-->
									<div class="form-group">
									  <label class="col-md-4 control-label" for="template-phone">Office phone number</label>  
									  <div class="col-md-5">
									  	<input id="template-phone" name="template-phone" type="text" placeholder="00000000" class="form-control input-md" required="">
									  </div>
									</div>
									
									<!-- Textarea -->
									<div class="form-group">
									  <label class="col-md-4 control-label" for="template-content">Template Content</label>
									  <div class="col-md-7">                     
									    <textarea class="form-control" id="template-content" name="template-content" rows="5">You have an appointment on (date) at (time) with Victoria Legal Aid. Location of appointment is at (location). To change call us on (phone).</textarea>
									    <span class="help-block">Tags:</span>  
									    <button type="button" class="btn btn-primary btn-xs shortcut-tag">(date)</button>
									    <button type="button" class="btn btn-primary btn-xs shortcut-tag">(time)</button>
									    <button type="button" class="btn btn-primary btn-xs shortcut-tag">(location)</button>
									    <button type="button" class="btn btn-primary btn-xs shortcut-tag">(phone)</button>
									    
									  </div>
									</div>
									
									<div class="form-group">
										<div class="col-md-3 pull-right">
											<button type="submit" class="btn btn-success save-template">Save</button>
										</div>
									</div>
									
								</fieldset>
							</form>
						</div>
					  </div>
					</div>
			    </div>
		  </div>

			<div id="fade"></div>
	    	<div id="modal">
	    		<div id="loading"><i class="fa fa-spinner fa-spin" style="font-size:24px"></i></div>
	    	</div>
	    	<div id="forms" class="col-md-5"></div>
		</div>
		
		<footer class="footer">
			<div class="container">
				<p class="text-muted"><i class="fa fa-github" aria-hidden="true"></i> If you find an issue or just want to give us your feedback, please use this link. (<a href="https://github.com/CodeforAustralia/sms-notification-reminder/issues/" target="_blank">Link</a>)</p>
			</div>
		</footer>

	</body>
</html>