<?php
session_start();

// reload counter
if (!isset($_SESSION['counter'])) $_SESSION['counter']=0;
 " ".$_SESSION['counter']++."
<a href=".$_SERVER['PHP_SELF'].'?'.session_name().'='.session_id().">";

require_once 'database.php';

// check session
if (!isset($_SESSION['logged_id'])) {

	if (isset($_POST['login'])) {
		
		$login = filter_input(INPUT_POST, 'login');
		$password = filter_input(INPUT_POST, 'pass');	
		$userQuery = $db->prepare('SELECT login, password FROM admins WHERE login = :login');
		$userQuery->bindValue(':login', $login, PDO::PARAM_STR);
		$userQuery->execute();
		
		$user = $userQuery->fetch();
		
		if ($user && password_verify($password, $user['password'])) {
			$_SESSION['logged_id'] = $user['login'];
			unset($_SESSION['bad_attempt']);
			
		} else {
			$_SESSION['bad_attempt'] = true;
			header('X-Frame-Options: SAMEORIGIN');
			header('Location: index.php');
			exit();
		}	
		} else {
		header('X-Frame-Options: SAMEORIGIN');
		header('Location: index.php');
		exit();
		}
		}
		$usersQuery = $db->query('SELECT * FROM hosts');
		$users = $usersQuery->fetchAll();


		if( isset( $_POST['LOGOUT'] ) )
			{
			
			require_once "connect1.php";	
			$conn1 = new mysqli($host, $db_user, $db_password, $db_name);
			$conn1 = mysqli_connect ($host, $db_user, $db_password, $db_name);
			mysqli_select_db ($conn1, $db_name);
			$sessionactiveadmin = $_SESSION['logged_id'];
			$resultadmin = mysqli_query ($conn1, "SELECT `id`, `activetime`, `activeadmin` FROM `adminlogs` WHERE `activeadmin`='$sessionactiveadmin' ORDER BY `id` DESC LIMIT 1");
			while ($line = mysqli_fetch_array ($resultadmin))
			{
			$outid = $line ['id'];

			if ($updateadmin = mysqli_query ($conn1, "UPDATE `adminlogs` SET `active`=0 WHERE `id`='$outid'"))
			{
				$_SESSION['logoutcorrect']=true;
				header('X-Frame-Options: SAMEORIGIN');
				header('Location: logout.php');
			}
			else
			{
			
			}
			}
			}
			

?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="utf-8">
    <title>Hosts</title>
	
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	
	</head>

	<body>

	<div class="container">
		<div class="row">
			<div class="col-md-12 margin-left row-m-t">
			<h1><span class="glyphicon glyphicon-bookmark"></span>Hosts</h1>
			</div>			
		</div>
	</div>
	
	<div class="container">
		<div class="row">
			<div class="col-md-12 align_right">
			
			<form method="POST">
			<p>
			<input type="submit" class="btn btn-primary margin-left row-m-t margin-right-1" name="LOGOUT" value="LOGOUT" onClick='location.href="logout.php"'>
			</p>
			</form>
			</div>
		</div>			
	</div>
		
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			
			<form>
			<input type="button" class="btn btn-success margin-left row-m-t" value="Manage hosts" onClick='location.href="registration_host.php"'>
			<input type="button" class="btn btn-success margin-left row-m-t" value="Manage admin accounts" onClick='location.href="registration_admin.php"'>
			<input type="button" class="btn btn-warning margin-left row-m-t" value="Manage responsible engineers" onClick='location.href="contacts.php"'>
			<input type="button" class="btn btn-info margin-left row-m-t" value="Activity logs" onClick='location.href="admin_logs.php"'>
			<input type="button" class="btn btn-info margin-left row-m-t" value="Host downtime logs" onClick='location.href="host_logs.php"'>
			</form>
			</div>
		</div> 	
	</div>
	
	
	<div class="container">
		<div class="row">
		<div class="col-md-12 margin-left row-m-t">
			<?php
			$sessionactiveadmin = $_SESSION['logged_id'];
			echo "<h4>Administrator: ".$sessionactiveadmin.' </h4>';
			$time1 = time();
			
			$time2 = date ("r", $time1);
			 echo '<h4>Server date and time: ' . $time2."</h4>";
			 ?>
			</div>
		</div>
	 
		<div class="row">
		<div class="col-md-12 margin-left">
		
		<table class="table">
		
		<tr><th colspan="7">Number of hosts: <?= $usersQuery->rowCount() ?></th></tr>
		<tr> <th>ID</th> <th>Host name</th> <th>Availability</th> <th>Number of attempts</th> <th>Total inactivity time</th> <th>Last disconnection time</th> <th>Responsible engineer</th> </tr>
	
	<?php
	
	require_once "connect1.php";

	$connect = new mysqli($host, $db_user, $db_password, $db_name);
	
	$connect = mysqli_connect ($host, $db_user, $db_password);
	$sessionactiveadmin = $_SESSION['logged_id'];
	mysqli_select_db ($connect, $db_name);
	$inquiry = mysqli_query ($connect, "SELECT * FROM hosts");
	
	//convert data into the table
	while ($line = mysqli_fetch_array ($inquiry))
	{
		$id = $line ['id'];
		$name = $line ['host'];
		$count = $line ['attempts'];
		$time = $line ['time'];
		$inactive = $line ['date of inactive'];
		$responsible = $line ['responsible'];
		$active = $line ['active'];
		
		if($active==0){
		$hours = (int)($time/3600);
		$minutes = (int)($time/60%60);
		$seconds = $time%60;
		echo "<tr> <th>$id</th> <td>$name</td> <td>Active</td>
		<td>$count</td><td>$hours h. $minutes min. $seconds s.</td><td>$inactive</td>
		<td>$responsible</td></tr>";
	}
	else{
		$hours = (int)($time/3600);
		$minutes = (int)($time/60%60);
		$seconds = $time%60;
		echo "<tr><td>$id</td><td>$name</td><td>Inactive</td>
		<td>$count</td><td>$hours  h. $minutes min. $seconds s.</td><td>$inactive</td>
		<td>$responsible</td></tr>";		
	}		
	}
	
?>
		
	
	</table>
	</div>
	</div>
	</div>	
	
	  
<?php

	$activeadmin = $_SESSION['counter'];
	$sessionactiveadmin = $_SESSION['logged_id'];

	require_once "connect1.php"; 
		
	$conn = new mysqli($host, $db_user, $db_password, $db_name);
	$conn = mysqli_connect ($host, $db_user, $db_password, $db_name);

	// check connection
	if ($activeadmin==1) {	  
	$ipaddress = $_SERVER["REMOTE_ADDR"];
	function ip_details($ip) {
	$json = file_get_contents ("http://ipinfo.io/{$ip}/geo");
	$details = json_decode ($json);
	return $details;
	}
	$details = ip_details($ipaddress);
	$country = $details -> country;
	$region = $details -> region;	
	$city = $details -> city;	
	$loc = $details -> loc;  
	$nrip = $details -> ip;
	
	require_once "connect1.php";	
	$conn1 = new mysqli($host, $db_user, $db_password, $db_name);
	$conn1 = mysqli_connect ($host, $db_user, $db_password, $db_name);
	$timenow1 = time ();
	$timenow2 = date ("r", $timenow1);
	$timenow = date("Y-m-d H:i:s");
	
	//insert data into database  
	$sql = mysqli_query ($conn1, "INSERT INTO adminlogs (id, date, activetime, country, region, city, ipaddress, location, activeadmin, active) VALUES (NULL, '$timenow', '0', '".$country."', '".$region."', '".$city."', '".$nrip."', '".$loc."', '$sessionactiveadmin', '1')");
	}
	else{
		
	}
?>
	

	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      
    <script src="js/bootstrap.min.js"></script>
	    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
</body>
</html>