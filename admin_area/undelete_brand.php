<!-- --------------------------------------------------------------------------
--  Name: undelete_brand.php
--  Abstract: Undelete a brand from the database   
-- --------------------------------------------------------------------------->
<?php
// Connect to the database
include "../MySQLConnector.php";

// If valid login
if(isset($_SESSION['user_name']))
{
	// If posted
	if(isset($_GET['BrandID']))
	{
		// Get brandID
		$brandid = intval($_GET['BrandID']);
		
		// Get brand name
		$query = "SELECT strBrandTitle FROM tbrands 
		          WHERE intBrandID='$brandid'";
		$result = $conn->query($query);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$brandtitle = $row['strBrandTitle'];
					
		// Undelete brand
		$sql = "UPDATE tbrands 
		        SET intBrandStatusID = 1
		        WHERE intBrandID='$brandid'";
		$result = $conn->query($sql);	
		if($result)
		{
			echo "<script>alert('$brandtitle has been undeleted')</script>";
			echo "<script>window.open('index.php?view_categories','_self')</script>";
		}
	}
}
// Else, redirect to administrator login page
else
{
	header("Location:login.php");
}
?>