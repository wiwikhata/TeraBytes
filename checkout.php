<!-- --------------------------------------------------------------------------
--  Name: checkout.php
--  Abstract: Display the customer's cart details and shipping information
--  for review prior to order submission/confirmation. The PayPal link-button
--  does not actually process a PayPal payment. 
-- --------------------------------------------------------------------------->
<?php
// Initialize variables
$customerid = $_SESSION['CustomerID'];
$_SESSION['DeliveryIndex'] = 1;
$shipping = 0.00;
$shippingcharge = 0.00;
$amountpayable = 0.00;
$product_count = 0;

// Get customer information for shipping table
$query = "SELECT * FROM VAllCustomerDeliveryAddresses
          WHERE intCustomerID='$customerid'
          AND   intDeliveryIndex= 1";
$results = $conn->query($query);
$count = $results->rowCount();
if($count > 0)
{
	$row = $results->fetch(PDO::FETCH_ASSOC);
	$customername = $row['strCustomerName'];
	$address1     = $row['strAddress1'];
	$address2     = $row['strAddress2'];
	$city         = $row['strCity'];
	$state        = $row['strState'];
	$country      = $row['strCountry'];	
	$zipcode      = $row['strZipCode'];	
}

// Get customer cart totals
$query = "SELECT * FROM VCustomerCartTotalSummaries 
          WHERE intCustomerID='$customerid'";
$results = $conn->query($query);
while($row = $results->fetch(PDO::FETCH_ASSOC))
{
    $product_count = $row['intTotalProducts'];
    $total_price   = $row['decTotalPrice'];
    $total_price   = number_format((float)$total_price, 2, '.', '');
    $amountpayable = $total_price;
}

// Next-day shipping
if(isset($_POST['submit']))
{
    // Add next-day shipping option charges
	if(isset($_POST['chkShipping']))
	{				
	    $shipping = 7.50 * $product_count;
	    $shippingcharge = number_format($shipping, 2, '.', '');
        $amountpayable = $total_price + $shipping;
        $amountpayable = number_format((float)$amountpayable, 2, '.', '');     		
    }      
}
// Form post
if(isset($_POST['submit3alt']))
{    
    // If alternate address chosen
    if(isset($_POST['altchk']))
    {       
        // Assign fields
        $customername = $_POST['altcustomername'];
        $address1     = $_POST['altaddress1'];
        $address2     = $_POST['altaddress2'];
        $city         = $_POST['altcity'];
        $state        = $_POST['altstate'];
        $countryid    = $_POST['altcountry'];	
        $zipcode      = $_POST['altzipcode'];
                
        // Use database stored procedure to add delivery address
        $sql = 'CALL uspAddDeliveryAddress(?,?,?,?,?,?,?,?)';
        $stmt = $conn->prepare($sql);
        // Bind parameters
        $stmt->bindParam(1, $customerid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 11);
        $stmt->bindParam(2, $customername, PDO::PARAM_STR, 50);
        $stmt->bindParam(3, $address1, PDO::PARAM_STR, 50);
        $stmt->bindParam(4, $address2, PDO::PARAM_STR, 50);
        $stmt->bindParam(5, $city, PDO::PARAM_STR, 50);
        $stmt->bindParam(6, $state, PDO::PARAM_STR, 50);
        $stmt->bindParam(7, $countryid, PDO::PARAM_INT, 11);
        $stmt->bindParam(8, $zipcode, PDO::PARAM_STR, 50);        
        // Execute
        $stmt->execute();
        // Return the delivery index value & assign to session variable
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['DeliveryIndex'] = $row['intDeliveryIndex']; 
        $stmt->closeCursor();
        // Redirect
        echo "<script>window.open('index.php?menukey=62', '_self')</script>";
    }       
}
// Primary address
elseif(isset($_POST['submit3']))
{
    // Set session variable
    $_SESSION['DeliveryIndex'] = 1;
    // Redirect
    echo "<script>window.open('index.php?menukey=62', '_self')</script>";
}
?>
<div id="checkout-heading">
    <h2>Please review your order</h2>
</div>
<div id="main-content">
    <!-- Order Summary Form -->
    <?php		
    echo 
    '<div id="shoppingCart">         
        <div id="cart-header">
            <h3>Your Order Summary</h3>
        </div>         
        <form name="frmCheckout" id="frmCheckout" action="" method="post">                   
            <table id="cartSummary" >          
                <tr bgcolor="#00ccff">
                    <th width="30px">Qty</th>
                    <th width="200px">Item Description</th>
                    <th width="60px">Price (ea)</th>
                    <th width="60px">Total</th>
                </tr>';

                // Get customer's cart items			
                $query = 
                "SELECT * FROM VIndividualCustomerCartSummaries 
                 WHERE intCustomerID='$customerid'";                
                $results = $conn->query($query);
                $count = $results->rowCount();
                if($count > 0)
                {
                    // Var used to alternate row color
                    $b = 0;				
                    // Loop through cart products
                    while($row = $results->fetch(PDO::FETCH_ASSOC))
                    {
                        // Alternate row background color
                        $bg_color = ($b++ %2 == 1) ? 'odd' : 'even';
                                            
                        // Get product information
                        $producttitle    = $row['strProductTitle'];
                        $productprice    = $row['decProductPrice'];
                        $productquantity = $row['intQuantity'];
                        $producttotal    = $row{'decTotalProductPrice'};					
                        
                        // Format
                        $producttotal  = number_format((float)$producttotal, 2, '.', '');
                        
                    // Product summary section
                    echo	
                    '<tr align="center" class="'. $bg_color . '">
                        <td>' . $productquantity . '</td>
                        <td>' . $producttitle  . '</td>
                        <td>' . "$" . $productprice . '</td>
                        <td>' . "$" . $producttotal . '</td>							
                     </tr>';
                    }
                    // Total summary section 
                    echo  
                    '<tr bgcolor="#f8f7f7">
                        <td colspan="4"><hr /></td>
                     </tr>			
                     <tr class="totals">
                        <td colspan="2">&nbsp;</td>
                        <td colspan="1" align="center">SubTotal:</td>
                        <td align="right">' . "$" . $total_price . "&nbsp;&nbsp;&nbsp;" . '</td>
                     </tr>			
                     <tr class="totals">						
                        <td colspan="2" class="ship-charge">&nbsp;&nbsp;			
                            <input type="checkbox" name="chkShipping" id="chkShipping" /> 
                            &nbsp;Next Day Shipping (+$7.50/item)
                        </td>
                        <td colspan="1" align="center">Shipping:</td>
                        <td align="right">' . "$" . $shippingcharge . "&nbsp;&nbsp;&nbsp;" . '</td>
                     </tr>									
                     <tr class="totals" >
                        <td colspan="2">&nbsp;</td>
                        <td colspan="1" align="center"><b>Order Total:</b></td>
                        <td align="right">' . "$" . $amountpayable . "&nbsp;&nbsp;&nbsp;" . '</td>		
                     </tr>';
                }
            echo
            '</table>           
            <input type="submit" name="submit" id="update_total" value="Update total" 		                   style="display:none;"/>            
        </form>
        <a href="index.php?menukey=6" class="return">Back to Cart</a>			
    </div>';
    ?>		
    <div id="main-shipping">       
        <div id="shipping-header">
            <h3>Shipping Information*</h3>			
        </div>
        <form name="frmPrimaryAddress" id="frmPrimaryAddress" action="" method="post">   			
            <table name="customerdatatble" id="customerdatatable">
                <tr>                                    
                    <td>
                        <input type="text" id="customername" name="customername" 
                               class=" input-static edit_user" value='<?=$customername;?>'>
                    </td>               
                </tr>				
                <tr>		                   
                    <td>
                        <input type="text" id="address1" name="address1" class="input-static edit_user" 
                               value='<?=$address1;?>'>
                    </td>               
                </tr>						
                <tr>		           
                    <td>
                        <input type="text" id="address2" name="address2" class="input-static edit_user" 
                               value='<?=$address2;?>'>
                    </td>
                </tr>						
                <tr>					                        
                    <td>
                        <input type="text" id="city" name="city" class="input-static edit_user"                value='<?=$city;?>'>
                    </td>                    
                </tr>						
                <tr>					
                    <td>
                        <input type='text' id='state' name='state' class="input-static edit_user" 
                               value='<?=$state;?>'>
                    </td>
                </tr>
                <tr>																	
                    <td>
                        <input type='text' id='country' name='country' class="input-static edit_user" 
                               value='<?=$country;?>'>
                    </td>
                </tr>	
                <tr>
                    <td>
                        <input type='text' id='zipcode' name='zipcode' class="input-static edit_user" 
                               value='<?=$zipcode;?>'>
                    </td>
                </tr>           
            </table>
            <input type='submit' name='submit3' id='submit3'> 
        </form>
    </div>
    <div id='alt-shipping'> 
        <form name="frmDeliveryAddress" id="frmDeliveryAddress" action="" method="post">      
            <div id="shipping-header">
                <h3>Alternate Shipping Data*</h3>			
            </div>			
            <table name="customeraltdatatable" id="customeraltdatatable">
                <tr class="first-row">                                    
                    <td>
                        <input type="text" id="altcustomername" name="altcustomername"                         placeholder="Recipient Name" required>
                    </td>               
                </tr>				
                <tr>		                   
                    <td>
                        <input type="text" id="altaddress1" name="altaddress1" placeholder="Address1"
                               required>
                    </td>               
                </tr>						
                <tr>		           
                    <td>
                        <input type="text" id="altaddress2" name="altaddress2" placeholder="Address2">
                    </td>
                </tr>						
                <tr>					                        
                    <td>
                        <input type="text" id=altcity" name="altcity" placeholder="City" required>
                    </td>                    
                </tr>						
                <tr>					
                    <td>
                        <input type='text' id='altstate' name='altstate' placeholder="State" required >
                    </td>
                </tr>
                <tr>																	                
                    <td>           
                        <select name="altcountry" id="altcountry">
                            <option value="32" selected>United States</option>			
                                <!-- Create countries drop-down list -->					
                                <?=getAllCountries();?>	                    						
                        </select>
                    </td>          
                </tr>	
                <tr>
                    <td>
                        <input type='text' id='altzipcode' name='altzipcode' placeholder="Postal Code"
                        required>
                    </td>
                </tr>                           
            </table>
            <input type='checkbox' name='altchk' id='altchk' > 
            <input type='submit' name='submit3alt' id='submit3alt'>                            
        </form>                    
    </div>
    <div id="chkbox">										               
        <input type="checkbox" name="alt" id="alt" >
        <span>*Check to send to alternate address</span>                              
    </div>    
</div>
<div id="payment">
    <h2> Click button below to confirm and submit your order!</h2>			
    <!-- Display the payment button. -->    
    <button type="button" name="button1" id="button"></button>	
</div>																						

