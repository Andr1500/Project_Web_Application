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
	if (isset($_POST['email']))
	{
		//validation check
		$everything_ok=true;
		
		//check username
		$nick = $_POST['nick'];
		
		//check length of username
		if ((strlen($nick)<3) || (strlen($nick)>20))
		{
			$everything_ok=false;
			$_SESSION['e_nick']="Username must be at least 3 characters long";
		}
		if (ctype_alnum($nick)==false)
		{
			$everything_ok=false;
			$_SESSION['e_nick']="Username can contain only letters and numbers";
		}
		// check e-mail address
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$everything_ok=false;
			$_SESSION['e_email']="Please provide an existing e-mail address";
		}
		// check password
		$password_1 = $_POST['password_1'];
		$password_2 = $_POST['password_2'];
		
		if ((strlen($password_1)<8) || (strlen($password_1)>20))
		{
			$everything_ok=false;
			$_SESSION['e_password']="Password must contain between 8 and 20 characters";
		}
		if ($password_1!=$password_2)
		{
			$everything_ok=false;
			$_SESSION['e_password_2']="Passwords do not match";
		}	
		$password_hash = password_hash($password_1, PASSWORD_DEFAULT);
		
		// save input data
		$_SESSION['fr_nick'] = $nick;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_password_1'] = $password_1;
		$_SESSION['fr_password_2'] = $password_2;
		
		require_once "connect1.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try 
		{
			$conn = new mysqli($host, $db_user, $db_password, $db_name);
			if ($conn->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//looking for identical e-mail in the database
				$result = $conn->query("SELECT id FROM admins WHERE email='$email'");
				
				if (!$result) throw new Exception($conn->error);
				
				$mailsindb = $result->num_rows;
				if($mailsindb>0)
				{
					$everything_ok=false;
					$_SESSION['e_email']="This e-mail address already exists in the Database";
				}		
				// looking for identical username in the database
				$result = $conn->query("SELECT id FROM admins WHERE login='$nick'");
				
				if (!$result) throw new Exception($conn->error);
				
				$nicksindb = $result->num_rows;
				if($nicksindb>0)
				{
					$everything_ok=false;
					$_SESSION['e_nick']="This name is already in use. Please enter a different name";
				}
				if ($everything_ok==true)
				{
					//if everything is ok, add a new admin account
					
					if ($conn->query("INSERT INTO admins VALUES (NULL, '$nick', '$password_hash', '$email')"))
					{
						$_SESSION['registrationok']=true;
						$_SESSION['e_addadmin']="New admin account has been added to the Database";
					}
					else
					{
						throw new Exception($conn->error);
					}
				}
				$conn->close();
			}
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Database is currently unavailable</span>';
			echo '<br />Information: '.$e;
		}
		
	}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta charset="utf-8">
	 <title>New admin account</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	</head>

	<body>
	
	<div class="container-2">
		<div class="row">
			<div class="col-md-12">
			<h2><span class="glyphicon glyphicon-bookmark"></span>Manage admin accounts</h2>
			<form>
			<input type="button" class="btn btn-primary margin-left-less row-m-t" value="RETURN" onClick='location.href="list.php"'>
			<input type="button" class="btn btn-warning margin-left row-m-t" value="Delete an admin account" onClick='location.href="delete_admin.php"'>
			</form>
		</div> 	
	</div>

	 <div class="container-1">

	 <main>
	 <article>
	<form method="post">
		<div class="row row-m-t margin-left-less">
		
		<h3>Username: <br /> <input type="text" value="<?php
			if (isset($_SESSION['fr_nick']))
			{
				echo $_SESSION['fr_nick'];
				unset($_SESSION['fr_nick']);
			}
		?>" name="nick" /><br />
		
		<?php
			if (isset($_SESSION['e_nick']))
			{
				echo '<span style="color:red;">'.$_SESSION['e_nick'].'</span>';
				unset($_SESSION['e_nick']);
			}
		?></h3>
		
		<h3>E-mail: <br /> <input type="text" value="<?php
			if (isset($_SESSION['fr_email']))
			{
				echo $_SESSION['fr_email'];
				unset($_SESSION['fr_email']);
			}
		?>" name="email" /><br />
		
		<?php
			if (isset($_SESSION['e_email']))
			{
				echo '<span style="color:red;">'.$_SESSION['e_email'].'</span>';
				
				unset($_SESSION['e_email']);
			}
		?></h3>
		
		
		<h3>Password: <br /> <input type="password"  value="<?php
			if (isset($_SESSION['fr_password_1']))
			{
				echo $_SESSION['fr_password_1'];
				unset($_SESSION['fr_password_1']);
			}
		?>" name="password_1" /><br />
		
		<?php
			if (isset($_SESSION['e_password']))
			{
				echo '<h3 ><span style="color:red;">'.$_SESSION['e_password'].'</span></h3>';
				
				unset($_SESSION['e_password']);
			}
		?></h3>	
		
		
		<h3>Repeat your password: <br /> <input type="password" value="<?php
			if (isset($_SESSION['fr_password_2']))
			{
				echo $_SESSION['fr_password_2'];
				unset($_SESSION['fr_password_2']);
			}
		?>" name="password_2" /><br />
		
		<?php
			if (isset($_SESSION['e_password_2']))
			{
				echo '<h3 ><span style="color:red;">'.$_SESSION['e_password_2'].'</span></h3>';
				
				unset($_SESSION['e_password_2']);
			}
		?>	
		</h3>
		
		
		<?php
		if (isset($_SESSION['e_addadmin']))
		{
			echo '<h3><span style="color:green;">'.$_SESSION['e_addadmin'].'</span></h3>';
			unset($_SESSION['e_addadmin']);
		}
		?>
	
		</div>
		<input type="submit" class="btn btn-success margin-left-less row-m-t" value="Add a new admin account" />
		
		</form>
		
		</article>
		</main>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>