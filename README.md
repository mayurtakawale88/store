# REST api

Rest api for create, update and delete products into sample store application. Only authenticated user can do this all operations. Default username and password for authentication as follows.
    ```
     Username : mayur
     Password : 123456
    ```

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
	 ``` 
	     API URL : http://localhost/store/?action=login
         POST Parameters :
	     username : mayur
	     password : 123456
 	 ```

	2. To add product into store
	 ``` 
	     API URL : http://localhost/store/?action=add
         POST Parameters :
	     name  : test
	     price : 140 
 	 ```

	3. To Edit product into store
	 ``` 
	     API URL : http://localhost/store/?action=edit
         POST Parameters :
	     id  : 13
	     name  : test
	     price : 140 
 	 ```

	4. To Delete product from store
	 ``` 
	     API URL : http://localhost/store/?action=delete
         POST Parameters :
	     id  : 13
 	 ```

	5. To Search product into store
	 ``` 
	     API URL : http://localhost/store/?action=search
         POST Parameters :
	     name  : test
 	 ```

	6. To Log out from store
         ``` 
             API URL : http://localhost/store/?action=logout 
	 ```
