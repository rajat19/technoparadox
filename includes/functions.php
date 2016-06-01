<?php
class Users {
	public $tableName = 'users';
	public $connect;

	function __construct(){
		//database configuration
		$dbServer = 'localhost';
		$dbUsername = 'root';
		$dbPassword = '';
		$dbName = 'technoparadox';
		
		//connect databse
		$con = mysqli_connect($dbServer,$dbUsername,$dbPassword,$dbName);
		if(mysqli_connect_errno()){
			die("Failed to connect with MySQL: ".mysqli_connect_error());
		}else{
			$this->connect = $con;
		}
	}
	
	function checkUser($oauth_provider,$oauth_uid,$fname,$lname,$email,$gender,$locale,$link,$picture){
		$prevQuery = mysqli_query($this->connect,"SELECT * FROM $this->tableName WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$oauth_uid."'") or die(mysqli_error($this->connect));
		if(mysqli_num_rows($prevQuery) > 0){
			$update = mysqli_query($this->connect,"UPDATE $this->tableName SET oauth_provider = '".$oauth_provider."', oauth_uid = '".$oauth_uid."', fname = '".$fname."', lname = '".$lname."', email = '".$email."', gender = '".$gender."', locale = '".$locale."', picture = '".$picture."', gpluslink = '".$link."', modified = '".date("Y-m-d H:i:s")."' WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$oauth_uid."'") or die(mysqli_error($this->connect));
		}else{
			$insert = mysqli_query($this->connect,"INSERT INTO $this->tableName SET oauth_provider = '".$oauth_provider."', oauth_uid = '".$oauth_uid."', fname = '".$fname."', lname = '".$lname."', email = '".$email."', gender = '".$gender."', locale = '".$locale."', picture = '".$picture."', gpluslink = '".$link."', created = '".date("Y-m-d H:i:s")."', modified = '".date("Y-m-d H:i:s")."'") or die(mysqli_error($this->connect));
		}
		
		$query = mysqli_query($this->connect,"SELECT * FROM $this->tableName WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$oauth_uid."'") or die(mysqli_error($this->connect));
		$result = mysqli_fetch_array($query);
		return $result;
	}

	function addLoc($oauth_uid, $lat, $lon) {
		$query = mysqli_query($this->connect, "UPDATE $this->tableName SET lat='$lat', lon='$lon' WHERE oauth_uid='$oauth_uid'") or die(mysqli_error($this->connect)) ;
	}

	function getLoc($id) {
		$q = mysqli_query($this->connect, "SELECT lat, lon FROM $this->tableName WHERE oauth_uid='$id'");
		$r = mysqli_fetch_array($q);

		return $r;
	}

	function setLogout($id) {
		$current = date("Y-m-d H:i:s", strtotime("+3 hours 30 minutes"));
		$query = mysqli_query($this->connect, "UPDATE $this->tableName SET logout='".$current."' WHERE oauth_uid='$id'") or die(mysqli_connect($this->connect));
	}

	function findPeople($id) {
		$current = date("Y-m-d H:i:s", strtotime("+5 hours 30 minutes"));
		$query = mysqli_query($this->connect, "SELECT * FROM $this->tableName s, $this->tableName t WHERE s.oauth_uid!='$id' AND s.modified < '$current' AND s.modified > t.logout AND s.oauth_uid = t.oauth_uid");
		return $query;
	}

	function userdetails($id) {
		$q = mysqli_query($this->connect, "SELECT * FROM $this->tableName WHERE oauth_uid='$id'");
		$r = mysqli_fetch_array($q);

		return $r;
	}

	function calculatedistance($address) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSLVERSION,3);
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $address,
        CURLOPT_USERAGENT => 'Rajat Srivastava'
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    if(!curl_exec($curl)){
        die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
    }
    // Close request to clear up some resources
    curl_close($curl);
    $arr = json_decode($resp, true);
    return $arr;
}
}
?>