<button onclick="getgooglelocation();">Get Location</button> or <button onclick="getmanuallocation()">Set Location</button>

<script type="text/javascript">
	function getgooglelocation() {
		var add = "https://www.googleapis.com/geolocation/v1/geolocate?key=";
		var key = "AIzaSyAErQ52cVQDdBBVFFuHP5vBksvVEjWNXvE";
		var jsonLatLong;

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                jsonLatLong = xmlhttp.responseText;
                document.getElementById("txtHint").innerHTML = jsonLatLong;
                console.log(jsonLatLong);
                
            }
		};
		xmlhttp.open("POST", add + key, true);
        xmlhttp.send();   
	}

	function getmanuallocation() {
		var add = "getaddress.php";

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("hiddiv").innerHTML = xmlhttp.responseText;
            }
		};
		xmlhttp.open("POST", add, true);
        xmlhttp.send();   
	}
</script>
<p id="txtHint"></p>
<div id="hiddiv"></div>
<?php
if(isset($_POST['setloc'])) {
	$add = explode("$", $_POST['list']);
	var_dump($add);
}
?>