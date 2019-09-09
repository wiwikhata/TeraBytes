<!-- --------------------------------------------------------------------------
--   Name: my_orders.php
--   Abstract: Display a summary of the customer's orders with each one 
--   detailed separately.  
-- --------------------------------------------------------------------------->
<?php	
// Initialize variables
date_default_timezone_set('America/New_York');
$customerid = $_SESSION['CustomerID'];
	
// If customer is logged in
if($customerid > 0)
{                  
	// Get order count
	$query = "SELECT MAX(intOrderIndex) AS MaxIndex 
			  FROM tcustomerorders 
			  WHERE intCustomerID=$customerid";						
	$results = $conn->query($query);
	$row     = $results->fetch(PDO::FETCH_ASSOC);
	$count   = $row['MaxIndex'];	
	if ($count > 0)
	{
?>		
		<div id=yourOrders>
			<div id="orderHeader">
				<h1>Your Order Summaries</h1>								  
			</div>
			<p style="width:699px;margin-left:30px;text-align:center;background-color:#e6e6ff;				      font-size:16px; font-family:arial; font-weight:bold">
				Excluding any expedited shipping charges
			</p>			
			<!-- New table for each order -->
			<?php		
			for($intIndex = 0; $intIndex < $count; $intIndex += 1)
			{
				$total_price   = 0.00;
				$orderNumber   = $intIndex + 1;
				$productnumber = 0;

				/* order # and date */
				$sql = "SELECT dtmOrderDate FROM tcustomerorders
						WHERE intCustomerID='$customerid' 
						AND intOrderIndex='$orderNumber'";
				$result = $conn->query($sql);
				$row = $result->fetch(PDO::FETCH_ASSOC);
				$tempDate = $row['dtmOrderDate'];
				$date = strtotime($tempDate);
				$orderDate = date("m/d/y", $date);											
				?>			
				<table id="ordersTable" cellspacing="0">
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
						<tr>
							<td style="width:700px"><hr/></td>				
						</tr>
						<!-- table header section -->							
						<td>
							<table id="header-table">									
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
						<!-- product data section -->
						<?php
						$sql = "SELECT * FROM VAllCustomerOrderSummaries
								WHERE intCustomerID='$customerid' 
								AND intOrderIndex='$orderNumber'";
						$results = $conn->query($sql);		
						while($row = $results->fetch(PDO::FETCH_ASSOC))
						{
							$productnumber += 1;
							$producttitle = $row['strProductTitle'];
							$quantity     = $row['intQuantity'];
							$productprice = $row['decSellingPrice'];
							$productprice = number_format($productprice, 2, '.', '');
							$producttotal = $row['decTotalSellingPrice'];
							$producttotal = number_format($producttotal, 2, '.', '');
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
					<!-- order total section -->
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
		</div>
	<?php
	}
	else
	{
	?>			
		<p style="font-face:bolder;font-size:30px;color:black;text-align:center;margin-top:40px">
			You have no previous orders
		</p>
	<?php	
	}
}								
?>

	


