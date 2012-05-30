<? include('auth.php');
include('config.php');
include('func.php');
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Konnte keine Verbindung zur Datenbank aufbauen');
}
header1();
?>
<title>xythobuz.org CMS Administration</title>
</head>
<body>
<div class="admin">
<h1>xythobuz.org CMS Admin-Area</h1>
<h2>Pages</h2>
<a href="admin/addpage.php">Add Page</a><br>
<a href="admin/editpage.php">Edit Page</a><br>
<a href="admin/deletepage.php">Delete Page</a><br>
<h2>Blog</h2>
<a href="admin/addnews.php">Add News</a><br>
<a href="admin/editnews.php">Edit News</a><br>
<a href="admin/deletenews.php">Delete News</a><br>
<a href="admin/deletecomment.php">Manage Comments</a><br>
<h2>Links</h2>
<a href="admin/addlink.php">Add Link</a><br>
<a href="admin/editlink.php">Edit Links</a><br>
<h2>Misc</h2>
<a href="admin/users.php">Manage Users</a><br>
<h2>Code</h2>
<a href="admin/addcodebody.php">Add Code Body</a><br>
<a href="admin/editcodebody.php">Edit Code Body</a><br>
<a href="admin/addcodenav.php">Add Code Nav</a><br>
<a href="admin/editcodenav.php">Edit Code Nav</a><br>
<a href="admin/addcodehead.php">Add Code Head</a><br>
<a href="admin/editcodehead.php">Edit Code Head</a><br>
<hr>
<?
	// Update sitemap
	$sitemap = "<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\nxmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\nxsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\nhttp://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n\n<url>\n\t<loc>".$xythobuzCMS_root."/index.php</loc>\n</url>\n<url>\n\t<loc>".$xythobuzCMS_root."/news.php</loc>\n</url>\n\n";
	$sql = 'SELECT
				kuerzel
			FROM
				cms
			WHERE NOT
				nolink = 1
			ORDER BY
				id';
	$result = mysql_query($sql);
	if (!$result) {
		die ('Query-Error!');
	}
	while ($row = mysql_fetch_array($result)) {
		$sitemap = $sitemap."<url>\n\t<loc>";
		$sitemap = $sitemap.$xythobuzCMS_root."/index.php?p=";
		$sitemap = $sitemap.$row['kuerzel'];
		$sitemap = $sitemap."</loc>\n</url>\n\n";

		$sitemap = $sitemap."<url>\n\t<loc>";
		$sitemap = $sitemap.$xythobuzCMS_root."/index.php?p=";
		$sitemap = $sitemap.$row['kuerzel'];
		$sitemap = $sitemap."&amp;lang=".$xythobuzCMS_lang2;
		$sitemap = $sitemap."</loc>\n</url>\n\n";
	}
	$sitemap = $sitemap."</urlset>";
	$path = 'sitemap.xml';
	if(!file_exists($path)) {
		if (touch($path)) {
			print "Created $path...<br>\n";
		} else {
			print "Could not create $path...<br>\n";
			exit;
		}
	}
	if (!$handle = fopen($path, "w")) {
		print "Kann $path nicht öffnen!<br>\n";
		exit;
	}
	if (!fwrite($handle, $sitemap)) {
		print "Kann nicht in $path schreiben!<br>\n";
		exit;
	}
	fclose($handle);
	print "Saved $path...<br>\n";

	
	// Recreate RSS Feed
	$sql = 'SELECT
				datum,
				ueberschrift,
				inhalt,
				id
			FROM
				cms_news
			ORDER BY
				datum DESC';
	$result = mysql_query($sql);
	if (!$result) {
		die ("Error!!");
	}
	$rss = '<?xml version="1.0"?>'."\n";
	$rss = $rss.'<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
	$rss = $rss."\t<channel>\n\t\t<atom:link href=\"".$xythobuzCMS_root."/rss.xml\" rel=\"self\" type=\"application/rss+xml\" />\n\t\t<title>$xythobuzCMS_root Blog</title>\n";
	$rss = $rss."\t\t<link>".$xythobuzCMS_root."/news.php</link>\n";
	$rss = $rss."\t\t<description>".$xythobuzCMS_root.". - Artikel und Links</description>\n";
	$rss = $rss."\t\t<language>de-de</language>\n";
	$rss = $rss."\t\t<generator>xythobuz.org CMS</generator>\n";
	$rss = $rss."\t\t<ttl>60</ttl>\n";
	// $count = 0;
	while ($row = mysql_fetch_array($result)) {
		/* if ($count >= 5) {
			break;
		} */
		$rss = $rss."\t\t<item>\n";
		$rss = $rss."\t\t\t<title>".str_replace('>', '&gt;', str_replace('<', '&lt;', stripslashes($row['ueberschrift'])))."</title>\n";

		$rss = $rss."\t\t\t<link>".$xythobuzCMS_root."/news.php?beitrag=".$row['id']."</link>\n";

		$rss = $rss."\t\t\t<description>".str_replace("&lt;a href=\"img", "&lt;a href=\"".$xythobuzCMS_root."/img", str_replace("&lt;a href=\"/", "&lt;a href=\"".$xythobuzCMS_root."/", str_replace("src=\"img", "src=\"".$xythobuzCMS_root."/img", str_replace("src=\"/", "src=\"".$xythobuzCMS_root."/", str_replace('>', '&gt;', str_replace("<", "&lt;", str_replace('&', '&amp;', stripslashes($row['inhalt']))))))))."</description>\n";
		$rss = $rss."\t\t\t<pubDate>".date(DATE_RSS, strtotime($row['datum']))."</pubDate>\n";
		$rss = $rss."\t\t\t<guid>".$xythobuzCMS_root."/news.php?beitrag=".$row['id']."</guid>\n";
		$rss = $rss."\t\t</item>\n";
		// $count++;
	}
	$rss = $rss."\t</channel>\n</rss>";

	$path = 'rss.xml';
	if(!file_exists($path)) {
		if (touch($path)) {
			print "Created $path...<br>\n";
		} else {
			print "Could not create $path...<br>\n";
			exit;
		}
	}
	if (!$handle = fopen($path, "w")) {
		print "Could not open $path<br>\n";
		exit;
	}
	if (!fwrite($handle, $rss)) {
		print "Could not write to $path<br>\n";
		exit;
	}
	fclose($handle);
	print "Saved $path...<br>\n";
?>
<hr>
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
		// Top google abfragen => Tabelle
		// Andere referers
		// Löschen button => admin.php?clean

		$i = 0;
		while ($row = mysql_fetch_array($result)) {
			if ($i == 0) {
				echo '<table border="1">';
				echo "<tr><th>Referer</th><th>Date</th><th>Target</th></tr>";
			}
			$i++;
			$ref = stripslashes($row['referer']);
			if (strlen($ref) > 40) {
				$refText = substr($ref, 0, 40)."...";
			} else {
				$refText = $ref;
			}
			echo "<tr><td><a href=\"".$ref."\">".$refText."</a></td>";
			echo "<td>".$row['datum']."</td>";
			echo "<td><a href=\"".$xythobuzCMS_root.$row['ziel']."\">".$row['ziel']."</a></td></tr>";
		}
		if ($i == 0) {
			echo "There are no recorded referes!";
		} else {
			echo "</table>";
			echo '<br><form action="admin.php" method="get">';
			echo '<input type="submit" name="clean" value="Clear" />';
			echo '</form>';
		}
	} else {
		echo "Could not get referers (".mysql_error($result).")!";
	}
} else {
	// Delete old referers
	$sql = 'DELETE FROM cms_referer';
	$result = mysql_query($sql);
	if (!$result) {
		echo "Could not delete referers (".mysql_error($result).")!";
	} else {
		echo "Cleared referers!";
		echo "<br><a href=\"admin.php\">OK!</a>";
	}
}
?>
<hr>
<a href="logout.php">Logout</a><br>
<a href="index.php">Home</a><br>
<a href="admin.php">Admin</a><br>
</div>
</body>
</html>
<? mysql_close($db); ?>