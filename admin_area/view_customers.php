<!-- --------------------------------------------------------------------------
--   Name: view_customers.php
--   Abstract: Display all customers 
-- --------------------------------------------------------------------------->
<?php
// Open database connection
include ("../MySqlConnector.php");
$_GET['menukey'] = 7;
?>
<link rel="stylesheet" href="styles/style2.css" media="all" />					
<table width="795" align="center" bgcolor="pink">
	<tr>
		<td colspan="6" align="center"><h2>View All Customers</h2></td>		
	</tr>	
	<tr id="tableheader" align="center" bgcolor="#187eae">
		<th>CustomerID</th>
		<th>Customer Name</th>
		<th>User Name</th>
		<th>Email Address</th>
		<th>Delete/Undelete</th>	
	</tr>			
	<!-- Prepare and execute query -->
	<?php
	$sql = "SELECT * FROM tcustomers";
	$results = $conn->query($sql);			
	
	// Instantiate new pagination object
	$pager = new PS_Pagination($conn, $sql, 7, 5);
	$result_set = $pager->paginate();
	
	// Default background row color
	$bg = '#eeeeee';       
				
	// Loop through customers table
	while($row = $result_set->fetch(PDO::FETCH_ASSOC))
	{
		$customerid    = $row["intCustomerID"];
		$customername  = $row["strCustomerName"];
		$username      = $row["strUserName"];
		$email         = $row["strEmailAddress"];
		$statusID      = $row["intCustomerStatusID"];
		
		// Alternate row background color
		$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
	?>			
	<!-- Display data for each customer -->
	<tr class="row" align="center" bgcolor="<?=$bg ?>">
		<td><?=$customerid ?></td>
		<td><?=$customername ?></td>
		<td><?=$username ?></td>
		<td><?=$email ?></td>
		<td>
		<?php
			if($statusID == 1)
			{
			?>
				<a href="index.php?menukey=70&amp;CustomerID=<?=$customerid ?>" 
					title="Delete Customer">
					<img src="images/delete.jpg" border=0>
				</a>
			<?php																			
			}
			else
			{
			?>						
				<a href="index.php?menukey=71&amp;CustomerID=<?=$customerid ?>" 
					title="Undelete Customer">
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
	'<tr id="links" bgcolor="#187eae" color="black">
		<td colspan="6" height="50">
			<center>
				<font face=verdana size=2 color=black>'; 
					echo $pager->renderFullNav(); 
				echo 
				'</font>
			</center>
		</td>			
	</tr>';
	?>			
</table>
<input type="button" value="Home" onclick="location.href='index.php?menukey=7';"/>		
