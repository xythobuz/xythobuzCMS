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
<title>xythobuz.org - Edit page</title>
</head>
<body>
<div class="admin">
<h1>Edit Page</h1>
<?

if ( (!isset($_POST['id'])) && (!isset($_GET['id'])) ) {
?>
<p>Select Page:</p>
<form action="editpage.php" method="post">
	<select name="id">
<?
	$sql = 'SELECT
		id,
		linktext
	FROM
		cms
	ORDER BY
		id';
	$result = mysql_query($sql);
	if (!$result) {
		echo "Querry Error!";
		exit;
	}
	while ($row = mysql_fetch_array($result)) {
		echo '<option value="';
		echo $row['id'];
		echo '">';
		echo $row['linktext'];
		echo "</option>\n";
	}
?>
	</select>
	<input type="submit" name="formaction" value="Edit" />
</form>
<?
} else if (isset($_POST['id'])) {
	$sql = 'SELECT
		kuerzel,
		linktext,
		beschreibung,
		ord,
		inhalt,
		inhalt_en,
		nolink
	FROM
		cms
	WHERE id = '.mysql_real_escape_string($_POST['id']);
	$result = mysql_query($sql);
	if (!$result) {
		echo "Query-Error";
		exit;
	}
	$row = mysql_fetch_array($result);
?>
	<form action="editpage.php?id=<? echo $_POST['id']; ?>" method="post">
	<fieldset>
		<label>Linktext: <input type="text" name="link" value="<?
			echo $row['linktext'];
?>"></label><br>
		<label>Beschreibung: <input type="text" name="desc" value="<?
			echo $row['beschreibung'];
?>"></label><br>
		<label>Kategorie: <select name="kat">
			<option value="0">0 -&gt; Root</option>
			<?
	$sql = 'SELECT
		id, linktext, kategorie
	FROM
		cms
	WHERE
		id = '.mysql_real_escape_string($_POST['id']);
	$result = mysql_query($sql);
	if (!$result) {
		echo "Query Error!";
		exit;
	}
	$row2 = mysql_fetch_array($result);
	$parentSelected = $row2['kategorie'];

	$sql = 'SELECT
		id, linktext
	FROM
		cms';
	$result = mysql_query($sql);
	if (!$result) {
		echo "Query Error!";
		exit;
	}
	while ($row2 = mysql_fetch_array($result)) {
		if ($row2['id'] != $_POST['id']) {
			echo '<option value="';
			echo $row2['id'];
			echo '"';
			if ($row2['id'] == $parentSelected) {
				echo " selected";
			}
			echo '>';
			// echo $row2['id'];
			// echo " -&gt; ";
			echo $row2['linktext'];
			echo "</option>\n";
		}
	}
?>
		</select></label><br>
		<label>Order: <input type="text" name="ord" value="<?
			echo $row['ord'];
?>"></label><br>
		<? if ($row['nolink'] == 0) { ?>
		<label>Unclickable: <input type="checkbox" name="click" value="true"></label><br>
		<? } else { ?>
		<label>Unclickable: <input type="checkbox" name="click" value="true" checked="checked"></label><br>
		<? } ?>
		<textarea name="content" rows="20" cols="68"><?
			echo htmlspecialchars(stripslashes($row['inhalt']));
?></textarea><br>
		<textarea name="content_en" rows="20" cols="68"><?
			echo htmlspecialchars(stripslashes($row['inhalt_en']));
?></textarea><br>
		<input type="submit" name="formaction" value="Save" />
	</fieldset>
</form>
<?
} else {
	if ( ($_POST['link'] == "") || ($_POST['desc'] == "") || ($_POST['kat'] == $_GET['id']) || ($_POST['ord'] == "") || ($_POST['content'] == "") || ($_POST['content_en'] == "") ) {
		echo "Empty Field!\n";
		exit;
	}
	$link = mysql_real_escape_string($_POST['link']);
	$desc = mysql_real_escape_string($_POST['desc']);
	$kat = mysql_real_escape_string($_POST['kat']);
	$ord = mysql_real_escape_string($_POST['ord']);
	$content = mysql_real_escape_string($_POST['content']);
	$content_en = mysql_real_escape_string($_POST['content_en']);
	$id = mysql_real_escape_string($_GET['id']);
	if ($_POST['click'] == "true") {
		$click = "1";
	} else {
		$click = "0";
	}

	$sql = 'UPDATE
		cms
	SET
		linktext = "'.$link.'",
		beschreibung = "'.$desc.'",
		kategorie = '.$kat.',
		ord = '.$ord.',
		inhalt = "'.$content.'",
		inhalt_en = "'.$content_en.'",
		nolink = '.$click.'
	WHERE
		id = '.mysql_real_escape_string($_GET['id']);
	$result = mysql_query($sql);
	if (!$result) {
		echo "Query Error. Nothing updated!";
		exit;
	}
	echo "Edited ";
	echo $link;
	echo "successfully!";
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
