<!DOCTYPE html>
<html lang="en">
	<head>
		<title>SMS Reminder Admin</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/modal.css">
		<link rel="stylesheet" href="css/admin-styles.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script type="text/javascript">
            $(function() {
    	    	$("#forms").show();
            });
		</script>
	</head>
    <body>
        <div id="fade"></div>
	    	<div id="forms" class="col-md-5">
                <form method="post">
                  <div class="form-group">
                    <label for="password">Administrator password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                  </div>
    			  <div class="form-group">
					  <div class="pull-right">
                          <button type="submit" class="btn btn-default">Submit</button>
                      </div>
                  </div>
    			  <div class="row">
                  </div>
                </form>
	    	</div>
		</div>
	</body>
</html>