<!-- --------------------------------------------------------------------------
--   Name: customer_register.php
--   Abstract: Customer registration and/or edit form 
-- --------------------------------------------------------------------------->
<?php
// Initialize variables
$customerid    = $_SESSION['CustomerID'];
$ErrorMessages = array();	
$Error         = "";			
$Length        = 0;
$customernameError = $address1Error = $address2Error = $cityError = $stateError =
$zipcodeError = $usernameError = $passwordError = $emailError = $phoneError = false;

// Existing customer?
if($_SESSION['CustomerID'] != 0)
{
    // Get values from database 
	$sql = "SELECT * FROM VAllCustomerData
            WHERE intCustomerID = '$customerid'"; 
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	// Assign values from database to local variables
	$customerid   = $row['intCustomerID'];
	$ip           = $row['strIPAddress'];
	$customername = $row['strCustomerName'];
	$address1     = $row['strAddress1'];
	$address2     = $row['strAddress2'];
	$city         = $row['strCity'];
	$state        = $row['strState'];
    $countryid    = $row['intCountryID'];
    $strCountry   = $row['strCountry'];
	$zipcode      = $row['strZipCode'];
	$username     = $row['strUserName'];
	$password     = $row['strPassword'];
	$email        = $row['strEmailAddress'];
	$phone        = $row['strPhoneNumber'];
}
// New customer
else
{
    $customername  = "";
    $ip            = "";
    $address1      = "";
    $address2      = "";
    $city          = "";
    $state         = "";
    $countryid     = 0;
    $countrystatus = "";
    $zipcode       = "";
    $username      = "";
    $password      = "";
    $email         = "";
    $phone         = "";
}

// If form has been submitted
if(isset($_POST['submit']))
{
    $ip           = getIp();
    $customername = $_POST['customername'];
	$address1     = $_POST['address1'];
	$address2     = $_POST['address2'];
	$city         = $_POST['city'];
	$state        = $_POST['state'];
    $countryid    = $_POST['country'];	
	$zipcode      = $_POST['zipcode'];
	$username     = $_POST['username'];
	$password     = $_POST['password'];
	$email        = $_POST['email'];
    $phone        = $_POST['phone'];   
    
    // ------------------------------------------------------------------------
	// Validate form input fields (server-side)
    // ------------------------------------------------------------------------
    
    // Customer name
    if(empty($customername))
	{
		$Error = "-- Enter your customer name";
        array_push($ErrorMessages, $Error);       
        $customernameError = true;
	}
	else
	{
        $customername = trim($customername);		
        // Proper length?
        if((strlen($customername) < 4) || (strlen($customername) > 30))
        {
            $Error = "-- Name must be 4-30 characters (incl spaces)";
            array_push($ErrorMessages, $Error);
            $customernameError = true;
        }		
        // Test format (start with a letter; these special characters allowed: - . ' , and space) 
        $pattern = '/^[a-zA-Z\'][0-9a-zA-Z-.,\'\s]*$/';

        if (!preg_match($pattern, $customername))
        {
            $Error = "-- Name must start with a letter with <b> . ' - , </b>allowed";
            array_push($ErrorMessages, $Error);
            $customernameError = true;
        }
    }
    // Address 1
    if(empty($address1))
    {
		$Error = "-- Enter your address";
        array_push($ErrorMessages, $Error);
        $address1Error = true;
	}
	else
	{	
		$address1 = trim($address1);
		// Proper length?
		if(strlen($address1) > 30)
		{
			$Error = "-- Address can not exceed 30 characters";
            array_push($ErrorMessages, $Error);
            $address1Error = true;
		}		
		// Test format (alphanumeric with these special characters allowed: - . ' , # and space) 
		$pattern = '/^[a-zA-Z0-9 .,\'#-]*$/';

		if (!preg_match($pattern, $address1))
		{
			$Error = "-- Address must be alphanumeric with ' . , - or # allowed";
            array_push($ErrorMessages, $Error);
            $address1Error = true;
		}				
	}			
	// Address2
	if(!empty($address2))
	{
		$address2 = trim($address2);
		// Proper length?
		if(strlen($address2) > 30)
		{
			$Error = "-- Address can not exceed 30 characters";
            array_push($ErrorMessages, $Error);
            $address2Error = true;
		}		
		// Test format (alphanumeric with these special characters allowed: - . ' , # and space) 
		$pattern = '/^[a-zA-Z0-9 -.,\'# ]*$/';

		if(!preg_match($pattern, $address2))
		{
			$Error = "-- Address must be alphanumeric with ' . , - or # allowed";
            array_push($ErrorMessages, $Error);
            $address2Error = true;
		}				
	}	
	// City
	if(empty($city))
    {
		$Error = "-- Enter your city";
        array_push($ErrorMessages, $Error);
        $cityError = true;
	}
	else
	{	
		$city = trim($city);
		// Proper length?
		if(strlen($city) > 30)
		{
			$Error = "-- City name can not exceed 30 characters";
            array_push($ErrorMessages, $Error);
            $cityError = true;
		}		
		// Test format (start with a letter; these special characters allowed: - . ' , and space) 
        $pattern = '/^[a-zA-Z\'][0-9a-zA-Z-.,\'\s]*$/';

		if (!preg_match($pattern, $city))
		{
			$Error = "-- City must start with a letter with - . ' , and space allowed";
            array_push($ErrorMessages, $Error);
            $cityError = true;
		}				
	}		
	// State
    if(empty($state))
    {
        $Error = "-- Enter your state/region/province";
        array_push($ErrorMessages, $Error);
        $stateError = true;
    }
    else
    {
		$state = trim($state);
		// Proper length?
		if(strlen($state) > 30)
		{
			$Error = "-- State/region/province can not exceed 30 characters";
            array_push($ErrorMessages, $Error);
            $stateError = true;
		}		
		// Test format (start with a letter; these special characters allowed: - . ' , and space) 
		$pattern = '/^[a-zA-Z\'][0-9a-zA-Z-.,\'\s]*$/';

		if (!preg_match($pattern, $state))
		{
			$Error = "-- State must start with a letter with - . ' , and space allowed";
            array_push($ErrorMessages, $Error);
            $stateError = true;
		}				
	}		
	// ZipCode
	if(!empty($zipcode))
    {        
		$zipcode = trim($zipcode);
		$pattern = '/^[0-9]{5}(?:-[0-9]{4})?$/';
		
		if(!preg_match($pattern, $zipcode))
		{
			$Error = "-- Invalid Zipcode: ##### or #####-####";
            array_push($ErrorMessages, $Error);
            $zipcodeError = true;
		}	
    }
    // User name
    if(empty($username))
	{
		$Error = "-- Enter your User Name";
        array_push($ErrorMessages, $Error);
        $usernameError = true;
	}
	else 
	{
		$username = trim($username);
		// Test boundaries
		if((strlen($username) < 6) || (strlen($username) > 30))
		{
			$Error = "-- User Name must be between 6-30 characters";
            array_push($ErrorMessages, $Error);
            $usernameError = true;
        }
        else if($_SESSION['CustomerID'] == 0)
        {
            // Check if user name already exists
            $checkUser = 
            "SELECT * FROM tcustomers 
             WHERE strUserName = '$username'";
            $results = $conn->query($checkUser);
            $count = $results->rowCount(); 
            if($count != 0)
            {	
                $Error = "-- This user name has already been assigned";
                array_push($ErrorMessages, $Error);
                $usernameError = true;
            }
        }
    }
    // Password
	if(empty($password))
	{
		$Error = "-- Enter your Password";
        array_push($ErrorMessages, $Error);
        $passwordError = true;
	}
	else
	{
		$password = trim($password);
		// Test boundaries
		if((strlen($password) < 6) || (strlen($password) > 30))
		{
			$Error = "-- Password must be between 6-30 characters";
            array_push($ErrorMessages, $Error);
            $passwordError = true;
		}
    }
    // Email address
	if(empty($email))
	{
		$Error = "-- Enter your Email Address";
        array_push($ErrorMessages, $Error);
        $emailError = true;
	}
	else
	{
		$email = trim($email);
		$pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/';
		if (!preg_match($pattern, $email))
		{
			$Error = "-- Invalid Email Address";
            array_push($ErrorMessages, $Error);
            $emailError = true;
		}		
    }
    // Phone number
    if(!empty($phone))
    {
        if(strlen($phone) > 15 )
		{
			$Error = "-- Phone number may not exceed 15 characters";
            array_push($ErrorMessages, $Error);
            $phoneError = true;
		}
    }
    // Any errors?
    $Length = count($ErrorMessages);
    
    // If no errors
	if($Length == 0)
	{				       
        // New customer
        if($_SESSION['CustomerID'] == 0)
        {            
            // Add customer data
            $sql = 'CALL uspAddCustomer(?,?,?,?,?,?,?,?,?,?,?,?)';
            $stmt = $conn->prepare($sql);
            // Bind parameters
            $stmt->bindParam(1, $ip, PDO::PARAM_STR, 50);
            $stmt->bindParam(2, $customername, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 50);
            $stmt->bindParam(3, $address1, PDO::PARAM_STR, 50);
            $stmt->bindParam(4, $address2, PDO::PARAM_STR, 50);
            $stmt->bindParam(5, $city, PDO::PARAM_STR, 50);
            $stmt->bindParam(6, $state, PDO::PARAM_STR, 50);
            $stmt->bindParam(7, $countryid, PDO::PARAM_INT, 11);
            $stmt->bindParam(8, $zipcode, PDO::PARAM_STR, 50); 
            $stmt->bindParam(9, $username, PDO::PARAM_STR, 50);
            $stmt->bindParam(10, $password, PDO::PARAM_STR, 50); 
            $stmt->bindParam(11, $email, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 50);  
            $stmt->bindParam(12, $phone, PDO::PARAM_STR, 50);
            // Execute
            $stmt->execute();
            // Return variables
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['CustomerID'] = $row['intCustomerID']; 
            $separatedname = explode(" ", $customername);									
            $_SESSION['firstname'] = $separatedname[0];	
            $_SESSION['email'] = $row['strEmailAddress'];                    
        }
        // Existing customer
        else
        {
            // Edit customer data
            $sql = 'CALL uspEditCustomer(?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $stmt = $conn->prepare($sql);
            // Bind parameters
            $stmt->bindParam(1, $customerid, PDO::PARAM_INT, 11);
            $stmt->bindParam(2, $ip, PDO::PARAM_STR, 50);
            $stmt->bindParam(3, $customername, PDO::PARAM_STR, 50);
            $stmt->bindParam(4, $address1, PDO::PARAM_STR, 50);
            $stmt->bindParam(5, $address2, PDO::PARAM_STR, 50);
            $stmt->bindParam(6, $city, PDO::PARAM_STR, 50);
            $stmt->bindParam(7, $state, PDO::PARAM_STR, 50);
            $stmt->bindParam(8, $countryid, PDO::PARAM_INT, 11);
            $stmt->bindParam(9, $zipcode, PDO::PARAM_STR, 50); 
            $stmt->bindParam(10, $username, PDO::PARAM_STR, 50);
            $stmt->bindParam(11, $password, PDO::PARAM_STR, 50); 
            $stmt->bindParam(12, $email, PDO::PARAM_STR, 50);  
            $stmt->bindParam(13, $phone, PDO::PARAM_STR, 50);
            // Execute
            $stmt->execute();                   
        } 
         // Redirect to my_account.php
        echo "<script>window.open('customers/my_account.php', '_self')</script>";        
    }
}
?>
<!-- Server-side error section -->
<section id="errors">
    <?php
    if(isset($_POST['submit'])) 	
    {
        // Display any error messages
        $Length = count($ErrorMessages);	
        if($Length > 0)
        {			
            echo 
            "<div class='errorgroup'>
                <span class='firstline'>Please correct the following errors:</span><br>
             	<span class='message' id='message'>";								
                    foreach($ErrorMessages as $message)
                    {
                        echo "&nbsp;&nbsp;" . $message . "<br />";
                    }
             	"</span>
            </div>";
        }
    }
    ?>
</section>
<!-- Customer Registration/Edit form -->
<form name="frmCustomerRegisterEdit" id="frmCustomerRegisterEdit" action="" method="post">
    <!-- New Account -->
    <?php
    if($customerid == 0)
    {
    ?>
        <div class="register-header">
            <h2>Create New Account</h2>			
        </div>     
    <?php
    }
    // Existing account
    else
    {
    ?>
        <div class="register-header">
            <h2>Edit Your Account</h2>			
        </div>
    <?php
    }
    ?>        
    <table name="customerdata" id="customerdata" width="500" cellspacing="4" frame="Box">
        <tr>           
            <td>
                <label for "customername">Name: 
                    <span class="tip">(3-30 characters)</span>
                </label>
                <span class="required">*</span>
            </td>					 
            <?php             
            if($customernameError == false)
            {
                echo 
                '<td>
                    <input type="text" id="customername" name="customername" required placeholder = "first & last name" value="' . htmlspecialchars($customername) . '"/>
                 </td>';
            }
            else
            {
                echo 
                '<td>
                    <input type="text" class="input-focus" id="customername" name="customername" required value="' . htmlspecialchars($customername) . '"/>
                 </td>';   
            }                
            ?>
        </tr>				
        <tr>
            <td>
                <label for "address1">Street Address 1:</label>
                <span class="required">*</span>
            </td>
            <?php
            if($address1Error == false)
            {
                echo 
                '<td>
                    <input type="text" id="address1" name="address1" required 
                           value="' . htmlspecialchars($address1) . '"/>
                 </td>';
            }
            else
            {
                echo 
                '<td>
                    <input type="text" class="input-focus" id="address1" name="address1" required 
                           value="' . htmlspecialchars($address1) . '"/>
                 </td>';
            }
            ?>
        </tr>						
        <tr>
            <td>
                <label for "address2">Street Address 2:</label>
            </td>
            <?php
            if($address2Error == false)
            {
                echo 
                '<td>
                    <input type="text" id="address2" name="address2" 
                           value="' . htmlspecialchars($address2) . '"/>
                 </td>';
            }
            else
            {
                echo 
                '<td>
                    <input type="text" class="input-focus" id="address2" name="address2"  
                           value="' . htmlspecialchars($address2) . '"/>
                 </td>';
            }
            ?>
        </tr>						
        <tr>
            <td>
                <label for "city">City:</label>
                <span class="required">*</span>
            </td>
            <?php
            if($cityError == false)
            {
                echo                   
                '<td>
                    <input type="text" id="city" name="city" required 
                           value="' . htmlspecialchars($city) . '"/>
                 </td>';
            }
            else
            {
                echo                   
                '<td>
                    <input type="text" class="input-focus" id="city" name="city" required 
                           value="' . htmlspecialchars($city) . '"/>
                 </td>';
            }            
            ?>
        </tr>						
        <tr>
            <td>
                <label for "state">State/Region/Province:</label>
                <span class="required">*</span>
            </td>
            <?php
            if($stateError == false)
            {
            ?>
                <td>
                    <input type='text' id='state' name='state' required value='<?=$state;?>'>
                </td>
            <?php
            }
            else
            {
            ?>
                <td>
                    <input type='text' class='input-focus' id='state' name='state' required 
                           value='<?=$state;?>'>
                </td>
            <?php
            }
            ?>
        </tr>
        <tr>
            <td>
                <label for "country">Country:</label>
                <span class="required">*</span>
            </td> 
            <td>
            <?php
            if($customerid == 0)
            {
            ?>
                <select name="country" id="country">
                    <option value="32" selected>United States</option>			
                        <!-- Create countries drop-down list -->					
                        <?=getAllCountries();?>	                    						
                </select>
            <?php
            }
            // Existing customer
            else 
            {
               $countrystatus = "selected";		       
            ?>
                <select name="country" id="country">
                    <option value="<?=$countryid;?>" selected><?=$strCountry;?></option>			
                    <!-- Create countries drop-down list -->					
                    <?=getAllCountries();?>	                    						
                </select>    
            <?php   
            }
            ?>									
            </td>
        </tr>	
        <tr>
            <td>
                <label for "zipcode">Postal Code:</label>
                <span class="required">*</span>
            </td>
            <?php
            if($zipcodeError == false)
            {
            ?>
                <td>
                    <input type='text' id='zipcode' name='zipcode' value='<?=$zipcode;?>'>
                </td>
            <?php
            }
            else
            {
            ?>
                <td>
                    <input type='text' class='input-focus' id='zipcode' name='zipcode' required
                           value='<?=$zipcode;?>'>
                </td>
            <?php
            }
            ?>
        </tr>
        <tr>            
            <td>
                <label for "username">User Name: 
                    <span class="tip">(6-30 characters)</span>
                </label>
                <span class="required">*</span>
            </td>
            <?php
            if($_SESSION['CustomerID'] == 0)
            {
                if($usernameError == false)
                {
                ?>               
                    <td>
                        <input type='text' id='username' name='username' required 
                               value='<?=$username;?>'>
                    </td>
                <?php
                }
                else
                {
                ?>
                    <td>
                        <input type='text' class='input-focus' id='username' name='username' required 
                               value='<?=$username;?>'>
                    </td> 
                <?php   
                }
            }
            else
            {
            ?>
                <!-- For edit prevent user from changing user name -->
                <td>
                    <input type='text' class='edit_user input-static' id='username' name='username' 
                           value='<?=$username;?>'>
                </td>
            <?php
            }
            ?>                        
        </tr>
        <tr>
            <td>
                <label for "password">Password: 
                    <span class="tip">(6-30 characters)</span>
                </label>
                <span class="required">*</span>
            </td>
            <?php
            if($_SESSION['CustomerID'] == 0)
            {
                if($passwordError == false)
                {
                ?>               
                    <td>
                        <input type='password' id='password' name='password' required 
                               value='<?=$password;?>'>
                    </td>
                <?php
                }
                else
                {
                ?>
                    <td>
                        <input type='password' class='input-focus' id='password' name='password'               required value='<?=$password;?>'>
                    </td> 
                <?php   
                }
            }
            else
            {
            ?>
                <!-- For edit prevent user from changing password here -->
                <td>
                    <input type='password' class='edit_user input-static' id='password'                        name='password' value='<?=$password;?>'>
                </td>
            <?php
            }
            ?>          
        </tr>
        <tr>
            <td>
                <label for "email">Email Address:</label>
                <span class="required">*</span>
            </td>
            <?php
            if($emailError == false)
            {
            ?>   
                <td>
                    <input type='text' id='email' name='email' required value='<?=$email;?>'>
                </td>
            <?php
            }
            else
            {
            ?>
                <td>
                    <input type='text' class='input-focus' id='email' name='email' required 
                           value='<?=$email;?>'>
                </td>
            <?php
            }
            ?>            
        </tr>
        <tr>
            <td><label for "phone">Phone Number:<label></td>
            <?php
            if($phoneError == false)
            {
            ?>   
                <td>
                    <input type='text' id='phone' name='phone' value='<?=$phone;?>'>
                </td>
            <?php
            }
            else
            {
            ?>   
                <td>
                    <input type='text' class='input-focus' id='phone' name='phone' value='<?=$phone;?>'>
                </td>
            <?php
            }
            ?>
        </tr>		
        <tr>
            <!-- Hidden column to hold IP address -->
            <td>&nbsp;
                <input type='hidden' name='ipaddress' value='<?=$ip;?>'>
            </td>
            <!-- Required field span -->
            <td>&nbsp;&nbsp;
                <span class="required">*</span>
                <span class="subscript">= Required Fields</span>
            </td>
        </tr>				
    </table>
    <div id="buttons">
        <!-- If adding new customer -->
        <?php
        if($_SESSION['CustomerID'] == 0)
        {
        ?>       
            <input type="submit" name="submit" value="Create Account">      
            <input type="reset" name="clear" value="Clear" onclick="btnClear_Click()">                
            <input type="button" name="cancel" value="Cancel" onclick="location.href='index.php';">	
        <?php
        }
        // Edit existing customer      
        else
        {
        ?>
            <input type="submit" name="submit" style="margin-left:90px;" value="Edit Account">    	
            <input type="button" name="cancel" value="Cancel" 
                   onclick="location.href='customers/my_account.php';">	
        <?php    
        }
        ?>								        			
    </div>    					
</form>

<!-- ----------------------------- Javascript ----------------------------- -->
<script type="text/javascript" language="javascript"> 
    // ----------------------------------------
    // btnClear_Click()
    // Clear any server-side error messages
    // ----------------------------------------
    function btnClear_Click()
    {
        try
        {
            var errors = document.getElementById("errors");
            
            // Clear server-side error messages								
            if(errors.innerHTML != "") errors.innerHTML = "";
        }
        catch (excError)
        {
            alert("customer_register.php::btnClear()\n"
            + excError.name + ', ' + excError.message);
        }
    }
</script>			

