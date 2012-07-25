<?
	if (basename($_SERVER['PHP_SELF']) == "count.php") {
		include('config.php');
		$db = mysql_connect($sql_host, $sql_username, $sql_password);
		mysql_select_db($sql_database);
		if (mysql_errno()) {
			die ('Konnte keine Verbindung zur Datenbank aufbauen');
		}
	}

	$pageViews = 1;

	// Log referer if desired
	$tmp = basename($_SERVER['PHP_SELF']);
	if (($tmp == "news.php") || ($tmp == "index.php")) {
		if (isset($_SERVER['HTTP_REFERER'])) {
			// Build name of page visited
			$target = $_SERVER['PHP_SELF'];
			if ($_SERVER['QUERY_STRING'] != "") {
				$target = $target.'?'.$_SERVER['QUERY_STRING'];
			}
			// Log referer
			$sql = 'INSERT INTO
				cms_referer(datum, referer, ziel)
			VALUES
				(FROM_UNIXTIME('.time().'),
				"'.mysql_real_escape_string($_SERVER['HTTP_REFERER']).'",
				"'.mysql_real_escape_string($target).'")';
			$result = mysql_query($sql);
		}
	}

	// Detect a bot/crawler/spider whatever you want to call it
	$search = array("bot", "spider", "crawler", "search", "archive");
	$pattern = '/('.implode('|', $search).')/';
	if ((isset($_SERVER['HTTP_USER_AGENT'])) && (preg_match($pattern, strtolower($_SERVER['HTTP_USER_AGENT']))) > 0) {
		// Log bot
		$sql = 'SELECT day, bots
			FROM cms_bots
			WHERE day = "'.date('Y-m-d').'"';
		$result = mysql_query($sql);
		if (!$result) {
			die("Database Error!");
		}
		$row = mysql_fetch_array($result);
		if ($row) {
			// Theres a record for this day
			$count = $row['bots'];
			$count++;
			$sql = 'UPDATE cms_bots
			SET
				bots = '.$count.'
			WHERE
				day = "'.date('Y-m-d').'"';
			$result = mysql_query($sql);
			if (!$result) {
				die("Database Error");
			}
		} else {
			// Create new record
			$sql = 'INSERT INTO cms_bots(day, bots)
				VALUES ( "'.date('Y-m-d').'", 1 )';
			$result = mysql_query($sql);
			if (!$result) {
				echo mysql_error();
				die("Database Error.");
			}
		}
	} else {
		// Increment pageview count
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
		// --> Log visitor
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
	}

	// Print todays visitors
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
