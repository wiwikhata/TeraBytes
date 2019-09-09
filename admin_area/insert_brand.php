<!-- --------------------------------------------------------------------------
--  Name: insert_brand.php
--  Abstract: A form for adding a new product brand    
-- --------------------------------------------------------------------------->
<?php
$brandtitle = "";

// If valid login
if(isset($_SESSION['user_name']))
{
	// If posted
	if(isset($_POST['insert_brand']))
	{
		$brandtitle = $_POST['txtBrand'];	
		$query = "SELECT * FROM tbrands 
				  WHERE strBrandTitle='$brandtitle'"; 
		$result = $conn->query($query);
		$count = $result->rowCount();	
		
		if($count == 0)
		{
			$sql = "INSERT INTO tbrands(strBrandTitle, intBrandStatusID)
					VALUES('$brandtitle', 1)";
			$result = $conn->query($sql);
			if($result)
			{
				// Display success message 
				echo "<script>alert('Brand added.')</script>";
				echo "<script>window.open('index.php','_self')</script>";
			}
		}
		else
		{
			// Display error message 
			echo "<script>alert('Brand already exists.')</script>";
			echo "<script>window.open('index.php','_self')</script>";	
		}	
	}
	?>
	<link rel="stylesheet" href="styles/style6.css" media="all" />					
	<form name="frmInsertBrand" id="frmInsertBrand" action="" method="POST" />
		<div id="brandheader">
			<h2><b><u>Add New Brand</u></b></h2>
		</div>
		<div id="content">
			<label for="txtBrand" id="brand">
				<b>Brand Name:</b>&nbsp;&nbsp;
				<input type="text" name="txtBrand" id="txtBrand" required /><br/>
			</label>	
			<input type="submit" name="insert_brand" id="insert_brand" value="Add Brand" />
			<input type="button" name="cancel" id="cancel" value="Cancel" 						   			   onclick="location.href='index.php?menukey=6';" />				
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