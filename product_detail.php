<!-- --------------------------------------------------------------------------
--   Name: product_detail.php
--   Abstract: Display product details    
-- --------------------------------------------------------------------------->
<?php
$categoryid = 0;
$brandid = 0;
$search_query = "";

if(isset($_SESSION['categoryid']))
{
    $categoryid = $_SESSION['categoryid'];
}
elseif(isset($_SESSION['brandid']))
{
    $brandid = $_SESSION['brandid'];
}
elseif(isset($_SESSION['KeywordSearch']))
{    	
    $search_query = $_SESSION['KeywordSearch'];
}

$productid = $_GET['productid'];
$query = "SELECT * FROM VActiveProducts 
          WHERE intProductID='$productid'";
$results = $conn->query($query);
$count = $results->rowCount();

if($count > 0)
{
    // Loop through results
    while($row = $results->fetch(PDO::FETCH_ASSOC))
    {
        $productid          = $row['intProductID'];
        $producttitle       = $row['strProductTitle'];
        $productprice       = $row['decSellingPrice'];			
        $productimage       = $row['strProductImage'];
        $productdescription = $row['strProductDescription'];
    ?>
        <div class='details_box'>											
            <h3><?=$producttitle?></h3>
            <img src='admin_area/product_images/<?=$productimage?>'/>
            <p class="price">Price: $<?=$productprice?></p>									
            <div class='product_description'>
                <p><?=$productdescription?></p>
            </div>
            <br />
            <div class='bottom_buttons'>
                <?php
                if($categoryid != 0)
                {
                ?>
                    <a href='index.php?menukey=1amp;categoryid=<?=$categoryid;?>' 
                       class='home inner inner3'>Back
                    </a>
                <?php
                }
                elseif($brandid != 0)
                {
                ?>
                    <a href='index.php?menukey=1amp;brandid=<?=$brandid;?>' 
                       class='home inner inner3'>Back
                    </a>
                <?php
                }
                elseif($search_query != "")
                {
                ?>
                    <a href='index.php?menukey=4' 
                       class='home inner inner3'>Back
                    </a>
                <?php
                }
                else
                {
                ?>
                    <a href='index.php' class='home inner inner3'>Back</a>
                <?php
                }
                ?>               
                <a href='index.php?add_cart=<?=$productid?>'>
                    <!-- Add to Cart button -->
                    <button class='add inner inner3' style='margin-left:10px;'></button>
                </a>
            </div>
        </div>
    <?php
    }
}
?>
