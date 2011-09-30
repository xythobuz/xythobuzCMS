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
<a href="admin/addpage.php">Add Page</a><br>
<a href="admin/editpage.php">Edit Page</a><br>
<a href="admin/deletepage.php">Delete Page</a><br>
<a href="admin/addnews.php">Add News</a><br>
<a href="admin/editnews.php">Edit News</a><br>
<a href="admin/deletenews.php">Delete News</a><br>
<a href="admin/deletecomment.php">Manage Comments</a><br>
<a href="admin/users.php">Manage Users</a><br>
<a href="admin/addlink.php">Add Link</a><br>
<a href="admin/editlink.php">Edit Links</a><br>
<hr>
<? if (isset($xythobuzCMS_piwiktoken)) { ?>
<a href="/piwik/">Piwik Statistics</a><br>
<? }

	// Update sitemap
	$sitemap = "<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\nxmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\nxsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\nhttp://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n\n<url>\n\t<loc>http://www.xythobuz.org/index.php</loc>\n</url>\n<url>\n\t<loc>http://www.xythobuz.org/news.php</loc>\n</url>\n\n";
	$sql = 'SELECT
				kuerzel
			FROM
				cms
			ORDER BY
				id';
	$result = mysql_query($sql);
	if (!$result) {
		die ('Query-Error!');
	}
	while ($row = mysql_fetch_array($result)) {
		$sitemap = $sitemap."<url>\n\t<loc>";
		$sitemap = $sitemap."http://www.xythobuz.org/index.php?p=";
		$sitemap = $sitemap.$row['kuerzel'];
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
	$rss = $rss.'<rss version="2.0">'."\n";
	$rss = $rss."\t<channel>\n\t\t<title>xythobuz.org Blog</title>\n";
	$rss = $rss."\t\t<link>http://www.xythobuz.org/news.php</link>\n";
	$rss = $rss."\t\t<description>xythobuz.org - Artikel und Links von xythobuz</description>\n";
	$rss = $rss."\t\t<language>de-de</language>\n";
	$rss = $rss."\t\t<generator>xythobuz.org CMS</generator>\n";
	while ($row = mysql_fetch_array($result)) {
		$rss = $rss."\t\t<item>\n";
		$rss = $rss."\t\t\t<title>".str_replace('>', '&gt;', str_replace('<', '&lt;', stripslashes($row['ueberschrift'])))."</title>\n";

		$rss = $rss."\t\t\t<link>http://www.xythobuz.org/news.php?beitrag=".$row['id']."</link>\n";

		$rss = $rss."\t\t\t<description>".str_replace('>', '&gt;', str_replace('<', '&lt;', stripslashes($row['inhalt'])))."</description>\n";
		$rss = $rss."\t\t\t<pubDate>".date(DATE_RSS, strtotime($row['datum']))."</pubDate>\n";
		$rss = $rss."\t\t\t<guid>http://www.xythobuz.org/news.php?beitrag=".$row['id']."</guid>\n";
		$rss = $rss."\t\t</item>\n";
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

mysql_close($db);
?>
<hr>
<a href="logout.php">Logout</a><br>
<a href="index.php">Home</a><br>
<a href="admin.php">Admin</a><br>
</div>
</body>
</html>
