<?
if (basename($_SERVER['PHP_SELF']) == "stats.php") {
	// Script is called on its own, establish connection and print html head
	include('config.php');
	$db = mysql_connect($sql_host, $sql_username, $sql_password);
	mysql_select_db($sql_database);
	if (mysql_errno()) {
		die ('Konnte keine Verbindung zur Datenbank aufbauen');
	}

	$renderVisitors = 0;
	$renderBots = 0;
	if (isset($_GET['img'])) {
		include("render.php");
		exit;
	} else if (isset($_GET['imgB'])) {
		$renderVisitors = 1;
		include("render.php");
		exit;
	} else if (isset($_GET['imgC'])) {
		$renderBots = 1;
		include("render.php");
		exit;
	}
	
	include('func.php');
	header1();
?>
<title>xythobuzCMS Statistics</title>
</head>
<body>
<div class="admin">
<h1>xythobuzCMS Statistics</h1>
<p>The left diagram shows the Pageviews this month,
the right diagram shows the Visitors this month.
Below, you see the referers of the visitors.
Google search terms are listed separately, as are internal and external links.</p>
<hr>
<?
}
?>
<div style="float: left; width: 33%;">
<h3>Pageviews</h3>
<a href="stats.php?img&amp;w=600"><img src="stats.php?img&amp;w=200" alt="Pageviews this Month"></a>
<?
	$sql = 'SELECT day, visitors FROM cms_visitors WHERE MONTH(day) = '.date('m');
	$result = mysql_query($sql);
	if ($result) {
		$viewsThisMonth = 0;
		while($row = mysql_fetch_array($result)) {
			$viewsThisMonth += $row['visitors'];
		}
	} else {
		$viewsThisMonth = 0;
	}
	if ($viewsThisMonth != 0)
		echo "<p>".$viewsThisMonth." this month</p>";

	$sql = 'SELECT day, visitors FROM cms_visitors WHERE MONTH(day) = '.date('m')-1;
	$result = mysql_query($sql);
	if ($result) {
		$viewsLastMonth = 0;
		while($row = mysql_fetch_array($result)) {
			$viewsLastMonth += $row['visitors'];
		}
	} else {
		$viewsLastMonth = 0;
	}
	if ($viewsLastMonth != 0)
		echo "<p>".$viewsLastMonth." last month</p>";

	$sql = 'SELECT day, visitors FROM cms_visitors';
	$result = mysql_query($sql);
	if ($result) {
		$views = 0;
		while($row = mysql_fetch_array($result)) {
			$views += $row['visitors'];
		}
	} else {
		$views = 0;
	}
	if ($views != 0)
	echo "<p>".$views." overall</p>";
?>
</div>

<div style="float: left; width: 33%;">
<h3>Visitors</h3>
<a href="stats.php?imgB&amp;w=600"><img src="stats.php?imgB&amp;w=200" alt="Visitors this Month"></a>
<?
	$sql = 'SELECT count(ip) AS count, day
		FROM cms_visit
		GROUP BY day
		HAVING MONTH(day) = '.date('m').'
		ORDER BY day ASC';
	$result = mysql_query($sql);
	if ($result) {
		$visitsThisMonth = 0;
		while($row = mysql_fetch_array($result)) {
			$visitsThisMonth += $row['count'];
		}
	} else {
		$visitsThisMonth = 0;
	}
	if ($visitsThisMonth != 0)
		echo "<p>".$visitsThisMonth." this month</p>";

	$sql = 'SELECT count(ip) AS count, day
		FROM cms_visit
		GROUP BY day
		HAVING MONTH(day) = '.date('m') - 1;
	$result = mysql_query($sql);
	if ($result) {
		$visitsLastMonth = 0;
		while($row = mysql_fetch_array($result)) {
			$visitsLastMonth += $row['count'];
		}
	} else {
		$visitsLastMonth = 0;
	}
	if ($visitsLastMonth != 0)
		echo "<p>".$visitsLastMonth." last month</p>";

	$sql = 'SELECT count(ip) AS count, day
		FROM cms_visit
		GROUP BY day';
	$result = mysql_query($sql);
	if ($result) {
		$visits = 0;
		while($row = mysql_fetch_array($result)) {
			$visits += $row['count'];
		}
	} else {
		$visits = 0;
	}
	if ($visits != 0)
		echo "<p>".$visits." overall</p>";
?>
</div>

<div style="float: left; width: 33%;">
<h3>Bots</h3>
<a href="stats.php?imgC&amp;w=600"><img src="stats.php?imgC&amp;w=200" alt="Bots this Month"></a>
<?
	$sql = 'SELECT bots AS count, day
		FROM cms_bots
		GROUP BY day
		HAVING MONTH(day) = '.date('m').'
		ORDER BY day ASC';
	$result = mysql_query($sql);
	if ($result) {
		$visitsThisMonth = 0;
		while($row = mysql_fetch_array($result)) {
			$visitsThisMonth += $row['count'];
		}
	} else {
		$visitsThisMonth = 0;
	}
	if ($visitsThisMonth != 0)
		echo "<p>".$visitsThisMonth." this month</p>";

	$sql = 'SELECT bots AS count, day
		FROM cms_bots
		GROUP BY day
		HAVING MONTH(day) = '.date('m') - 1;
	$result = mysql_query($sql);
	if ($result) {
		$visitsLastMonth = 0;
		while($row = mysql_fetch_array($result)) {
			$visitsLastMonth += $row['count'];
		}
	} else {
		$visitsLastMonth = 0;
	}
	if ($visitsLastMonth != 0)
		echo "<p>".$visitsLastMonth." last month</p>";

	$sql = 'SELECT bots AS count, day
		FROM cms_bots
		GROUP BY day';
	$result = mysql_query($sql);
	if ($result) {
		$visits = 0;
		while($row = mysql_fetch_array($result)) {
			$visits += $row['count'];
		}
	} else {
		$visits = 0;
	}
	if ($visits != 0)
		echo "<p>".$visits." overall</p>";
?>
</div>
<?
	if (($viewsThisMonth != 0) && ($visitsThisMonth != 0))
		echo "<p>~".floor($viewsThisMonth / $visitsThisMonth)." Views / Visitor this month</p>";
	if (($viewsLastMonth != 0) && ($visitsLastMonth != 0))
		echo "<p>~".floor($viewsLastMonth / $visitsLastMonth)." Views / Visitor last month</p>";
	if (($views != 0) && ($visits != 0))
		echo "<p>~".floor($views / $visits)." Views / Visitor overall</p>";
?>
<div style="clear: left;">
<p>
</div>
<hr>
<h2>Referers</h2>
<?
if (!isset($_GET['clean'])) {
	// Get referers
	$sql = 'SELECT
		datum, referer, ziel
	FROM
		cms_referer
	ORDER BY
		datum DESC';
	$result = mysql_query($sql);
	if ($result) {
		$i = 0; // Count to see if there are any referes
		$g = 0; // Index for googleTerm
		$googleTerm = array(); // Store google search terms
		$googleLink = array(); // Store full google link
		$int = 0; // Index for internalLinks
		$internalLinks = array();
		$o = 0; // Index for otherLinks
		$otherLinks = array(); // Store other links (index: o)
		while ($row = mysql_fetch_array($result)) {
			$ref = $row['referer'];
			if (eregi('google\.', $ref)) {
				// Google visitor
				$googleLink[$g] = $ref;
				$ref = $ref.'&';
				preg_match('/q=(.*)&/UiS', $ref, $googleTerm[$g]); // Store search term
				$googleTerm[$g] = $googleTerm[$g][1]; // Thats the search term
				$g++;
			} else if (eregi(str_ireplace('www.', '', parse_url($xythobuzCMS_root, PHP_URL_HOST)), $ref)) {
				// Internal link
				$internalLinks[$int++] = $ref;
			} else {
				// Other link. Save it
				$otherLinks[$o++] = $ref;
			}
			$i++;
		}
		if ($i == 0) {
			echo "There are no recorded referers. Maybe the list was cleared not long ago?";
		} else {
			// Show stats
			if (count($googleTerm) > 0) {
				// We have google referers. Show them
				echo "<div style=\"float: left; width: 33%;\">";
				echo "<table style=\"width: 100%;\" border=\"1\"><tr><th>Google search term</th></tr>";
				foreach ($googleTerm as $key => $term) {
					$link = $googleLink[$key];
					if (eregi('url?', $link)) {
						$link = str_replace("url?", "#", $link);
					}
					if (urldecode($term) != "") {
						echo "<tr><td><a href=\"".$link."\">".urldecode($term)."</a></td></tr>";
					}
				}
				echo "</table></div>\n";
			}
			if (count($otherLinks) > 0) {
				// We got other links. Show them
				echo "<div style=\"float: left; width: 33%;\">";
				echo "<table style=\"width: 100%;\" border=\"1\"><tr><th>External link</th></tr>";
				foreach ($otherLinks as $link) {
					echo "<tr><td>";
					echo '<a href="'.$link.'">'.str_ireplace('www.', '', parse_url($link, PHP_URL_HOST)).'</a>';
					echo "</td></tr>";
				}
				echo "</table></div>\n";
			}
			if (count($internalLinks) > 0) {
				// We got internal links. Show them
				echo "<div style=\"float: left; width: 33%; align: center;\">";
				echo "<table style=\"width: 100%;\" border=\"1\"><tr><th>Internal link</th></tr>";
				foreach ($internalLinks as $link) {
					echo "<tr><td>";
					echo '<a href="'.$link.'">'.str_ireplace($xythobuzCMS_root, '', str_ireplace("www.", "", $link)).'</a>';
					echo "</td></tr>";
				}
				echo "</table></div>\n";
			}

			// Clear Button
			echo '<div style="clear: left;">';
			if (basename($_SERVER['PHP_SELF']) == "admin.php") {
				echo '<form action="admin.php" method="get">';
				echo '<input type="submit" name="clean" value="Clear" />';
				echo '</form>';
			}
			echo "</div>";
		}
	} else {
		echo "Could not get referers (".mysql_error($result).")!";
	}
} else {
	// Delete old referers
	include("auth.php"); // Requires authentication!
	$sql = 'DELETE FROM cms_referer';
	$result = mysql_query($sql);
	if (!$result) {
		echo "Could not delete referers (".mysql_error($result).")!";
	} else {
		echo "Cleared referers!";
		echo "<br><a href=\"admin.php\">OK!</a>";
	}
}

if (basename($_SERVER['PHP_SELF']) == "stats.php") {
?>
<hr>
<a href="index.php">Home</a>
</div>
</body>
</html>
<?
	mysql_close($db);
}
