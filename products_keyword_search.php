<!-- --------------------------------------------------------------------------
--   Name: products_keyword_search.php
--   Abstract: Search for products by keywords
--   A very simple search based upon brand and category keywords (in that order)
-- --------------------------------------------------------------------------->
<?php
$search_query = "";
// Define desired # of products to display per page
$results_per_page = 6;

if(!empty($_SESSION['KeywordSearch'])) 
{
    $search_query = $_SESSION['KeywordSearch'];				
}
elseif(!empty($_POST['txtUserQuery'])) 
{	
    $_SESSION['KeywordSearch'] = test_input($_POST["txtUserQuery"]);
    $search_query = $_SESSION['KeywordSearch']; 
}

if($search_query != "")
{      
    $query = "SELECT * FROM VActiveProducts
              WHERE strProductKeywords 
              LIKE '%" . $search_query . "%'"; 
    $results = $conn->query($query);
    $count = $results->rowCount();

    if($count == 0)
    {
        echo 
        "<b style='font-size:24px; margin-top:40px; margin-bottom:20px; margin-left:260px'>
                Product not found!
        </b>
        <p>&nbsp;</p>"; 
    }
    else
    {
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

        if (isset($_SESSION['KeywordSearch']))
        {            
            $sql = 'SELECT * FROM VActiveProducts' 
                 . ' WHERE strProductKeywords LIKE "%' . $search_query . '%"'
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
        <!-- Pagination nav bar -->
        <div class="page_list" style="clear:both; text-align:center; padding-top:20px">        
        <?php
        if($number_of_pages > 1)
        {       
            for($page = 1; $page <= $number_of_pages; $page ++)
            {       
                echo 
                '<a href="index.php?menukey=4&amp;page=' . $page . '">' . $page . '</a>';   
            }
        }                 
        ?>            
        </div>                   
    <?php
    }
}
else 
{
    echo 
    "<b style='font-size:24px; margin-top:40px; margin-bottom:20px; margin-left:260px'>
        No product selected!
    </b>
    <p>&nbsp;</p>";    
}

function test_input($data) 
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
?>






