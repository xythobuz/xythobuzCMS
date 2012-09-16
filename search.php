<?
include('config.php');
include('func.php');
header1();
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Konnte keine Verbindung zur Datenbank aufbauen');
}
body1();

echo "<h1>Suche</h1>\n";
if (!isset($_GET['term'])) {
?>
		<form action="search.php" method="get">
			<fieldset>
				<label>Suchbegriff: <input type="text" name="term"></label>
				<input type="submit" value="Suchen">
			</fieldset>
		</form>
<?
} else {
	$resultKuerzel = array();
	$resultLink = array();
	$resultDesc = array();
	$resultVal = array();
	$resultBlog = array();
	$i = 0;
	$sql = 'SELECT
		inhalt, kuerzel, linktext, beschreibung
	FROM
		cms';
	$result = mysql_query($sql);
	if (!$result) {
		die("Database Error");
	}
	while ($row = mysql_fetch_array($result)) {
		$res = substr_count($row['inhalt']." ".$row['beschreibung'], $_GET['term']);
		if ($res != 0) {
			$resultVal[$i] = $res;
			$resultBlog[$i] = 0;
			$resultKuerzel[$i] = $row['kuerzel'];
			$resultLink[$i] = $row['linktext'];
			$resultDesc[$i] = $row['beschreibung'];
			$i++;
		}
	}
	$sql = 'SELECT
		inhalt, id, ueberschrift, datum
	FROM
		cms_news';
	$result = mysql_query($sql);
	if (!$result) {
		die("Database Error");
	}
	while ($row = mysql_fetch_array($result)) {
		$res = substr_count($row['inhalt']." ".$row['ueberschrift'], $_GET['term']);
		if ($res != 0) {
			$resultVal[$i] = $res;
			$resultBlog[$i] = 1;
			$resultKuerzel[$i] = $row['id'];
			$resultLink[$i] = $row['ueberschrift'];
			$resultDesc[$i] = $row['datum'];
			$i++;
		}
	}

	if ($i != 0) {
		if ($i != 1) {
?>
		<p><? echo $i; ?> Ergebnisse gefunden.</p>
<?
		} else {
?>
		<p><? echo $i; ?> Ergebnis gefunden.</p>
<?
			
}
		$resultOrder = array();
		$max = 0;
		$k = 0;
		for ($j = 0; $j < $i; $j++) {
			if ($resultVal[$j] > $max) {
				$max = $resultVal[$j];
			}
		}
		while ($max > 0) {
			for ($j = 0; $j < $i; $j++) {
				if ($resultVal[$j] == $max) {
					$resultOrder[$k] = $j;
					$k++;
				}
			}
			$max--;
		}
		for($j = 0; $j < $i; $j++) {
			if ($resultBlog[$resultOrder[$j]] == 0) {
?>
		<h2><a href="index.php?p=<? echo $resultKuerzel[$resultOrder[$j]]; ?>"><? echo $resultLink[$resultOrder[$j]]; ?></a></h2>
		<p><? echo $resultDesc[$resultOrder[$j]]; ?></p>
		<hr>
<?
			} else {
?>
		<h2><a href="news.php?beitrag=<? echo $resultKuerzel[$resultOrder[$j]]; ?>"><? echo $resultLink[$resultOrder[$j]]; ?></a></h2>
		<p><? echo $resultDesc[$resultOrder[$j]]; ?></p>
		<hr>
<?
			}
		}
	} else {
?>
		<p>Nichts gefunden!</p>
<?
	}
}

?>
	</div>
	<nav>
<?
$sql = 'SELECT
	id,
	kuerzel,
	linktext,
	kategorie,
	ord
FROM
	cms
ORDER BY
	ord';
$result = mysql_query($sql);
if(!$result) {
	die ('Etwas stimmte mit dem Query nicht');
}
$rows = array();
$co = 0;
while ($row = mysql_fetch_array($result)) {
	$rows[$co] = $row;
	$co++;
}
echo "<ul class=\"navigation\">";
foreach ($rows as $i => $v) {
	if ($v['kategorie'] == 0) {
		if ($_GET['lang'] == "en") {
			printID($rows, $i, 0, "en");
		} else {
			printID($rows, $i, 0, "de");
		}
	}
}
echo "</ul>\n";
bottom1($_GET['lang']);
mysql_close();
bottom2();
?>
