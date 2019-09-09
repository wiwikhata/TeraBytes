# TeraBytes Warehouse

This repo contains the files for a project that is a significantly redesigned version of the Udemy course:
> E-Commerce Website in PHP and MySQL From Scratch" by Abdul Wali

The TeraBytes Warehouse (on-line tech products site) project is an exercise in creating a desk-top, ecommerce application using HTML, CSS, JavaScript, Apache 2.4; php v7.3.5 and MySQL v5.7.

This repo contains my most recent version of this website.

## Some features

- The project has been pre-populated with data for easy testing and modification
- The Administrative area must be accessed directly: eg: /TeraBytes/admin_area/index.php
    A pre-populated administrator is: username:jsmart password:smart10
- Use your own server credentials in the MySqlConnector connector string
- Use your own email credentials in the phpmailer sections
- A paginator class is included to properly group larger result-sets
- Some simple server-side validation of form inputs is performed
- jQuery is used to add some effects and event handling
- The database utilizes a price history table populated by a trigger when a product is edited
    This allows for non-static product pricing & accurate historical customer order price data
- A table for alternative delivery addresses is included
- Database views and stored procedures are used to greatly simplify programmatic coding and
    limit the number of required database interractions
- Triggers are used to add/edit some of the dependent tables when appropriate

In addition I used the following resources in my development process:

- Gimp 2.10 for image importing/manipulation and for banner creation
- TINYMCE to create a rich text-editor for product adding/editing
- Database Schema Designer (DBSD) to create, design and test the database
- MySQL Workbench v6.3CE for database execution & scripting
