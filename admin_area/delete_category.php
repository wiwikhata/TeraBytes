<!-- --------------------------------------------------------------------------
--  Name: delete_category.php
--  Abstract: Delete a category from the database (set status to inactive)  
-- --------------------------------------------------------------------------->
<?php
// Connect to the database
include "../MySQLConnector.php";

// If valid login
if(isset($_SESSION['user_name']))
{
	// If posted
	if(isset($_GET['CategoryID']))
	{
		// Get categoryID
		$categoryid = intval($_GET['CategoryID']);
		
		// Get category name
		$query = "SELECT strCategoryTitle 
		          FROM tcategories 
				  WHERE intCategoryID='$categoryid'";
		$result = $conn->query($query);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$categorytitle = $row['strCategoryTitle'];
					
		// Delete category
		$sql = "UPDATE tcategories 
			    SET intCategoryStatusID = 2
		        WHERE intCategoryID='$categoryid'";
		$result = $conn->query($sql);	
		if($result)
		{
			echo "<script>alert('$categorytitle has been deleted')</script>";
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
