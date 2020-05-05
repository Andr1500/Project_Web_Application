<?php
session_start();

require_once 'database.php';

// check session
if (!isset($_SESSION['logged_id'])) {

	if (isset($_POST['login'])) {
		
		$login = filter_input(INPUT_POST, 'login');
		$password = filter_input(INPUT_POST, 'pass');
		
		$userQuery = $db->prepare('SELECT id, password FROM admins WHERE login = :login');
		$userQuery->bindValue(':login', $login, PDO::PARAM_STR);
		$userQuery->execute();
		
		$user = $userQuery->fetch();
		
		if ($user && password_verify($password, $user['password'])) {
			$_SESSION['logged_id'] = $user['login'];
			unset($_SESSION['bad_attempt']);
		} else {
			$_SESSION['bad_attempt'] = true;
			header('X-Frame-Options: SAMEORIGIN');
			header('Location: list.php');
			exit();
		}
			
		} else {
		header('X-Frame-Options: SAMEORIGIN');
		header('Location: list.php');
		exit();
	}
}

$usersQuery = $db->query('SELECT * FROM hostlogs');
$users = $usersQuery->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Host logs</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

     <link rel="stylesheet" href="css/bootstrap.min.css" >
	
</head>

<body>

    <div class="container">
		<div class="row">
			<div class="col-md-12 margin-left row-m-t">
			
			<h1><span class="glyphicon glyphicon-bookmark"></span>Host downtime logs</h1>
			
			<form>
			<input type="button" class="btn btn-primary margin-left-less row-m-t" value="RETURN" onClick='location.href="list.php"'>
			</form>
			
		</div> 	
	</div>
	</div>
	
	<div class="container">
	<div class="row">
		<div class="col-md-12 margin-left row-m-t">
	
		<table class="table">
		
	
		<thead>
		
		<tr><th colspan="5">Number of logs: <?= $usersQuery->rowCount() ?></th></tr>	
		<tr><th>ID</th><th>Host</th><th>Attempts</th><th>Inactivity time</th><th>Last disconnection time</th></tr>
		
		</thead>
		
			
		<?php
		
		require_once "connect1.php"; 
		
		// connect with the database
		$connect = new mysqli($host, $db_user, $db_password, $db_name);
	
		$connect = mysqli_connect ($host, $db_user, $db_password);
		mysqli_select_db ($connect, $db_name);
			
		$result = mysqli_query ($connect, "SELECT * FROM hostlogs ");		

			//fetch data from darabase
			while ($line = mysqli_fetch_array ($result))
			{
			$id = $line ['id'];
			$host = $line ['host'];
			$attempts = $line ['attempts'];
			$timebefore = $line ['time'];
			$dateinactive = $line ['date of inactive'];
			$hours = (int)($timebefore/3600);
			$minutes = (int)($timebefore/60%60);
			$seconds = $timebefore%60;
			
			print"<TR><TD>"; 
			echo $id;

			print"</TD><TD>"; 
			echo $host;
			
			print"</TD><TD>"; //attempts
			echo $attempts;

			print"</TD><TD>"; //total inactivity time
			echo "$hours h. $minutes min.  $seconds s.";

			print"</TD><TD>"; //Last disconnection time
			echo $dateinactive;
			}
			print "</table>";	
			
			?>	
			
			
		
		</div>
		
		</div>	
            

    </div>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <script src="js/bootstrap.min.js"></script>
</body>
</html>