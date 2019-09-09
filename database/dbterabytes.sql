-- ---------------------------------------------------------------------------------
-- Author: Douglas Heller       
-- Abstract: Create a database for Terabytes Warehouse using MySQL.
-- Utilizes a price history table to log product price changes. This allows 
-- the ability to preserve historical customer order-price data based upon product    
-- pricing at the time of each order. The history table is populated using an update 
-- trigger on the products table.
-- Includes a table to store alternative shipping locations for each customer
-- Developed using MySql v5.7; MySql Workbench v6.3
-- This script should run from top to bottom without error.
-- Many thanks to my friend & mentor Patrick (Pat) Callahan for his instruction and
-- guidance in database programming and in the creation of this database in particular.

-- ---------------------------------------------------------------------------------
-- CREATE SCHEMA IF NOT EXISTS dbterabytes DEFAULT CHARACTER SET utf8;
USE dbterabytes;

-- ---------------------------------------------------------------------------------
-- DROP COMMANDS 
-- ---------------------------------------------------------------------------------
-- Stored Procedures
DROP PROCEDURE IF EXISTS uspAddCustomer;
DROP PROCEDURE IF EXISTS uspEditCustomer;
DROP PROCEDURE IF EXISTS uspAddProduct;
DROP PROCEDURE IF EXISTS uspEditProduct;
DROP PROCEDURE IF EXISTS uspAddToCustomerCart;
DROP PROCEDURE IF EXISTS uspAddCustomerOrder;
DROP PROCEDURE IF EXISTS uspAddCustomerOrderItems;
DROP PROCEDURE IF EXISTS uspAddDeliveryAddress;

-- Views
DROP VIEW IF EXISTS VAllCustomerData;
DROP VIEW IF EXISTS VAllCustomerDeliveryAddresses;
DROP VIEW IF EXISTS VActiveProducts;
DROP VIEW IF EXISTS VRandomActiveProducts;
DROP VIEW IF EXISTS VCurrentProductPrices;
DROP VIEW IF EXISTS VIndividualCustomerCartSummaries;
DROP VIEW IF EXISTS VCustomerCartTotalSummaries;
DROP VIEW IF EXISTS VAllCustomerOrderSummaries;
DROP VIEW IF EXISTS VIndividualOrderSummaries;

-- Tables
DROP TABLE IF EXISTS tcustomercarts;
DROP TABLE IF EXISTS tcustomerorderitems;
DROP TABLE IF EXISTS tcustomerorders;
DROP TABLE IF EXISTS tcustomerorderstatuses;
DROP TABLE IF EXISTS tproductpricehistories;
DROP TABLE IF EXISTS tproducts;
DROP TABLE IF EXISTS tproductstatuses;
DROP TABLE IF EXISTS tbrands;
DROP TABLE IF EXISTS tbrandstatuses;
DROP TABLE IF EXISTS tcategories;
DROP TABLE IF EXISTS tcategorystatuses;
DROP TABLE IF EXISTS tcustomerdeliveryaddresses;
DROP TABLE IF EXISTS tdeliveryaddressstatuses;
DROP TABLE IF EXISTS tcustomers;
DROP TABLE IF EXISTS tcustomerstatuses;
DROP TABLE IF EXISTS tcountries;
DROP TABLE IF EXISTS tadministrators;

-- Triggers
DROP TRIGGER IF EXISTS tgrtcustomersafterinsert;
DROP TRIGGER IF EXISTS tgrtcustomersafterupdate;
DROP TRIGGER IF EXISTS tgrtproductsafterupdate;


-- ---------------------------------------------------------------------------------
-- CREATE TABLES  
-- ---------------------------------------------------------------------------------
-- Administrators
CREATE TABLE tadministrators 
(
	intAdminID      			INTEGER      	NOT NULL AUTO_INCREMENT,
	strAdminName    			VARCHAR(100) 	NOT NULL,
	strUserName     			VARCHAR(100) 	NOT NULL,
	strPassword     			VARCHAR(100) 	NOT NULL,
	strEmailAddress 			VARCHAR(100) 	NOT NULL,
	CONSTRAINT tadministrators_PK PRIMARY KEY CLUSTERED (intAdminID)
)
ENGINE = InnoDB;

-- Countries
CREATE TABLE tcountries 
(
	intCountryID 				INTEGER      	NOT NULL AUTO_INCREMENT,
	strCountry   				VARCHAR(50) 	NOT NULL,
    CONSTRAINT tcountries_PK PRIMARY KEY CLUSTERED (intCountryID)
)
ENGINE = InnoDB;

-- Customers
CREATE TABLE tcustomers
(
	intCustomerID               INTEGER         NOT NULL,
    strIPAddress                VARCHAR(50)     NOT NULL,
	strCustomerName 			VARCHAR(50) 	NOT NULL,
    strAddress1     			VARCHAR(50)     NOT NULL,
	strAddress2                 VARCHAR(50)     NOT NULL,
    strCity                     VARCHAR(50) 	NOT NULL,
    strState                    VARCHAR(50) 	NOT NULL,
    intCountryID                INTEGER     	NOT NULL,
    strZipCode                  VARCHAR(50) 	NOT NULL,
    strUserName                 VARCHAR(50) 	NOT NULL,
    strPassword                 VARCHAR(50) 	NOT NULL,
    strEmailAddress             VARCHAR(50) 	NOT NULL,
    strPhoneNumber              VARCHAR(50)  	NOT NULL,
	intCustomerStatusID         INTEGER			NOT NULL,
    CONSTRAINT tcustomers_PK PRIMARY KEY CLUSTERED (intCustomerID)
)
ENGINE = INNODB;

-- Customer Statuses
CREATE TABLE tcustomerstatuses
(
	intCustomerStatusID			INTEGER		    NOT NULL AUTO_INCREMENT,
	strCustomerStatus	    	VARCHAR(50)		NOT NULL,	
	CONSTRAINT tcustomerstatuses_PK PRIMARY KEY CLUSTERED (intCustomerStatusID) 
)
ENGINE = INNODB;

-- Customers Delivery Addresses
CREATE TABLE tcustomerdeliveryaddresses
(
	intCustomerID               INTEGER         NOT NULL,
    intDeliveryIndex            INTEGER    		NOT NULL,
	strDeliveryName 			VARCHAR(50) 	NOT NULL,
    strAddress1     			VARCHAR(50)     NOT NULL,
	strAddress2                 VARCHAR(50)     NOT NULL,
    strCity                     VARCHAR(50) 	NOT NULL,
    strState                    VARCHAR(50)     NOT NULL,
    intCountryID                INTEGER         NOT NULL,
    strZipCode                  VARCHAR(50) 	NOT NULL,
    intDeliveryAddressStatusID  INTEGER    		NOT NULL,
    CONSTRAINT tcustomerdeliveryaddresses_PK PRIMARY KEY CLUSTERED (intCustomerID, intDeliveryIndex)
)
ENGINE = INNODB;

-- Delivery Address Statuses
CREATE TABLE tdeliveryaddressstatuses
(
	intDeliveryAddressStatusID	INTEGER		    NOT NULL AUTO_INCREMENT,
	strDeliveryAddressStatus	VARCHAR(50)		NOT NULL,	
	CONSTRAINT tdeliveryaddressstatuses_PK PRIMARY KEY CLUSTERED (intDeliveryAddressStatusID) 
)
ENGINE = INNODB;

-- Brands
CREATE TABLE tbrands 
(
  intBrandID    				INTEGER      	NOT NULL AUTO_INCREMENT,
  strBrandTitle 				VARCHAR(50) 	NOT NULL,
  intBrandStatusID              INTEGER			NOT NULL,
  CONSTRAINT tbrands_PK PRIMARY KEY CLUSTERED (intBrandID)
)
ENGINE = InnoDB;

-- Brand Statuses
CREATE TABLE tbrandstatuses
(
	intBrandStatusID			INTEGER		    NOT NULL AUTO_INCREMENT,
	strBrandStatus	    	    VARCHAR(50)		NOT NULL,	
	CONSTRAINT tbrandstatuses_PK PRIMARY KEY CLUSTERED (intBrandStatusID) 
)
ENGINE = INNODB;

-- Categories
CREATE TABLE tcategories 
(
	intCategoryID    			INTEGER         NOT NULL AUTO_INCREMENT,
	strCategoryTitle            VARCHAR(50)     NOT NULL,
    intCategoryStatusID			INTEGER		    NOT NULL,    
	CONSTRAINT tcategories_PK PRIMARY KEY CLUSTERED (intCategoryID)
)
ENGINE = InnoDB;

-- Category Statuses
CREATE TABLE tcategorystatuses
(
	intCategoryStatusID			INTEGER		    NOT NULL AUTO_INCREMENT,
	strCategoryStatus	    	VARCHAR(50)		NOT NULL,	
	CONSTRAINT tcategorystatuses_PK PRIMARY KEY CLUSTERED (intCategoryStatusID) 
)
ENGINE = INNODB;

-- Products
CREATE TABLE tproducts 
(
  intProductID          		INTEGER        	NOT NULL,
  intCategoryID         		INTEGER        	NOT NULL,
  intBrandID            		INTEGER        	NOT NULL,
  strProductTitle       		VARCHAR(50)  	NOT NULL,
  decSellingPrice       		DECIMAL(10,2) 	NOT NULL,
  strProductDescription 		TEXT          	NOT NULL,
  strProductImage       		VARCHAR(100)  	NOT NULL,
  strProductKeywords    		VARCHAR(100)  	NOT NULL,
  dtmCreated                    DATETIME        NOT NULL,
  intProductStatusID			INTEGER			NOT NULL,
  CONSTRAINT tproducts_PK PRIMARY KEY CLUSTERED (intProductID)
)
ENGINE = InnoDB;

-- Product Statuses
CREATE TABLE tproductstatuses
(
	intProductStatusID		    INTEGER		    NOT NULL AUTO_INCREMENT,
	strProductStatus			VARCHAR(50)		NOT NULL,	
	CONSTRAINT tproductstatuses_PK PRIMARY KEY CLUSTERED (intProductStatusID)
)
ENGINE = INNODB;

-- Customer Carts
CREATE TABLE tcustomercarts 
(
  intCustomerID 				INTEGER       	NOT NULL,
  intProductID  				INTEGER       	NOT NULL,  
  intQuantity   				INTEGER       	NOT NULL,
  CONSTRAINT tcustomercarts_PK PRIMARY KEY CLUSTERED (intCustomerID, intProductID)  
)
ENGINE = InnoDB;

-- Customer Orders
CREATE TABLE tcustomerorders 
(
	intCustomerID 				INTEGER  		NOT NULL,
    intDeliveryIndex 		    INTEGER			NOT NULL,
	intOrderIndex 				INTEGER 		NOT NULL,
	dtmOrderDate  				DATETIME    	NOT NULL,    
	intCustomerOrderStatusID	INTEGER         NOT NULL,
	CONSTRAINT tcustomerorders_PK PRIMARY KEY CLUSTERED (intCustomerID, intDeliveryIndex, intOrderIndex)  
)
ENGINE = InnoDB;

-- Customer Order Statuses
CREATE TABLE tcustomerorderstatuses
(
	intCustomerOrderStatusID	INTEGER		    NOT NULL AUTO_INCREMENT,
	strCustomerOrderStatus		VARCHAR(50)		NOT NULL,	
	CONSTRAINT tcustomerorderstatuses_PK PRIMARY KEY CLUSTERED (intCustomerOrderStatusID)
)
ENGINE = INNODB;

-- Customer Order Items
CREATE TABLE tcustomerorderitems 
(
	intCustomerID 				INTEGER 		NOT NULL,
    intDeliveryIndex 		    INTEGER			NOT NULL,
	intOrderIndex 				INTEGER 		NOT NULL,
	intProductID  				INTEGER 		NOT NULL,
	intQuantity   				INTEGER 		NOT NULL,
	CONSTRAINT tcustomerorderitems_PK PRIMARY KEY CLUSTERED (intCustomerID, intDeliveryIndex, intOrderIndex, intProductID) 
)
ENGINE = InnoDB;

-- Product Price Histories
-- This table tracks product price updates through time
CREATE TABLE tproductpricehistories
(
	intProductID                INTEGER         NOT NULL,
	intChangeIndex              INTEGER         NOT NULL,
    intCategoryID         		INTEGER       	NOT NULL,
	intBrandID            		INTEGER       	NOT NULL,
	strProductTitle             VARCHAR(50)     NOT NULL,
    decSellingPrice             DECIMAL(10,2)   NOT NULL,
	strProductDescription		TEXT            NOT NULL,
    strProductImage       		VARCHAR(100)  	NOT NULL,
	strProductKeywords    		VARCHAR(100)  	NOT NULL,
	intProductStatusID			INTEGER			NOT NULL,
	dtmCreated                  DATETIME        NOT NULL,	
	dtmChanged                  DATETIME        NOT NULL,
	CONSTRAINT tproductpricehistories_PK PRIMARY KEY CLUSTERED (intProductID, intChangeIndex)
)
ENGINE = INNODB;

-- ---------------------------------------------------------------------------------
-- FOREIGN KEYS
-- ---------------------------------------------------------------------------------
ALTER TABLE tcustomers ADD CONSTRAINT tcustomers_tcountries_FK1
FOREIGN KEY (intCountryID) REFERENCES tcountries (intCountryID);

ALTER TABLE tcustomers ADD CONSTRAINT tcustomers_tcustomerstatuses_FK2
FOREIGN KEY (intCustomerStatusID) REFERENCES tcustomerstatuses (intCustomerStatusID);

ALTER TABLE tbrands ADD CONSTRAINT tbrands_tbrandstatuses_FK1
FOREIGN KEY (intBrandStatusID) REFERENCES tbrandstatuses (intBrandStatusID);

ALTER TABLE tcategories ADD CONSTRAINT tcategories_tcategorystatuses
FOREIGN KEY (intCategoryStatusID) REFERENCES tcategorystatuses (intCategoryStatusID);

ALTER TABLE tproducts ADD CONSTRAINT tproducts_tcategories_FK1
FOREIGN KEY (intCategoryID) REFERENCES tcategories (intCategoryID);

ALTER TABLE tproducts ADD CONSTRAINT tproducts_tbrands_FK2
FOREIGN KEY (intBrandID) REFERENCES tbrands (intBrandID);

ALTER TABLE tproducts ADD CONSTRAINT tproducts_tproductstatuses_FK3
FOREIGN KEY (intProductStatusID) REFERENCES tproductstatuses (intProductStatusID);

ALTER TABLE tcustomercarts ADD CONSTRAINT tcustomercarts_tproducts_FK1
FOREIGN KEY (intProductID) REFERENCES tproducts (intProductID);

ALTER TABLE tcustomercarts ADD CONSTRAINT tcustomercarts_tcustomers_FK2
FOREIGN KEY (intCustomerID) REFERENCES tcustomers (intCustomerID);

ALTER TABLE tcustomerdeliveryaddresses ADD CONSTRAINT tcustomerdeliveryaddresses_tcustomers_FK1
FOREIGN KEY (intcustomerid) REFERENCES tcustomers (intcustomerID);

ALTER TABLE tcustomerdeliveryaddresses ADD CONSTRAINT tcustomerdeliveryaddresses_tcountries_FK2
FOREIGN KEY (intcountryid) REFERENCES tcountries (intcountryid);

ALTER TABLE tcustomerdeliveryaddresses ADD CONSTRAINT tcustomerdeliveryaddresses_tdeliveryaddressstatuses_FK3
FOREIGN KEY (intDeliveryAddressStatusID) REFERENCES tdeliveryaddressstatuses (intDeliveryAddressStatusID);

ALTER TABLE tcustomerorders ADD CONSTRAINT tcustomerorders_tcustomerdeliveryaddresses_FK1
FOREIGN KEY (intCustomerID, intDeliveryIndex) REFERENCES tcustomerdeliveryaddresses (intCustomerID, intDeliveryIndex);

ALTER TABLE tcustomerorders ADD CONSTRAINT tcustomerorders_tcustomerorderstatuses_FK2
FOREIGN KEY (intCustomerOrderStatusID) REFERENCES tcustomerorderstatuses (intCustomerOrderStatusID);

ALTER TABLE tcustomerorderitems ADD CONSTRAINT tcustomerorderitems_tcustomerorders_FK1 
FOREIGN KEY (intCustomerID, intDeliveryIndex, intOrderIndex) REFERENCES tcustomerorders (intCustomerID, intDeliveryIndex, intOrderIndex);

ALTER TABLE tcustomerorderitems ADD CONSTRAINT tcustomerorderitems_tproducts_FK2 
FOREIGN KEY (intProductID) REFERENCES tproducts (intProductID);

ALTER TABLE tproductpricehistories ADD CONSTRAINT tproductpricehistories_tproducts_FK1
FOREIGN KEY(intProductID) REFERENCES tproducts (intProductID);


-- ---------------------------------------------------------------------------------
-- INDEXES
-- ---------------------------------------------------------------------------------


-- ---------------------------------------------------------------------------------
-- VIEWS
-- ---------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------
-- AllCustomerData
-- Get all data for each customer
-- ---------------------------------------------------------------------------------
CREATE VIEW VAllCustomerData
AS
SELECT
	tc.intCustomerID,            
    tc.strIPAddress,               
	tc.strCustomerName,
    tc.strAddress1,     		
	tc.strAddress2,                 
    tc.strCity,                    
    tc.strState,                    
    tc.intCountryID,
    tcc.strCountry,
    tc.strZipCode,                  
    tc.strUserName,                
    tc.strPassword,                
    tc.strEmailAddress,             
    tc.strPhoneNumber,             
	tc.intCustomerStatusID         
FROM 
	tcustomers as tc,
    tcountries as tcc
WHERE
	tc.intCountryID = tcc.intCountryID
ORDER BY
	intCustomerID;
    
-- ---------------------------------------------------------------------------------
-- All customer delivery addresses 
-- ---------------------------------------------------------------------------------
CREATE VIEW VAllCustomerDeliveryAddresses
AS
SELECT
	TD.intCustomerID,
    TCS.strCustomerName,
    TD.intDeliveryIndex,
    TD.strDeliveryName,
    TD.strAddress1,
    TD.strAddress2,
    TD.strCity,
    TD.strState,
    TD.intCountryID,
    TC.strCountry,
    TD.strZipCode,
    TD.intDeliveryAddressStatusID,
    TDS.strDeliveryAddressStatus
FROM
	tcustomers as TCS,
    tcustomerdeliveryaddresses as TD,
    tdeliveryaddressstatuses as TDS,
    tcountries as TC
WHERE
	TCS.intCustomerID = TD.intCustomerID 
AND TD.intDeliveryAddressStatusID = TDS.intDeliveryAddressStatusID
AND TD.intCountryID = TC.intCountryID

GROUP BY
	TD.intCustomerID,
    TD.intDeliveryIndex;

-- ---------------------------------------------------------------------------------
-- ActiveProducts
-- Get currently active products
-- ---------------------------------------------------------------------------------
CREATE VIEW VActiveProducts
AS
SELECT 
	TP.intProductID,
	TP.intCategoryID,
    TC.strCategoryTitle,
	TP.intBrandID,
    TB.strBrandTitle,
	TP.strProductTitle,
    TP.strProductDescription, 
	TP.decSellingPrice,			
	TP.strProductImage,
    TP.strProductKeywords 
FROM 
	tproducts AS TP
		-- Join needed for dtmChanged
		Left Outer Join tproductpricehistories AS TPH
		ON(TP.intProductID = TPH.intProductID)
        
        JOIN tcategories AS TC
        ON( TP.intCategoryID = TC.intCategoryID)
        
        JOIN tbrands AS TB
        ON( TP.intBrandID = TB.intBrandID)
WHERE
	TP.intProductStatusID = 1
AND TC.intCategoryStatusID = 1
AND TB.intBrandStatusID = 1
GROUP BY
	TP.intProductID,
	TP.strProductTitle,
	TP.decSellingPrice,
	TP.dtmCreated
ORDER BY
	TP.intProductID;
    
-- ---------------------------------------------------------------------------------
-- RandomActiveProducts
-- Get 6 randomly selected & currently active products
-- ---------------------------------------------------------------------------------
CREATE VIEW VRandomActiveProducts
AS
SELECT 
	TP.intProductID,
	TP.intCategoryID,
	TP.intBrandID,
	TP.strProductTitle,
	TP.decSellingPrice,			
	TP.strProductImage
FROM 
	tproducts AS TP
		-- Join needed for dtmChanged
		Left Outer Join tproductpricehistories AS TPH
		ON(TP.intProductID = TPH.intProductID)
        
        JOIN tcategories AS TC
        ON( TP.intCategoryID = TC.intCategoryID)
        
        JOIN tbrands AS TB
        ON( TP.intBrandID = TB.intBrandID)
WHERE
	TP.intProductStatusID = 1
AND TC.intCategoryStatusID = 1
AND TB.intBrandStatusID = 1

ORDER BY RAND() LIMIT 0,6;
	
-- ---------------------------------------------------------------------------------
-- CurrentProductPrices
-- Get the current selling prices for all items
-- ---------------------------------------------------------------------------------
CREATE VIEW VCurrentProductPrices
AS
SELECT 
	TP.intProductID,
	TP.strProductTitle,
	TP.strProductDescription,	
	TP.decSellingPrice,
	-- Default to dtmCreated if no change has been made
	IFNULL(MAX(TPH.dtmChanged), TP.dtmCreated) AS dtmLastChanged  
FROM 
	tproducts AS TP
		-- Join needed for dtmChanged
		Left Outer Join tproductpricehistories AS TPH
		ON(TP.intProductID = TPH.intProductID) 	
GROUP BY
	TP.intProductID,
	TP.strProductTitle,
	TP.strProductDescription,	
	TP.decSellingPrice,
	TP.dtmCreated;
    
-- ---------------------------------------------------------------------------------
-- IndividualCustomerCartSummaries
-- Get a summary of a customer's cart by product
-- ---------------------------------------------------------------------------------
CREATE VIEW VIndividualCustomerCartSummaries
AS
SELECT
	tc.intCustomerID,
    tc.strCustomerName,
    tp.intProductID,
    tp.strProductTitle,
    tp.strProductImage,
    tcc.intQuantity,    
    (SELECT
		cpp.decSellingPrice
	 FROM
		VCurrentProductPrices as cpp
	 WHERE
		tp.intProductID = cpp.intProductID
    ) as decProductPrice,
    tcc.intQuantity *
	  (SELECT
		 cpp.decSellingPrice
	   FROM
		 VCurrentProductPrices as cpp
	   WHERE
		 tp.intProductID = cpp.intProductID
	  ) as decTotalProductPrice 
FROM
	tcustomers as tc,
    tproducts as tp,
    tcustomercarts as tcc    
WHERE
	tc.intCustomerID = tcc.intCustomerID
AND tp.intProductID = tcc.intProductID;

-- ---------------------------------------------------------------------------------
-- CustomerCartTotalSummaries
-- Get total number of products and total price for a customer's cart
-- ---------------------------------------------------------------------------------
CREATE VIEW VCustomerCartTotalSummaries
AS
SELECT
	tc.intCustomerID,
    tc.strCustomerName,
	SUM(ccipt.intQuantity) as intTotalProducts,
    SUM(ccipt.decTotalProductPrice) as decTotalPrice
FROM
	TCustomers as tc,
    VIndividualCustomerCartSummaries as ccipt
WHERE
	tc.intCustomerID = ccipt.intCustomerID
    
GROUP BY tc.intCustomerID;
     
-- ---------------------------------------------------------------------------------
-- AllCustomerOrderSummaries
-- Get all customer information, order and product information as well as summaries for
-- the totals of all orders using the product price at the time of order.
-- Uses a derived table (named sub-query in the FROM clause) for greater efficiency
-- This view is critical in tracking customer orders through time and can be used
-- in creating a number of other important financial reports.
-- ---------------------------------------------------------------------------------
CREATE VIEW VAllCustomerOrderSummaries
AS
SELECT
	TC.intCustomerID,            	
	TC.strCustomerName,    
	IFNULL(dtCOPP.intOrderIndex, 0) AS intOrderIndex,        	
	IFNULL(dtCOPP.dtmOrderDate, ' ') AS dtmOrderDate,    
    IFNULL(dtCOPP.intDeliveryIndex, 0) AS intDeliveryIndex, 
    IFNULL(dtCOPP.strDeliveryName, ' ') AS strDeliveryName,
    IFNULL(dtCOPP.intProductID, 0) AS intProductID,
    IFNULL(dtCOPP.strProductTitle, ' ') AS strProductTitle,
    IFNULL(dtCOPP.intBrandID, 0) AS intBrandID,
    IFNULL(dtCOPP.strBrandTitle, ' ') AS strBrandTitle,    
    IFNULL(dtCOPP.intCategoryID, 0) AS intCategoryID,
    IFNULL(dtCOPP.strCategoryTitle, ' ') AS strCategoryTitle,        
	IFNULL(dtCOPP.intQuantity, ' ') AS intQuantity,
	IFNULL(dtCOPP.decSellingPrice, 0) AS decSellingPrice,
	IFNULL(dtCOPP.intQuantity * dtCOPP.decSellingPrice, 0) AS decTotalSellingPrice 
FROM
	TCustomers AS TC  -- Table A
		INNER JOIN
		(
			-- Create a derived table so that logic is executed only once
			SELECT
				TCO.intCustomerID,
                TCDA.intDeliveryIndex,
            	TCDA.strDeliveryName,
				TCO.intOrderIndex,
				TCO.dtmOrderDate,
				TCOI.intProductID, 
            	TP.strProductTitle,
                TP.intBrandID,
                TB.strBrandTitle,
                TP.intCategoryID,
                TC.strCategoryTitle,
				TCOI.intQuantity,            	
				(
					-- Has there been a price change for the item in the history table?
					-- If yes:
						-- Does this order date fall within the change history dates?
						-- Yes
							-- 1: Use the selling price from the history table
						-- No
							-- 2: Use the selling price from the products table 
					-- No
						-- 3: Use the selling price from the products table 
					IFNULL
					(
						-- 1: Get the selling price from the history table
						(SELECT 
							TPH.decSellingPrice 
						 FROM 
							tproductpricehistories AS TPH 
						 WHERE 
							  TCOI.intProductID = TPH.intProductID 
						 AND  TCO.dtmOrderDate BETWEEN TPH.dtmCreated AND TPH.dtmChanged
						),
						-- 2, 3: Get the current selling price from the products table
						(SELECT 
							  TP.decSellingPrice 
						 FROM 
							  tproducts AS TP 
						 WHERE 
							  TCOI.intProductID = TP.intProductID 
						)
					)
				) AS decSellingPrice				
			FROM
				tcustomerorders     AS TCO,		
				tcustomerorderitems AS TCOI,	
                tproducts AS TP,			
                tbrands AS TB,                
                tcategories AS TC,             
                tcustomerdeliveryaddresses as TCDA		
			WHERE
				TCO.intCustomerID = TCOI.intCustomerID	
			AND TCO.intDeliveryIndex = TCOI.intDeliveryIndex            
			AND TCO.intOrderIndex = TCOI.intOrderIndex            
            AND TCOI.intCustomerID = TCDA.intCustomerID
            AND TCOI.intDeliveryIndex = TCDA.intDeliveryIndex					
            AND TCOI.intProductID = TP.intProductID	    
            AND TP.intBrandID = TB.intBrandID			
            AND TP.intCategoryID = TC.intCategoryID		
			AND TCO.intCustomerOrderStatusID = 1 		-- Only for purchased orders (not cancelled or returned) 
		) AS dtCOPP	-- Table B(CustomerOrderProductPrice) 
        
		-- Join A to B (cross-query join)			 
		ON(TC.intCustomerID = dtCOPP.intCustomerID)
GROUP BY
	TC.intCustomerID,   
    dtCOPP.intOrderIndex,
    dtCOPP.intDeliveryIndex,
    dtCOPP.intProductID;
    
-- ---------------------------------------------------------------------------------
-- IndividualOrderSummaries
-- Get a summary of all individual orders for all customers 
-- ---------------------------------------------------------------------------------
CREATE VIEW VIndividualOrderSummaries
AS
SELECT
	COT.intCustomerID,
	COT.strCustomerName, 
	COT.intOrderIndex AS intOrderIndex,
	COT.dtmOrderDate AS dtmOrderDate,
    COT.intDeliveryIndex AS intDeliveryIndex,
    COT.strDeliveryName AS strDeliveryName,	
	SUM(COT.intQuantity) AS intTotalItems,
	CAST(SUM(COT.intQuantity * COT.decSellingPrice) AS DECIMAL(10,2)) AS decOrderTotalPrice				
FROM
	VAllCustomerOrderSummaries AS COT
GROUP BY
	COT.intCustomerID,
	COT.strCustomerName, 
	COT.intOrderIndex,
	COT.dtmOrderDate;
    

-- ---------------------------------------------------------------------------------
-- FUNCTIONS
-- ---------------------------------------------------------------------------------


-- ---------------------------------------------------------------------------------
-- STORED PROCEDURES
-- ---------------------------------------------------------------------------------
DELIMITER //

-- Add Customer
CREATE PROCEDURE uspAddCustomer
(
	strIPAddress			VARCHAR(50),
	strCustomerName         VARCHAR(50),
	strAddress1             VARCHAR(50),
    strAddress2             VARCHAR(50),    
	strCity                 VARCHAR(50),  
	strState                VARCHAR(50),
    intCountryID			INTEGER,
	strZipCode              VARCHAR(50),
    strUserName				VARCHAR(50),
    strPassword				VARCHAR(50),
    strEmailAddress			VARCHAR(50),
	strPhoneNumber          VARCHAR(50)    	
)
BEGIN
	DECLARE intNewCustomerID  INTEGER;   
	START TRANSACTION;	
		-- Get the highest ID and Lock the Table until the end of the transaction
		SELECT MAX(intCustomerID) + 1 INTO intNewCustomerID FROM TCustomers FOR UPDATE;
		-- Is the ID null (i.e. the Table is empty)?
		IF intNewCustomerID IS NULL THEN
			-- Default ID to 1
			SELECT 1 INTO intNewCustomerID;
		END IF;
		-- Create new record
		INSERT INTO TCustomers(intCustomerID, strIPAddress, strCustomerName, strAddress1, strAddress2, strCity, strState, intCountryID, 
							   strZipCode, strUserName, strPassword, strEmailAddress, strPhoneNumber, intCustomerStatusID)
		VALUES(intNewCustomerID, strIPAddress, strCustomerName, strAddress1, strAddress2, strCity, strState, intCountryID, strZipCode, 
               strUserName, strPassword, strEmailAddress, strPhoneNumber, 1);
	COMMIT;
	-- Return values to calling program
	SELECT intNewCustomerID AS intCustomerID, strCustomerName as strCustomerName, strEmailAddress as strEmailAddress;
END
//

-- Edit customer
CREATE PROCEDURE uspEditCustomer
(
	intCustomerID			INTEGER,
    strIPAddress			VARCHAR(50),
	strCustomerName         VARCHAR(50),
	strAddress1             VARCHAR(50),
    strAddress2             VARCHAR(50),    
	strCity                 VARCHAR(50),  
	strState                VARCHAR(50),
    intCountryID			INTEGER,
	strZipCode              VARCHAR(50),
    strUserName				VARCHAR(50),
    strPassword				VARCHAR(50),
    strEmailAddress			VARCHAR(50),
	strPhoneNumber          VARCHAR(50)    	
)
BEGIN
	START TRANSACTION;    
		-- Update the record
		UPDATE
			TCustomers AS tc
		SET
			tc.strIPAddress	   = strIPAddress,
			tc.strIPAddress    = strIPAddress,
			tc.strAddress1     = strAddress1,
			tc.strAddress2     = strAddress2,    
			tc.strCity         = strCity,  
			tc.strState        = strState,
			tc.intCountryID	   = intCountryID,
			tc.strZipCode      =  strZipCode,
			tc.strUserName     = strUserName,
			tc.strPassword	   = strPassword,
			tc.strEmailAddress = strEmailAddress,
			tc.strPhoneNumber  = strPhoneNumber            	
		WHERE
			TC.intCustomerID = intCustomerID;
	COMMIT;
END
//

-- Add Product
CREATE PROCEDURE uspAddProduct
(
	intCategoryID			INTEGER,
	intBrandID         		INTEGER,
	strProductTitle         VARCHAR(50),
    decSellingPrice         DECIMAL(10,2),    
	strProductDescription   TEXT,  
	strProductImage         VARCHAR(100),
    strProductKeywords		VARCHAR(100),
	dtmCreated              DATETIME    
)
BEGIN
	DECLARE intNewProductID  INTEGER;   
	START TRANSACTION;	
		-- Get the highest ID and Lock the Table until the end of the transaction
		SELECT MAX(intProductID) + 1 INTO intNewProductID FROM TProducts FOR UPDATE;
		-- Is the ID null (i.e. the Table is empty)?
		IF intNewProductID IS NULL THEN
			-- Default ID to 1
			SELECT 1 INTO intNewProductID;
		END IF;
		-- Create new record
		INSERT INTO TProducts(intProductID, intCategoryID, intBrandID, strProductTitle, decSellingPrice, strProductDescription, 
							  strProductImage, strProductKeywords, dtmCreated, intProductStatusID)
		VALUES(intNewProductID, intCategoryID, intBrandID, strProductTitle, decSellingPrice, strProductDescription, 
			   strProductImage, strProductKeywords, dtmCreated, 1);
	COMMIT;
	-- Return primary key to calling program
	SELECT intNewProductID AS intProductID;
END
//

-- Edit product
CREATE PROCEDURE uspEditProduct
(
	intProductID			INTEGER,
    decSellingPrice			DECIMAL(10,2),
	strProductDescription   TEXT,
	dtmCreated              DATETIME     	
)
BEGIN
	START TRANSACTION;    
		-- Update the record
		UPDATE
			TProducts  AS tp
		SET
			tp.decSellingPrice = decSellingPrice,
			tp.strProductDescription = strProductDescription, 				  
			tp.dtmCreated = dtmCreated			         	
		WHERE
			tp.intProductID = intProductID;
	COMMIT;
END
//

-- Add to customer card
CREATE PROCEDURE uspAddToCustomerCart
(
	intCustomerID 			INTEGER,
    intProductID			INTEGER
)
BEGIN
	START TRANSACTION;
		INSERT INTO tcustomercarts(intCustomerID, intProductID, intQuantity)
        SELECT
			intCustomerID, 
            intProductID, 
            1
		WHERE NOT EXISTS
		(SELECT 
			1
		 FROM 
			tcustomercarts as tcc
		 WHERE
			 tcc.intCustomerID = intCustomerID
		 AND tcc.intProductID  = intProductID
		);
	COMMIT;
END
//

-- Add Delivery Address
CREATE PROCEDURE uspAddDeliveryAddress
(
	 intCustomerID			INTEGER,
     strDeliveryName		VARCHAR(50),
	 strAddress1            VARCHAR(50),
     strAddress2            VARCHAR(50),
     strCity				VARCHAR(50),     
	 strState               VARCHAR(50),     
	 intCountryID           INTEGER,
	 strZipCode             VARCHAR(50)	
)
BEGIN
	DECLARE intNewDeliveryIndex	  INTEGER;
	START TRANSACTION;	
		-- Get the highest ID and Lock the Table until the end of the transaction
		SELECT MAX( intDeliveryIndex ) + 1 INTO intNewDeliveryIndex FROM TCustomerDeliveryAddresses as cda
		WHERE cda.intCustomerID = intCustomerID FOR UPDATE;
		-- Is the ID null (i.e. the Table is empty)?
		IF intNewDeliveryIndex IS NULL THEN
			-- Default ID to 1
			SELECT 1 INTO intNewDeliveryIndex;
		END IF;
		-- Create new record
		INSERT INTO TCustomerDeliveryAddresses(intCustomerID, intDeliveryIndex, strDeliveryName, strAddress1, 
					strAddress2, strCity, strState, intCountryID, strZipCode, intDeliveryAddressStatusID)
		VALUES(intCustomerID, intNewDeliveryIndex, strDeliveryName, strAddress1, strAddress2, strCity, strState, 
			   intCountryID, strZipcode, 2);
	COMMIT;
	-- Return primary key to calling program
	SELECT intCustomerID AS intCustomerID, intNewDeliveryIndex AS intDeliveryIndex;
END
//

-- Add Customer Order
CREATE PROCEDURE uspAddCustomerOrder
(
	intCustomerID			INTEGER,
	intDeliveryIndex        INTEGER
)
BEGIN
	DECLARE intNewOrderIndex  INTEGER;   
	START TRANSACTION;	
		-- Get the highest index and Lock the Table until the end of the transaction
		SELECT 
			MAX(intOrderIndex) + 1 INTO intNewOrderIndex 
        FROM 
			tcustomerorders AS tco 
        WHERE 
			tco.intCustomerID = intCustomerID FOR UPDATE;
		-- Is the ID null (i.e. the Table is empty)?
		IF intNewOrderIndex IS NULL THEN
			-- Default ID to 1
			SELECT 1 INTO intNewOrderIndex;
		END IF;
		-- Create new record
		INSERT INTO TCustomerOrders(intCustomerID, intDeliveryIndex, intOrderIndex, dtmOrderDate, intCustomerOrderStatusID)  							  
		VALUES(intCustomerID, intDeliveryIndex, intNewOrderIndex, CURDATE(), 1);
	COMMIT;
	-- Return primary key to calling program
	SELECT intCustomerID AS intCustomerID, intDeliveryIndex AS intDeliveryIndex, intNewOrderIndex AS intOrderIndex;
END
//

-- Add Customer Order Items
CREATE PROCEDURE uspAddCustomerOrderItems
(
	intCustomerID			INTEGER,
	intDeliveryIndex        INTEGER,
    intOrderIndex        	INTEGER,
    intProductID			INTEGER,
    intQuantity				INTEGER
)
BEGIN
	START TRANSACTION;	
		-- Create new record
		INSERT INTO TCustomerOrderItems(intCustomerID, intDeliveryIndex, intOrderIndex, intProductID, intQuantity)  							  
		VALUES(intCustomerID, intDeliveryIndex, intOrderIndex, intProductID, intQuantity);
	COMMIT;
	-- Return primary key to calling program
	SELECT intCustomerID AS intCustomerID, intDeliveryIndex AS intDeliveryIndex, intOrderIndex AS intOrderIndex, intProductID AS intProductID;
END
//


-- ---------------------------------------------------------------------------------
-- INSERTS
-- ---------------------------------------------------------------------------------
DELIMITER ;
-- Administrators
INSERT INTO tadministrators (intAdminID, strAdminName, strUserName, strPassword, strEmailAddress) 
VALUES
(1, 'Jason Smart', 'jsmart', 'smart10', 'jsmart@tbw.com'),
(2, 'Sheila Pharoah', 'spharoah', 'pharoah', 'spharoah@tbw.com');
	  
-- Countries
INSERT INTO tcountries (intCountryID, strCountry) 
VALUES
(1, 'Andora'),
(2, 'Austria'),
(3, 'Belgium'),
(4, 'Denmark'),
(5, 'Finland'),
(6, 'France'),
(7, 'Germany'),
(8, 'Ireland'),
(9, 'Italy'),
(10, 'Luxembourg'),
(11, 'Monaco'),
(12, 'Netherlands'),
(13, 'Norway'),
(14, 'Portugal'),
(15, 'Spain'),
(16, 'Sweden'),
(17, 'Switzerland'),
(18, 'Croatia'),
(19, 'Czech Republic'),
(20, 'Hungary'),
(21, 'Poland'),
(22, 'Slovakia'),
(23, 'Slovenia'),
(24, 'England'),
(25, 'Scotland'),
(26, 'Northern Ireland'),
(27, 'Wales'),
(28, 'Australia'),
(29, 'New Zealand'),
(30, 'Israel'),
(31, 'Canada'),
(32, 'United States'),
(33, 'Mexico'),
(34, 'Taiwan'),
(35, 'Japan'),
(36, 'India'),
(37, 'Philippines');

-- CustomerStatuses
INSERT INTO tcustomerstatuses (intCustomerStatusID, strCustomerStatus)
VALUES(1, 'Active'),
	  (2, 'Inactive');

-- Customers
INSERT INTO tcustomers (intCustomerID, strIPAddress, strCustomerName, strAddress1, strAddress2, strCity, strState, intCountryID, strZipCode, 
						strUserName, strPassword, strEmailAddress, strPhoneNumber, intCustomerStatusID)
VALUES
(1, '::1', 'Hyam Manley', '3400 Northgate Blvd', 'Unit #5', 'Cheviot', 'Ohio', 32, '45222 ', 'hmanley', 'iammanley', 'hmanley@gmail.com', '', 1),
(2, '::1', 'Sally Neuman', '333 W Main St', '', 'Dent', 'Ohio', 32, '45333 ', 'sneuman', 'sally082', 'sneuman@gmail.com', '', 1),
(3, '::1', 'Lilly Smithson', '488 Pine Valley Bluff', 'Unit #6', 'Indian Hills', 'Ohio', 32, '45789 ', 'smithson', 'smithson', 'lsmithson@gmail.com', '', 1),
(4, '::1', 'Mary Smith', '731 Northwind Ave', 'Unit #6', 'South Fairmont', 'Ohio', 32, '45244 ', 'msmith', 'smithers', 'msmith@yahoo.com', '(513)244-2888', 1),
(5, '::1', 'Michael Crowley', '2 Main Street E', 'Apt #2', 'Cincinnati', 'Ohio', 32, '452312 ', 'mcrowley', 'master2', 'mcrowley@cs.com', '(513)888-9878', 1),
(6, '::1', 'Morton Silver', '398 Oak St', '', 'Indian Hills', 'Ohio', 32, '46331 ', 'msilver', 'silversmith', 'msilver@aol.com', '', 1),
(7, '::1', 'Barbara Culverson', '981 Pine Bluff Ave', 'Apt 2A', 'Hamilton', 'Ohio', 32, '46333 ', 'bculverson', 'joyous', 'bculverson@cbell.com', '', 1),
(8, '::1', 'Tyrus J Wheatley', '1224 Northbend Road', '', 'Cheviot', 'Ohio', 32, '45387 ', 'tjwheatley', 'chopper22', 'tjwheatley@gmail.com', '(513)288-4500', 1),
(9, '::1', 'Antonia Maples', '888 Hollow Wood Road', '', 'East Price Hill', 'Ohio', 32, '45121 ', 'amaples', 'vermont', 'amaples@gmail.com', '(513)666-9999', 1),
(10, '::1', 'Joyce Cruze', '675 Ivy Hill Road', '', 'Delhi', 'Ohio', 32, '45242 ', 'jcruze', 'cruzin', 'jcruze@gmail.com', '(513)888-1212', 1),
(11, '::1', 'Hanna Richards', '912 Juniper Ave', 'Apt #1', 'Covedale', 'Ohio', 32, '46555 ', 'hrichards', 'richards', 'hrichards@cs.com', '', 1),
(12, '::1', 'DeAndre Russell', '451 Elm Street', 'Apt 12', 'Cincinnati', 'Ohio', 32, '45232 ', 'drussell', 'hoops12', 'drussel@bok.net', '(888)455-8769', 1),
(13, '::1', 'Marcus Matheson', '434 Hopple St', '', 'Camp Washington', 'Ohio', 32, '48765 ', 'mmatheson', 'markymark', 'math@nky.org', '(513)256-4766', 1),
(14, '::1', 'Rachael Ferguson', '56 Westside Lane', '', 'Addyston', 'Ohio', 32, '44655 ', 'rferguson', 'fergy77', 'rferguson@yahoo.com', '', 1),
(15, '::1', 'Maria Motley', '777 Elm Street', 'Unit 3b', 'Norwood', 'Ohio', 32, '42331 ', 'mmotley', 'marmot08', 'mmothley@ gmail.com', '', 1),
(16, '::1', 'Kyle Holbart', '356 N 29th St', '', 'Bridgetown', 'Ohio', 32, '47888 ', 'kholbart', 'clipper89', 'kholbart@gky.com', '', 1),
(17, '::1', 'Donna Hightower', '163 East River Drive', '', 'Covington', 'Kentucky', 32, '49999 ', 'dhightower', 'nicely', 'dhightower@gmail.com', '(888)677-2132', 1),
(18, '::1', 'Philip Staples', '88 Willow Circle', 'Apt #4', 'Granview', 'Ohio', 32, '48677 ', 'pstaples', 'golden', 'pstaples@aol.com', '', 1),
(19, '::1', 'Carole Waverly', '2351 E Union Street', '', 'Finneytwon', 'Ohio', 32, '45444 ', 'cwaverly', 'wavyone', 'cwaverly@gmail.com', '', 1),
(20, '::1', 'Peter Bogdanovich', '485 Spruce Street', '', 'Miamitown', 'Ohio', 32, '46888 ', 'pboggy', 'peterman', 'pbogdanovich@cs.com', '', 1),
(21, '::1', 'Holly J Bligh', '922 Sycamore Lane ', 'Unit #8', 'Covedale', 'Ohio', 32, '46555 ', 'hjbligh', 'hollyj', 'hrichards@cs.com', '', 1),
(22, '::1', 'George Allen Sappington', '12 Cherry Tree Terr', '', 'Norwood', 'Ohio', 32, '47212 ', 'gasappington', 'sappy2', 'gsappington@gmail.com', '(513)566-6755', 1),
(23, '::1', 'Miriam Lloyd', '677 Avery Street', '', 'Dent', 'Ohio', 32, '45333 ', 'mlloyd', 'lloyd1977', 'mlloyd@gmail.com', '', 1),
(24, '::1', 'Sue-Ellen Masters', '812 Farmer Street', 'Apt #1', 'Dent', 'Ohio', 32, '45333 ', 'semasters', 'masters88', 'smasters@gmail.com', '', 1),
(25, '::1', 'Barkley A Wallace', '744 Windley Lane', 'Unit #6', 'South Fairmont', 'Ohio', 32, '45244 ', 'bawallace', 'wallyworld', 'bwallace@yahoo.com', '5132442888', 1),
(26, '::1', 'James T Bollings', '212 Washington Ave', '', 'St Bernard', 'Ohio', 32, '48777 ', 'jbollongs', 'bollings54', 'jbollings@cbell.com', '', 1),
(27, '::1', 'Howard Harkings', '107 Deer Creek Crossing', 'Apt #7', 'Cleves', 'Ohio', 32, '45767 ', 'hharkings', 'harkings', 'hharkings@gmail.com', '', 1),
(28, '::1', 'Clara Lamont', '71 Andrews Ave', '', 'Bridgetown', 'Ohio', 32, '47888 ', 'clamont', 'baller55', 'clamont@cstate.com', '(513)447-7609', 1),
(29, '::1', 'Dakota Miller', '3951 Delhi Ave', 'Apt #2', 'Cincinnati', 'OH', 32, '45234 ', 'dfmiller', 'pancho54', 'dfhellerjr@gmail.com', '', 1);

--  Statuses
INSERT INTO tdeliveryaddressstatuses (intDeliveryAddressStatusID, strDeliveryAddressStatus)
VALUES(1, 'Primary'),
	  (2, 'Secondary');
      
-- Customer Delivery Addresses
INSERT INTO tcustomerdeliveryaddresses (intCustomerID, intDeliveryIndex, strDeliveryName, strAddress1, strAddress2, strCity, strState, 
										intCountryID, strZipCode, intDeliveryAddressStatusID)
VALUES
(1, 1, 'Hyam Manley', '3400 Northgate Blvd', 'Unit #5', 'Cheviot', 'Ohio', 32, '45222', 1),
(1, 2, 'DeAndre Russell', '451 Elm Street', 'Apt 12', 'Cincinnati', 'Ohio', 32, '45232', 2),
(2, 1, 'Sally Neuman', '333 W Main St', '', 'Dent', 'Ohio', 32, '45333', 1),
(3, 1, 'Lilly Smithson', '488 Pine Valley Bluff', 'Unit #6', 'Indian Hills', 'Ohio', 32, '45789', 1),
(4, 1, 'Mary Smith', '731 Northwind Ave', 'Unit #6', 'South Fairmont', 'Ohio', 32, '45244', 1),
(5, 1, 'Michael Crowley', '2 Main Street E', 'Apt #2', 'Cincinnati', 'Ohio', 32, '452312', 1),
(6, 1, 'Morton Silver', '398 Oak St', '', 'Indian Hills', 'Ohio', 32, '46331',  1),
(7, 1, 'Barbara Culverson', '981 Pine Bluff Ave', 'Apt 2A', 'Hamilton', 'Ohio', 32, '46333', 1),
(8, 1, 'Tyrus J Wheatley', '1224 Northbend Road', '', 'Cheviot', 'Ohio', 32, '45387', 1),
(9, 1, 'Antonia Maples', '888 Hollow Wood Road', '', 'East Price Hill', 'Ohio', 32, '45121', 1),
(10, 1, 'Joyce Cruze', '675 Ivy Hill Road', '', 'Delhi', 'Ohio', 32, '45242', 1),
(11, 1, 'Hanna Richards', '912 Juniper Ave', 'Apt #1', 'Covedale', 'Ohio', 32, '46555', 1),
(12, 1, 'DeAndre Russell', '451 Elm Street', 'Apt 12', 'Cincinnati', 'Ohio', 32, '45232', 1),
(13, 1, 'Marcus Matheson', '434 Hopple St', '', 'Camp Washington', 'Ohio', 32, '48765', 1),
(14, 1, 'Rachael Ferguson', '56 Westside Lane', '', 'Addyston', 'Ohio', 32, '44655', 1),
(15, 1, 'Maria Motley', '777 Elm Street', 'Unit 3b', 'Norwood', 'Ohio', 32, '42331', 1),
(16, 1, 'Kyle Holbart', '356 N 29th St', '', 'Bridgetown', 'Ohio', 32, '47888', 1),
(17, 1, 'Donna Hightower', '163 East River Drive', '', 'Covington', 'Kentucky', 32, '49999', 1),
(18, 1, 'Philip Staples', '88 Willow Circle', 'Apt #4', 'Granview', 'Ohio', 32, '48677', 1),
(19, 1, 'Carole Waverly', '2351 E Union Street', '', 'Finneytwon', 'Ohio', 32, '45444', 1),
(20, 1, 'Peter Bogdanovich', '485 Spruce Street', '', 'Miamitown', 'Ohio', 32, '46888', 1),
(21, 1, 'Holly J Bligh', '922 Sycamore Lane', 'Unit #8', 'Covedale', 'Ohio', 32, '46555', 1),
(22, 1, 'George Allen Sappington', '12 Cherry Tree Terr', '', 'Norwood', 'Ohio', 32, '47212', 1),
(23, 1, 'Miriam Lloyd', '677 Avery Street', '', 'Dent', 'Ohio', 32, '45333', 1),
(24, 1, 'Sue-Ellen Masters', '812 Farmer Street', 'Apt #1', 'Dent', 'Ohio', 32, '45333', 1),
(25, 1, 'Barkley A Wallace', '744 Windley Lane', 'Unit #6', 'South Fairmont', 'Ohio', 32, '45244', 1),
(26, 1, 'James T Bollings', '212 Washington Ave', '', 'St Bernard', 'Ohio', 32, '48777', 1),
(27, 1, 'Howard Harkings', '107 Deer Creek Crossing', 'Apt #7', 'Cleves', 'Ohio', 32, '45767', 1),
(28, 1, 'Clara Lamont', '71 Andrews Ave', '', 'Bridgetown', 'Ohio', 32, '47888', 1),
(29, 1, 'Dakota Miller', '3951 Delhi Ave', 'Apt #2', 'Cincinnati', 'OH', 32, '45234 ', 1);

-- Brand Statuses
INSERT INTO tbrandstatuses (intBrandStatusID, strBrandStatus)
VALUES(1, 'Active'),
	  (2, 'Inactive');

-- Brands
INSERT INTO tbrands (intBrandID, strBrandTitle, intBrandStatusID) 
VALUES
(1, 'HP', 1),
(2, 'Asus', 1),
(3, 'Dell', 1),
(4, 'Lenovo', 1),
(5, 'LG', 1),
(6, 'Apple', 1),
(7, 'Nikon', 1),
(8, 'Sony', 1),
(9, 'Canon', 1),
(10, 'Microsoft', 1),
(11, 'Google', 1),
(12, 'Samsung', 1);

-- Category Statuses
INSERT INTO tcategorystatuses (intCategoryStatusID, strCategoryStatus)
VALUES(1, 'Active'),
	  (2, 'Inactive');

-- Categories
INSERT INTO tcategories (intCategoryID, strCategoryTitle, intCategoryStatusID) 
VALUES
(1, 'LapTops', 1),
(2, 'DeskTops', 1),
(3, 'Phones', 1),
(4, 'Cameras', 1),
(5, 'iPads', 1),
(6, 'Tablets', 1),
(7, 'Printers', 1);

-- Product Statuses
INSERT INTO tproductstatuses (intProductStatusID, strProductStatus)
VALUES(1, 'Active'),
	  (2, 'Inactive');

-- Products
INSERT INTO tproducts (intProductID, intCategoryID, intBrandID, strProductTitle, decSellingPrice, strProductDescription, strProductImage, 
                       strProductKeywords, dtmCreated, intProductStatusID) 
VALUES
(1, 1, 2, 'Asus UX330', '719.00', '<p>ASUS ZenBook UX330UA-AH54</p>\r\n<p>13.3-inch LCD Ultra-Slim Laptop</p>\r\n<p>Core i5 Processor, 8GB DDR3, 256GB SSD, Windows 10</p>\r\n<p>Harman Kardon Audio, Backlit keyboard, Fingerprint Reader</p>', 'AsusUX330.jpg', 'Asus laptops', '2019-01-01 00:00:00', 1),
(2, 6, 1, 'HP Omni 10', '367.85', '<p>The HP Omni 10 features a 10.1-inch HD screen</p>\r\n<p>Estimated 8.5 hours of battery life</p>\r\n<p>Intel Atom&nbsp;BayTrail processor (1.46 GHz) with 32 GB of storage and 2 GB of RAM</p>\r\n<p>Micro SD card port that allows for expandable&nbsp;storage option.</p>\r\n<p>Front and rear facing&nbsp;cameras</p>\r\n<p>Micro-USB and HDMI ports.</p>', 'hptablet.jpg', 'HP tablets','2019-01-01 00:00:00', 1),
(3, 4, 5, 'LG 360 CAM', '199.99','<p>LG 360 CAM</p>\r\n<p>360&deg; and 180&deg; image capture with one click.</p>\r\n<p>Dual 13 MP wide-angle cameras</p>\r\n<p>2K&nbsp;video recording,</p>\r\n<p>16 MP spherical image support</p>\r\n<p>5.1-channel surround-sound&nbsp;recording</p>\r\n<p>Supported on Android 5.0 (L OS) or later, and iOS 8 or later</p>', 'lgcamera.jpg', 'LG cameras', '2019-01-01 00:00:00', 1),
(4, 2, 4, 'Lenovo Y900 RE ', '1699.99', '<p>Razor Edition</p>\r\n<p>6th Gen Intel&reg; Core&trade; i7 processor</p>\r\n<p>Windows 10 Home</p>\r\n<p>16 GB DDR4 memory</p>\r\n<p>2 TB HDD with 256 GB SSD storage</p>\r\n<p>NVIDIA&reg; GeForce&reg; GTX 1080 8 GB graphics&nbsp;</p>\r\n<p>Gigabit LAN RJ45,&nbsp;</p>\r\n<p>HDMI in-out combo&nbsp;</p>', 'lenovo2.jpg', 'Lenovo desktops', '2019-01-01 00:00:00', 1),
(5, 5, 6, 'Apple iPad Pro+ ', '949.99', '<p>12.9-inch redesigned Retina display&nbsp;</p>\r\n<p>256GB A10X Fusion chip delivers more power than most PC laptops</p>\r\n<p>iOS&mdash;the most advanced mobile operating&nbsp;system</p>\r\n<p>Wi-Fi&nbsp;</p>\r\n<p>Apple&nbsp;Pencil</p>\r\n<p>Smart Keyboard</p>\r\n<p>&nbsp;</p>', 'ipad.jpg', 'Apple ipads', '2019-01-01 00:00:00', 1),
(6, 1, 3, 'Dell Latitude 7380', '1199.99', '<p>Impressive power in a premium design.</p>\r\n<p>13-inch laptop in a compact 12-inch frame.&nbsp;</p>\r\n<p>Full HD display &amp; Intel&reg; Core i processors&nbsp;</p>\r\n<p>Premium power on the go.</p>', 'dell7380.jpg', 'Dell laptops', '2019-01-01 00:00:00', 1),
(7, 3, 5, 'LG V30', '819.99', '<p>64 GB; front/rear cameras</p>\r\n<p>6.0\" QHD+ OLED FullVision Display&nbsp;</p>\r\n<p>Cine EffectF/1.6&nbsp;</p>\r\n<p>Glass Camera LensPoint&nbsp;</p>\r\n<p>ZoomWide Angle Lenses</p>\r\n<p>Hi-Fi Video Recording</p>', 'lgphone.jpg', 'LG phones', '2019-01-01 00:00:00', 1),
(8, 4, 7, 'Nikon D5300 ', '489.00', '<p>18-55mm F/3.5-5.6G AF-P VR Lens</p>\r\n<p>24.2 Megapixels Sensor</p>\r\n<p>Built-in Wireless and GPS</p>\r\n<p>Full HD 1080P at 30/25/24p</p>\r\n<p>100-12800 ISO Expandable to 25600</p>\r\n<p>5 FPS Continuous Shooting</p>\r\n<p>3.2\" Vari-angle TFT-LCD</p>', 'nikoncamera.jpg', 'Nikon cameras', '2019-01-01 00:00:00', 1),
(9, 1, 3, 'Dell Inspiron 17 5000', '1399.99', '<p>17-inch laptop designed for high performance whether it be</p>\r\n<p>work, gaming or home entertainment. Stunning graphics.&nbsp;</p>\r\n<p>8th Generation Intel&reg; Core&trade; i7-8550U Processor</p>\r\n<p>Windows 10 Home 64-bit English</p>\r\n<p>16GB Single Channel DDR4 2400MHz</p>\r\n<p>Dual drives with 256GB Solid State Drive+ 2TB 5400 rpm Hard Drive</p>\r\n<p>4GB GDDR5 AMD graphics card&nbsp;</p>', 'dell17.jpg', 'Dell laptops', '2019-01-01 00:00:00', 1),
(10, 2, 6, 'Apple iMac ', '1249.00', '<p>21.5 inch display</p>\r\n<p>3.0GHz quad-core Intel Core i5 processor with Turbo Boost up to 3.5GHz</p>\r\n<p>8GB 2400MHz memory</p>\r\n<p>1TB hard drive</p>\r\n<p>Radeon Pro 555 with 2GB video memory</p>\r\n<p>Retina 4K 4096-by-2304 P3 display</p>', 'appleimac.jpg', 'Apple desktops', '2019-01-01 00:00:00', 1),
(11, 4, 7, 'Nikon D750 DSLR  ', '1499.00', '<p>AF-S NIKKOR 24-120mm</p>\r\n<p>f/4G ED VR Lens&nbsp;</p>\r\n<p>Full frame 24.3 megapixel CMOS image sensor&nbsp;&nbsp;</p>\r\n<p>EXPEED 4 image processor.</p>\r\n<p>Full HD 60/50/30/25/24p video.</p>\r\n<p>Tilting Vari-angle LCD display.</p>\r\n<p>Built-in Wi-Fi connectivity and compatibility with the WT-5a&nbsp;</p>\r\n<p>UT-1 Communication Unit.</p>', 'nikond7502.jpg', 'Nikon cameras', '2019-01-01 00:00:00', 1),
(12, 5, 2, 'Asus ZenPad 3.8.0', '287.89', '<p>Processor: Qualcomm Snapdragon 650 MSM8956</p>\r\n<p>Graphics adapter: Qualcomm Adreno 510</p>\r\n<p>Memory: 4096 MB, LPDDR3</p>\r\n<p>Display: 7.9 inch 4:3&nbsp;</p>\r\n<p>2048x1536 pixel 324 PPI&nbsp;</p>\r\n<p>Capacitive Touchscreen</p>\r\n<p>Storage: 32 GB&nbsp;</p>\r\n<p>eMMC Flash&nbsp;</p>', 'asuszenpad.jpg', 'Asus ipads', '2019-01-01 00:00:00', 1),
(13, 3, 6, 'Apple iPhone 8 Plus', '799.99', '<p>5.5-inch widescreen LCD Multi-Touch display with IPS technology</p>\r\n<p>1920-by-1080-pixel resolution at 401 ppi</p>\r\n<p>A11 Bionic chip with 64-bit architecture, neural engine and embedded M11 motion coprocessor</p>\r\n<p>Memory: 64GB</p>\r\n<p>Operating System: iOS&reg; 11</p>\r\n<p>Voice to send messages, set reminders, and more.</p>\r\n<p>Video calling</p>\r\n<p>Wi-Fi connectivity</p>\r\n<p>Music Player: iTunes&reg;</p>\r\n<p>Sensors: Touch ID fingerprint, barometer, three-axis gyro, accelerometer, proximity, ambient light</p>', 'iphone8.png', 'Apple phones', '2019-01-01 00:00:00', 1),
(14, 2, 1, 'HP EliteOne 800 ', '1296.78', '<p><strong>G3 23.8\" All-in-One PC&nbsp;</strong></p>\r\n<p>Windows 10 Pro</p>\r\n<p>7th Generation Intel&reg; Core&trade; i7-7700 Processor</p>\r\n<p>16 GB (2x8GB) DDR4-2400 SODIMM Memory</p>\r\n<p>512 GB FIPS 140-2 Self-Encrypting (SED) 2.5\" 2nd SSD</p>\r\n<p>2 MP Webcam with Dual Microphone Array</p>\r\n<p>23.8\" diagonal FHD display</p>\r\n<p>Intel HD Graphics 630</p>', 'hpdesktop.png', 'HP desktops', '2019-01-01 00:00:00', 1),
(15, 3, 1, 'HP Elite x3', '823.99', '<p>Windows 10</p>\r\n<p>RAM: 4 GB</p>\r\n<p>3-in-1</p>\r\n<p>4G LTE</p>\r\n<p>64 GB</p>\r\n<p>16 MP (8 MP front camera)</p>\r\n<p>5.96\"</p>\r\n<p>2560 x 1440 pixels (494 ppi)</p>\r\n<p>desk dock</p>', 'hpmobile.jpg', 'HP phones', '2019-01-01 00:00:00', 1),
(16, 6, 4, 'Lenovo Yoga Book', '399.99', '<p>Processor: Intel&reg; Atom&trade; x5-Z8550 Processor (2.40GHz 2MB)</p>\r\n<p>Operating System: ANDROID 6.0</p>\r\n<p>Hard Drive: 64GB</p>\r\n<p>Warranty: One year</p>\r\n<p>Memory: 4.0GB LPDDR3</p>\r\n<p>Bluetooth: Bluetooth Version 4.0</p>\r\n<p>Battery: 2 Cell Li-Polymer</p>', 'lenovotablet.jpg', 'Lenovo tablets', '2019-01-01 00:00:00', 1),
(17, 4, 7, 'Nikon D7200', '1039.59', '<p>24.2 MP DX-format CMOS image sensor</p>\r\n<p>No Optical Low-Pass Filter (OLPF)</p>\r\n<p>51 point autofocus system</p>\r\n<p>6 frames per second (fps) shooting capacity</p>\r\n<p>EXPEED 4 image processing;&nbsp;</p>\r\n<p>ISO Sensitivity: ISO 100 - 25,600.Lens&nbsp;</p>\r\n<p>Built in Wi-Fi&nbsp;&nbsp;</p>\r\n<p>Near Field Communication (NFC) for instant sharing</p>\r\n<p>&nbsp;</p>', 'nikon7200.jpg', 'Nikon cameras', '2019-01-01 00:00:00', 1),
(18, 5, 3, 'Dell XPS 13', '1299.99', '<p>7th Generation Intel&reg; Core&trade; i7-7Y75 Processor</p>\r\n<p>Windows 10 Home 64-bit English</p>\r\n<p>8GB LPDDR3 1866MHz</p>\r\n<p>512GB PCIe Solid State Drive</p>\r\n<p>13.3-in. touch display</p>\r\n<p>Intel HD graphics</p>', 'dellipad.jpg', 'Dell ipads', '2019-01-01 00:00:00', 1),
(19, 5, 4, 'Lenovo ZA2J  ', '184.99', '<p>Android 7.1&nbsp;</p>\r\n<p>Screen Size:10.1 in</p>\r\n<p>Operating System:Android 7.1 (Nougat)</p>\r\n<p>Processor Type:APQ8017</p>\r\n<p>Processor Speed:1.4 GHz</p>\r\n<p>RAM:2 GB</p>', 'lenovoipad.jpg', 'Lenovo ipads', '2019-01-01 00:00:00', 1),
(20, 2, 2, 'Asus VivoPC ', '999.99', '<p>Windows 10 operating system</p>\r\n<p>7th Gen Intel&reg; Core&trade; i7-7700&nbsp;&nbsp;</p>\r\n<p>8GB system memory for advanced multitasking</p>\r\n<p>DVD/CD drive</p>\r\n<p>1TB hard drive&nbsp;</p>\r\n<p>NVIDIA GeForce GTX 1050 graphics</p>\r\n<p>4 USB 3.0 ports&nbsp;</p>\r\n<p>Multi-display capability</p>', 'asusdesktop.jpg', 'Asus desktops', '2019-01-01 00:00:00', 1),
(21, 1, 5, 'LG Gram 15Z970 ', '1359.99', '<p>Intel Core i7-7500U Processor 2.7GHz</p>\r\n<p>Microsoft Windows 10 Home 64-bit</p>\r\n<p>16GB RAM</p>\r\n<p>512GB Solid State Drive</p>\r\n<p>Intel HD Graphics 620</p>\r\n<p>Micro-SD Card Reader</p>\r\n<p>Dual Band 802.11ac Wireless</p>\r\n<p>Bluetooth</p>\r\n<p>15.6\" Full HD IPS Display</p>\r\n<p>&nbsp;</p>', 'lglaptop.jpg', 'LG laptops', '2019-01-01 00:00:00', 1),
(22, 4, 7, 'Nikon D7100 DSLR ', '1296.99', '<p>18-140mm VR lens</p>\r\n<p>Battery charger</p>\r\n<p>Rechargeable lithium-ion battery (EN-EL15)</p>\r\n<p>Strap, body cap, lens cap</p>\r\n<p>NikonView NX2 software CD-ROM</p>\r\n<p>USB and A/V cables</p>', 'nikonvideocam.jpg', 'Nikon cameras', '2019-01-01 00:00:00', 1),
(23, 4, 8, 'Sony PXW Camcorder ', '1925.00', '<p>Sony PXW-X70 Professional XDCAM Compact Camcorder</p>\r\n<p>1\" Exmor R CMOS Sensor; HD Recording</p>\r\n<p>Built-In SD Media Card Slots; Viewfinder &amp; Flip-Out LCD Screen</p>\r\n<p>XAVC, AVCHD, DV File Based Recording; Slow &amp; Quick Motion</p>\r\n<p>3G-SDI &amp; HDMI Output; Wireless LAN Control</p>\r\n<p>Planned Upgrade To UHD 4K</p>', 'sonycamcorder.jpg', 'Sony cameras', '2019-01-01 00:00:00', 1),
(24, 3, 8, 'Sony Xperia X', '235.00', '<p>Display: IPS LCD capacitive touchscreen, 16M colors</p>\r\n<p>Size: 5.0 inches</p>\r\n<p>Resolution:1080 x 1920 pixels, 16:9 ratio</p> \r\n<p>OS: Android 6.0.1 (Marshmallow), 7.1.1 (Nougat)</p>\r\n<p>Chipset: Qualcomm MSM8956 Snapdragon 650</p>\r\n<p>CPU: Hexa-core (4x1.4 GHz Cortex-A53 & 2x1.8 GHz Cortex-A72)</p>\r\n<p>Memory: 64 GB, 3 GB RAM - F5122</p>\r\n<p>Camera: 23 MP (f/2.0, 24mm, 1/2.3\") with phase detection autofocus, LED flash</p>\r\nVideo: 1080p@60fps', 'sonyperformance.jpg', 'Sony phones', '2019-01-01 00:00:00', 1),
(25, 1, 8, 'Sony VAIO S', '1099.99', '<p>Windows 10 Pro</p>\r\n<p>Intel Core i7-6500U (4MB Cache, up to 3.10GHz)</p>\r\n<p>8 GB DDR3 Memory</p>\r\n<p>13.3\" Full HD (1920x1080) Display</p>\r\n<p>256 GB Flash Memory Solid State</p>', 'sonyvaio.jpg', 'Sony, laptops', '2019-01-01 00:00:00', 1),
(26, 4, 8, 'Sony DSCRX10/B ', '798.00', '<p>20.2 MP Digital Still Camera with 3-Inch LCD Screen</p>\r\n<p>Optical Sensor Resolution: 20.2 megapixels</p>\r\n<p>Image Stabilization</p>\r\n<p>Optical Zoom: 8.3</p>\r\n<p>Form Factor: LR-like (bridge)</p>\r\n<p>Display Size: 3 inches</p>', 'sonyDSCW830.jpg', 'Sony cameras', '2019-01-01 00:00:00', 1),
(27, 1, 4, 'Lenovo 320S  ', '568.99', '<p>Windows 10</p>\r\n<p>LED-Backlit 14.0 inch display</p>\r\n<p>Operating System: Windows 10</p>\r\n<p>Intel i5-7200U Processor&nbsp;</p>\r\n<p>8.0 GB DDR4 RAM</p>\r\n<p>Hard Disk Size: 256.0 GB SSD</p>\r\n<p>HDMI 802.11ac&nbsp;</p>\r\n<p>Dolby Audio</p>\r\n<p>Webcam&nbsp;</p>\r\n<p>Bluetooth&nbsp;</p>', 'lenovolaptop.jpg', 'Lenovo laptops', '2019-01-01 00:00:00', 1),
(28, 1, 1, 'HP dv7-6c80us   ', '669.59', '<p>Windows 10 Home Premium 64-bit</p>\r\n<p>Intel Core i7-2670QM Processor 2.20GHz with Turbo Boost Technology</p>\r\n<p>8GB SDRAM RAM</p>\r\n<p>750GB 5400RPM Hard Drive</p>\r\n<p>17.3-Inch Screen&nbsp;</p>\r\n<p>Intel HD Graphics 3000 1696 MB</p>', 'hplaptop.jpg', 'HP laptops', '2019-01-01 00:00:00', 1),
(29, 6, 2, 'Asus Flip C213SA ', '369.99', '<p>Convertible Chromebook</p>\r\n<p>Chrome OS</p>\r\n<p>4 GB RAM</p>\r\n<p>32 GB SSD - (eMMC)</p>\r\n<p>Intel Celeron N3350 / 1.1 GHz</p>\r\n<p>Intel HD Graphics 500</p>\r\n<p>Display: 11.6\"</p>\r\n<p>Keyboard, touchpad, stylus</p>', 'asusnotebook.jpg', 'Asus tablets', '2019-01-01 00:00:00', 1),
(30, 2, 3, 'Dell XPS 8920 ', '789.99', '<p>3.6 GHz Intel Core i7-7700 Quad-Core</p>\r\n<p>16GB 2400 MHz DDR4 RAM</p>\r\n<p>AMD Radeon RX 460 Graphics Card (2GB)</p>\r\n<p>1TB 7200 rpm SATA III 3.5\" Hard Drive</p>\r\n<p>SuperMulti DVD Burner | SDXC Card Reader</p>\r\n<p>1 x Gigabit Ethernet Port</p>\r\n<p>802.11ac Wi-Fi &amp; Bluetooth 4.0</p>\r\n<p>USB 3.1 | USB 3.0 | USB 2.0</p>\r\n<p>USB Wired Keyboard &amp; Mouse Included</p>\r\n<p>Windows 10 Home (64-Bit)</p>', 'dell_xps8920.jpg', 'Dell desktops', '2019-01-01 00:00:00', 1),
(31, 1, 4, 'Lenovo MX 720', '1129.99', '<p>Windows 10 Home</p>\r\n<p>2.7 GHz Intel Core i7</p>\r\n<p>8 GB DDR4 RAM</p>\r\n<p>256 GB M.2 PCIe SSD</p>\r\n<p>12-inch detachable display with full high-resolution</p>\r\n<p>Integrated graphics</p>\r\n<p>Thunderbolt 3 USB-C port</p>', 'lenovomiix.jpg', 'Lenovo laptops', '2019-01-01 00:00:00', 1),
(32, 6, 5, 'LG G-Pad X ', '258.99', '<p>4G LTE: Band 2- 4- 5- 17 and 29; LTE Roaming Bands 1- 3 and 7</p>\r\n<p>10.1\" Full HD IPS Display&nbsp;</p>\r\n<p>8MP with Auto Focus</p>\r\n<p>Bluetooth V4.1 Powerful and portable</p>\r\n<p>Reader Mode</p>\r\n<p>Quad-core processor</p>\r\n<p>32GB internal memory</p>\r\n<p>Dual Window multitasking</p>', 'lgipad.jpg', 'LG tablets', '2019-01-01 00:00:00', 1),
(33, 1, 6, 'Apple MacBook Pro', '1799.00', '<p>3.1GHz dual-core 7th-generation Intel Core i5 processor</p>\r\n<p>Turbo Boost up to 3.5GHz</p>\r\n<p>8GB 2133MHz LPDDR3 memory</p>\r\n<p>256GB SSD storage1</p>\r\n<p>Intel Iris Plus Graphics 650</p>\r\n<p>Four Thunderbolt 3 ports</p>\r\n<p>Touch Bar and Touch ID</p>', 'applemacbook.jpg', 'Apple laptops', '2019-01-01 00:00:00', 1),
(34, 3, 4, 'Lenovo Z2 Force', '720.00', '<p>Processor: Qualcomm&reg; Snapdragon&trade; 830 Octa-Core up to 2.5 Ghz</p>\r\n<p>OS: Nougat 7.0.1</p>\r\n<p>Display: 5.5\" Quad HD POLED</p>\r\n<p>Camera: 2 MP with Enhanced Depth of Field / 5 MP with Wide-Angle Lens</p>\r\n<p>RAM: 4GB</p>\r\n<p>Storage: 64GB expandable to 2TB</p>\r\n<p>Headphone jack</p>\r\n<p>Speaker: single (front-facing)</p>', 'motozforce.png', 'Lenovo phones', '2019-01-01 00:00:00', 1),
(35, 3, 6, 'Apple - iPhone X ', '1099.99', '<p>5.8-inch Super Retina HD display with HDR and True Tone&sup1;</p>\r\n<p>All-glass and stainless steel design, water, and dust resistant</p>\r\n<p>12MP dual cameras with dual IOS, Portrait mode, Portrait lighting&nbsp;&nbsp;</p>\r\n<p>4K video up to 60 fps</p>\r\n<p>7MP TrueDepth front camera with Portrait mode, Portrait Lighting</p>\r\n<p>Facial ID for secure authentication and Apple Pay</p>\r\n<p>A11 Bionic, most powerful and smart chip in a smartphone</p>\r\n<p>Wireless charging&nbsp;</p>', 'appleiphonex.jpg', 'Apple phones', '2019-01-01 00:00:00', 1),
(36, 5, 6, 'Apple iPad 8', '795.95', '<p>Display Size:10.5 inches</p>\r\n<p>Operating System:ios 10</p>\r\n<p>Flash Memory Installed Size:256.0</p>\r\n<p>Native Resolution:1668 x 2224</p>\r\n<p>Display Technology:LED-Lit</p>\r\n<p>&nbsp;</p>', 'appleipadpro.jpg', 'Apple ipads', '2019-01-01 00:00:00', 1),
(37, 5, 6, 'Apple iPad Pro', '734.95', '<p>Display Size:10.5 inches</p>\r\n<p>Operating System:ios 10</p>\r\n<p>Flash Memory Installed Size:256.0</p>\r\n<p>Native Resolution:1668 x 2224</p>\r\n<p>Display Technology:LED-Lit</p>\r\n<p>&nbsp;</p>', 'appleipadpro.jpg', 'Apple ipads', '2019-01-01 00:00:00', 1),
(38, 2, 8, 'Sony Vaio Tap 21 ', '999.99', '<p>Windows 10 Home Edition</p>\r\n<p>Intel Core i5 4th Generation</p>\r\n<p>Memory: 960GB SDD 1.60GHz</p>\r\n<p>RAM: 4GB</p>\r\n<p>Touchscreen 20inch display</p>\r\n<p>Graphics: Integrated/On-Board Graphics</p>', 'sonyvaio2.jpg', 'Sony desktops', '2019-01-01 00:00:00', 1),
(39, 2, 2, 'Asus G11CD', '1899.99', '<p>Windows 10 Pro 64-bit Edition&nbsp;</p>\r\n<p>Intel Core i7-6700 6th Gen&nbsp;&nbsp;</p>\r\n<p>Memory: 2 TB SSD 3.4 GHz</p>\r\n<p>RAM: 32GB DDR4 SDRAM</p>\r\n<p>NVIDIA GeForce GTX 980</p>\r\n<p>4 GB GDDR5 SDRAM</p>\r\n<p>7.1 channel surround</p>\r\n<p>High Definition Audio</p>\r\n<p>&nbsp;</p>', 'asusG11CD.jpg', 'Asus desktops', '2019-01-01 00:00:00', 1),
(40, 4, 8, 'Sony Alpha a9 ', '4792.00', '<p>Sony Alpha a9 24.2MP Full-Frame Mirrorless Digital Camera&nbsp;&nbsp;</p>\r\n<p>NP-FZ100 Rechargeable Lithium-Ion Battery&nbsp;</p>\r\n<p>BC-QZ1 Battery Charger&nbsp;</p>\r\n<p>AC-UUD12 AC Adapter&nbsp;&nbsp;</p>\r\n<p>Eyepiece Cup&nbsp;</p>\r\n<p>Micro USB Cable&nbsp;</p>\r\n<p>Lens Compatibility: Sony E-mount lenses</p>\r\n<p>Lens Mount: E-mount</p>\r\n<p>Aspect Ratio: 3:2</p>\r\n<p>Number of Pixels (Effective): Approx. 24.2 megapixels</p>\r\n<p>Bundle Includes:</p>\r\n<p>- Sony GP-X1EM Grip Extension&nbsp;</p>\r\n<p>- Sony BC-QZ1 Battery Charger&nbsp;</p>\r\n<p>- Sony NP-FZ100 Lithium-Ion Rechargeable Battery</p>', 'sonya9_.jpg', 'Sony cameras', '2019-01-01 00:00:00', 1),
(41, 4, 7, 'Nikon D60', '184.99', '<p>DSLR Camera&nbsp;</p>\r\n<p>18-55mm f/3.5-5.6G Auto Focus-S Nikkor Zoom Lens</p>\r\n<p>Capture images to SD/SDHC memory cards&nbsp;</p>\r\n<p>Continuous shooting at 3 fps&nbsp;</p>\r\n<p>Active Dust Reduction System with Airflow Control</p>\r\n<p>Extraordinary 10.2-megapixel DX-format Nikon picture quality</p>\r\n<p>2.5-inch LCD screen&nbsp;</p>\r\n<p>Horizontal and vertical orientation are detected automatically</p>', 'nikond60_.jpg', 'Nikon cameras', '2019-01-01 00:00:00', 1),
(42, 4, 7, 'Nikon COOLPIX P500', '539.99', '<p>36x Wide-Angle Optical Zoom-NIKKOR ED Glass Lens.</p>\r\n<p>12.1-megapixel CMOS sensor for high-speed operation&nbsp;</p>\r\n<p>Exceptional low-light performance.</p>\r\n<p>Capture 5 shots in one second at full resolution</p>\r\n<p>Full HD (1080p) Movie with Stereo sound and HDMI Output</p>\r\n<p>5-way VR Image Stabilization System</p>\r\n<p>&nbsp;</p>', 'nikoncoolplex_.jpg', 'Nikon cameras', '2019-01-01 00:00:00', 1),
(43, 2, 5, 'LG 4K UHD Monitor', '697.00', '<p>27\" LED-Lit Monitor with USB Type-C</p>\r\n<p>4K UHD (3840 x 2160) IPS Monitor</p>\r\n<p>sRGB over 99%</p>\r\n<p>Color Calibration Pro</p>\r\n<p>USB 3.0 Quick Charge</p>\r\n<p>USB Type-C. Brightness (cd/m2) : 350 cd/m2.&nbsp;</p>\r\n<p>Contrast Ratio : 5M:1</p>\r\n<p>On-Screen Control with Screen Split 2.0</p>', 'lguhd.jpg', 'LG desktops', '2019-01-01 00:00:00', 1),
(44, 6, 5, 'LG E10 ', '159.99', '<p>Display Size:10.1 inches</p>\r\n<p>Operating System: Android 5.0</p>\r\n<p>RAM: 1GB</p>\r\n<p>Flash Memory Installed Size:16</p>\r\n<p>Native Resolution:1280 x 800</p>\r\n<p>Display Technology:LCD</p>', 'lge10_.jpg', 'LG tablets', '2019-01-01 00:00:00', 1),
(45, 1, 1, 'HP Pavilion Power ', '864.65', '<p>Display Size: 15.6 inches</p>\r\n<p>Windows 10 Home</p>\r\n<p>RAM: 12GB DDR4 SDRAM</p>\r\n<p>Hard Disk Size: 1TB</p>\r\n<p>Processor: 7th Generation Intel(R) Core(TM) i7-7700HQ Processor&nbsp;</p>\r\n<p>Quad-Core 2.8GHz up to 3.8GHz&nbsp;</p>\r\n<p>Graphics: AMD Radeon RX 550&nbsp;&nbsp;</p>\r\n<p>2GB GDDR5 dedicated graphics and 8134 MB total graphics memory&nbsp;</p>', 'hppavilion.jpg', 'HP laptops', '2019-01-01 00:00:00', 1),
(46, 2, 1, 'HP Pavilion 510', '649.00', '<p>Windows 10</p>\r\n<p>Intel Quad Core i7-6700T&nbsp;</p>\r\n<p>2.8 Ghz turbo to 3.6Ghz, 8M cache&nbsp;</p>\r\n<p>8GB DDR4 RAM&nbsp;</p>\r\n<p>1TB 7200RPM HDD&nbsp;</p>\r\n<p>Intel HD graphics 530&nbsp;</p>\r\n<p>Monitor not included&nbsp;</p>', 'hp510.jpg', 'HP desktops', '2019-01-01 00:00:00', 1),
(47, 1, 2, 'Asus ROG Strix ', '1449.99', '<p>Windows 10 Home</p>\r\n<p>RAM: 12GB DDR4&nbsp;</p>\r\n<p>Storage: 1TB 7200 rpm HDD</p>\r\n<p>Screen: 17.3 inches</p>\r\n<p>Intel i7-7700HQ 2.8 GHz</p>\r\n<p>GeForce GTX 1070 8GB&nbsp;</p>', 'asusrog_.jpg', 'Asus laptops', '2019-01-01 00:00:00', 1),
(48, 2, 3, 'Dell OptiPlex 7050', '1285.58', '<p>Intel Core i7 7700 3.6 GHz&nbsp;</p>\r\n<p>16 GB RAM&nbsp;</p>\r\n<p>1 TB HDD&nbsp;</p>\r\n<p>Intel HD Graphics 630&nbsp;&nbsp;</p>\r\n<p>Windows 10 Pro</p>', 'dell7050.jpg', 'Dell desktops', '2019-01-01 00:00:00', 1),
(49, 2, 3, 'Dell Monitor U2415', '214.95', '<p>UltraSharp&nbsp;</p>\r\n<p>24 inch&nbsp;</p>\r\n<p>1920 x 1200&nbsp;</p>\r\n<p>LED Backlit&nbsp;</p>\r\n<p>HDMI&nbsp;</p>\r\n<p>DisplayPort&nbsp;</p>\r\n<p>76 Hz&nbsp;</p>\r\n<p>IPS Panel</p>', 'dellmonitor.jpg', 'Dell desktops', '2019-01-01 00:00:00', 1),
(50, 4, 9, 'Canon EOS 6D', '1299.00', '<p>20.2MP full frame CMOS sensor</p>\r\n<p>4.5 frames per second continuous shooting</p>\r\n<p>1080p HD video recording with manual controls</p>\r\n<p>11-point AF system</p>\r\n<p>3 inch LCD with 1,040,000 dots.Lens&nbsp;</p>\r\n<p>Mount: Canon EF mount</p>', 'canoneos.jpg', 'Canon cameras', '2019-01-01 00:00:00', 1),
(51, 7, 9, 'Canon PIXMA Pro9000 ', '489.00', '<p>Model: Mark II Inkjet Photo Printer&nbsp;&nbsp;</p>\r\n<p>Maximum 4800x2400 dpi&nbsp;</p>\r\n<p>FINE printhead technology</p>\r\n<p>Photo Lab quality 11-inchx14-inch color photo&nbsp;</p>\r\n<p>Support for fine art paper up to 13\"x19\"&nbsp;&nbsp;</p>\r\n<p>Two separate paper paths&nbsp;&nbsp;</p>\r\n<p>Front feeder for heavy-weight paper types</p>\r\n<p>Easy-PhotoPrint Pro plug-in software</p>\r\n<p>Ambient Light Correction feature&nbsp;</p>', 'canonSX425_.jpg', 'Canon printers', '2019-01-01 00:00:00', 1),
(52, 7, 9, 'Canon MF634C Laser', '339.99', '<p>All-in-One, Wireless, Duplex Laser Printer</p>\r\n<p>3-year limited warranty&nbsp;</p>\r\n<p>Print at speeds of up to 19 pages per minute</p>\r\n<p>Scans both sides of your document in a single pass</p>\r\n<p>Connects to mobile devices without a router&nbsp;&nbsp;</p>\r\n<p>Wi-Fi Direct connection</p>\r\n<p>Hi-capacity toner options&nbsp;</p>', 'canonlaser.jpg', 'Canon printers', '2019-01-01 00:00:00', 1),
(53, 4, 9, 'Canon PowerShot ', '144.00', '<p>8x (28&ndash;224mm) Optical Zoom</p>\r\n<p>20.0 Megapixel sensor combined with the DIGIC 4+ Image Processor</p>\r\n<p>Shoot 720p HD video with a dedicated movie button</p>\r\n<p>Smart AUTO selects the proper settings for the camera&nbsp;</p>\r\n<p>ECO Mode helps reduce power consumption for longer battery life</p>\r\n<p>&nbsp;</p>', 'canonSL1500_.jpg', 'Canon cameras', '2019-01-01 00:00:00', 1),
(54, 7, 3, 'Dell C1760NW Printer', '129.99', '<p>Color Laser Printer&nbsp;</p>\r\n<p>Plain Paper&nbsp;</p>\r\n<p>Network Ready&nbsp;</p>\r\n<p>WPS and USB 2.0 High speed connectivity&nbsp;</p>\r\n<p>Dell Clear View LED technology</p>\r\n<p>Innovative LED printing technology</p>\r\n<p>Windows 10 compatible</p>\r\n<p>Print up to 15 ppm in black; 12 ppm in color&nbsp;</p>\r\n<p>150-sheet input tray, 10-sheet bypass tray and 100-sheet output bin.</p>\r\n<p>Max Resolution: (B&amp;W) 600 dpi and (Color) 600 dpi</p>', 'delllaser.jpg', 'Dell printers', '2019-01-01 00:00:00', 1),
(55, 7, 1, 'HP LaserJet ', '649.99', '<p>Model: Enterprise M607n&nbsp; &nbsp;</p>\r\n<p>Up to 1200 x 1200 dpi</p>\r\n<p>Up to 55 ppm black&nbsp;</p>\r\n<p>1 Gigabit Ethernet 10/100/1000T network</p>\r\n<p>17\"W x 18.3\"D x 15\"H</p>\r\n<p>100-sheet multipurpose feeder&nbsp;</p>\r\n<p>550-sheet input feeder</p>\r\n<p>1.2 GHz</p>\r\n<p>ENERGY STAR certified</p>\r\n<p>2.7\" LCD with keypad</p>', 'hplaser.jpg', 'HP printers', '2019-01-01 00:00:00', 1),
(56, 7, 5, 'LG Pocket Photo 3 ', '129.99', '<p>Newest 2015 Model of Pocket Photo</p>\r\n<p>Portable Photo Printer</p>\r\n<p>iOS, Android, Windows Phone 8</p>\r\n<p>Resolution - 313 X 600 dpi</p>\r\n<p>High Quality Photos Printer with Zink photo paper</p>', 'lgprinter.jpg', 'LG printers', '2019-01-01 00:00:00', 1),
(57, 7, 8, 'Sony UPX898MD', '630.59', '<p>Graphic laser printer</p>\r\n<p>High print quality</p>\r\n<p>A6 prints in less than two seconds</p>\r\n<p>Hybrid operation with analog and digital inputs</p>\r\n<p>Store pictures on a USB flash drive</p>\r\n<p>Compact space saving design</p>\r\n<p>Enhanced operability</p>', 'sonylaser.jpg', 'Sony printers', '2019-01-01 00:00:00', 1),
(58, 4, 9, 'Canon Vixia HF ', '1099.00', '<p>Camcorder - 1080p</p>\r\n<p>Dynamic Image Stabilizer</p>\r\n<p>Dolby Digital AC-3 (2 channel) recording</p>\r\n<p>Resolution: 1920 x 1080</p>\r\n<p>20 x zoom lens - 3.67 - 73.4 mm - f/1.8-2.8</p>\r\n<p>Viewfinder: LCD&nbsp;</p>\r\n<p>Memory Card: Dual SD card</p>', 'canoncamcorder.jpg', 'Canon cameras', '2019-01-01 00:00:00', 1),
(59, 7, 9, 'Canon ImageRunner 1435i', '559.00', '<p>Digital MultiFunction Laser Printer</p>\r\n<p>Copy, Print, Scan, Send, Fax</p>\r\n<p>Letter, Legal</p>\r\n<p>Scan Resolution: Up to 600 x 600 dpi</p>\r\n<p>Paper Sizes: 5-1/2&rdquo; x 5&rdquo; to 8-1/2&rdquo; x 14&rdquo;</p>\r\n<p>Memory: RAM 512MB&nbsp;</p>\r\n<p>Standard USB Port</p>\r\n<p>Operation Panel: 5-line LCD</p>', 'canonmulti.jpg', 'Canon printers', '2019-01-01 00:00:00', 1),
(60, 2, 1, 'HP Pavillion 570', '609.99', '<p>23 inch full HD 1920 x 1080 flat-panel screen</p>\r\n<p>VGA and HDMI input ports (1 each)</p>\r\n<p>Intel HD graphics card</p>\r\n<p>8 GB DDR4-2400 SDRAM RAM</p>\r\n<p>1 TB SATA 2700rpm Hard Drive</p>\r\n<p>Windows 10 Home Premium</p>\r\n<p>USB keyboard and optical mouse&nbsp;</p>\r\n<p>&nbsp;</p>', 'hppavillion2018.jpg', 'HP desktops', '2019-01-01 00:00:00', 1),
(61, 1, 10, 'Microsoft Surface 2', '819.95', '<p>8 GB&nbsp; DDR SDRAM</p>\r\n<p>128 GB Flash Memory SSD</p>\r\n<p>Intel i5 processor</p>\r\n<p>13.5 inch screen size</p>\r\n<p>Intel HD graphics card</p>\r\n<p>Window Home 10 Premium</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>', 'micro_surface.jpg', 'Microsoft laptops', '2019-08-07 13:33:46', 1),
(62, 1, 10, 'Microsoft Surface', '550.00', '<p>13.5 inch screen</p>\r\n<p>4 GB&nbsp; DDR3 SDRAM</p>\r\n<p>128 GB Flash Memory SSD</p>\r\n<p>Intel i3 processor</p>\r\n<p>Intel HD graphics</p>\r\n<p>Touch screen</p>\r\n<p>Windows Home 10</p>', 'micro_surface2.jpg', 'Microsoft laptops', '2019-08-07 16:44:00', 1),
(63, 6, 10, 'Microsoft Surface Go', '575.95', '<p>10 inch HD display</p>\r\n<p>8 GB DDR SDRAM</p>\r\n<p>128 GB Flash Memory SSD</p>\r\n<p>Intel Pentium Gold processor</p>\r\n<p>Intel HD graphics</p>\r\n<p>Windows 10 Home</p>', 'microsoft_go.jpg', 'Microsoft tablets', '2019-08-08 10:21:49', 1),
(64, 6, 10, 'Microsoft Surface Pro 6', '772.49', '<p>12.3 inch screen</p>\r\n<p>8 GB DDR SDRAM</p>\r\n<p>128 GB Flash Memory SSD</p>\r\n<p>Intel i5 processor</p>\r\n<p>Intel HD graphics</p>\r\n<p>Windows 10 Home</p>', 'micro_surface-pro__.jpg', 'Microsoft tablets', '2019-08-08 10:51:01', 1),
(65, 6, 10, 'Microsoft Surface Pro 5', '625.49', '<p>12.3 inch HD screen</p>\r\n<p>4GB DDR SDRAM</p>\r\n<p>128 GB Flash Memory SSD</p>\r\n<p>Intel Core M3 Processor</p>\r\n<p>Intel HD graphics</p>\r\n<p>Windows 10 Home</p>', 'micro_surface-pro.jpg', 'Microsoft tablets', '2019-08-08 11:26:48', 1),
(66, 2, 10, 'Microsoft Surface Studio', '2321.39', '<p>28 inch PixelSense Monitor with touchscreen</p>\r\n<p>13.5 million pixels of color and clarity</p>\r\n<p>32 GB&nbsp;DDR SDRAM</p>\r\n<p>2 TB&nbsp;Flash Memory SSD</p>\r\n<p>Intel Core i7 processor</p>\r\n<p>Distinct NVIDIA 4GB GeForce graphics card</p>\r\n<p>Windows Home Premium</p>', 'micro_.jpg', 'Microsoft desktops', '2019-08-08 12:51:39', 1),
(67, 6, 11, 'Google Pixel Slate', '964.99', '<p>12.3 inch 6 million pixel HD screen</p>\r\n<p>8 GB DDR SDRAM</p>\r\n<p>128 GB Flash Memory SSD</p>\r\n<p>Intel Core i5 processor</p>\r\n<p>Keyboard &amp; Pen/Stylus included</p>', 'google1_.jpg', 'Google tablets', '2019-08-09 11:32:46', 1),
(68, 3, 11, 'Google Pixel 3a', '385.49', '<p>5.6 inch OLED display</p>\r\n<p>128 GB storage</p>\r\n<p>8 GB RAM</p>\r\n<p>Android 9 PIE</p>\r\n<p>Dual 8-megapixel camera</p>\r\n<p>2.5 GHz Qualcomm Snapdragon 845 processor</p>', 'shopping.png', 'Google phones', '2019-08-09 12:42:01', 1),
(69, 3, 11, 'Google Pixel XL', '197.95', '<p>5.5 inch quad HD display</p>\r\n<p>4 GB RAM&nbsp;</p>\r\n<p>128 GB storage</p>\r\n<p>Android v7.1(Nougat)</p>\r\n<p>Quad core&nbsp; processor (2.15 GHz, Dual core, Kryo)</p>\r\n<p>Duel cameras: 12.3 MP rear camera, 8 MP front camera</p>', 'shopping (1).png', 'Google phones', '2019-08-09 15:39:39', 1),
(70, 3, 11, 'Google Pixel 3a XL', '459.95', '<p>6 inch HD display</p>\r\n<p>8 GB RAM</p>\r\n<p>128 GB storage</p>\r\n<p>Android v9.0 (PIE)</p>\r\n<p>Duel cameras: 12.3 MP</p>\r\n<p>Qualcomm Snapdragon 670 processor</p>', 'google-pixel_.jpg', 'Google phones', '2019-08-09 16:09:00', 1),
(71, 6, 11, 'Google Pixelbook', '965.99', '<p>12.3 inch HD display</p>\r\n<p>8 GB RAM</p>\r\n<p>128 GB Flash Memory SSD</p>\r\n<p>Intel Core i5 processor</p>\r\n<p>Chrome operating system</p>\r\n<p>Flexible 4-in-1 design</p>\r\n<p>Touch Pad</p>', 'gooogle3_.jpg', 'Google tablets', '2019-08-10 11:14:05', 1),
(72, 7, 1, 'HP Photosmart Premium', '155.00', '<p>Print, Scan, &amp; Copy in one energy-efficient package</p>\r\n<p>3.5 inch touchscreen</p>\r\n<p>Up to 33 ppm black &amp; 32 ppm color print speeds</p>\r\n<p>Rotate, crop, adjust brightness, or apply color effects using touchscreen</p>\r\n<p>Wi-Fi, Ethernet, Bluetooth, USB and PicBridge</p>\r\n<p>125-sheet input tray and 20-sheet photo tray</p>', 'hpprinter_.jpg', 'HP printers', '2019-08-11 11:57:55', 1),
(73, 7, 3, 'Dell C3765dnf ', '522.99', '<p>Multi-function business color laser printer</p>\r\n<p>High efficiency &amp; quality for medium to large workgroups</p>\r\n<p>Protect sensitive data with LDAP &amp; Kerberos authentification</p>\r\n<p>10-digit password keypad on operating panel</p>\r\n<p>Windows 10 compatible</p>\r\n<p>Max duty cycle of up to 80,000 copies/month</p>', 'dellprinter_.jpg', 'Dell printers', '2019-08-11 12:18:08', 1),
(74, 4, 9, 'Canon EOS  Rebel T6 ', '439.95', '<p>18-55mm EF-S f/3.5-5.6 is II telephoto lens</p>\r\n<p>55 mm high-def wide-angle lense with 2x telephoto lense</p>\r\n<p>Transcend 16 &amp; 32 GB Flash Memory Cards</p>\r\n<p>Digital DSLR auto-power slave flash</p>\r\n<p>High-speed SD USB card reader</p>\r\n<p>3-piece UV filter kit</p>\r\n<p>Camera case &amp; tripods (incl table-top model)</p>', 'canonbundle_.jpg', 'Canon cameras', '2019-08-11 13:51:36', 1),
(75, 4, 8, 'Sony Alpha a600', '628.50', '<p>Mirrorless digital camera with 16-50mm f/3.5-5.6 SS lens</p>\r\n<p>Move theater-quality full HD 1080p video</p>\r\n<p>Micro USB cable</p>\r\n<p>2x SanDisk Ultra SDXC 64GB Memory cards</p>\r\n<p>Deco Gear 40.5 mm Macro and regular filter kits</p>\r\n<p>Deco Gear camera bag</p>\r\n<p>12 inch tripod</p>\r\n<p>Editing suite, Office suite premium &amp; Slide-show maker</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>', 'sonycamera_.jpg', 'Sony cameras', '2019-08-11 19:20:48', 1),
(76, 1, 3, 'Dell Inspiron 15 5000', '746.95', '<p>15.6 inch HD display</p>\r\n<p>Intel Core i7 1.8GHz processor</p>\r\n<p>8 GB DDR4 RAM</p>\r\n<p>Dual drive: 128 flash SSD + 1TB HDD</p>\r\n<p>Intel HD graphics 620</p>\r\n<p>Windows 10</p>', 'dell1500_.jpg', 'Dell laptops', '2019-08-12 10:05:53', 1),
(77, 1, 6, 'Apple MacBook Pro +', '2019.99', '<p>15.4 inch HD retina display</p>\r\n<p>32 GB DDR4 RAM</p>\r\n<p>512 GB SSD hard drive</p>\r\n<p>2.3 GHz Intel Core i9 processor</p>\r\n<p>Intel UHD 630 graphics</p>\r\n<p>Radeon Pro 560X with 4GB GDDR5 dedicated memory</p>', 'applemacbook2_.jpg', 'Apple laptops', '2019-08-12 11:10:54', 1),
(78, 3, 11, 'Google Pixel 2XL', '249.94', '<p>6 inch HD screen</p>\r\n<p>64 GB memory</p>\r\n<p>Android operating system</p>\r\n<p>Qualcomm Snapdragon 835 processor</p>\r\n<p>12.2 mp rear camera, 8mp front camera</p>\r\n<p>Image stabilization</p>\r\n<p>Water resistant metal unibody</p>\r\n<p>&nbsp;</p>', 'goggle4_.jpg', 'Google phones', '2019-08-13 19:34:37', 1),
(79, 6, 2, 'Asus Flip C434TA', '555.99', '<p>14 inch HD screen</p>\r\n<p>4 GB RAM</p>\r\n<p>64 GB Hard Drive</p>\r\n<p>Core M processor</p>\r\n<p>Intel HD Graphics 615</p>\r\n<p>Chrome OS</p>\r\n<p>Touchscreen</p>\r\n<p>&nbsp;</p>', 'asuschrome_.jpg', 'Assus tablets', '2019-08-15 10:12:31', 1),
(80, 6, 4, 'Lenovo C330', '249.99', '<p>Chromebook</p>\r\n<p>11.6 inch HD screen</p>\r\n<p>4 GB RAM</p>\r\n<p>64 GB Hard Drive</p>\r\n<p>MediaTek MT8173C processor</p>\r\n<p>Chrome OS</p>', 'lenovo1_.jpg', 'Lenovo tablets', '2019-08-15 10:33:19', 1),
(81, 3, 4, 'Lenovo Phab 2 Pro', '369.99', '<p>6.4 inch HD screen</p>\r\n<p>4 GB RAM</p>\r\n<p>64 GB Memory storage</p>\r\n<p>Snapdragon 652 processor</p>\r\n<p>8 MP RGB front camera</p>\r\n<p>3 rear cameras: 16MP RGB, Depth camera, Fish-eye camera</p>\r\n<p>Tango augmented reality, gaming &amp; utilities</p>\r\n<p>&nbsp;</p>', 'lenovo3_.jpg', 'Lenovo phones', '2019-08-15 12:10:46', 1),
(82, 2, 4, 'Lenovo IdeaCenter 520', '818.99', '<p>23.8 inch touchscreen HD Monitor</p>\r\n<p>12GB DDR4 RAM</p>\r\n<p>1TB HDD</p>\r\n<p>Intel Core i5 processor</p>\r\n<p>Intel integrated 630 graphics</p>\r\n<p>Windows 10 Home</p>', 'lenovo4_.jpg', 'Lenovo desktops', '2019-08-15 12:59:59', 1),
(83, 2, 2, 'Asus Zen AiO', '1260.99', '<p>23.8 inch HD touchscreen</p>\r\n<p>8GB DDR4 RAM</p>\r\n<p>128GB SSD + 1TB Flash Memory SSD Hard Drive</p>\r\n<p>Intel Core i7 processor</p>\r\n<p>NVIDIA GeForce GTX-1050 graphics co-processor</p>\r\n<p>Windows 10 Home</p>', 'asus1_.jpg', 'Asus desktops', '2019-08-15 14:04:10', 1),
(84, 3, 5, 'LG V35 Thin Q', '299.99', '<p>6 inch HD display</p>\r\n<p>6 GB RAM</p>\r\n<p>64 GB memory</p>\r\n<p>Qualcomm Snapdragon 845 octa-core processor</p>\r\n<p>Dual 16 MP standard-angle and wide-angle rear cameras with AI flash</p>\r\n<p>8 MP wide-angle front-facing camera</p>\r\n<p>Android OS</p>', 'lgphone2_.jpg', 'LG phones', '2019-08-15 21:19:45', 1),
(85, 7, 9, 'Canon MG7720', '530.00', '<p>Wireless All-in-One printer</p>\r\n<p>Inkjet</p>\r\n<p>Printer, Scanner &amp; Copier</p>\r\n<p>Mobile and Tablet ready</p>\r\n<p>Airprint and Google Cloud compatible</p>\r\n<p>3.5 inch touchscreen</p>', 'canon1_.jpg', 'Canon printers', '2019-08-16 08:27:28', 1),
(86, 1, 5, 'LG Gram Thin', '1523.33', '<p>17 inch HD screen</p>\r\n<p>16 GB DDR4 RAM</p>\r\n<p>512 GB Flash Memory SSD</p>\r\n<p>Intel Core i7 processor</p>\r\n<p>Intel HD Graphics 610</p>\r\n<p>Windows 10 Home</p>', 'lglaptop2_.jpg', 'LG laptops', '2019-08-16 08:41:33', 1),
(87, 7, 9, 'Canon MB2720', '99.99', '<p>Wireless All-In-One printer</p>\r\n<p>Print, Scan, Copy &amp; FAX</p>\r\n<p>2 paper cassettes (letter &amp; legal sizes)</p>\r\n<p>Mobile-ready using Canon PRINT app</p>\r\n<p>Windows 10</p>\r\n<p>&nbsp;</p>', 'canon2_.jpg', 'Canon printers', '2019-08-16 09:41:11', 1),
(88, 7, 8, 'Sony Digital Photo Printer', '209.99', '<p>Digital photo printer</p>\r\n<p>Flip-up LCD screen</p>\r\n<p>View, enlarge &amp; edit images</p>\r\n<p>Touch-sensitive screen operation</p>\r\n<p>Prints directly from memory stick or PCMCIA type II cards</p>\r\n<p>PC and MAC compatible</p>', 'sonypic.jpg', 'Sony printers', '2019-08-16 09:54:29', 1),
(89, 1, 2, 'Asus VivoBook', '579.00', '<p>15.6 inch HD screen</p>\r\n<p>8 GB DDR4 RAM</p>\r\n<p>1 TB HDD</p>\r\n<p>Intel Core i5 processor</p>\r\n<p>Intel UHD Graphics 620 with 8 GB RAM</p>\r\n<p>Windows 10 Home</p>', 'asus2_.jpg', 'Asus laptops', '2019-08-16 10:05:47', 1),
(90, 1, 6, 'Apple MacBook Air', '979.99', '<p>13.3 inch retina display</p>\r\n<p>8 GB RAM</p>\r\n<p>256 GB SSD storage</p>\r\n<p>Intel Core i5 processor</p>\r\n<p>IntelUHD Graphics 617</p>\r\n<p>Two Thunderbolt 3 (USB-C) ports</p>', 'apple2_.jpg', 'Apple laptops', '2019-08-17 11:55:19', 1),
(91, 3, 12, 'Samsung Galaxy 10+', '1199.99', '<p>Samsung Galaxy Note 10+</p>\r\n<p>6.8 inch dynamic AMOLED HD+ display</p>\r\n<p>Android 9 Pie operating system</p>\r\n<p>Snapdragon 855 processor</p>\r\n<p>12 GB memory</p>\r\n<p>512 GB storage</p>\r\n<p>S-pen for dynamic control of camera, gallery, music &amp; video</p>\r\n<p>1 front&nbsp; camera: (10-MP f/2.2)</p>\r\n<p>4 rear cameras: (16-MP f/2.2, 12-MP wide-angle f/1.5-f/2.4,</p>\r\n<p>&nbsp; &nbsp;12-MP telephoto f/2.1, VGA)</p>', 'samsung1_.jpg', 'Samsung phones', '2019-09-03 19:44:38', 1),
(92, 6, 12, 'Samsung Tablet S5e', '439.99', '<p>Samsung Galaxy Tab S5e</p>\r\n<p>10.5 inch HD display</p>\r\n<p>4 GB RAM</p>\r\n<p>64 GB DDR4 SDRAM</p>\r\n<p>Snapdragon QSD8250 processor</p>\r\n<p>Adreno 615 graphics co-processor</p>\r\n<p>Wi-Fi, Bluetooth, GPS</p>\r\n<p>&nbsp;</p>', 'samsung2_.jpg', 'Samsung tablets', '2019-09-03 20:07:36', 1),
(93, 6, 12, 'Samsung TabPro S', '889.01', '<p>Samsung Galaxy TabPro S Convertible</p>\r\n<p>12 inch HD display</p>\r\n<p>8 GB RAM</p>\r\n<p>256 GB DDR4 SDRAM</p>\r\n<p>Intel Core M3 processor</p>\r\n<p>Intel HD Graphics 515</p>\r\n<p>Keyboard, touchscreen</p>\r\n<p>Windows 10 Home</p>', 'samsung3_.jpg', 'Samsung tablets', '2019-09-03 20:21:56', 1),
(94, 3, 12, 'Samsung Galaxy S10e', '739.99', '<p>5.8 inch dynamic AMOLED screen</p>\r\n<p>Android 9.0 Pie operating system</p>\r\n<p>6 GB DDR4 SDRAM</p>\r\n<p>128 GB Internal flash memory</p>\r\n<p>Qualcomm Snapdragon 855 processor</p>\r\n<p>10 MP front-facting camera</p>\r\n<p>16 MP ultra-wide &amp; 12 MP wide rear cameras</p>\r\n<p>&nbsp;</p>', 'samsung5.png', 'Samsung phones', '2019-09-04 09:33:18', 1),
(95, 6, 12, 'Samsung Notebook 7', '689.95', '<p>13.3 inch HD display</p>\r\n<p>8 GB DDR4 SDRAM</p>\r\n<p>256 GB DDR SDRAM Flash memory</p>\r\n<p>Intel Core i5 processor</p>\r\n<p>Intel HD graphics 620</p>\r\n<p>Touchscreen</p>\r\n<p>Windows 10</p>', 'samsung6_.jpg', 'Samsung tablets', '2019-09-04 09:58:55', 1),
(96, 3, 12, 'Samsung Galaxy A50', '253.99', '<p>6.4 inch HD screen</p>\r\n<p>4 GB RAM</p>\r\n<p>64 GB storage</p>\r\n<p>Android Pie v9.0 operating system</p>\r\n<p>Octa Core processor</p>\r\n<p>Front camera: 25 MP (F2.0)</p>\r\n<p>Rear cameras: 25 MP(F1.7) , 8 MP(F2.2), &amp; 5 MP(F2.2)</p>', 'samsung7_.jpg', 'Samsung phones', '2019-09-04 11:11:38', 1);

-- Customer Order Statuses
INSERT INTO tcustomerorderstatuses (intCustomerOrderStatusID, strCustomerOrderStatus)
VALUES(1, 'Purchased'),
	  (2, 'Cancelled'),
	  (3, 'Returned');
          
-- Customer orders
INSERT INTO tcustomerorders (intCustomerID, intDeliveryIndex, intOrderIndex, dtmOrderDate, intCustomerOrderStatusID) 
VALUES
(1, 1, 1, '2019-01-20', 1),
(1, 2, 2, '2019-02-26', 1),
(1, 1, 3, '2019-01-21', 1),
(2, 1, 1, '2019-01-22', 1),
(3, 1, 1, '2019-01-21', 1),
(3, 1, 2, '2019-01-22', 1),
(4, 1, 1, '2019-01-22', 1),
(5, 1, 1, '2019-01-22', 1),
(6, 1, 1, '2019-01-22', 1),
(7, 1, 1, '2019-01-22', 1),
(8, 1, 1, '2019-01-22', 1),
(9, 1, 1, '2019-01-22', 1),
(9, 1, 2, '2019-01-23', 1),
(10, 1, 1, '2019-01-22', 1),
(11, 1, 1, '2019-01-22', 1),
(12, 1, 1, '2019-01-22', 1),
(13, 1, 1, '2019-01-22', 1),
(14, 1, 1, '2019-01-22', 1),
(15, 1, 1, '2019-01-22', 1),
(16, 1, 1, '2019-01-22', 1),
(17, 1, 1, '2019-01-22', 1);

-- Customer order items
INSERT INTO tcustomerorderitems (intCustomerID, intDeliveryIndex, intOrderIndex, intProductID, intQuantity) 
VALUES
(1, 1, 1, 27, 1),
(1, 1, 1, 29, 1),
(1, 1, 1, 51, 1),
(1, 2, 2, 9, 1),
(1, 2, 2, 35, 1),
(1, 2, 2, 50, 1),
(1, 1, 3, 51, 1),
(2, 1, 1, 40, 1),
(3, 1, 1, 13, 1),
(3, 1, 1, 20, 1),
(3, 1, 1, 43, 1),
(3, 1, 2, 47, 1),
(4, 1, 1, 49, 1),
(4, 1, 1, 50, 1),
(5, 1, 1, 31, 1),
(5, 1, 1, 54, 1),
(6, 1, 1, 18, 1),
(6, 1, 1, 39, 1),
(7, 1, 1, 19, 1),
(7, 1, 1, 46, 1),
(8, 1, 1, 4, 1),
(8, 1, 1, 50, 1),
(9, 1, 1, 17, 1),
(9, 1, 1, 23, 1),
(9, 1, 1, 38, 1),
(9, 1, 2, 9, 1),
(10, 1, 1, 27, 1),
(10, 1, 1, 36, 1),
(11, 1, 1, 35, 1),
(12, 1, 1, 11, 1),
(13, 1, 1, 31, 1),
(13, 1, 1, 41, 1),
(14, 1, 1, 32, 1),
(14, 1, 1, 41, 1),
(14, 1, 1, 53, 1),
(15, 1, 1, 12, 1),
(15, 1, 1, 25, 1),
(16, 1, 1, 17, 1),
(16, 1, 1, 53, 1),
(17, 1, 1, 10, 1),
(17, 1, 1, 15, 1),
(17, 1, 1, 54, 1);


-- ---------------------------------------------------------------------------------
-- TRIGGERS
-- ---------------------------------------------------------------------------------
DELIMITER //
-- ---------------------------------------------------------------------------------
-- Customer After Insert
-- Inserts customer data to the delivery address table at the first index position  
-- --------------------------------------------------------------------------------- 
CREATE TRIGGER tgrtcustomersafterinsert AFTER INSERT 
ON TCustomers 
FOR EACH ROW
BEGIN	
	-- Delivery Index
	DECLARE New_intDeliveryIndex INTEGER DEFAULT 1;
    DECLARE New_intStatusID      INTEGER DEFAULT 1;
    
    -- Copy existing values (from Old) 
	INSERT INTO tcustomerdeliveryaddresses
	( 
		intCustomerID,
		intDeliveryIndex,
		strDeliveryName,
		strAddress1,
		strAddress2,
		strCity,
		strState,
		intCountryID,
		strZipCode,
        intDeliveryAddressStatusID
	) 
	VALUES
    (
		New.intCustomerID, 						
		New_intDeliveryIndex,
        New.strCustomerName,
        New.strAddress1,       
		New.strAddress2,
        New.strCity,      
		New.strState,
        New.intCountryID,
        New.strZipCode,
        New_intStatusID
	);  				
END;
//

-- ---------------------------------------------------------------------------------
-- Customer After Update
-- Updates a customer's primary shipping data after an edit of customer's information 
-- --------------------------------------------------------------------------------- 
CREATE TRIGGER tgrtcustomersafterupdate AFTER UPDATE 
ON TCustomers 
FOR EACH ROW
BEGIN	
    -- Copy existing values (from Old) 
	UPDATE tcustomerdeliveryaddresses
    SET
		strDeliveryName = NEW.strCustomerName,
		strAddress1     = NEW.strAddress1,
		strAddress2     = NEW.strAddress2,
		strCity         = NEW.strCity,
		strState        = NEW.strState,
		intCountryID    = NEW.intCountryID,
		strZipCode      = NEW.strZipCode,
        intDeliveryAddressStatusID = 1
	WHERE
		intCustomerID   = OLD.intCustomerID
	AND intDeliveryIndex = 1;
	  				
END;
//

-- ---------------------------------------------------------------------------------
-- Products After Update
-- Populates the history table with product prices during defined time intervals 
-- --------------------------------------------------------------------------------- 
CREATE TRIGGER tgrtproductsafterupdate AFTER UPDATE
ON TProducts 
FOR EACH ROW
BEGIN
	-- Date Changed 
	DECLARE New_dtmChanged DATETIME DEFAULT NOW();
	
	-- Copy existing values (from Old) into the history table each time an item's price is changed.
	INSERT INTO tproductpricehistories
	( 
		intProductID,
		intChangeIndex,
        intCategoryID,
        intBrandID,
        strProductTitle,
		decSellingPrice,        
		strProductDescription,
		strProductImage,
        strProductKeywords,    					
		dtmCreated,	
		intProductStatusID,
		dtmChanged        
	) 
	SELECT 
		OLD.intProductID, 						
		(SELECT
			-- Default to 1 if first update, otherwise increment 
			-- highest current change index by 1
			IFNULL(MAX(intChangeIndex) + 1, 1) 
		 FROM			  
			tproductpricehistories AS TPH	
		  -- Cross-query join
		 WHERE 
			TPH.intProductID = OLD.intProductID		  
		) AS intChangeIndex,
        OLD.intCategoryID,
        OLD.intBrandID,        
		OLD.strProductTitle,
        OLD.decSellingPrice,        
		OLD.strProductDescription,
        OLD.strProductImage,
        OLD.strProductKeywords, 		
		OLD.dtmCreated,
		OLD.intProductStatusID,
		New_dtmChanged;	
END;
//



