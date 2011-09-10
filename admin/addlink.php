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
<title>xythobuz.org - Add Link</title>
</head>
<body>
<div class="admin">
<h1>Add Link</h1>
<?

if ($_POST['title'] == "") {
?>
<form action="addlink.php" method="post">
	<label>Title: <input type="text" name="title"></label><br>
	<label>URL: <input type="text" name="url"></label><br>
	<label>Order: <input type="text" name="order"></label><br>
	<input type="submit" name="formaction" value="Add Link">
</form>
<?
} else {
	$sql = 'INSERT INTO
			cms_links(title, url, ord)
		VALUES
			( "'.mysql_real_escape_string($_POST['title']).'",
			"'.mysql_real_escape_string($_POST['url']).'",
			'.mysql_real_escape_string($_POST['order']).' )';
	$result = mysql_query($sql);
	if (!$result) {
		die("Query Error!");
	}
	echo "Added Link!";
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
