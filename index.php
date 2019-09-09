<!-- --------------------------------------------------------------------------
--   Name: index.php
--   Abstract: Startup page for the TeraBytes Warehouse website 
-- --------------------------------------------------------------------------->
<?php
// Start session
session_start();
	
// Includes	
include ("functions/functions.php");
include ("classes/pagination.php");

// If customer not logged in
if(empty($_SESSION['CustomerID'])) 
{	 
	 $_SESSION['CustomerID'] = 0;   	
	 $_SESSION['firstname']  = "Guest";  		 
}
?>
<! DOCTYPE>
<html>
	<head>
		<title>TeraBytes Warehouse</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="vendors/css/normalize.css">  				
		<link rel="stylesheet" href="styles/style.css" media="all" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script src="scripts/jquery.js"></script>		
	</head>
	<body>		
		<!-- Main Wrapper -->
		<div class="main_wrapper">
			<!-- Header -->
			<header class="header">
				<?php include "header.php"; ?>
			</header>
			<!-- Menu bar -->
			<div class="menubar">
				<!-- Menu items -->
				<ul class="menu">
					<li><a href="index.php?menukey=0">Home</a></li>
					<li><a href="index.php?menukey=2">All Products</a></li>
					<li><a href="customers/my_account.php">My Account</a></li>
					<li><a href="index.php?menukey=5">Sign Up</a></li>
					<li><a class="cart-link" href="index.php?menukey=6">Shopping Cart</a></li>
					<li><a href="index.php?menukey=7">Contact Us</a></li>
				</ul>
				<!-- Keyword search form -->
				<div id="form">
					<form name="frmSearch" id="frmSearch" action="index.php?menukey=4" 
						  method="POST" enctype="multipart/form-data">
						<input type="text" class="user_query" name="txtUserQuery"                              placeholder="&nbsp;Search for a Product"/>
						<input type="submit" name="submit" value=""/>				
					</form>
				</div>
			</div>
			<!-- Sidebar -->
			<aside class="sidebar">
				<?php include "sidebar.php"; ?>				
			</aside>
			<!-- Welcome bar -->
			<section class="welcome">
				<?php Add_to_Cart(); ?>
				<?php include "welcome.php"; ?>					
			</section>
			<!-- Content section -->
			<section class="content">
				<?php		
					if (isset($_GET['menukey']))  
					{
						$menukey = $_GET['menukey'];
					} 
					else 
					{
						$menukey = 0;
					}
					switch ($menukey) 
					{
						case 0:
							unset($_SESSION['categoryid']);	
							unset($_SESSION['brandid']);
							unset($_SESSION['KeywordSearch']);											
							include "products_search.php";
							break;	
						case 1:							
							if(isset($_GET['categoryid']))
							{
								$_SESSION['categoryid'] = $_GET['categoryid'];
								if(isset($_SESSION['brandid']))
								{
									unset($_SESSION['brandid']);
								}
								if(isset($_SESSION['KeywordSearch']))
								{
									unset($_SESSION['KeywordSearch']);
								}									
							}							
							else if(isset($_GET['brandid']))
							{
								$_SESSION['brandid'] = $_GET['brandid'];
								if(isset($_SESSION['categoryid']))
								{
									unset($_SESSION['categoryid']);
								}
								if(isset($_SESSION['KeywordSearch']))
								{
									unset($_SESSION['KeywordSearch']);
								}	
							}												
							include "products_search.php";
							break;													
						case 2:
							include "products_all.php";
							break;
						case 3:
							include "product_detail.php";
							break;
						case 4:	
							if(isset($_SESSION['categoryid']))
							{
								unset($_SESSION['categoryid']);
							}
							if(isset($_SESSION['brandid']))
							{
								unset($_SESSION['brandid']);
							}					
							include "products_keyword_search.php";
							break;
						case 5:
							if($_SESSION['CustomerID'] != 0)
							{
								echo 
								"<b style='font-size:24px; margin-top:40px; margin-bottom:20px; 			   margin-left:250px'>
									You are already logged in.
								</b>
								<p>&nbsp;</p>";	
							}
							else
							{
								include "login.php";								
							}
							break;
						case 51:
							include "customer_register.php";
							break;
						case 52:
							include "forgot_password.php";
							break;
						case 53:
							include "logout.php";
							break;						
						case 54:
							include "reset_password.php";
							break;
						case 6:
							include "customer_cart.php";
							break;
						case 61:
							include "checkout.php";
							break;
						case 62:
							include "confirm.php";
							break;
						case 7:
							include "contact.php";
							break;						
						default:
							unset($_SESSION['categoryid']);	
							unset($_SESSION['brandid']); 
							unset($_SESSION['KeywordSearch']);											
							include "products_search.php";
							break;
					}
				?>
			</section>
			<div class="clearfix"></div>							
			<!-- Footer -->
			<footer><?php include "footer.php"; ?></footer>
		</div>
	</body>
</html>
