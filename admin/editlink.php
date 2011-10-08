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
<title>xythobuz.org - Edit Link</title>
</head>
<body>
<div class="admin">
<h1>Edit Link</h1>
<?

if (($_GET['w'] == "") && ($_GET['d'] == "")) {
	$sql = 'SELECT
		ord,
		title,
		url,
		id
	FROM
		cms_links
	ORDER BY
		ord ASC';
	$result = mysql_query($sql);
	if (!$result) {
		die ("Query Error");
	}
?>
<table border="1">
<tr><th>Order</th>
<th>Title</th>
<th>URL</th>
<th>Save</th>
<th>Delete</th></tr>
<?
	while ($row = mysql_fetch_array($result)) {
		echo "<form action=\"editlink.php?w=";
		echo $row['id']."\" method=\"post\">";
		echo "<tr>";
		echo "<td><input type=\"text\" name=\"order\" value=\"".stripslashes($row['ord'])."\"></td>";
		echo "<td><input type=\"text\" name=\"titel\" value=\"".stripslashes($row['title'])."\"></td>";
		echo "<td><input type\"text\" name=\"link\" value=\"".stripslashes($row['url'])."\"></td>";
		echo "<td><input type=\"submit\" name=\"formaction\" value=\"Save\"></td>\n";
		echo "<td><a href=\"editlink.php?d=".$row[id]."\">Delete</a></td>";
		echo "</tr></form>";
	}
} else if ($_GET['w'] != "") {
	$sql = 'UPDATE
		cms_links
	SET
		url = "'.mysql_real_escape_string($_POST['link']).'",
		title = "'.mysql_real_escape_string($_POST['titel']).'",
		ord = '.mysql_real_escape_string($_POST['order']).'
	WHERE
		id = '.mysql_real_escape_string($_GET['w']);
	$result = mysql_query($sql);
	if (!$result) {
		die ("Query Error!");
	}
	echo "Edited!";
} else if ($_GET['d'] != ""){
	$sql = 'DELETE FROM cms_links
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
