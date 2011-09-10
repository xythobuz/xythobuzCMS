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
<title>xythobuz.org - Add News</title>
</head>
<body>
<div class="admin">
<?

if (!isset($_POST['ueber'])) {
?>
<h1>Add News</h1>
<form action="addnews.php" method="post">
	<fieldset>
		<label>Überschrift: <input type="text" name="ueber"></label><br>
		<textarea name="content" rows="20" cols="68"></textarea><br>
		<input type="submit" name="formaction" value="Add" />
	</fieldset>
</form>
<?
} else {
	$sql = 'INSERT INTO
		cms_news(datum, ueberschrift, inhalt)
	VALUES
		(FROM_UNIXTIME('.time().'),
		"'.mysql_real_escape_string($_POST['ueber']).'",
		"'.mysql_real_escape_string($_POST['content']).'")';
	$result = mysql_query($sql);
	if (!$result) {
		echo "Error!";
		exit;
	}
	echo "News added!";
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
