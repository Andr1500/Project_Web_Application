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
	if (isset($_POST['host1']))
	{
		//validation check
		$everything_ok=true;
		
		
		$host1 = $_POST['host1'];
		
		//check length of host name
		if ((strlen($host1)<3) || (strlen($host1)>30))
		{
			$everything_ok=false;
			$_SESSION['e_host1']="Host name must be at least 3 characters long";
		}

		$person1 = $_POST['person1'];
		
		//remember entered data
		$_SESSION['fr_host1'] = $host1;
		$_SESSION['fr_person1'] = $person1;
		
		require_once "connect1.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try 
		{
			$conn1 = new mysqli($host, $db_user, $db_password, $db_name);

			if ($conn1->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//check whether this host address exists in the database or not
				$result1 = $conn1->query("SELECT id FROM hosts WHERE host='$host1'");
				
				if (!$result1) throw new Exception($conn1->error);
				
				$hostsindb = $result1->num_rows;
				if($hostsindb>0)
				{
					$everything_ok=false;
					$_SESSION['e_host1']="This host already exists in the Database";
				}

				//check whether this responsible engineer exists in the database or not
				$result1 = $conn1->query("SELECT id FROM responsible WHERE `responsible person`='$person1'");
				
				if (!$result1) throw new Exception($conn1->error);
				
				$personindb = $result1->num_rows;
				if($personindb==0)
				{
					$everything_ok=false;
					$_SESSION['e_person1']="Please pick the responsible engineer from the table or add a new entry";
				}
				
				if ($everything_ok==true)
				{
				$port = '80';
				$fp = @fsockopen($host1, $port, $errno, $errstr, 30);
				
				if ($fp) { 
				
					//add host data to the Database, host is active
					if ($conn1->query("INSERT INTO hosts VALUES (NULL, '$host1', '0', '0', NULL,'$person1', '0')"))
					{
						$_SESSION['success_add']=true;
						$_SESSION['e_add1']="Host has been added to the database. Host is active";
					}
					else
					{
						throw new Exception($conn1->error);
					}
				}
					else
					{
					//add host data to the Database, host is inactive
					if ($conn1->query("INSERT INTO hosts VALUES (NULL, '$host1', '1', '0', NOW(),'$person1', '1')"))
					{
						
						$_SESSION['e_add2']="Host has been added to the database. Host is inactive";
					}
					else
					{
						throw new Exception($conn1->error);
					}
				
				}
				}
				$conn1->close();
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
	 <title>New host</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	</head>

	<body>
	
	<div class="container-2">
		<div class="row">
			<div class="col-md-12">
			<h2><span class="glyphicon glyphicon-bookmark"></span>Manage hosts</h2>
			<form>
			<input type="button" class="btn btn-primary margin-left-less row-m-t" value="RETURN" onClick='location.href="list.php"'>
			<input type="button" class="btn btn-warning margin-left row-m-t" value="Delete a host" onClick='location.href="delete_host.php"'>
			</form>
		</div> 	
	</div>

	 <div class="container-1">

	 <main>
	 <article>
	<form method="post">
		<div class="row row-m-t margin-left-less">
		
		<h3>Add a new host: <br/> <input type="text" value="<?php
			if (isset($_SESSION['fr_host1']))
			{
				echo $_SESSION['fr_host1'];
				unset($_SESSION['fr_host1']);
			}
		?>" name="host1" /><br/>
		
		<?php
			if (isset($_SESSION['e_host1']))
			{
				echo '<h3 ><span style="color:red;">'.$_SESSION['e_host1'].'</span></h3>';
				unset($_SESSION['e_host1']);
			}
		?></h3>
		
		<h3>Responsible engineer: <br/> <input type="text" value="<?php
			if (isset($_SESSION['fr_person']))
			{
				echo $_SESSION['fr_person1'];
				unset($_SESSION['fr_person1']);
			}
		?>" name="person1" /><br/>
		
		<?php
			if (isset($_SESSION['e_person1']))
			{
				echo '<h3 ><span style="color:red;">'.$_SESSION['e_person1'].'</span></h3>';
				unset($_SESSION['e_person1']);
			}
		?></h3>

		<?php
			if (isset($_SESSION['e_add1']))
			{
				echo '<h3 ><span style="color:green;">'.$_SESSION['e_add1'].'</span></h3>';
				unset($_SESSION['e_add1']);
			}
		?>
		
		<?php
			if (isset($_SESSION['e_add2']))
			{
				echo '<h3><span style="color:green;">'.$_SESSION['e_add2'].'</span></h3>';
				unset($_SESSION['e_add2']);
			}
		?>
	
		</div>
		<p><input type="submit"  class="btn btn-success margin-left-less row-m-t" value="Add a new host" /></p>
		
		</form>
		
		</article>
		</main>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>