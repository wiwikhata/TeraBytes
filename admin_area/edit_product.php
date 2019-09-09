<!-- --------------------------------------------------------------------------
--  Name: edit_product.php
--  Abstract: Update a product's selling price and/or description in the database   
-- --------------------------------------------------------------------------->
<?php
// Set date variable
date_default_timezone_set('America/New_York');
$dt = new DateTime();
$now = $dt->format('Y-m-d H:i:s');

// If valid login
if(isset($_SESSION['user_name']))
{
	// Get ProductID
	if(isset($_GET['ProductID']))
	{
		$productid = intval($_GET['ProductID']);	
	}

	// Get data for selected product from database
	$sql = "SELECT * FROM tproducts 
	        WHERE intProductID =" . $productid;               			 
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC); 
				 
	// Assign values
	$category_id        = $row['intCategoryID'];
	$brand_id           = $row['intBrandID'];
	$productname        = $row['strProductTitle'];
	$productprice       = $row['decSellingPrice'];
	$productdescription = $row['strProductDescription'];
	$productimage	    = $row['strProductImage'];
	$productkeywords    = $row['strProductKeywords'];
	$productstatus_id   = $row['intProductStatusID'];

	// If posted
	if(isset($_POST['edit_post']))
	{		
		// Edit only price, description and dtmCreated
		$product_price = $_POST['product_price'];
		$product_description = $_POST['product_description'];

		// Create sql command
		$sql = 'CALL uspEditProduct(?,?,?,?)';
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $productid, PDO::PARAM_INT, 11);
		$stmt->bindParam(2, $product_price, PDO::PARAM_STR, 50);
		$stmt->bindParam(3, $product_description, PDO::PARAM_LOB);
		$stmt->bindParam(4, $now, PDO::PARAM_STR, 50);
		// Execute
		$stmt->execute();		
		if($stmt)
		{
			// Success
			echo "<script>alert('Product successfully edited.')</script>";
			echo "<script>window.open('index.php?view_products','_self')</script>";
		}
		else
		{
			// Fail
			echo "<script>alert('Product edit failed.')</script>";
		}
	}						
	?>	
	<link rel="stylesheet" href="styles/style3.css" media="all" />					
	<form name="frmEditProduct" id="frmEditProduct" action="" method="POST" 
		  enctype="multipart/form-data" />						
		<div id="tableheader">
			<h2>Edit Product Price or Description</h2>				
		</div>												
		<table width="800" align="center" bgcolor="orange" frame="box" >
			<tr>
				<td align="right" class="product_title"><b>Product Title:</b></td>
				<td>
					<input type="text" name="product_title" id="product_title" size="40" required   		   value="<?=$productname ?>"/>
				</td>
			</tr>
			<tr>
				<td align="right"><b>Product Category:</b></td>
				<td>
					<!-- Product category select box -->
					<select name="product_category" id="product_category" >
						<option>Select a category</option>
						<?php																
						$query = "SELECT * FROM tcategories";
						$results = $conn->query($query);
						if($results->rowCount() > 0)
						{
							// Loop through results
							while($row = $results->fetch(PDO::FETCH_ASSOC))
							{
								$categoryid = $row['intCategoryID'];
								$categorytitle = $row['strCategoryTitle'];
								
								// Display option value 
								echo 
								'<option value="' . $categoryid . '">' 
									. $categorytitle . 
								'</option>';
							}
							// Preserve selected option value in form						
							if($category_id != 0)
							{								
								$categorystatus = "selected";
								$sql = "SELECT strCategoryTitle 
										FROM tcategories 
										WHERE intCategoryID =" . $category_id;
								$result = $conn->query($sql);
								$row = $result->fetch(PDO::FETCH_ASSOC);
								$category_title = $row['strCategoryTitle'];
								echo
								'<option value="' . $category_id . '"' . $categorystatus .'>' 
									. $category_title . 
								'</option>';												
							}
						}
						?>						
					</select>
				</td>
			</tr>
			<tr>
				<td align="right"><b>Product Brand:</b></td>
				<td>						
					<!-- Product brand select box -->
					<select name="product_brand" id="product_brand">
						<option>Select a brand</option>
						<?php																
						$query = "SELECT * FROM tbrands";
						$results = $conn->query($query);
						if($results->rowCount() > 0)
						{
							// Loop through result-set
							while($row = $results->fetch(PDO::FETCH_ASSOC))
							{
								$brandid    = $row['intBrandID'];
								$brandtitle = $row['strBrandTitle'];
								echo 
								'<option value="' . $brandid . '">'
									. $brandtitle . 
								'</option>';	
							}
							// Preserve selected option value in form	
							if($brand_id != 0)
							{								
								$brandstatus = "selected";
								$sql = "SELECT strBrandTitle 
										FROM tbrands 
										WHERE intBrandID =" . $brand_id;
								$result = $conn->query($sql);
								$row = $result->fetch(PDO::FETCH_ASSOC);
								$brand_title = $row['strBrandTitle'];
								echo    
								'<option value="' . $brand_id  . '"' . $brandstatus . '>' 
									. $brand_title . 
								'</option>';										
							}		
						}			
						?>						
					</select>
				</td>					
			</tr>					
			<tr>
				<td align="right"><b>Product Price:</b></td>
				<td>
					<input type="text" name="product_price" id="product_price" size="8" required 			   value="<?=$productprice; ?>"/>
				</td>
			</tr>
			<tr>
				<td align="right"><b>Product Description:</b></td>
				<td>
					<!-- include tinymce -->
					<script>tinymce.init({selector:'textarea'});</script>		
					<textarea name="product_description" id="product_description" cols="15" rows="10">
						<?=$productdescription; ?>
					</textarea>
				</td>
			</tr>
			<tr>
				<td align="right"><b>Product Keywords:</b></td>
				<td>
					<input type="text" name="product_keywords" id="product_keywords" size="40" 
						   value="<?=$productkeywords; ?>"/>
				</td>
			</tr>				
		</table>
		<div id="buttons">
			<input type="submit" name="edit_post" id="edit_post" value="Edit Product" />
			<input type="button" name="cancel" id="cancel" value="Cancel" 						   		       onclick="location.href='index.php?menukey=2';" />
		</div>
	</form>				
<?php
}
// Else, redirect to administrator index page
else
{
	header("Location:index.php");
}
?>