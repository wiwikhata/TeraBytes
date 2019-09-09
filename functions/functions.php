<!-- --------------------------------------------------------------------------
--   Name: functions.php
--   Abstract: Various database functions used throughout site  
-- --------------------------------------------------------------------------->
<?php
// Connect to the database
include "MySQLConnector.php";		

// Get the user's IP address
function getIp() 
{
	$ip = $_SERVER['REMOTE_ADDR'];
	if(!empty($_SERVER['HTTP_CLIENT_IP'])) 
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} 
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} 
	return $ip;
}

// Add to Cart
function Add_to_Cart()
{		
	if(isset($_GET['add_cart']))
	{	
		global $conn;			
		$productid  = $_GET['add_cart'];
		$customerid = $_SESSION['CustomerID'];

		$sql = 'CALL uspAddToCustomerCart(?,?)';
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $customerid, PDO::PARAM_INT, 11);
		$stmt->bindParam(2, $productid, PDO::PARAM_INT, 11);
		$stmt->execute(); 
		
		// Return to index.php 
		echo "<script>window.open('index.php'. '_self')</script>";			
		
	}
}

// Get Countries from database
function getAllCountries()
{						
	global $conn;	
	$countryid = $_POST['country'];
	$countrystatus = "";
	
	// Create/execute query
	$query = "SELECT * FROM tcountries ORDER BY strCountry";
	$results = $conn->query($query);
	foreach($results as $country) 
	{
	?>
		<option value="<?= $country["intCountryID"]; ?>">
			<?= $country["strCountry"]; ?>
		</option>
	<?php
	}

	// Set selected country and preserve value in form	
	if($countryid != 0)
	{								
		$countrystatus = "selected";
		$sql = 
		"SELECT intCountryID, strCountry 
		 FROM tcountries 
		 WHERE intCountryID =" . $countryid;
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		echo 
		'<option value="' . $countryid  . '"' . $countrystatus . '>' 
			. $row['strCountry'] . 
		'</option>';																
	}	
}
?>