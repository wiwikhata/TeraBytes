<!-- --------------------------------------------------------------------------
--   Name: change_password.php
--   Abstract: Change customer's password  
-- --------------------------------------------------------------------------->
<?php
$customerid = $_SESSION['CustomerID'];
$ip = GetIP();
$errorMessage = "";

if($customerid != 0)
{
	// If posted
	if(isset($_POST['change_password']))
	{
		$currentPassword  = $_POST['current_password'];
		$newPassword      = $_POST['new_password'];
		$newPasswordAgain = $_POST['new_password_again'];
		
		// Verify current password
		$sql = "SELECT strPassword 
		        FROM tcustomers 
		        WHERE intCustomerID = '$customerid' 
				AND strPassword = '$currentPassword'";
		$result = $conn->query($sql);
		$count = $result->rowCount();
		if($count == 0)
		{
			$errorMessage = "Password not found!";	
		}
		elseif($newPassword != $newPasswordAgain)
		{
			$errorMessage = "New passwords don't match.";		
		}
		else
		{
			$newPassword = trim($newPassword);
			// Test boundaries
			if((strlen($newPassword) < 6) || (strlen($newPassword) > 30))
			{
				$errorMessage = "Password must be between 6-30 characters";
			}
			else
			{
				// Update password
				$sql = 
				"UPDATE tcustomers 
				 SET strPassword='$newPassword' 
				 WHERE intCustomerID='$customerid'";
				$result = $conn->query($sql);			
				if($result)
				{
					$errorMessage = "Password changed";		
				}
			}
		}			
	}			
	if($errorMessage != "")
	{
		echo 
		"<div id='messageGroup'>
			<p class='password-error'>$errorMessage</p>
		 </div>";	
	}		
	else
	{
?>
		<div id="password_change">
			<h2 style="text-align:center; margin-top:10px; font-family:arial;">
				Change Your Password
			</h2>
			<form name="frmChangePassword" id="frmChangePassword" action="" method="post">
				<label class="label">
					<b>Enter current password:</b>
					<span id="first">&nbsp;</span>
					<input type="password" class="pass" name="current_password" required>
				</label></br>
				<label class="label">
					<b>Enter new password:</b>
					<span id="second">&nbsp;</span>
					<input type="password" class="pass" name="new_password" required>
				</label></br>
				<label class="label">
					<b>Enter new password again:</b>
					<span id="third">&nbsp;</span>
					<input type="password" class="pass" name="new_password_again" required>
				</label>
				</br></br>						
				<input type="submit" name="change_password" value="Change Password">
			</form>
		</div>
<?php
	}
}
?>