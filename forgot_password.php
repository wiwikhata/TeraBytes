<!-- -------------------------------------------------------------------------------
<!-- Name: forgot_password.php
<!-- Abstract: A form to allow a customer to login to the site when the password
<!-- has been forgotten. The forgottn password is forwarded to the customer's
<!-- email address.  
<!-- ------------------------------------------------------------------------------>
<?php
if(isset($_POST['submit']))
{
	$username = $_POST['username'];
	$query = "SELECT * FROM tcustomers WHERE strUserName='$username'";
    $result = $conn->query($query);

    // If no match is found
    if($result->rowCount() == 0)
	{
		// Display error message
		echo "<script>alert('User Name is incorrect. Please try again.')</script>";		
	}
	else
	{
        $row = $result->fetch(PDO::FETCH_ASSOC);
		// Set customerid
		$_SESSION['CustomerID'] = $row['intCustomerID'];
				
        // Return to index.php
        echo "<script>window.open('index.php?menukey=54', '_self')</script>";   
	}
}
?>
<form name="frmForgotPassword" id="frmForgotPassword" action="" method="post">
    <table width="500px" cellspacing="8" bgcolor="skyblue" frame="box">
        <tr align="center">
            <td colspan="3">
                <h2><u>Forgot Password?</u></h2>
            </td>
        </tr>		
        <tr align="center">
            <td align="right"><b>User Name:</b></td>
            <td colspan="2" align="left">
                <input class="forgot-username" type="text" name="username" 
                       placeholder="&nbsp;Enter your user name" required />
            </td>
        </tr>        
        <tr align="center">
            <td align="right">
                <input type="submit" name="submit" value="Reset Password"/>
            </td>
            <td align="center">
                <input type="button" name="return"  value="Return to Login" 			                       
				onclick="location.href='reset_password.php';"/>
            </td>
        </tr>
        <tr align="center"><td colspan="2">&nbsp;</td></tr>
    </table>						
</form>