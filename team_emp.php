<?php
	include("dbase.php");
	if(isset($_GET['id_emp']) && !empty($_GET['id_emp']))
	{
		$id_emp = $_GET['id_emp'];
		$sql="SELECT * FROM users WHERE id = $id_emp" ;
		$result=mysqli_query($conn,$sql);
		if($row=mysqli_fetch_assoc($result)){
			//var_dump($row);
			if($row['team_member'] == 0)
				$team_member = 1;
			else
				$team_member = 0;
		
			$sql="UPDATE users SET team_member = $team_member WHERE id = $id_emp";
			if(mysqli_query($conn,$sql)){ 
					header('location:tab_employee.php');die;
				
				}
		}
		else
		{
			echo '<h1>Employé introuvable</h1>';
		}
	}
	else
	{
		echo '<h1>Employé inconnu</h1>';
	}
?>