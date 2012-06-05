<? include('../auth.php');
include('../config.php');
include('../func.php');
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Konnte keine Verbindung zur Datenbank aufbauen');
}
header1();
?>
<title>xythobuz.org - Delete News</title>
</head>
<body>
<div class="admin">
<h1>Delete News</h1>
<?

if (!isset($_GET['w'])) {
	$sql = 'SELECT
		id,
		ueberschrift
	FROM
		cms_news
	ORDER BY
		id';
	$result = mysql_query($sql);
	if (!$result) {
		die ('Query-Error!');
	}
?>
<table border="1">
<tr><th>ID</th>
<th>Überschrift</th>
<th>Löschen?</th></tr>
<?
	while ($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td>".$row['id']."</td>\n";
		echo "<td>".$row['ueberschrift']."</td>\n";
		echo '<td><a href="deletenews.php?w='.$row['id'].'">Löschen</a></td></tr>'."\n";
	}
?>
</table>
<?
} else {
	$sql = 'DELETE FROM cms_news
		WHERE id = '.mysql_real_escape_string($_GET['w']).'';
	$result = mysql_query($sql);
	if (!$result) {
		print "Database delete failed! (42)<br>\n";
		exit;
	}
	print "Deleted Database Entry...<br>\n";
}

mysql_close($db);
?>
<hr>
<a href="<? echo $xythobuzCMS_root; ?>/logout.php">Logout</a><br>
<a href="<? echo $xythobuzCMS_root; ?>/index.php">Home</a><br>
<a href="<? echo $xythobuzCMS_root; ?>/admin.php">Admin</a><br>
</div>
</body>
</html>
