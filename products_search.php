<!-- --------------------------------------------------------------------------
--   Name: products_search.php
--   Abstract: Search for products by category, brand or randomly
-- --------------------------------------------------------------------------->
<?php
$search_type = "";
$search_variable = "";

// Define desired # of products to display per page
$results_per_page = 6;

// Category?
if (isset($_SESSION['categoryid']))
{
    $categoryid = $_SESSION['categoryid'];
    $query = "SELECT * FROM VActiveProducts
              WHERE intCategoryID='$categoryid'";  
    $results = $conn->query($query);
    $count = $results->rowCount();
    $search_type = "categoryid=";
    $search_variable = $categoryid;
}

// Brand?
else if (isset($_SESSION['brandid']))
{
    $brandid = $_SESSION['brandid'];
    $query = "SELECT * FROM VActiveProducts
              WHERE intBrandID='$brandid'";  
    $results = $conn->query($query);
    $count = $results->rowCount();
    $search_type = "brandid=";
    $search_variable = $brandid;
}

// Random
else
{
    $query = "SELECT * FROM VRandomActiveProducts";
    $results = $conn->query($query);
    $count = $results->rowCount();
}

// Determine number of pages needed
$number_of_pages = ceil($count/$results_per_page);

// Determine what page # the user has selected 
if (!isset($_GET['page'])) 
{
    $page = 1;
} 
else 
{
    $page = $_GET['page'];
}
// Compute the first row to display based upon the page the user has selected
$this_page_first_result = ($page-1) * $results_per_page;

// Products by category?
if (isset($_SESSION['categoryid']))
{
    $sql = 'SELECT * FROM VActiveProducts'
         . ' WHERE intCategoryID=' . $categoryid  
         . ' LIMIT '  . $this_page_first_result . ',' .  $results_per_page;            
    $results = $conn->query($sql);
}

// Products by brand?
if (isset($_SESSION['brandid']))
{
    $sql = 'SELECT * FROM VActiveProducts'
         . ' WHERE intBrandID=' . $brandid  
         . ' LIMIT '  . $this_page_first_result . ',' .  $results_per_page;             
    $results = $conn->query($sql);
}

// Obtain and display the data
while($row = $results->fetch(PDO::FETCH_ASSOC))
{
    $productid    = $row['intProductID'];
    $producttitle = $row['strProductTitle'];
    $productprice = $row['decSellingPrice'];			
    $productimage = $row['strProductImage'];
?>        			         			
    <div class='product'>
        <h3><?=$producttitle;?></h3>
        <img src='admin_area/product_images/<?=$productimage;?>'/>
        <p>
            Price: $<?=$productprice;?>
        </p>
        <a class='detail' href='index.php?menukey=3&amp;productid=<?=$productid;?>'>Details</a>
        <a href='index.php?add_cart=<?=$productid;?>'>
            <button class='cart-button'></button>
        </a>
    </div>
<?php
}
?>
<!-- Pagination bar -->
<div class="page_list" style="clear:both; text-align:center; padding-top:20px">        
<?php
if($number_of_pages > 1)
{       
    for($page = 1; $page <= $number_of_pages; $page ++)
    {       
        echo 
        '<a href="index.php?menukey=1&amp;' . $search_type . $search_variable . 
            '&amp;page=' . $page . '">' . $page . 
        '</a>';
    }
}                 
?>            
</div>             

         




   



