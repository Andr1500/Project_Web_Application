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

$usersQuery = $db->query('SELECT * FROM adminlogs');
$users = $usersQuery->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	
    <meta charset="utf-8">
    <title>Admin. logs</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

     <link rel="stylesheet" href="css/bootstrap.min.css" >
</head>

<body>

    <div class="container">
		<div class="row">
			<div class="col-md-12 margin-left row-m-t">
			
			<h1><span class="glyphicon glyphicon-bookmark"></span>Admin activity logs</h1>	
           
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
		
			
		<tr><th>ID</th><th>Date</th><th>Active time</th><th>Country</th><th>Region</th><th>City</th><th>IP Address</th><th>Location</th><th>Admin</th></tr>
		
		</thead>
		
				
		<?php
				
		require_once "connect1.php";

		$connect = new mysqli($host, $db_user, $db_password, $db_name);
	
		$connect = mysqli_connect ($host, $db_user, $db_password);
		mysqli_select_db ($connect, $db_name);
			
		$result = mysqli_query ($connect, "SELECT * FROM adminlogs ");		

			//fetch data from database
			while ($line = mysqli_fetch_array ($result))
			{
			$id = $line ['id'];
			$date = $line ['date'];
			$activetime = $line ['activetime'];
			$country = $line ['country'];
			$region = $line ['region'];
			$city = $line ['city'];
			$ipaddress = $line ['ipaddress'];
			$location = $line ['location'];
			$activeadmin = $line ['activeadmin'];
			$hours = (int)($activetime/3600);
			$minutes = (int)($activetime/60%60);
			$seconds = $activetime%60;
			
			print "<TR><TD>"; //ID of the session
			echo $id;

			print "</TD><TD>"; // Date and time of logging in
			echo $date;

			print "</TD><TD>";// Time of active session			
			echo "$hours h. $minutes min.  $seconds s.";

			print "</TD><TD>"; // Country 
			echo $country;
			
			print "</TD><TD>"; // region 
			echo $region;
			
			print "</TD><TD>"; // city 
			echo $city;

			print"</TD><TD>"; // IP address of an admin device
			echo $ipaddress;

			print"</TD><TD>
			<a href='
			https://www.google.com/maps/search/?api=1&query=$city'>GoogleMaps</a><br>"; 
			// location of admin device

			print
			"</TD><TD>";
			echo $activeadmin; // Active admin 
			print"</TD></TR>\n";
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