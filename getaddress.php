Select location from dropdown list
<form action="getmyloc.php" method="POST">
	<select name="list">
		<?php
			$conn = mysqli_connect('localhost', 'root', '', 'technoparadox');
			$q1 = mysqli_query($conn, "SELECT * FROM locations ORDER BY city");
			while($row = mysqli_fetch_array($q1)) {
				$state = $row['state'];
				$city = $row['city'];
				$id = $row['id'];
				$lat = $row['lat'];
				$lon = $row['lon'];
				$arr = implode('$', array($lat, $lon));
				echo "<option value='$arr'>$city, $state</option>";
			}
		?>
	</select>
	<input type="submit" name="setloc" value="Enter the location">
</form>