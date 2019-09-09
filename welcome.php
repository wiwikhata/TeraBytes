<!-- --------------------------------------------------------------------------
--   Name: welcome.php
--   Abstract: The customer welcome section
-- --------------------------------------------------------------------------->
<?php
if($_SESSION['CustomerID'] != 0)
{
    $customerid = $_SESSION['CustomerID'];

    // Test for cart items
    $query = 
	"SELECT * FROM VCustomerCartTotalSummaries
     WHERE intCustomerID='$customerid'";    
	$results = $conn->query($query);			
    $count = $results->rowCount();
    
    echo 
    '<span class="left">
        Welcome&nbsp;&nbsp;' . $_SESSION['firstname'] . '&nbsp;&nbsp;
     </span>';
    
    // If cart items exist
    if($count > 0)
    {        
        $row = $results->fetch(PDO::FETCH_ASSOC);
        $total_cart_items = $row['intTotalProducts'];
        $total_price = $row['decTotalPrice'];

        // Display summary
        echo
        '<span class="middle">
            Your Shopping Cart -
            <u>Total Items:</u> ' . $total_cart_items . '&nbsp;&nbsp;
            <u>Total Price:</u>&nbsp;$' . $total_price .
            '<a class="cart-link" href="index.php?menukey=6">
                Go to Cart
             </a>	
         </span>';
    }    
    echo 
    "<a class='log-status right' a href='index.php?menukey=53'>
        Logout
     </a>";	
}
else
{
    if(!isset($_GET['add_cart']))
    {
        echo 
        '<span class="left">
            <b>Welcome Guest!&nbsp;&nbsp;</b>
        </span>'; 
        
        echo 
        "<a class='log-status right' href='index.php?menukey=5'>
            Login/Register
        </a>";
    }
    elseif(isset($_GET['add_cart']))
    {
        echo 
			"<span class='left'>
            	<b>Welcome Guest!&nbsp;&nbsp;</b>
        	</span> 
			<b style='font-size:24px;margin-top:40px;margin-bottom:20px;margin-left:30px;color:red'>
				Please login or register your account
			</b>
			<a class='log-status right' href='index.php?menukey=5'>
            	Login/Register
        	</a>
			<p>&nbsp;</p>";
    }		
}

