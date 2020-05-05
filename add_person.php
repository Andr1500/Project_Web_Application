<?php
session_start();

require_once 'database.php';
// check of the session

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
		
		header('Location: list.php');
		header('X-Frame-Options: SAMEORIGIN');
		exit();
	}
}

?>

<?php
	if (isset($_POST['email']))
	{
		//validation check
		$everything_ok=true;
		
		//check name of responsible engineer
		$nick = $_POST['nick'];
		
		//check length of nickname
		if ((strlen($nick)<3) || (strlen($nick)>30))
		{
			$everything_ok=false;
			$_SESSION['e_nick']="Name must be at least 3 characters long";
		}
		
		
		//check phone number of responsible engineer
		$phone = $_POST['phone'];
		
		//check length of phone number
		if ((strlen($phone)<9) || (strlen($phone)>20))
		{
			$everything_ok=false;
			$_SESSION['e_phone']="Phone number must contain at least 9 digits";
		}
		
		if (ctype_digit($phone)==false)
		{
			$everything_ok=false;
			$_SESSION['e_phone1']="Phone number must contain only numbers";
		}
		
		// check e-mail address
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$everything_ok=false;
			$_SESSION['e_email']="Please provide an existing e-mail address";
		}
		
		
		// save input data
		$_SESSION['fr_nick'] = $nick;
		$_SESSION['fr_phone'] = $phone;
		$_SESSION['fr_email'] = $email;
		
		
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
				$result = $conn->query("SELECT id FROM responsible WHERE `e-mail`='$email'");
				
				if (!$result) throw new Exception($conn->error);
				
				$personmail = $result->num_rows;
				if($personmail>0)
				{
					$everything_ok=false;
					$_SESSION['e_email']="This e-mail already exists in the Database";
				}		
				// looking for identical name in the database
				$result = $conn->query("SELECT id FROM responsible WHERE `responsible person`='$nick'");
				
				if (!$result) throw new Exception($conn->error);
				
				$personname = $result->num_rows;
				if($personname>0)
				{
					$everything_ok=false;
					$_SESSION['e_nick']="This name is already in use. Please enter a different name";
				}
				if ($everything_ok==true)
				{
					//if everything is ok, add a new responsible engineer
					
					if ($conn->query("INSERT INTO responsible VALUES (NULL, '$nick', '$phone', '$email')"))
					{
						$_SESSION['addpersonok']=true;
						$_SESSION['e_addperson']="New responsible engineer has been added to the database";
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
	<title>New engineer</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	</head>

	<body>
	
	<div class="container-2">
		<div class="row">
			<div class="col-md-12">
			<h2><span class="glyphicon glyphicon-bookmark"></span>Add a new responsible engineer entry</h2>
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
		
		<h3>Name: <br /> <input type="text" value="<?php
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
		
		<h3>Phone number: <br /> <input type="text" value="<?php
			if (isset($_SESSION['fr_phone']))
			{
				echo $_SESSION['fr_phone'];
				unset($_SESSION['fr_phone']);
			}
		?>" name="phone" /><br />
		
		<?php
			if (isset($_SESSION['e_phone']))
			{
				echo '<span style="color:red;">'.$_SESSION['e_phone'].'</span>';
				
				unset($_SESSION['e_phone']);
			}
		?>
		
		<?php
			if (isset($_SESSION['e_phone1']))
			{
				echo '<span style="color:red;">'.$_SESSION['e_phone1'].'</span>';
				
				unset($_SESSION['e_phone1']);
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
		
		<?php
		if (isset($_SESSION['e_addperson']))
		{
			echo '<h3><span style="color:green;">'.$_SESSION['e_addperson'].'</span></h3>';
			unset($_SESSION['e_addperson']);
		}
		?>
	
		</div>
		<input type="submit" class="btn btn-success margin-left-less row-m-t" value="Add a new responsible engineer" />
		
		</form>
		
		</article>
		</main>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>