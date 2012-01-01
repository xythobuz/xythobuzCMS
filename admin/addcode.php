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
<title>xythobuz.org - Add Code</title>
</head>
<body>
<div class="admin">
<h1>Add Code</h1>
<p>Insert some code before the body tag...</p>
<?

if ($_REQUEST['content'] == "") {
?>
<form action="addcode.php" method="post">
	<textarea name="content" rows="20" cols="68">Code here</textarea><br>
	<input type="submit" name="formaction" value="Add Link">
</form>
<?
} else {
	$sql = 'INSERT INTO
			cms_code(inhalt)
		VALUES
			( "'.mysql_real_escape_string($_REQUEST['content']).'" )';
	$result = mysql_query($sql);
	if (!$result) {
		die("Query Error!");
	}
	echo "Added Code!";
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
