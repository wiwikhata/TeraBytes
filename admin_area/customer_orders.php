<!-- --------------------------------------------------------------------------
--   Name: customer_orders.php
--   Abstract: Display a summary of the customer's orders. Each order is detailed
--   separately.  
-- --------------------------------------------------------------------------->
<?php
// Initialize variables
date_default_timezone_set('America/New_York');
$productnumber = 0;
$total_price = 0.00;
$producttitle = "";	
$quantity = 0;
$productprice = 0.00;
$producttotal = 0.00;

// Get customerid and name
$customerid = $_GET['CustomerID'];
$sql= "SELECT strCustomerName 
       FROM tcustomers 
	   WHERE intCustomerID='$customerid'";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$customername = $row['strCustomerName'];

// Test if customer has any previous orders?
$query = "SELECT COUNT(intOrderIndex) AS COUNT 
          FROM  tcustomerorders 
		  WHERE intCustomerID='$customerid'";
$results = $conn->query($query);
$row = $results->fetch(PDO::FETCH_ASSOC);
$count = $row['COUNT'];
if($count == 0)
{
	// No previous orders
	echo "<script>alert('Customer has no previous orders')</script>";	
	echo "<script>window.open('index.php?menukey=8', '_self')</script>";	
}
?>
<link rel="stylesheet" href="styles/style4.css" media="all" />											<div id="orderHeader">
	<h1 align="center">Customer Order Summary</h1>
	<input type="text" name="txtCustomerName" id="txtCustomerName" value="<?=$customername;?>" />
</div>				
<!-- New table for each order -->
<?php
$intIndex = 0;		
for($intIndex = 0; $intIndex < $count; $intIndex += 1)
{
	$total_price = 0.00;
	// Get order number and order date
	$orderNumber = $intIndex + 1;
	$sql = "SELECT dtmOrderDate FROM tcustomerorders
			WHERE intCustomerID='$customerid' 
			AND intOrderIndex='$orderNumber'";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$tempDate = $row['dtmOrderDate'];
	$date = strtotime($tempDate);
	$orderDate = date("m/d/y", $date);											
?>			
	<table id="ordersTable" width="700" cellspacing="0" border="1" >
		<tr bgcolor="yellow">
			<td>
				<table bgcolor="yellow">
					<td style="width:350px" class="topHeader">
						Order # <?=$orderNumber; ?>
					</td>
					<td style="width:250px" class="topHeader">
						Order Date: <?=$orderDate; ?>
					</td>
					<td style="width:100px" class="topHeader">&nbsp;</td>
				</table>
			</td>			
		</tr>										
		<tr>
			<td>
				<table>
					<td style="width:100px" class="header">Item #</td>
					<td style="width:200px" class="header">Product(s)</td>
					<td style="width:100px" class="header">Quantity</td>
					<td style="width:150px" class="header">Price(ea)</td>
					<td style="width:150px" class="header">Product Total</td>
				</table>
			</td>
			<tr>
				<td style="width:700px"><hr/></td>				
			</tr>
			<?php
				$productnumber = 0;
				$sql = "SELECT * FROM VAllCustomerOrderSummaries
						WHERE intCustomerID='$customerid' 
						AND intOrderIndex='$orderNumber'";
				$results = $conn->query($sql);		
				while($row = $results->fetch(PDO::FETCH_ASSOC))
				{
					$productnumber += 1;
					$producttitle = $row['strProductTitle'];
					$quantity = $row['intQuantity'];
					$productprice = $row['decSellingPrice'];
					$producttotal = $row['decTotalSellingPrice'];
			?>
					<tr>
						<td>
							<table>
								<td style="width:100px" class="row">
									<?=$productnumber; ?>
								</td>
								<td style="width:200px" class="row">
									<?=$producttitle; ?>
								</td>
								<td style="width:100px" class="row">
									<?=$quantity;?>
								</td>		
								<td style="width:150px" class="row">
									<?= "$" . $productprice; ?>
								</td>
								<td style="width:150px" class="row">
									<?= "$" . $producttotal; ?>
								</td>
							</table>
						</td>
					</tr>
				<?php	
				}
				?>								
				<tr>
					<td style="width:700px"><hr/></td>
				</tr>				
			</tr>		
		</tr>
		<!-- Get order total-->
		<?php
			$sql = "SELECT * FROM VIndividualOrderSummaries
					WHERE intCustomerID='$customerid' 
					AND intOrderIndex='$orderNumber'";
			$results = $conn->query($sql);
			while($row = $results->fetch(PDO::FETCH_ASSOC))
			{					
				$total_price += $row['decOrderTotalPrice'];
				$total_price = number_format($total_price, 2, '.', '');
				$deliveryname = $row['strDeliveryName'];
			}
		?>						
		<tr bgcolor="yellow">
			<td>
				<table>
					<td style="width:400px"class="totals">
						Delivered to: <?=$deliveryname; ?>
					</td>
					<td style="width:150px"class="totals" align="right">
						Order Total:
					</td>
					<td style="width:150px"class="totals" align="center">
						<?= "$" . $total_price; ?>
					</td>
				</table>
			</td>
		</tr>																			
	</table>
	<br>
<?php
}
?>
<input type="button" value="Home" onclick="location.href='index.php?menukey=8';"/>						

	

	


