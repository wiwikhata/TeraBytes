<!-- --------------------------------------------------------------------------
--  Name: view_customer_orders.php
--  Abstract: Display the list of customers with the option to view orders   
-- --------------------------------------------------------------------------->
<?php
// Open database connection
include ("../MySqlConnector.php");
$_GET['menukey'] = 8;
?>
<link rel="stylesheet" href="styles/style2.css" media="all" />				
<table width="795" align="center" bgcolor="pink">
	<tr>
		<td colspan="6" align="center"><h2>View Customer Orders</h2></td>		
	</tr>	
	<tr id="tableheader" align="center" bgcolor="#187eae">
		<th>CustomerID</th>
		<th>Customer Name</th>
		<th>User Name</th>
		<th>Email Address</th>
		<th>Orders</th>	
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
			<a href="index.php?menukey=81&amp;CustomerID=<?=$customerid ?>" title="View Orders">
				<img src="images/receipt.jpg" height="16" width="16" border=0>
			</a>
		</td>
	</tr>	
	<?php
	}
	// Display the navigation links
	echo
	'<tr id="links" bgcolor="#187eae" color="white">
		<td colspan="6" height="50">
			<center>
				<font face=verdana size=2 color=black>'; 
					echo $pager->renderFullNav(); 
				echo' 
				</font>
			</center>
		</td>			
	</tr>';
	?>			
</table>
<input type="button" value="Home" onclick="location.href='index.php';"/>				
	
