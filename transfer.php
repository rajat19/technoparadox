<?php
	$conn = mysqli_connect('localhost', 'root', '');
	mysqli_select_db($conn, 'mysearch');

	$q1 = mysqli_query($conn, "SELECT * FROM locations WHERE name_0='india' AND name_2 IS NOT NULL AND name_3 IS NULL");
	while($row = mysqli_fetch_array($q1)) {
		$state = $row['name_1'];
		$city = $row['name_2'];
		$lat = $row['lat'];
		$lon = $row['lon'];

		mysqli_select_db($conn, 'technoparadox');
		$q2 = mysqli_query($conn, "INSERT INTO locations (state, city, lat, lon) VALUES ('$state', '$city', '$lat', '$lon')");
		if($q2)
			echo "Inserted".$city." , ".$state;
		else
			echo "<b>NOT Inserted".$city." , ".$state."</b>";
		echo "<br>";
	}

?>
