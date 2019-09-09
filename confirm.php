<!-- --------------------------------------------------------------------------
--   Name: confirm.php
--   Abstract: Confirm the customer's order, update the database for the customer's
--   order and delete the customer's cart. Send an order confirmation to the
--   customer's email address. The body for phpmailer is set up to accomodate
--   a dynamic table using data from the database.  
-- --------------------------------------------------------------------------->
<?php
// If using PHPMailer these must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Import PHPMailer class 
require 'C:\Apache24\composer\vendor\autoload.php';

// Set variables
$customerid        = $_SESSION['CustomerID'];
$customerfirstname = $_SESSION['firstname'];
$email             = $_SESSION['email'];
$deliveryindex     = $_SESSION['DeliveryIndex'];

// Add customer order
$sql = 'CALL uspAddCustomerOrder(?,?)';
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $customerid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 11);
$stmt->bindParam(2, $deliveryindex, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 11);
// Execute
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$orderindex = $row['intOrderIndex'];
$stmt->closeCursor();

// Get items from the customer's cart
$query = 
"SELECT 
	intProductID,
	intQuantity
 FROM 		 
    tcustomercarts
 WHERE
	intCustomerID='$customerid'"; 
$results = $conn->query($query);

// Loop through result set
while($row = $results->fetch(PDO::FETCH_ASSOC))
{
	// Get product and quantity
	$productid = $row['intProductID'];
	$quantity  = $row['intQuantity'];
	    
    // Insert values into customerorderitems
    $sql = 'CALL uspAddCustomerOrderItems(?,?,?,?,?)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $customerid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 11);
    $stmt->bindParam(2, $deliveryindex, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 11);
    $stmt->bindParam(3, $orderindex, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 11);
    $stmt->bindParam(4, $productid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 11);
    $stmt->bindParam(5, $quantity, PDO::PARAM_INT, 11);
    // Execute
    $stmt->execute();
    $stmt->closeCursor();   
}

// Delete customer's current cart
$sql = "DELETE FROM tcustomercarts 
        WHERE intCustomerID = '$customerid'";
$result = $conn->query($sql);

// Reset delvery address session variable
$_SESSION['DeliveryIndex'] = 1;
?>

<div id="confirmation-content">
    <h2><u>ORDER CONFIRMED!</u></h2>
    <br>						
    <h3>
        Your payment has been processed.<br> 
        Thank you <?=$customerfirstname; ?> for shopping with us.
    </h3>
    <br>
    <p>A confirmation message has been sent to your email address.</p>	
    <br>					
    <h3><a href="http://localhost/TeraBytes/index.php">Return to Home Page</a></h3>
</div>

<!-- ----------------------------------------------------------------------------
// Optional Email section - Using PHPMailer
// Requires installation of composer and PHPMailer in the server
// When used with gmail may require settings change to allow less secure apps
// -------------------------------------------------------------------------- -->
<?php
// Instantiate new mailer object
$mail = new PHPMailer(true);                    
try 
{			 
    //$mail->SMTPDebug = 2;                     // Uncomment to enable verbose debugging  
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
    $mail->SMTPSecure = 'tls';					// Encryption (for gmail)
    $mail->Username = 'your username';	        // SMTP username 
    $mail->Password = 'your password';          // SMTP password
    $mail->Port = 587;                          // TCP port to connect to
    
    // Sender
    $mail->setFrom('administrator@terabytes.com', 'administrator');
    
    // Recipient
    $mail->addAddress($email);              

    // Set email format to HTML
    $mail->isHTML(true);
    
    // Subject
    $mail->Subject = 'Order Confirmation and Details';
                        
    // Content (body)
    $mail->Body =
    "<p>Hello <b style='color:blue;'>$customerfirstname</b>. 
        Thank you for ordering from TeraBytes Warehouse. Please find your order details listed below. 
        Your order will arrive shortly. Please shop with us again soon!
    </p>		
    <table width='500' align='center' bgcolor='#FFCC99' border='2'>		
        <tr align='center'>
            <td colspan='6'>
                <h2>Your Order Details from terabyes.com</h2>
            </td>
        </tr>				
        <tr align='center'>
            <th><b>Product Name</b></th>
            <th><b>Quantity</b></th>				
        </tr>"; 
        $query = 
        "SELECT * FROM vallcustomerordersummaries
		 WHERE intCustomerID='$customerid'
		 AND intOrderIndex ='$orderindex'"; 
		$results = $conn->query($query);
		$count = $results->rowCount();
		if($count > 0)
		{
			while($row = $results->fetch(PDO::FETCH_ASSOC))
			{
				$producttitle = $row['strProductTitle'];
				$quantity = $row['intQuantity'];
				$mail->Body .=  
				"<tr align='center'>
					<td>$producttitle</td>
					<td>$quantity</td>			
				 </tr>";		
			}
		}
    $mail->Body .= 
    "</table>					
     <h2> 
        <a href='http://localhost/TeraBytes/index.php'>Click here</a> to continue shopping.
     </h2>		
     <h3> Thank you for purchasing from <b>TeraBytes Warehouse</b></h3>";		 
    
$mail->send();
//  echo 'Message has been sent';  -- for debugging
} 
catch (Exception $e) 
{
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
?>											
	