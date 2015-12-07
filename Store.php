<?php

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'Db.php');

Class Store {
	private $_connection = null;

	public function __construct() {
		$this->_connection = new Db();
	}

	/**
	* Login to perform addition, deletino etc operations
	*
	* @return array $data response to api call
	*/
	public function login() {
		$data     = array();
		$error    = array();
		$username = "";
		$passeord = "";

		if(empty($_SESSION['userid'])) {
			if(!empty($_POST['username']) && !empty($_POST['password'])) {
				$username = $_POST['username'];
				$password = $_POST['password'];

				$sql = "SELECT id, username FROM admin WHERE username = ? and password = ?";
				$params[] = $username;
				$params[] = $password;

				$result = $this->_connection->query($sql, $params);
				
				if(!empty($result) && !empty($result[0]['id'])) {
					$_SESSION['userid'] = $result[0]['id'];
					$_SESSION['username'] = $result[0]['username'];

					$data = array(
						"success" => true,
						"data"    => array($result[0]),
						"error"   => $error
						);
				} else {
					$error['warning'] = "Invalid username or password";
					$data = array(
						"success" => true,
						"data"    => array(),
						"error"   => array($error)
						);

				}

			} else {

				if(empty($_POST['username'])) {
					$error['username'] = "Username required";
				}
				if(empty($_POST['password'])) {
					$error['password'] = "passowrd required";
				}

				$data = array(
					'success' => false,
					'data'	  => array(),
					'error'	  => array($error),
					);
			}
		} else {
			$error['notice'] = 'user already logged in';
			$data = array(
					'success' => false,
					'data'	  => array(),
					'error'	  => array($error)
					);
		}

		return $data;
	}


	/**
	* To get logged out from system
	*
	* @return array $data response to api call
	*/
	public function logout() {
		$data  = array();
		$error = array();

		session_destroy();

		$data = array(
			'success' => true,
			'data'	  => array(),
			'error'   => $error
			);

		return $data;
	}


	/**
	* Add product to store
	*
	*@return array $data response to api call
	*/
	public function add() {
		$data    = array();
		$error   = array();
		$success = false;

		if(!empty($_SESSION['userid']) && $_SESSION['userid'] > 0) {
			$productName  = '';
			$productPrice = 0;

			if(!empty($_POST['name'])) {
				if(!preg_match('/[^A-Za-z0-9]/', $_POST['name'])) {
					$productName = $_POST['name'];
				} else {
					$error['name'] = "Invalid product name";	
				}
				
			} else {
				$error['name'] = "Please provide product name";
			}

			if(!empty($_POST['price'])) {
				if(is_numeric($_POST['price'])) {
					$productPrice = $_POST['price'];
				} else {
					$error['price'] = "Invalid price";
				}
			} else {
				$error['price'] = "Please provide product price";
			}

			if(empty($error)) {
				$sql = "INSERT INTO product(`name`,`price`) values(?, ?)";
				$params[] = $productName;
				$params[] = $productPrice;

				$response = $this->_connection->execute($sql, $params);
				if($response) {
					$lastInsertedId = $this->_connection->lastInsertId();
					$success = true;
					$data = array(
						'success' => $success,
						'data'    => array($this->isProductExists($lastInsertedId, true)),
						'error'   => array()
					); 

				} else {
					$error['warning'] = "Database error";
					$data = array(
						'success' => $success,
						'data'    => $data,
						'error'   => $error
					);
				}

			} else {
				$data = array(
					'success' => $success,
					'data'    => array(),
					'error'   => array($error)
					);
			}


		} else {
			$error['warning'] = "Unauthorized access";
			$data = array(
				'success' => $success,
				'data'    => array(),
				'error'	  => array($error)
				);
		}

		return $data;
	}


	/**
	* Edit product exists into store
	*
	*@return array $data response to api call
	*/
	public function edit() {
		$data    = array();
		$error   = array();
		$success = false;

		if(!empty($_SESSION['userid']) && $_SESSION['userid'] > 0) {
			$productName  = '';
			$productPrice = 0;
			$productId 	  = 0;

			if(!empty($_POST['id'])) {
				if(is_numeric($_POST['id'])) {
					$productId = $_POST['id'];
				} else {
					$error['id'] = "Invalid product id";
				}
			} else {
				$error['id'] = "Please provide product id";
			}

			if(!empty($_POST['name'])) {
				if(!preg_match('/[^A-Za-z0-9]/', $_POST['name'])) {
					$productName = $_POST['name'];
				} else {
					$error['name'] = "Invalid product name";	
				}
				
			} else {
				$error['name'] = "Please provide product name";
			}

			if(!empty($_POST['price'])) {
				if(is_numeric($_POST['price'])) {
					$productPrice = $_POST['price'];
				} else {
					$error['price'] = "Invalid price";
				}
			} else {
				$error['price'] = "Please provide product price";
			}

			if(empty($error)) {
				
				if($this->isProductExists($productId)) {

					$sql = "UPDATE product SET name = ?, price = ? WHERE id = ?";
					$params[] = $productName;
					$params[] = $productPrice;
					$params[] = $productId;

					$response = $this->_connection->execute($sql, $params);

					if($response) {
						$success = true;
						$data = array(
							'success' => $success,
							'data'    => array($this->isProductExists($productId, true)),
							'error'   => array()
						);
					} else {
						$error['warning'] = "Database error";
						$data = array(
							'success' => $success,
							'data'    => $data,
							'error'   => $error
						);
					}

				} else {
					$error['id'] = "Product does not eixsts";
					$data = array(
						'success' => $success,
						'data'    => $data,
						'error'   => $error
					);
				}

			} else {
				$data = array(
					'success' => $success,
					'data'    => array(),
					'error'   => array($error)
					);
			}
		} else {
			$error['warning'] = "Unauthorized access";
			$data = array(
				'success' => $success,
				'data'    => array(),
				'error'	  => array($error)
				);
		}

		return $data;
	}


	/**
	* delete product from store
	*
	*@return array $data response to api call
	*/
	public function delete() {
		$data    = array();
		$error   = array();
		$success = false;

		if(!empty($_SESSION['userid']) && $_SESSION['userid'] > 0) {
			$productId 	  = 0;

			if(!empty($_POST['id'])) {
				if(is_numeric($_POST['id'])) {
					$productId = $_POST['id'];
				} else {
					$error['id'] = "Invalid product id";
				}
			} else {
				$error['id'] = "Please provide product id";
			}

			if(empty($error)) {
				
				if($this->isProductExists($productId)) {

					$sql = "DELETE FROM product WHERE id = ?";
					$params[] = $productId;

					$response = $this->_connection->execute($sql, $params);

					if($response) {
						$success = true;
						$data = array(
							'success' => $success,
							'data'    => array(),
							'error'   => array()
						);
					} else {
						$error['warning'] = "Database error";
						$data = array(
							'success' => $success,
							'data'    => $data,
							'error'   => $error
						);
					}

				} else {
					$error['id'] = "Product does not eixsts";
					$data = array(
						'success' => $success,
						'data'    => $data,
						'error'   => $error
					);
				}

			} else {
				$data = array(
					'success' => $success,
					'data'    => array(),
					'error'   => array($error)
					);
			}

		} else {
			$error['warning'] = "Unauthorized access";
			$data = array(
				'success' => $success,
				'data'    => array(),
				'error'	  => array($error)
				);
		}

		return $data;
	}



	/**
	* Search product from store
	*
	*@return array $data response to api call
	*/
	public function search() {
		$data    = array();
		$error   = array();
		$success = false;

		if(!empty($_SESSION['userid']) && $_SESSION['userid'] > 0) {
			$productName = '';
			$limit = 10;

			if(!empty($_GET['limit']) && $_GET['limit'] > 0) {
				$limit = $_GET['limit'];
			}

			if(!empty($_POST['name'])) {
				if(!preg_match('/[^A-Za-z0-9]/', $_POST['name'])) {
					$productName = $_POST['name'];
				} else {
					$error['name'] = "Invalid product name";	
				}
				
			} else {
				$error['name'] = "Please provide product name";
			}

			if(empty($error)) {
				$sql = "SELECT id, name, price FROM product WHERE name like concat('%',?,'%') limit ?";
				$params[] = $productName;
				$params[] = $limit;

				$result = $this->_connection->query($sql, $params);
				$success = true;
				if(!empty($result)) {
					$data = array(
						'success' => $success,
						'data'    => $result,
						'error'   => array()
					);
				} else {
					$error['notice'] = "No record found";
					$data = array(
						'success' => $success,
						'data'    => array(),
						'error'   => array($error)
					);
				}
 
			} else {
				$data = array(
					'success' => $success,
					'data'    => array(),
					'error'   => array($error)
					);
			}


		} else {
			$error['warning'] = "Unauthorized access";
			$data = array(
				'success' => $success,
				'data'    => array(),
				'error'	  => array($error)
				);
		}

		return $data;
	}


	/**
	* Check product id exists in store 
	*
	* @param int $id product id
	* @param boolean $data (optional) set only when product data is needed
	*
	* @return boolean
	*/
	private function isProductExists($id, $data = false) {
		$response = false;
		$sql = "SELECT id, name, price FROM product WHERE id = ?";
		$params[] = $id;

		$result = $this->_connection->query($sql, $params);

		if(!empty($result) && !empty($result[0]['id'])){
			if($data) {
				return $result[0];
			}
			$response = true;
		}

		return $response;
	}
}
?>