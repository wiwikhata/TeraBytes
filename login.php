<!-- --------------------------------------------------------------------------
--   Name: login.php
--   Abstract: The customer login form
-- --------------------------------------------------------------------------->
<?php
// If posted
if(isset($_POST['login']))
{
	$username  = $_POST['username'];
	$password  = $_POST['password'];
		
	// Prepare & execute query
	$query = "SELECT * FROM tcustomers 
              WHERE strUserName='$username' 
              AND strPassword='$password'";
	$result = $conn->query($query);
	$count = $result->rowCount();	
		
    // If no match is found
	if($count == 0)
	{
		// Display error message
		echo "<b style='font-size:24px; margin-top:40px; margin-bottom:20px; margin-left:100px'>
				User Name and/or Password incorrect. Please try again.
			  </b>
			  <p>&nbsp;</p>";
	}
	else
	{    			
        // Get result set
        $row = $result->fetch(PDO::FETCH_ASSOC);
		
        // Assign variables       
		$customerid   = $row['intCustomerID'];
		$customername = $row['strCustomerName'];		
		$useremail    = $row['strEmailAddress'];
        $ip           = $row['strIPAddress'];
        $activestatus = $row['intCustomerStatusID'];
        
        // Reactivate any inactive customer who logs in
        if($activestatus == 2)
        {
            $query =
			"UPDATE tcustomers
			 SET intCustomerStatusID = 1
             WHERE intCustomerID='$customerid'";
            
            $result = $conn->query($query);
        }
		
		// Set session variables			
		$_SESSION['CustomerID'] = $customerid;
		$separatedname          = explode(" ", $customername);									
        $_SESSION['firstname']  = $separatedname[0];
        $_SESSION['email']      = $useremail;
        
        // Return to index.php
        echo "<script>window.open('index.php', '_self')</script>";       
	}			
}
?>
<div class="form-login">
	<div class="form-login-heading">Login To Your Account</div>
    <form class="login-form" name="frmCustomerLogin" id="frmCustomerLogin" action=""
          method="post" enctype="multipart/form-data">
		<label for="username">
			<span>User Name: 
                <span class="required">*</span>
            </span>
            <input type="text" class="input-field" name="username" 
                   placeholder="&nbsp;Enter your user name" required value="" />
		</label>
		<label for="password">
			<span>Password: 
                <span class="required">*</span>
            </span>
            <input type="password" class="input-field" name="password" 
                   placeholder="&nbsp;Enter your password" required value="" />
        </label>        	
		<label for="login">
			<span> </span>
			<input type="submit" name="login" value="Login"/>
        </label>
        <a class="forgot" href="index.php?menukey=52">Forgot Password?</a>        
    </form>
    <h2 class="newcustomer">
        <a href="index.php?menukey=51">New Customer? Register Here!</a>
    </h2>
</div>
