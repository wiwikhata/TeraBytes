<!-- --------------------------------------------------------------------------
--  Name: edit_category.php
--  Abstract: A form to edit a product category    
-- --------------------------------------------------------------------------->
<?php
$categorytitle = "";

// If valid login
if(isset($_SESSION['user_name']))
{
	// Get CategoryID
	if(isset($_GET['CategoryID']))
	{
		$categoryid = intval($_GET['CategoryID']);
	}

	// Get data for the specified category from the database
	$sql = "SELECT * FROM tcategories 
	        WHERE intCategoryID =" . $categoryid;               			 
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$category_title = $row['strCategoryTitle']; 

	// If posted
	if(isset($_POST['edit_category']))
	{
		$categorytitle = $_POST['txtCategory'];		
		$sql = "UPDATE tcategories 
		        SET strCategoryTitle='$categorytitle' 
				WHERE intCategoryID='$categoryid'";
		$result = $conn->query($sql);
		if($result)
		{
			// Display success message 
			echo "<script>alert('Category updated.')</script>";
			echo "<script>window.open('index.php?menukey=4','_self')</script>";
		}	
	}
	?>
	<link rel="stylesheet" href="styles/style6.css" media="all" />					
	<form name="frmEditCategory" id="frmEditCategory" action="" method="POST" />
		<div id="categoryheader">
			<h2>
				<b><u>Edit Category</u></b>
			</h2>
		</div>
		<div id="content">
			<label for="txtCategory" id="category">
				<b>Category Name:</b>&nbsp;&nbsp;
				<input type="text" name="txtCategory" id="txtCategory" required 
					   value="<?=$category_title ?>" /><br/>
			</label>					
			<input type="submit" name="edit_category" id="edit_category" value="Edit Category" />
			<input type="button" name="cancel" id="cancel" value="Cancel" 							   	       onclick="location.href='index.php?menukey=4';" />				
		</div>
	</form>			
<?php
}
// Else, redirect to administrator login page
else
{
	header("Location:login.php");
}
?>