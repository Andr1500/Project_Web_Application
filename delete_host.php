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
			$_SESSION['logged_id'] = $user['id'];
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

?>

<?php
	if (isset($_POST['host2']))
	{
		// validation check
		$everything_ok=true;
		
		$host2 = $_POST['host2'];
		
		//check length of host
		if ((strlen($host2)<3) || (strlen($host2)>30))
		{
			$everything_ok=false;
			$_SESSION['e_host2']="Host address needs to be from 3 to 30 characters";
		}
		
		
		// remember added data
		$_SESSION['fr_host2'] = $host2;
		
		require_once "connect1.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		
			$conndb2 = new mysqli($host, $db_user, $db_password, $db_name);

			if ($conndb2->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//check whether this host address exists in the database or not
				$result1 = $conndb2->query("SELECT id FROM hosts WHERE host='$host2'");
				
				if (!$result1) throw new Exception($conndb2->error);
				
				$hostindb = $result1->num_rows;
				if($hostindb>0)
				{
					$everything_ok=true;
					 
					$deletehost = $conndb2->query ("DELETE FROM `hosts` WHERE `host` = '$host2'");
					$_SESSION['e_deletehost']="This host has been deleted from the database";
					
				}
				
				else
				{
				$everything_ok=false;
				$_SESSION['e_nodeletehost']="This host does not exist in the Database";
				
				$conndb2->close();
				}
	
			}
		
	}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta charset="utf-8">
	 <title>Delete host</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	</head>

	<body>
	<div class="container-2">
		<div class="row">
			<div class="col-md-12">
			
			<h2><span class="glyphicon glyphicon-bookmark"></span>Delete a host</h2>
			
			<form>
			<input type="button" class="btn btn-primary margin-left-less row-m-t" value="RETURN" onClick='location.href="list.php"'>
			</form>
			
		</div> 	
	</div>

	 <div class="container-1">
	
	 <main>
	 <article>
	<form method="post">
		<div class="row row-m-t margin-left-less">
		
		<h3>Host: <br/> <input type="text" value="<?php
			if (isset($_SESSION['fr_host2']))
			{
				echo $_SESSION['fr_host2'];
				unset($_SESSION['fr_host2']);
			}
		?>" name="host2" /><br/>
		
		<?php
			if (isset($_SESSION['e_host2']))
			{
				echo '<h3 ><span style="color:red;">'.$_SESSION['e_host2'].'</span></h3>';
		
				unset($_SESSION['e_host2']);
			}
		?></h3>
		
		<?php
			if (isset($_SESSION['e_deletehost']))
			{
				echo '<h3><span style="color:green;">'.$_SESSION['e_deletehost'].'</span></h3>';
				unset($_SESSION['e_deletehost']);
			}
		?>
		
		<?php
			if (isset($_SESSION['e_nodeletehost']))
			{
				echo '<h3><span style="color:red;">'.$_SESSION['e_nodeletehost'].'</span></h3>';
				unset($_SESSION['e_nodeletehost']);
			}
		?>
	
		</div>
		<p><input type="submit"  class="btn btn-success margin-left-less row-m-t" value="Delete a host" /></p>
		
		</form>
		
		</article>
		</main>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>