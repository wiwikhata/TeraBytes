<!-- --------------------------------------------------------------------------
--   Name: delete_customer.php
--   Abstract: Delete a customer account (set status to inactive)  
-- --------------------------------------------------------------------------->
<?php
// Connect to the database
include "../MySQLConnector.php";
$customerid = $_GET['CustomerID'];

// If valid login
if(isset($_SESSION['user_name']))
{
	if(isset($_POST['confirm']))
	{
		// Delete customer cart first
		$sql = "DELETE FROM tcart WHERE intCustomerID='$customerid'";
		$result = $conn->query($sql);
		if($result)
		{
			// Delete customer
			$query = "UPDATE tcustomers 
					  SET intCustomerStatusID = 2
					  WHERE intCustomerID='$customerid'";
			$result = $conn->query($query);
			
			echo "<script>alert('Customer account deleted!')</script>";						
			echo "<script>window.open('index.php', '_self')</script>";	
		}
	}
	?>
	<! DOCTYPE>
	<html>
		<head>
			<title>TeraBytes Warehouse - Adminstration Area - Delete Account</title>				
			<link rel="stylesheet" href="styles/style.css" media="all" />
			<style>
				h2
				{
					margin-top: -20px;
					padding-top: 50px;
					background-color: orange;
				}
				input[type=submit]
				{
					margin-left: 300px;;
				}
				#cancel
				{
					margin-left: 50px;
				}
				#frmDeleteCustomerAccount
				{
					height: 530px;
					background-color: orange;
				}
			</style>
		</head>
		<body>
			<h2 style="text-align:center"><u>Delete This Customer Account?</u></h2>
			<form name="frmDeleteCustomerAccount" id="frmDeleteCustomerAccount" action="" method="POST" >
				<br>
				<div id="buttons">
					<!-- Submit button -->
					<input type="submit" name="confirm" id="confirm" value="Confirm"> 																										
					<!-- Cancel button -->
					<input type="button" name="cancel" id="cancel" value="Cancel" 							   onclick="location.href='index.php';">				
				</div>		
			</form>	
		</body>
	</html>
<?php
}
// Else, redirect to administrator login page
else
{
	header("Location:login.php");
}
?>