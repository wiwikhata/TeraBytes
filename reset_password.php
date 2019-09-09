<!-- -------------------------------------------------------------------------------
<!-- Name: reset_password.php
<!-- Abstract: Generate a temporary password and email it to the user for use in
<!-- logging into the site. This requires the installation of phpmailer in the
<!-- server development environment (Apache24 in this case) 
<!-- ------------------------------------------------------------------------------>
<?php
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Import PHPMailer class 
require 'C:\apache24\composer\vendor\autoload.php';

// Set customerid
$customerid = $_SESSION['CustomerID'];

// Generate a random, temporary, 6-character password
$randomList = 'abcdefghijklmnopqrstuvwxyz0123456789#*$^+%';
$tempPassword = '';
$string_length = 6;
for ($intIndex = 0; $intIndex < $string_length; $intIndex += 1) 
{
	$tempPassword .= $randomList[rand(0, strlen($randomList ) - 1)];
}

// Update customer account with temporary password
$sql = "Update tcustomers 
		SET strPassword='$tempPassword' 
		WHERE intCustomerID='$customerid'";
$result = $conn->query($sql);

// Get customer name & email address
$query = "SELECT * FROM tcustomers 
          WHERE intCustomerID='$customerid'";
$results = $conn->query($query);
$row = $results->fetch(PDO::FETCH_ASSOC);
$customername  = $row['strCustomerName'];
$email         = $row['strEmailAddress'];
$separatedname = explode(" ", $customername);									
$customerfirstname = $separatedname[0];

// ----------------------------------------------------------------------------
// Email section - Using PHPMailer
// ----------------------------------------------------------------------------
// Instantiate new mailer object
$mail = new PHPMailer(true);                    // Passing `true` enables exceptions
try 
{
    // $mail->SMTPDebug = 2;                    // Uncomment to enable verbose debugging of output 
    $mail->isSMTP();                            // Set mailer to use SMTP
    $mail->SMTPOptions = array(
	'ssl' => array(
	'verify_peer' => false,
	'verify_peer_name' => false,
	'allow_self_signed' => true
	)
	);			
	$mail->Host = 'smtp.gmail.com';             // Specify SMTP server
    $mail->SMTPAuth = true;                     // Enable SMTP authentication
	$mail->Username = 'your user name';	        // SMTP username 
    $mail->Password = 'your password';          // SMTP password
    $mail->Port = 587;                          // TCP port to connect to
    
	// Sent from:
    $mail->setFrom('administrator@terabytes.com', 'administrator');
	
	// Sent to: using my own email address; normal usage: $email
    $mail->addAddress($email);              

    // Set email format to HTML
    $mail->isHTML(true);

	// Set subject
    $mail->Subject = 'Temporary Password';

	// Content (body)
	$body = 
    "<p>
        Hello <b style='color:blue;'>$customerfirstname</b>.<br> Please find your temporary password below. Login to your account using this password before changing it using the Change Password 
        form in the 'My Account' section of our website.
     </p>		
	 <table width='600' align='center' bgcolor='#FFCC99' border='2'>		
        <tr align='center'>
            <td colspan='6'><h2>TechnoBytes Warehouse</h2></td>
        </tr>				
		<tr align='center'>
            <td><h3><b>Your Temporary Password is:</b>&nbsp;&nbsp;<b>$tempPassword</b></h3></td>	
        </tr>							
	 </table>			
     <h3>
        For security purposes it's important that you change this password at your earliest convenience.
     </h3>			
	 <h2><a href='http://localhost/TeraBytes/index.php?menukey=5'>Click here</a> to login!</h2>		
     <h3>
        Thank you for shopping with us and please let us know if you are in need of any additional assistance.
     </h3>";
	
	$mail->Body = $body;
	$mail->AltBody = strip_tags($body);

    $mail->send();
//  echo 'Message has been sent';  -- for debugging
} 
catch (Exception $e) 
{
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
// Reset session variable to 0
$_SESSION['CustomerID'] = 0;
?>
<div id="reset">
	<div class="resetMessage">
		<h2 align="center" style="padding-top:5px;">Your password has been reset</h2>
		<p>
			A temporary password has been sent to your Email account. Please use this password
			to login to your customer account. For security purposes please change your temporary		
			password as soon as possible.							
		</p>
		<input type="button" name="OK" id="OK" value="OK" 
			   onclick="location.href='index.php?menukey=5';" />
	</div>
</div>
	

