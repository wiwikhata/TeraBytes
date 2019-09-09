<!-- --------------------------------------------------------------------------
--   Name: sidebar.php
--   Abstract: The sidebar containing categories and brands
-- --------------------------------------------------------------------------->
<!-- Categories -->
<div class="sidebar_title">Categories</div>
<ul class="sidebar_categories">
<?php
$query   = "SELECT * FROM tcategories
            WHERE intCategoryStatusID = 1";
$results = $conn->query($query);
$rows    = $results->fetchAll(); 

// Loop through rows
foreach($rows as $row)
{
    $categoryid = $row['intCategoryID'];
    $categorytitle = $row['strCategoryTitle'];
        
    // Display each category name
    ?>
    <li class=sidebar_list>
        <a href="index.php?menukey=1&amp;categoryid=<?=$categoryid;?>">
            <?=$categorytitle ?>
        </a>
    </li>
<?php
}	
?>
</ul>
<!-- Brands -->				
<div class="sidebar_title">Brands</div>
<ul class="sidebar_brands">
<?php
$query   = "SELECT * FROM tbrands
            WHERE intBrandStatusID = 1
            ORDER BY strBrandTitle";
$results = $conn->query($query);
$rows    = $results->fetchAll(); 

// Loop through rows
foreach($rows as $row)
{
    $brandid = $row['intBrandID'];
    $brandtitle = $row['strBrandTitle'];
        
    // Display each brand name
    ?>
    <li class=sidebar_list>
        <a href="index.php?menukey=1&amp;brandid=<?=$brandid;?>">
            <?=$brandtitle ?>
        </a>
    </li>
<?php
}	
?>
</ul>