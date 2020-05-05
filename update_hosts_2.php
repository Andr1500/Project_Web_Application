<?php
		
	require_once "connect1.php";
		
	//connecting to the database
	$connect = new mysqli($host, $db_user, $db_password, $db_name);
	
	$connect = mysqli_connect ($host, $db_user, $db_password);
	mysqli_select_db ($connect, $db_name);
			
	$result = mysqli_query ($connect, "SELECT * FROM hosts");
	$resultlogs = mysqli_query ($connect, "SELECT * FROM hostlogs");
		
	//fetch data from the database
	while ($line = mysqli_fetch_array ($result))
		{
		$id = $line ['id'];
		$name = $line ['host'];
		$count = $line ['attempts'];
		$time = $line ['time'];
		$inactive = $line ['date of inactive'];
		$responsible = $line ['responsible'];
		$active = $line ['active'];
		
		//monitoring hosts
		{
		$port = '80';
		$fp = @fsockopen($name, $port, $errno, $errstr, 30);
		if ($fp) { 
		//updating status of the host, host is active
		$update1 = mysqli_query ($connect, "UPDATE hosts SET attempts='$count' WHERE id='$id'");
		
		$update2 = mysqli_query ($connect, "UPDATE hosts SET `active`='0' WHERE id='$id'");
			
		} 
		else { 	
		
		//updating status of the host, host is inactive
		$name1 = $name;
		$effect=$count+1;
		$update = mysqli_query ($connect, "UPDATE hosts SET attempts='$effect' WHERE id='$id'");
		
		if ($active==1) {
		$update3 = mysqli_query ($connect, "UPDATE hosts SET `date of inactive`=NOW() WHERE id='$id'");
			
		}
		$active1=$active+1;
		$update4 = mysqli_query ($connect, "UPDATE hosts SET `active`='$active1' WHERE id='$id'");
		
		if ($effect>1) {			
		//calculating total inactivity time 
		
		$new_time = $time + 60;
		
		$update = mysqli_query ($connect, "UPDATE hosts SET time='$new_time' WHERE id='$id'");
		
		
		}  
		
		if ($active==1) {
		// add log to the database when host changes status to inactive
		require_once "connect1.php";
	
		$conn1 = new mysqli($host, $db_user, $db_password, $db_name);
		
		
		$addlogs = $conn1->query ("INSERT INTO hostlogs VALUES(NULL, '$name1', '1', '60', NOW())");
		}
		}
		}
		
		require_once "connect1.php";

		$connect = new mysqli($host, $db_user, $db_password, $db_name);
	
		$connect = mysqli_connect ($host, $db_user, $db_password);
		mysqli_select_db ($connect, $db_name);
	
		$resultlogs = mysqli_query ($connect, "SELECT `id`, `host`, `attempts`, `time` FROM `hostlogs` WHERE `host`='$name1' ORDER BY `id` DESC LIMIT 1");
		
		//fetch data from the database
		while ($line = mysqli_fetch_array ($resultlogs))
		{	
		$maxid = $line ['id'];
		$maxname = $line ['host'];
		$maxcount = $line ['attempts'];
		$maxtime = $line ['time'];
		{
		if ($active>1) {
		$maxcount1=$maxcount + 1;
		$updatelog = mysqli_query ($connect, "UPDATE hostlogs SET attempts='$maxcount1' WHERE  `id`='$maxid'");	
		//add log to hostlogs table
		$maxtime1=$maxtime + 60;
		$updateresult = mysqli_query ($connect, "UPDATE `hostlogs` SET `time`='$maxtime1' WHERE `id`='$maxid'");
		}
		else {	
		}	
		}	
	}
}
?>

<?php
require_once "connect1.php";

		$connect = new mysqli($host, $db_user, $db_password, $db_name);
	
		$connect = mysqli_connect ($host, $db_user, $db_password);
		mysqli_select_db ($connect, $db_name);
	
		$resultadmin = mysqli_query ($connect, "SELECT `id`, `activetime`, `activeadmin` FROM `adminlogs` WHERE `active`=1 ORDER BY `id` DESC LIMIT 1");
		
		//fetch data from the database
		while ($line = mysqli_fetch_array ($resultadmin))
		{	
		
		$adminid = $line ['id'];
		$admintime = $line ['activetime'];
		$actadmin = $line ['activeadmin'];
		$admactive = $line ['active'];
		echo "$adminid";
		echo "$admintime";
		echo "$actadmin";
		
		
		if ($adminid>1) {
		$admintime1=$admintime + 60;
		$updateadmin = mysqli_query ($connect, "UPDATE `adminlogs` SET `activetime`='$admintime1' WHERE `active`=1");
		//add log to adminlog table
		}
		else {	
		}
		}
?>



