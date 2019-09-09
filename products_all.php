<!-- --------------------------------------------------------------------------
--  Name: products_all.php
--  Abstract: Display all products 
-- --------------------------------------------------------------------------->        
<?php
$_GET['menukey'] = 2;
$sql = "SELECT * FROM VActiveProducts order by strBrandTitle";
            
// Instantiate new paginator
$pager = new PS_Pagination($conn, $sql, 6, 6);    
$results = $pager->paginate();

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
        <p>Price: $<?=$productprice;?></p>
        <a class='detail' href='index.php?menukey=3&amp;productid=<?=$productid;?>'>Details</a>
        <a href='index.php?add_cart=<?=$productid;?>'>
            <button class='cart-button'></button>
        </a>
    </div>
<?php 
}
?>
<div class="all_page_list" style="clear:both; text-align:center; padding-top:20px; color:blue">      
    <?php         
    echo $pager->renderFullNav();              
    ?>            
</div>






