<!-- --------------------------------------------------------------------------
--   Name: customer_cart.php
--   Abstract: The shopping cart page. Lists the products selected as well as
--   the quantities of each, product price, product total and cart sub-total.
--   Products can be removed from the cart or the quantities for selected
--   products can be changed.  
-- --------------------------------------------------------------------------->
<?php
$customerid = $_SESSION['CustomerID'];

// If updating cart
if(isset($_POST['update_cart']))
{
    // If removing products
    if(isset($_POST['chkRemove']))
    {		
        // Loop through checkboxes
        foreach($_POST["chkRemove"] as $productid)
        {
            // Delete checked items
            $query = "DELETE FROM tcustomercarts 
                      WHERE intCustomerID='$customerid'                
                      AND intProductID='$productid'";
            $results = $conn->query($query);				
        }
        // Any items left in cart?
        $query = 
        "SELECT COUNT(intProductID) AS intCount 
         FROM `tcustomercarts` 
         WHERE intCustomerID='$customerid'";            		  
        
        $results = $conn->query($query);
        $row = $results->fetch(PDO::FETCH_ASSOC);
        $count = $row['intCount'];

        if($count == 0)
        {
            // Refresh page
            echo "<script>window.open('index.php?menukey=6', '_self')</script>";
        }
    }
    // If updating product quantities
    else
    {
        // Get quantity values from form
        if(isset($_POST["quantity"]))
        {
            // Get the array of quantity values
            $quantityArray = $_POST['quantity'];
                                        
            // Get productid values from form
            if(isset($_POST["productid"]))
            {
                // Get the array of productid values
                $productidArray = $_POST['productid'];	

                // Loop through both arrays
                for($intIndex = 0; $intIndex < count($quantityArray); $intIndex += 1)
                {
                    // Get productid and quantity
                    $productid = $productidArray[$intIndex];
                    $quantity = $quantityArray [$intIndex];
                    
                    // If quantity is not 0
                    if($quantity != 0)
                    {						
                        // Update the database
                        $query =
                         "UPDATE tcustomercarts 
                          SET intQuantity='$quantity' 
                          WHERE intCustomerID='$customerid' 
                          AND intProductID='$productid'";
                        $results = $conn->query($query);
                    }
                    else
                    {
                        // Delete product
                        $query = 
                        "DELETE FROM tcustomercarts 
                         WHERE intCustomerID='$customerid' 
                         AND intProductID='$productid'";
                        $results = $conn->query($query);	
                    }
                }										
            }
        }
    }
    // Refresh page
    echo "<script>window.open('index.php?menukey=6', '_self')</script>";
}

// Continue shopping
if(isset($_POST['continue']))
{
    // Return to index.php
    echo "<script>window.open('index.php', '_self')</script>";
}
?>
<div id="customer_cart">
    <?php
    $query = 
    "SELECT * FROM VCustomerCartTotalSummaries 
     WHERE intCustomerID='$customerid'";
    $result = $conn->query($query);
	$count = $result->rowCount();	

    if($customerid == 0)
    {
        echo "<b style='font-size:24px; margin-top:40px; margin-left:245px'>
                You must register to buy!
              </b>
              <p>&nbsp;</p><p>&nbsp;</p>
              <a class='cart_home' href='index.php?menukey=51'>Register Here!</a>";	
    }
    else if($count == 0)
    {
        echo "<b style='font-size:24px; margin-top:40px; margin-left:275px'>Your cart is empty!</b>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <a class='cart_home' href='index.php'>Continue Shopping </a>";	
    }
    else
    {
        // Get total price
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $total_price = $row['decTotalPrice'];
         // Format to decimal
        $total_price = number_format((float)$total_price, 2, '.', '');	

        // Get cart product data
        $query = 
        "SELECT * FROM VIndividualCustomerCartSummaries as tc          
         WHERE tc.intCustomerID='$customerid'
         ORDER BY tc.intProductID";
        $results = $conn->query($query);
    ?>
        <form name="frmCart" id="frmCart" action="" method="POST" enctype="multipart/form-data" />		    <div>
                <div class="cartheading">
                    <h1>Your Shopping Cart</h1>								  
                </div>
            </div>								
            <table id="cart_table" width="700">
                <tr align="center">
                    <th class="header">Remove</th>
                    <th class="header">Product(s)</th>
                    <th class="header">Quantity</th>
                    <th class="header">Price(ea)</th>
                    <th class="header">Product Total</th>
                </tr>
                <tr>
                    <td colspan="5">
                        <span class="spacer">&nbsp;</span>
                    </td>									
                </tr>	               													
                <?php								                
                while($row = $results->fetch(PDO::FETCH_ASSOC))
                {
                    // Get data
                    $productid    = $row['intProductID'];
                    $producttitle = $row['strProductTitle'];
                    $productimage = $row['strProductImage'];
                    $quantity     = $row['intQuantity'];
                    $productprice = $row['decProductPrice'];
                    $producttotal = $row['decTotalProductPrice'];
                    
                    // Format to decimal
                    $productprice = number_format((float)$productprice, 2, '.', '');
                    $producttotal = number_format((float)$producttotal, 2, '.', '');
                ?>
                <!-- Hidden row to hold array of productid's -->
                <tr class="hidden">
                    <td class="row">
                        <input type="hidden" name="productid[]" id="productid" 			   
                               value="<?=$productid;?>" />
                    </td>									
                </tr>
                <tr class="row">
                    <td colspan="5">
                        <span class="spacer2">&nbsp;</span>
                    </td>									
                </tr>
                <tr align="center">
                    <td class="row">
                        <input type="checkbox" class="checks" name="chkRemove[]" 
                               value="<?=$productid; ?>" />
                    </td>
                    <td class="row">
                        <?=$producttitle; ?>
                        <br>
                        <img src="admin_area/product_images/<?=$productimage; ?>" 		 
                             width="60" height="60" />
                        <br>
                        <span>&nbsp;</span>
                    </td>
                    <td class="row">
                        <input type="text" name="quantity[]" id="quantity" class="cart-quantity" size="1"         value="<?=$quantity;?>" />
                    </td>						
                    <td class="row"><?= "$" . $productprice; ?></td>
                    <td class="row"><?= "$" . $producttotal; ?></td>
                </tr>
                <tr colspan="5">
                    <td height="5"></td>								
                </tr>									
                <?php						
                }																
                ?>								
                <tr class="row totals">	
                    <td colspan="3"></td>								
                    <td colspan="1" class="total-label">&nbsp;&nbsp;Cart Total:</td>
                    <td colspan="1" align="center"><?= "$" . $total_price; ?></td>
                </tr>						
                <tr align="center" class="actions">                   
                    <td colspan="1">
                        <input type="submit" id="update_cart" name="update_cart" value="Update Cart"/>
                    </td>                   
                    <td colspan="2" align="left">
                        <input type="submit" name="continue" value="Continue Shopping"/>
                    </td>
                    <td colspan="2" align="left">
                        <button id="cart-button" class="button">
                            <a href="index.php?menukey=61" id="checkout">Check Out</a>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>								
            </table>													
        </form>
    <?php
    }
    ?>
</div>