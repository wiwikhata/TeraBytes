<!-- --------------------------------------------------------------------------
--   Name: index.php
--   Abstract: The start page for the administration area of the site  
-- --------------------------------------------------------------------------->
<?php
// Include session
session_start();

// Includes	
include ("../MySqlConnector.php");
include ("../classes/pagination.php");

// If valid login
if(isset($_SESSION['user_name']))
{
?>
	<! DOCTYPE>
	<html>
		<head>
			<title>TeraBytes Warehouse - Administrator Panel</title>
			<link rel="stylesheet" href="styles/style.css" media="all" />
			<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>		
		</head>
		<body>			
			<div id="main_container">				
				<a href="index.php">
					<div id="header"></div>
				</a>
				<!-- Change background if other pages are being included -->
				<?php
				if (isset($_GET['menukey']))  
				{
					$menukey = $_GET['menukey'];
				}
				else 
				{
					$menukey = 0;	
				}
				if($menukey == 0)
				{
				?>
				<div id="left">
				<?php
				}
				else
				{
				?>
				<div id="left_alt">
				<?php
				}				
				// Which pages to include?
				switch ($menukey) 
				{					
					case 1:
						include "insert_product.php";
						break;
					case 2:
						include "view_products.php";
						break;
					case 20:
						include "edit_product.php";
						break;
					case 21:
						include "delete_product.php";
						break;
					case 22:
						include "undelete_product.php";
						break;
					case 3: 
						include "insert_category.php";
						break;
					case 4:
						include "view_categories.php";
						break;					
					case 40:
						include "edit_category.php";
						break;
					case 41:
						include "delete_category.php";
						break;
					case 42:
						include "undelete_category.php";
						break;
					case 5: 
						include "insert_brand.php";
						break;
					case 6:
						include "view_brands.php";
						break;					
					case 60:
						include "edit_brand.php";
						break;
					case 61:
						include "delete_brand.php";
						break;
					case 62:
						include "undelete_brand.php";
						break;
					case 7:
						include "view_customers.php";
						break;
					case 70:
						include "delete_customer.php";
						break;
					case 71:
						include "undelete_customer.php";
						break;
					case 8:
						include "view_customer_orders.php";
						break;
					case 81:
						include "customer_orders.php";
						break;
					default:
						break;
				}
				?>
				</div>									
				<div id="right">
					<h2 id="header2">Manage Content</h2>				
					<a href="index.php?menukey=1">Add New Product</a>
					<a href="index.php?menukey=2">View All Products</a>				
					<a href="index.php?menukey=3">Add New Category</a>
					<a href="index.php?menukey=4">View All Categories</a>
					<a href="index.php?menukey=5">Add New Brand</a>
					<a href="index.php?menukey=6">View All Brands</a>
					<a href="index.php?menukey=7">View All Customers</a>
					<a href="index.php?menukey=8">View Customer Orders</a>
					<a href="logout.php">Admin Logout</a>
					<a href="index.php">Home</a>												
				</div>				
			</div>			
		</body>
	</html>
<?php
}
// Else, redirect to administrator login page
else
{
	header("Location:login.php");
}
?>