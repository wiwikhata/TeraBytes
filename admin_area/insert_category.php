<!-- --------------------------------------------------------------------------
--  Name: insert_category.php
--  Abstract: A form for adding a new product category    
-- --------------------------------------------------------------------------->
<?php
$categorytitle = "";

// If valid login
if(isset($_SESSION['user_name']))
{
	// If posted
	if(isset($_POST['insert_category']))
	{
		$categorytitle = $_POST['txtCategory'];	
		$query = "SELECT * FROM tcategories 
				  WHERE strCategoryTitle ='$categorytitle'"; 
		$result = $conn->query($query);
		$count = $result->rowCount();
		
		if($count == 0)
		{				
			$sql = "INSERT INTO tcategories(strCategoryTitle, intCategoryStatusID)
					VALUES('$categorytitle', 1)";
			$result = $conn->query($sql);
			if($result)
			{
				// Display success message 
				echo "<script>alert('Category added.')</script>";
				echo "<script>window.open('index.php','_self')</script>";
			}
		}
		else 
		{
			// Display error message 
			echo "<script>alert('Category already exists.')</script>";
			echo "<script>window.open('index.php','_self')</script>";	
		}	
	}
	?>
	<link rel="stylesheet" href="styles/style6.css" media="all" />					
	<form name="frmInsertCategory" id="frmInsertCategory" action="" method="POST" />
		<div id="categoryheader">
			<h2><b><u>Add New Category</u></b></h2>
		</div>
		<div id="content">
			<label for="txtCategory" id="category">
				<b>Category Name:</b>&nbsp;&nbsp;
				<input type="text" name="txtCategory" id="txtCategory" required /><br/>
			</label>
			<input type="submit" name="insert_category" id="insert_category" 
				   value="Add Category"/>
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