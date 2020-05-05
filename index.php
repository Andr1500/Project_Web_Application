<?php
session_start();
$_SESSION['counter']=0;
if (isset($_SESSION['logged_id'])) {
	header('X-Frame-Options: SAMEORIGIN');
	header('Location: list.php');
	exit();
}	
?>

<!DOCTYPE html>
<html lang="en">
	
	<head>
	<meta charset="utf-8">
    <title>Monitoring hosts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	</head>
	
	<body>

	<div class="container-2">
		<div class="row">
			<div class="col-md-12">
			<h2>
			<span class="glyphicon glyphicon-bookmark"></span>SH Monitoring
			</h2>
			
			</div> 	
		</div>
	
	
	
	
    <div class="container-1">
	<div class="row">
        <main>
            <article>
				
                <form method="post" action="list.php">
					<div class="row row-m-t margin-left">
                    
					<h3>Login</h3>
					<label>
					<input type="text" name="login">
					</label>
					
					</div>
					<div class="row row-m-t margin-left">
                    
					<h3>Password</h3>
					<label>
					<input type="password" name="pass">
					</label>
					
					<?php
					if (isset($_SESSION['bad_attempt'])) {
						echo '<h3><span style="color:red;">Incorrect login or password. Please try again</span></h3>';
						unset($_SESSION['bad_attempt']);	
					}
					?>
					
					</div>
					
					
					<input type="submit" class="btn btn-primary btn-lg margin-left row-m-t" value="LOGIN" onClick='location.href="list.php"'>
					
					
					 	
				</div>
				
				<div class="row row-m-t margin-left">
				<h5><span style="color:red;">Access only for authorised users. All rights reserved.</span></h5>
				</div>	
				
				
				
                </form>
            </article>
        </main>
	</div>
	
	
	
    </div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	</div>
	
	</body>
</html>