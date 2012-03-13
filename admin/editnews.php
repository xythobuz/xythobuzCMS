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
<title>xythobuz.org - Edit News</title>
</head>
<body>
<div class="admin">
<?

if (!isset($_GET['w'])) {
	$sql = 'SELECT
		id,
		ueberschrift
	FROM
		cms_news
	ORDER BY
		id DESC';
	$result = mysql_query($sql);
	if (!$result) {
		die ('Query-Error!');
	}
?>
<table border="1">
<tr><th>ID</th>
<th>Heading</th>
<th>Edit?</th></tr>
<?
	while ($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td>".stripslashes($row['id'])."</td>\n";
		echo "<td>".stripslashes($row['ueberschrift'])."</td>\n";
		echo '<td><a href="editnews.php?w='.$row['id'].'">Edit</a></td></tr>'."\n";
	}
?>
</table>
<?
} else {
	if (!isset($_POST['inhalt'])) {
		// Show edit form
		$sql = 'SELECT
			ueberschrift,
			inhalt
		FROM cms_news
		WHERE id = '.stripslashes($_GET[w]);
		$result = mysql_query($sql);
		if (!$result) {
			die ('Could not read table cms_news');
		}
		$row = mysql_fetch_array($result);
?>
<form action="editnews.php?w=<? echo $_GET['w']; ?>" method="post">
	<fieldset>
		<label>Heading: <input type="text" name="head" value="<?
			echo $row['ueberschrift'];
?>"></label><br>
		<textarea name="inhalt" rows="20" cols="68"><?
			echo stripslashes($row['inhalt']);
?></textarea><br>
		<input type="submit" name="formaction" value="Save" />
	</fieldset>
</form>
<?
	} else {
		$sql = 'UPDATE
			cms_news
		SET
			ueberschrift = "'.mysql_real_escape_string($_POST['head']).'",
			inhalt = "'.mysql_real_escape_string($_POST['inhalt']).'"
		WHERE id = '.mysql_real_escape_string($_GET['w']);
		$result = mysql_query($sql);
		if (!$result) {
			echo mysql_error();
			die ("Could not update cms_news");
		}
		echo "Updated successfully!";
	}
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
