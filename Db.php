<?php
/**
* Create Database connection
*/
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');

Class Db {
	private $_connection = null;

	public function __construct() {
		$this->_connect();
	}

	/**
	* Create connection with Db
	*/
	private function _connect() {
		$this->_connection = mysqli_init();

		$_isConnected = @mysqli_real_connect(
				$this->_connection,
				DB_SERVER,
				DB_USERNAME,
				DB_PASSWORD,
				DB_NAME,
				DB_PORT,
				null,
				MYSQLI_CLIENT_FOUND_ROWS
			);

		if ($_isConnected === false || mysqli_connect_errno()) {
			$this->closeConnection();
		}
	}

	/**
	* Check whether Db connection is exists
	*/
	public function isConnected() {
		return ((bool) ($this->_connection instanceof mysqli));
	}
	
	/**
	* Colse Db connection
	*/
	public function closeConnection() {
		if ($this->isConnected()) {
			$this->_connection->close();
		}
		$this->_connection = null;
	}

	/**
	* Get last instered id
	*/
	public function lastInsertId() {
		return $this->_connection->insert_id;
	}
	
	/**
	* To execute Insert and Update queries
	* 
	* @param string $sql query to execute
	* @param array $params bind parameters required to execute query
	*
	* @return boolean
	*/
	public function execute($sql, $bind = array()) {
		
		if ( !is_array($bind) ) {
			$bind = array($bind);
		}
		
		$this->_connect();
		$stmt = $this->_connection->prepare($sql);
		if($stmt){
			$types = "";
			foreach($bind as $param) {
				if (is_int($param)) $types .= "i";
				else if (is_double($param)) $types .= "d";
				else if (is_string($param)) $types .= "s";
			}
			
			
			$bind_names[] = $types;
			for ($i=0; $i<count($bind);$i++)
			{
				$bind_name = 'bind' . $i;
				$$bind_name = $bind[$i];
				$bind_names[] = &$$bind_name;
			}
			
			if ( !empty( $bind ) ) {
				$return = call_user_func_array(array($stmt,'bind_param'),$bind_names);
			}
			
			$parse_query = $this->_preparedQuery($sql,$bind);
			
			$retval = $stmt->execute();

			return $retval;
		}
		
	}
	
	/**
	* To execute select queries 
	* 
	* @param string $sql query to execute
	* @param array $params bind parameters required to execute query
	*
	* @return array
	*/
	public function query($sql, $bind = array()) {
		if ( !is_array($bind) ) {
			$bind = array($bind);
		}
		
		$this->_connect();
		$stmt = $this->_connection->prepare($sql);
		if($stmt){
			$types = "";
			foreach($bind as $param) {
				if (is_int($param)) $types .= "i";
				else if (is_double($param)) $types .= "d";
				else if (is_string($param)) $types .= "s";
			}
			
			$bind_names[] = $types;
			for ($i=0; $i<count($bind);$i++) {
				$bind_name = 'bind' . $i;
				$$bind_name = $bind[$i];
				$bind_names[] = &$$bind_name;
			}


			if ( !empty( $bind ) ) {
				$return = call_user_func_array(array($stmt,'bind_param'),$bind_names);
			}
				
			$parse_query = $this->_preparedQuery($sql,$bind);
					
			$stmt->execute();
				
			$result = $stmt->get_result();
			$returns = array();

			while ($row = $result->fetch_assoc()) {
				$returns[] = $row;
			}
			
			return $returns;
		}
		
	}

	/**
	* Prepare query to avoid SQL injection
	* 
	* @param string $sql query to execute
	* @param array $params bind parameters required to execute query
	*
	* @return string
	*/
	private function _preparedQuery($sql,$params) {
		for ($i=0; $i<count($params); $i++)  {
			$sql = preg_replace('/\?/',$params[$i],$sql,1);
		}
		return $sql;
	}
}
?>