<!-- --------------------------------------------------------------------------
--  Name: undelete_product.php
--  Abstract: UnDelete a product from the database   
-- --------------------------------------------------------------------------->
<?php
// Connect to the database
include "../MySQLConnector.php";

// If valid login
if(isset($_SESSION['user_name']))
{
	// If posted
	if(isset($_GET['ProductID']))
	{
		// Get product ID
		$productid = intval($_GET['ProductID']);
		
		// Get product name
		$query = "SELECT strProductTitle 
		          FROM tproducts 
		          WHERE intProductID='$productid'";
		$result = $conn->query($query);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$producttitle = $row['strProductTitle'];
					
		// Set product status to active
		$sql = "UPDATE tproducts
				SET intProductStatusID = 1 
		        WHERE intProductID='$productid'";
		$result = $conn->query($sql);	
		if($result)
		{
			echo "<script>alert('$producttitle has been undeleted')</script>";
			echo "<script>window.open('index.php?view_products','_self')</script>";
		}
	}
}
// Else, redirect to administrator login page
else
{
	header("Location:login.php");
}
?>
