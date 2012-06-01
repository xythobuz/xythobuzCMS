<?
	if (basename($_SERVER['PHP_SELF']) == "count.php") {
		include('config.php');
		$db = mysql_connect($sql_host, $sql_username, $sql_password);
		mysql_select_db($sql_database);
		if (mysql_errno()) {
			die ('Konnte keine Verbindung zur Datenbank aufbauen');
		}
	}

	// Just echo the number of hits today
	$sql = 'SELECT day, visitors
		FROM cms_visitors
		WHERE day = "'.date('Y-m-d').'"';
	$result = mysql_query($sql);
	if (!$result) {
		die("Database Error!");
	}
	$row = mysql_fetch_array($result);
	if ($row) {
		// Theres a record for this day
		$count = $row['visitors'];
		$count++;
		$sql = 'UPDATE cms_visitors
		SET
			visitors = '.$count.'
		WHERE
			day = "'.date('Y-m-d').'"';
		$result = mysql_query($sql);
		if (!$result) {
			die("Database Error");
		}
		echo $count;
	} else {
		// Create new record
		$sql = 'INSERT INTO cms_visitors(day, visitors)
			VALUES ( "'.date('Y-m-d').'", 1 )';
		$result = mysql_query($sql);
		if (!$result) {
			echo mysql_error();
			die("Database Error.");
		}
		echo "1";
	}
?>