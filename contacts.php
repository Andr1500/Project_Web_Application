<?php
session_start();

require_once 'database.php';

//check session
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
			header('Location: list.php');
			exit();
		}
			
	} else {
		
		header('Location: list.php');
		exit();
	}
}

$usersQuery = $db->query('SELECT * FROM responsible');
$users = $usersQuery->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
	
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta charset="utf-8">
	<title>Contacts</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	</head>

	<body>

	<div class="container">
		<div class="row">
			<div class="col-md-12 margin-left row-m-t">
			
			<h1><span class="glyphicon glyphicon-bookmark"></span>Contact data</h1>
			
			<form>
			<input type="button" class="btn btn-primary margin-left-less row-m-t" value="RETURN" onClick='location.href="list.php"'>
			<input type="button" class="btn btn-success margin-left row-m-t" value="Add a new engineer entry" onClick='location.href="add_person.php"'>
			<input type="button" class="btn btn-warning margin-left row-m-t" value="Delete an engineer entry" onClick='location.href="delete_person.php"'>
			</form>
			
		</div> 	
	</div>
    
	<div class="container">
		<div class="row">
		<div class="col-md-12 row-m-t">
				
				<table class="table">
				
				
					<thead>
						<tr><th colspan="4">Contacts: <?= $usersQuery->rowCount() ?></th></tr>
						<tr><th colspan="4">Contacts:   </th></tr>
						<tr><th>ID</th><th>Responsible engineer</th><th>Phone number</th><th>E-mail</th></tr>
					</thead>
					<tbody><br /><?php
						foreach ($users as $user) {
							echo "<tr><td>{$user['id']}</td><td>{$user['responsible person']}</td><td>{$user['phone']}</td><td>{$user['e-mail']}</td></tr>";
						}
						?>
					<br />
					</tbody>
					
				</table>
				
		</div>
		</div>
	
    </div>
</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>