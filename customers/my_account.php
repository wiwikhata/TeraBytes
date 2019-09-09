<!-- --------------------------------------------------------------------------
--   Name: my_account.php
--   Abstract: Allow customer to manage his/her account 
-- --------------------------------------------------------------------------->
<?php
// Include session
session_start();

// Include functions.php	
include ("../functions/functions.php");
if(empty($_SESSION['CustomerID'])) 
{
	 // Not logged in
	 $_SESSION['CustomerID'] = 0;   
}
$customerid = $_SESSION['CustomerID'];
?>
<! DOCTYPE>
<html>
	<head>
		<title>TeraBytes Warehouse - My Account</title>				
		<link rel="stylesheet" href="../styles/style.css" media="all" />
		<link rel="stylesheet" href="styles/style2.css" media="all" />               
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script src="../scripts/jquery.js"></script>	
	</head>
	<div class="main_wrapper">
			<!-- Header -->
			<header class="header">
				<?php include "header.php"; ?>
			</header>
			<!-- Menu bar -->
			<div class="menubar">
				<!-- Menu items -->
				<ul class="menu">
					<li><a href="../index.php?menukey=0">Home</a></li>
					<li><a href="../index.php?menukey=2">All Products</a></li>
					<li><a href="my_account.php">My Account</a></li>
					<li><a href="../index.php?menukey=5">Sign Up</a></li>
					<li><a class="cart-link" href="../index.php?menukey=6">Shopping Cart</a></li>
					<li><a href="../index.php?menukey=7">Contact Us</a></li>
				</ul>
				<!-- Search Form-->
				<div id="form">
					<form name="frmSearch" id="frmSearch" action="../index.php?menukey=4" 
						  method="POST" enctype="multipart/form-data">
						<input type="text" class="user_query" name="txtUserQuery" placeholder="&nbsp;Search for a Product"/>
						<input type="submit" name="submit" value=""/>				
					</form>
				</div>								
			</div>
			<!-- Sidebar -->
			<aside class="sidebar">
				<?php include "sidebar.php"; ?>				
			</aside>
			<!-- Welcome section -->
			<section class="welcome">
				<?php Add_to_Cart(); ?>
				<?php include "welcome.php"; ?>					
			</section>
			<!-- Content section -->
			<section class="my_account-content">
				<?php
				if($customerid == 0)
				{
					echo 
					"<h2 style='padding-top:20px'>
						Please login or register your account.
					</h2>";
				}
				else
				{
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
						case 2:
							include "change_password.php";
							break;
						case 3:
							include "my_orders.php";
							break;
						case 4:
							include "delete_account.php";
							break;
						default:
							break;
					}
				}
				?>
			</section>						
			<div class="clearfix"></div>							
			<!-- Footer -->
			<footer style='padding-top:20px'><?php include "../footer.php"; ?></footer>
		</div>		
	</body>
</html>