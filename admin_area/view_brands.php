<!-- --------------------------------------------------------------------------
--  Name: view_brands.php
--  Abstract: Display all brands and allow for editing or deleting    
-- --------------------------------------------------------------------------->
<?php
?>
<link rel="stylesheet" href="styles/style5.css" media="all" />		
	<table class="edit" width="795" align="center" bgcolor="pink">
	<tr>
		<td colspan="4" align="center"><h2>View All Brands</h2></td>		
	</tr>	
	<tr class="top-row" id="tableheader" bgcolor="#187eae">
		<th>BrandID</th>
		<th>Brand Name</th>
		<th>Edit</th>
		<th>Delete/Undelete</th>	
	</tr>			
	<!-- Prepare and execute query -->
	<?php
	$sql = "SELECT * FROM tbrands";
	$results = $conn->query($sql);			
	
	// Default background row color
	$bg = '#eeeeee';       
				
	// Loop through brands table
	while($row = $results->fetch(PDO::FETCH_ASSOC))
	{
		$brandid    = $row["intBrandID"];
		$brandtitle = $row["strBrandTitle"];
		$statusID   = $row["intBrandStatusID"];
		
		// Alternate row background color
		$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
	?>			
	<!-- Display data for each brand -->
	<tr class="row" align="center" bgcolor="<?=$bg ?>">
		<td><?=$brandid ?></td>
		<td><?=$brandtitle ?></td>
		<td>
			<a href="index.php?menukey=60&amp;BrandID=<?=$brandid ?>" title="Edit Brand">
				<img src="images/edit.jpg" border=0>
			</a>
		</td>
		<td>
		<?php
			if($statusID == 1)
			{
			?>
				<a href="index.php?menukey=61&amp;BrandID=<?=$brandid ?>" title="Delete Brand">
					<img src="images/delete.jpg" border=0>
				</a>
			<?php																			
			}
			else
			{
			?>						
				<a href="index.php?menukey=62&amp;BrandID=<?=$brandid ?>" title="Undelete Brand">
					<img src="images/undelete.jpg" border=0>
				</a>
			<?php				
			}
			?>		
		</td>
	</tr>
	<?php
	}
	?>
</table>
<input type="button" value="Home" onclick="location.href='index.php';"/>
	