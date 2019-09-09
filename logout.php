<!-- --------------------------------------------------------------------------
--   Name: logout.php
--   Abstract: Log out of Customer session   
-- --------------------------------------------------------------------------->
<?php
if($_SESSION['CustomerID'] != 0)
{
    unset($_SESSION['CustomerID']);
    unset($_SESSION['firstname']);
    session_destroy();
}
echo "<script>window.open('index.php', '_self')</script>";
?>