<?
include('config.php');
include('func.php');
header1();
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Konnte keine Verbindung zur Datenbank aufbauen');
}
?><meta name="description" content="<?
if (!isset($_GET['p'])) {
	$_GET['p'] = "home";
}
$sql = 'SELECT
	beschreibung,
	inhalt_en
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
if (($_GET['lang'] != "en") && ($row['inhalt_en'] != "") && ($row['inhalt_en'] != "<p>No translation available...</p>")) {
	$langs = array( $xythobuzCMS_lang, $xythobuzCMS_lang2 );
	$bestlang = prefered_language($langs);
	if ($bestlang == $xythobuzCMS_lang2) {
		?><div class="bar"><?
			echo "<a href=\"index.php";
		if (isset($_GET['p'])) {
			echo "?p=".$_GET['p']."&amp;lang=en";
		} else {
			echo "?lang=en";
		}
		echo "\">This is available in your language (".$xythobuzCMS_lang2.")!</a>";
		?></div>
<?
	}
} else {
	$langs = array( $xythobuzCMS_lang, $xythobuzCMS_lang2 );
	$bestlang = prefered_language($langs);
	if ($bestlang == $xythobuzCMS_lang) {
		?><div class="bar"><?
			echo "<a href=\"index.php";
		if (isset($_GET['p'])) {
			echo "?p=".$_GET['p'];
		}
		echo "\">This is available in your language (".$xythobuzCMS_lang.")!</a>";
		?></div>
<?
	}
}
body3();

if ($_GET['lang'] != "en") {
	$temp = "inhalt";
} else {
	$temp = "inhalt_en";
}
?><div class="langswitch">
<?
if (isset($_GET['p'])) {
	echo "<br><a href=\"index.php?p=".$_GET['p']."\"><img src=\"img/flags/".$xythobuzCMS_lang.".png\" alt=\"Change language\"></a>"."\n";
	echo '<a href="index.php?p='.$_GET['p'].'&amp;lang=en"><img src="img/flags/'.$xythobuzCMS_lang2.".png\" alt=\"Change language\"></a>"."\n";
} else {
	echo '<br><a href="index.php"><img src="img/flags/'.$xythobuzCMS_lang.".png\" alt=\"Change language\"></a>"."\n";
	echo '<a href="index.php?lang=en"><img src="img/flags/'.$xythobuzCMS_lang2.".png\" alt=\"Change language\"></a>"."\n";
}
?></div>
<?
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
if (($row['inhalt'] != "") || ($row['inhalt_en'] != "")) {
	if ($temp == "inhalt") {
		echo stripslashes($row['inhalt']);
	} else {
		echo stripslashes($row['inhalt_en']);
	}
} else {
	echo "<h1>404 - Page not found</h1>\n<p>You followed an invalid link!</p>\n";
}
?></div><nav>
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
