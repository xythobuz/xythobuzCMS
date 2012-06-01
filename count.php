<?
	if (basename($_SERVER['PHP_SELF']) == "count.php") {
		include('config.php');
		$db = mysql_connect($sql_host, $sql_username, $sql_password);
		mysql_select_db($sql_database);
		if (mysql_errno()) {
			die ('Konnte keine Verbindung zur Datenbank aufbauen');
		}
	}

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
		$pageViews = $count;
	} else {
		// Create new record
		$sql = 'INSERT INTO cms_visitors(day, visitors)
			VALUES ( "'.date('Y-m-d').'", 1 )';
		$result = mysql_query($sql);
		if (!$result) {
			echo mysql_error();
			die("Database Error.");
		}
		$pageViews = 1;
	}

	// Insert day and ip into cms_visit
	$sql = 'SELECT day, ip
		FROM cms_visit
		WHERE day = "'.date('Y-m-d').'"';
	$result = mysql_query($sql);
	if (!$result) {
		die("Database Error!!");
	}
	$exists = 0;
	while($row = mysql_fetch_array($result)) {
		if (stripslashes($row['ip']) == $_SERVER['REMOTE_ADDR']) {
			$exists = 1;
			break;
		}
	}
	if ((!$exists) && ($_SERVER['REMOTE_ADDR'] != "")) {
		$sql = 'INSERT INTO cms_visit(day, ip)
			VALUES ( "'.date('Y-m-d').'", "'.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'" )';
		$result = mysql_query($sql);
		if (!$result) {
			die("Query Error!");
		}
	}

	$sql = 'SELECT count(ip) AS count, day
		FROM cms_visit
		GROUP BY day
		HAVING day = "'.date('Y-m-d').'"';
	$result = mysql_query($sql);
	if ($result) {
		$row = mysql_fetch_array($result);
		if ($row) {
			echo $row['count']."/".$pageViews;
		} else {
			echo "0/".$pageViews;
		}
	} else {
		echo "DBError";
	}
?>