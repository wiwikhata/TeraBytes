<!-- --------------------------------------------------------------------------
--  Name: view_products.php
--  Abstract: Display all products and allow for editing or deleting    
-- --------------------------------------------------------------------------->
<?php
$_GET['menukey'] = 2;
?>
<link rel="stylesheet" href="styles/style2.css" media="all" />			
<table width="795" align="center" bgcolor="pink">
	<tr>
		<td colspan="6" align="center">
			<h2>View All Products</h2>
		</td>		
	</tr>	
	<tr id="tableheader" align="center" bgcolor="#187eae">
		<th>ProductID</th>
		<th>Product Name</th>
		<th>Image</th>
		<th>Price</th>
		<th>Edit</th>
		<th>Delete/Undelete</th>	
	</tr>			
	<!-- Prepare and execute query -->
	<?php
	$sql = "SELECT * FROM tproducts, tbrands
			WHERE tproducts.intBrandID = tbrands.intBrandID
			ORDER BY tbrands.strBrandTitle";
	$results = $conn->query($sql);			
	$pager = new PS_Pagination($conn, $sql, 7, 6);	
	$result_set = $pager->paginate();
	
	// Default background row color
	$bg = '#eeeeee';       
				
	// Loop through products table
	while($row = $result_set->fetch(PDO::FETCH_ASSOC))
	{
		$productid    = $row["intProductID"];
		$producttitle = $row["strProductTitle"];
		$productimage = $row["strProductImage"];
		$productprice = $row["decSellingPrice"];
		$statusID     = $row["intProductStatusID"];
		
		// Alternate row background color
		$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
	?>			
	<!-- Display data for each product -->
	<tr align="center" bgcolor="<?=$bg ?>">
		<td><?=$productid ?></td>
		<td><?=$producttitle ?></td>
		<td>
			<img src="product_images/<?=$productimage ?>" width="50" height="50" />
		</td>
		<td>$<?=$productprice ?></td>
		<td>
			<a href="index.php?menukey=20&amp;ProductID=<?=$productid ?>" title="Edit Product">
			   <img src="images/edit.jpg" border=0>
			</a>
		</td>
		<td>					
			<?php
			if($statusID == 1)
			{
			?>
				<a href="index.php?menukey=21&amp;ProductID=<?=$productid ?>" title="Delete Product">
				   <img src="images/delete.jpg" border=0>
				</a>
			<?php																			
			}
			else
			{
			?>						
				<a href="index.php?menukey=22&amp;ProductID=<?=$productid ?>" title="Undelete Product">
				   <img src="images/undelete.jpg" border=0>
				</a>
			<?php				
			}
			?>		
		</td>
	</tr>	
	<?php
	}
	// Display the navigation links
	echo
	'<tr id="navlinks" bgcolor="#ebebe0">
		<td colspan=6>
			<center>
				<font face=verdana size=2 color=blue>'; 
					echo 
					$pager->renderFullNav(); 
				'</font>
			</center>
		</td>
	</tr>';
	?>			
</table>
<input type="button" value="Home" onclick="location.href='index.php?menukey=2';"/>
