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
<title>xythobuz.org - Delete Page</title>
</head>
<body>
<div class="admin">
<h1>Delete a page</h1>
<?

if (!isset($_GET['w'])) {
	$sql = 'SELECT
		id,
		kuerzel,
		linktext,
		beschreibung,
		kategorie,
		ord
	FROM
		cms
	WHERE
		kategorie = 0
	ORDER BY
		ord';
	$result = mysql_query($sql);
	if (!$result) {
		die ('Query-Error!');
	}
?>
<table border="1">
<tr><th>ID</th>
<th>Kürzel</th>
<th>LinkText</th>
<th>Beschreibung</th>
<th>Kategorie (Parent)</th>
<th>Order</th>
<th>Löschen?</th></tr>
<?
	while ($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td>".$row['id']."</td>\n";
		echo "<td>".$row['kuerzel']."</td>\n";
		echo "<td>".$row['linktext']."</td>\n";
		echo "<td>".$row['beschreibung']."</td>\n";
		echo "<td>".$row['kategorie']."</td>\n";
		echo "<td>".$row['ord']."</td>\n";
		echo '<td><a href="deletepage.php?w='.$row['kuerzel'].'">Löschen</a></td></tr>'."\n";

		$sql2 = 'SELECT id, kuerzel, linktext, beschreibung, kategorie, ord
		FROM cms
		WHERE kategorie = '.$row['id'].'
		ORDER BY ord';
		$result2 = mysql_query($sql2);
		if (!$result2) {
			die("Query Error!");
		}
		while ($row2 = mysql_fetch_array($result2)) {
			echo "<tr>";
			echo "<td>".$row2['id']."</td>\n";
			echo "<td>".$row2['kuerzel']."</td>\n";
			echo "<td>".$row2['linktext']."</td>\n";
			echo "<td>".$row2['beschreibung']."</td>\n";
			echo "<td>".$row2['kategorie']."</td>\n";
			echo "<td>".$row2['ord']."</td>\n";
			echo '<td><a href="deletepage.php?w='.$row2['kuerzel'].'">Löschen</a></td></tr>'."\n";
			$sql3 = 'SELECT id, kuerzel, linktext, beschreibung, kategorie, ord
			FROM cms
			WHERE kategorie = '.$row2['id'].'
			ORDER BY ord';
			$result3 = mysql_query($sql3);
			if (!$result3) {
				die("Query Error!");
			}

			while ($row3 = mysql_fetch_array($result3)) {
				echo "<tr>";
				echo "<td>".$row3['id']."</td>\n";
				echo "<td>".$row3['kuerzel']."</td>\n";
				echo "<td>".$row3['linktext']."</td>\n";
				echo "<td>".$row3['beschreibung']."</td>\n";
				echo "<td>".$row3['kategorie']."</td>\n";
				echo "<td>".$row3['ord']."</td>\n";
				echo '<td><a href="deletepage.php?w='.$row3['kuerzel'].'">Löschen</a></td></tr>'."\n";
			}
		}
	}
?>
</table>
<?
} else {
	$sql = 'DELETE FROM cms
		WHERE kuerzel = "'.$_GET['w'].'"';
	$result = mysql_query($sql);
	if (!$result) {
		print "Database delete failed! (4)<br>\n";
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
