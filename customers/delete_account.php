<!-- --------------------------------------------------------------------------
--   Name: delete_account.php
--   Abstract: Delete a customer account  
-- --------------------------------------------------------------------------->
<?php
$customerid = $_SESSION['CustomerID'];

// If customer is logged in
if($customerid > 0)
{
	if(isset($_POST['confirm']))
	{
		// Delete customer cart first
		$sql = "DELETE FROM tcustomercarts 
		        WHERE intCustomerID='$customerid'";
		$result = $conn->query($sql);
		if($result)
		{
			// Make customer status inactive
			$query =
			"UPDATE tcustomers
			 SET intCustomerStatusID = 2
			 WHERE intCustomerID='$customerid'";

			$result = $conn->query($query);
			
			echo "<script>alert('We're sorry you have deleted your account!')</script>";		
			unset($_SESSION['CustomerID']);				
			echo "<script>window.open('../index.php', '_self')</script>";	
		}
	}
}
else
{
	echo "<script>window.open('../index.php', '_self')</script>";
}
?>
<div id="delete-account">
	<h2 style="text-align:center; margin-bottom:20px">
		<u>Delete Your Account?</u>
	</h2>
	<form name="frmDeleteAccount" id="frmDeleteAccount" action="" method="POST" >
		<br>
		<div class="buttons">
			<!-- Submit button -->
			<input type="submit" name="confirm" id="confirm" value="Confirm"> 						
			<!-- Cancel button -->
			<input type="button" name="cancel" id="cancel" value="Cancel" 								   	   onclick="location.href='my_account.php';">				
		</div>		
	</form>	
</div>
	