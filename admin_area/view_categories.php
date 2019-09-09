<!-- --------------------------------------------------------------------------
--  Name: view_categories.php
--  Abstract: Display all categories and allow for editing or deleting    
-- --------------------------------------------------------------------------->
<?php
?>
<link rel="stylesheet" href="styles/style5.css" media="all" />		
<table width="795" align="center" bgcolor="pink">
	<tr>
		<td colspan="4" align="center"><h2>View All Categories</h2></td>		
	</tr>	
	<tr id="tableheader" bgcolor="#187eae">
		<th>CategoryID</th>
		<th>Category Name</th>
		<th>Edit</th>
		<th>Delete/Undelete</th>	
	</tr>			
	<!-- Prepare and execute query -->
	<?php
	$sql = "SELECT * FROM tcategories";
	$results = $conn->query($sql);			
	
	// Default background row color
	$bg = '#eeeeee';       
				
	// Loop through categories table
	while($row = $results->fetch(PDO::FETCH_ASSOC))
	{
		$categoryid    = $row["intCategoryID"];
		$categorytitle = $row["strCategoryTitle"];
		$statusID      = $row["intCategoryStatusID"];
		
		// Alternate row background color
		$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
	?>			
	<!-- Display data for each category -->
	<tr align="center" bgcolor="<?=$bg ?>">
		<td><?=$categoryid ?></td>
		<td><?=$categorytitle ?></td>
		<td>
			<a href="index.php?menukey=40&amp;CategoryID=<?=$categoryid ?>" title="Edit Category">
				<img src="images/edit.jpg" border=0>
			</a>
		</td>
		<td>
		<?php
			if($statusID == 1)
			{
			?>
				<a href="index.php?menukey=41&amp;CategoryID=<?=$categoryid ?>" title="Delete Category">
					<img src="images/delete.jpg" border=0>
				</a>
			<?php																			
			}
			else
			{
			?>						
				<a href="index.php?menukey=42&amp;CategoryID=<?=$categoryid ?>" 
					title="Undelete Category">
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
	