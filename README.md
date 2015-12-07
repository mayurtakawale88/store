# REST api

Rest api for create, update and delete products.
 
### Set up instructions
  1. Clone this project in your webserver's root directory
  2. Creating Database and required tables: 
    ```
    mysql -u root -p < store.sql
    ```
  3. Changes required to be done in config.php. Will need to change the default database credentials.
    
    ```
      	define("DB_NAME","store");
	define("DB_USERNAME","root");
	define("DB_PASSWORD","root");
	define("DB_SERVER","localhost");
	define("DB_PORT","3306");
    ```
### User manual 
	1. User required to login through default user added in database to perform CRUD operations on Product
	 *** http://localhost/store/?action=login
             username : mayur, 
	     password : 123456

	2. To add product into store
         *** http://localhost/store/?action=add
	     name  : test
	     price : 140 

	3. To Edit product into store
         *** http://localhost/store/?action=edit
	     id    : 13
	     name  : test
	     price : 140

	4. To Delete product from store
         *** http://localhost/store/?action=delete
	     id  : 13

	5. To Search product into store
         *** http://localhost/store/?action=search&limit=10
	     name  : test   

	6. To Log out from store
         *** http://localhost/store/?action=logout
