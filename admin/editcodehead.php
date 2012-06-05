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
<title>xythobuz.org - Edit Code</title>
</head>
<body>
<div class="admin">
<h1>Edit Code in &lt;head&gt;</h1>
<?

if ($_GET['d'] == "") {
	$sql = 'SELECT
		inhalt,
		id
	FROM
		cms_codehead';
	$result = mysql_query($sql);
	if (!$result) {
		die ("Query Error");
	}
?>
<table border="1">
<tr><th>Content</th>
<th>Delete</th></tr>
<?
	while ($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td>".htmlspecialchars(stripslashes($row['inhalt']))."</td>";
		echo "<td><a href=\"editcodehead.php?d=".$row['id']."\">Delete</a></td>";
		echo "</tr>";
	}
} else {
	$sql = 'DELETE FROM cms_codehead
		WHERE id = '.mysql_real_escape_string($_GET['d']);
	$result = mysql_query($sql);
	if (!$result) {
		die ("Query Error.");
	}
	echo "Deleted...";
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
