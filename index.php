<?
include('sql.php');
include('func.php');
header1();
?>
<meta name="description" content="<?
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Konnte keine Verbindung zur Datenbank aufbauen');
}
if (!isset($_GET['p'])) {
	$_GET['p'] = "home";
}
$sql = 'SELECT
	beschreibung
FROM
	cms
WHERE
	kuerzel = "'.$_GET['p'].'"';
$result = mysql_query($sql);
if (!$result) {
    die ('Etwas stimmte mit dem Query nicht!');
}

$row = mysql_fetch_array($result);
echo $row['beschreibung'];

?>">
<?
body2();
if ($_GET['lang'] != "en") {
	$langs = array( 'de', 'de-de', 'de-at', 'de-ch', 'en', 'en-us', 'en-uk' );
	$bestlang = prefered_language($langs);
	if (($bestlang == "en") || ($bestlang == "en-us") || ($bestlang == "en-uk")) {
		?><div class="bar"><?
		echo "<a href=\"index.php";
		if (isset($_GET['p'])) {
			echo "?p=".$_GET['p']."&amp;lang=en";
		} else {
			echo "?lang=en";
		}
		echo "\">This page could also be available in english!</a>";
		?></div><?
	}
}
body3();

if ($_GET['lang'] != "en") {
	$temp = "inhalt";
	if (isset($_GET['p'])) {
		echo '<a href="index.php?p='.$_GET['p'].'&amp;lang=en">English</a>'."\n";
	} else {
		echo '<a href="index.php?lang=en">English</a>'."\n";
	}
} else {
	$temp = "inhalt_en";
	if (isset($_GET['p'])) {
		echo '<a href="index.php?p='.$_GET['p'].'">Deutsch</a>'."\n";
	} else {
		echo '<a href="index.php">Deutsch</a>'."\n";
	}
}

$sql = 'SELECT
	'.$temp.'
FROM
	cms
WHERE
	kuerzel = "'.mysql_real_escape_string($_GET['p']).'"';
$result = mysql_query($sql);
if (!$result) {
    die ('Etwas stimmte mit dem Query nicht');
}
$row = mysql_fetch_array($result);
if ($temp == "inhalt") {
	echo stripslashes($row['inhalt']);
} else {
	echo stripslashes($row['inhalt_en']);
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

foreach ($rows as $i => $v) {
	if ($v['kategorie'] == 0) {
		if ($_GET['lang'] == "en") {
			printID($rows, $i, 0, "en");
		} else {
			printID($rows, $i, 0, "de");
		}
	}
}
bottom1($_GET['lang']);
mysql_close();
bottom2();
?>
