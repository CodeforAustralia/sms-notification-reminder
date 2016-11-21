<!DOCTYPE html>
<html lang="en">
	<head>
		<title>SMS Reminder</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/datepicker/bootstrap-datepicker.css">
		<link rel="stylesheet" href="css/modal.css">
		<link rel="stylesheet" href="css/styles.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.7.2/mustache.min.js"></script>
		<script src="js/bootstrap-datepicker.min.js"></script>
		<script src="js/main.js"></script>
		<script id="messages-template" type="text/template">			
			{{#cards}}
				<div class="event-card">				    
		  			<div class="col-xs-1">
				  		<span class="glyphicon glyphicon-user hidden-sm hidden-md hidden-lg"></span> <span class="hidden-xs"><strong>Name</strong></span>
			  		</div>
		  			<div class="col-xs-4">
				  		<span>{{name}}</span>
			  		</div>
		  			<div class="col-xs-1">
				  		<span class="glyphicon glyphicon-phone hidden-sm hidden-md hidden-lg"></span> <span class="hidden-xs"><strong>Phone</strong></span>
			  		</div>
		  			<div class="col-xs-3">
				  		<span>{{mobile}}</span>
			  		</div>
			  		<hr>
					<div class="container-fluid">
					  	<span><strong>Event Type</strong></span> <span>{{type}}</span>
					</div>
					<div class="container-fluid">
					  	<span>{{message}}</span>
					</div>
				</div>
			{{/cards}}
		</script>
		<script id="menu-template" type="text/template">			
			{{#items}}				
				<li id="{{email}}"><a href="#">{{name}}</a></li>
			{{/items}}
		</script>
	</head>

	<body>

		<header>

			<ul>
				<li><span><b>SMS</b> Reminder</span></li>
			    <li class="logout"><a class="active" href="/logout.php">Logout</a></li>
			</ul>
		</header>
		<div class="container-fluid">
		  <div class="row content"> 	
				<div class="col-sm-3 sidenav">
		  			<h3 id="current-calendar">Menu title #1</h3>
		      		<hr>
				  	<div id="calendar"></div>
		      		<hr>
					<ul class="nav nav-pills nav-stacked" id="calendar_list">
						<li id="{{id}}"><a href="#">{{name}}</a></li>
					</ul>
				</div>

		    <div class="col-sm-9 main-section">
		    	<div class="top-buttons">
					<button type="submit" class="btn btn-success" id="send_button">Send</button>
				</div>
				<hr>
				<div class="events-container">
				</div>
		    </div>
		  </div>

			<div id="fade"></div>
	    	<div id="modal">
	      		<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>
	    	</div>
		</div>
		
		<footer class="footer">
			<div class="container">
				<p class="text-muted"><i class="fa fa-github" aria-hidden="true"></i> If you find an issue or just want to give us your feedback, please use this link. (<a href="https://github.com/CodeforAustralia/sms-notification-reminder/issues/" target="_blank">Link</a>)</p>
			</div>
		</footer>

	</body>
</html>