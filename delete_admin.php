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
	if (isset($_POST['nick1']))
	{
		// validation check
		$everything_ok=true;
		
		$nick1 = $_POST['nick1'];
		
		//check length of username
		if ((strlen($nick1)<3) || (strlen($nick1)>18))
		{
			$everything_ok=false;
			$_SESSION['e_nick1']="Username needs to be from 3 to 18 characters";
		}
		
		
		// remember added data
		$_SESSION['fr_nick1'] = $nick1;
		
		require_once "connect1.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		
			$conndb1 = new mysqli($host, $db_user, $db_password, $db_name);

			if ($conndb1->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//check whether this account exists in the database or not
				$result1 = $conndb1->query("SELECT id FROM admins WHERE login='$nick1'");
				
				if (!$result1) throw new Exception($conndb1->error);
				
				$nickindb = $result1->num_rows;
				if($nickindb>0)
				{
					$everything_ok=true;
					 
					$deleteadmin = $conndb1->query ("DELETE FROM `admins` WHERE `login` = '$nick1'");
					$_SESSION['e_deleteadmin']="This admin account has been deleted from the database";
					
				}
				
				else
				{
				$everything_ok=false;
				$_SESSION['e_nodeleteadmin']="This admin account does not exist in the Database";
				
				$conndb1->close();
				}
	
				
			}
		
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta charset="utf-8">
	  <title>Delete an admin account</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	</head>

	<body>
	<div class="container-2">
		<div class="row">
			<div class="col-md-12">
			
			<h2><span class="glyphicon glyphicon-bookmark"></span>Delete an admin account</h2>
			
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
		
		<h3>Admin: <br/> <input type="text" value="<?php
			if (isset($_SESSION['fr_nick1']))
			{
				echo $_SESSION['fr_nick1'];
				unset($_SESSION['fr_nick1']);
			}
		?>" name="nick1" /><br/>
		
		<?php
			if (isset($_SESSION['e_nick1']))
			{
				echo '<div class="error">'.$_SESSION['e_nick1'].'</div>';
				unset($_SESSION['e_nick1']);
			}
		?></h3>
		
		
		<?php
		if (isset($_SESSION['e_deleteadmin']))
		{
			echo '<h3><span style="color:green;">'.$_SESSION['e_deleteadmin'].'</span></h3>';
			unset($_SESSION['e_deleteadmin']);
		}
		?>
		
		<?php
		if (isset($_SESSION['e_nodeleteadmin']))
		{
			echo '<h3><span style="color:red;">'.$_SESSION['e_nodeleteadmin'].'</span></h3>';
			unset($_SESSION['e_nodeleteadmin']);
		}
		?>
	
		</div>
		<p><input type="submit"  class="btn btn-success margin-left-less row-m-t" value="Delete an admin account" /></p>
		
		</form>
		
		</article>
		</main>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>