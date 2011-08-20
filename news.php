<?
include('sql.php');
include('func.php');
header1();
?>
<link rel="alternate" type="application/rss+xml" title="xythobuz.org RSS-Feed" href="http://www.xythobuz.org/rss.xml" />
<meta name="description" content="xythobuzs Blog">
<?
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Konnte keine Verbindung zur Datenbank aufbauen');
}
body1();
?>
	<p><a href="/rss.xml"><img src="/img/rss.png" alt="RSS Feed"></a></p>
<?

if (isset($_GET['beitrag'])) {
if (is_numeric($_GET['beitrag'])) {
if (isset($_POST['inhalt'])) {
	if (isset($_POST['autor'])) {
		$res = checkComment($_POST['inhalt']);
		if ($res != -1) {
			echo "Bad Word: ".$res."\n";
			exit;
		} else {
			$sql = 'INSERT INTO
				cms_comments(datum, autor, inhalt, parent)
			VALUES
				(FROM_UNIXTIME('.time().'),
				"'.mysql_real_escape_string($_POST['autor']).'",
				"'.mysql_real_escape_string($_POST['inhalt']).'",
				'.mysql_real_escape_string($_GET['beitrag']).')';
			$result = mysql_query($sql);
			if (!$result) {
				echo mysql_error();
				echo "Query Error!";
				exit;
			}
			echo "Comment added...";
		}
	}
} else {
	$sql = 'SELECT
		inhalt,
		ueberschrift,
		datum
	FROM
		cms_news
	WHERE
		id = '.mysql_real_escape_string($_GET['beitrag']);
	$result = mysql_query($sql);
	if (!$result) {
		echo "404 - Not Found!";
		exit;
	}
	$row = mysql_fetch_array($result);
?>
	<h2><? echo stripslashes($row['ueberschrift']); ?></h2>
	<p style="font-size:xx-small"><? echo $row['datum']; ?></p>
	<p><? echo stripslashes($row['inhalt']); ?></p>
<?
	$sql = 'SELECT
		inhalt,
		datum,
		autor
	FROM
		cms_comments
	WHERE
		parent = '.mysql_real_escape_string($_GET['beitrag']).'
	ORDER BY
		datum ASC';
	$result = mysql_query($sql);
	if ($result) {
		
		$row = mysql_fetch_array($result);
		if ($row == false) {
			echo "<hr><p>No Comments!</p>\n";
		} else {
			do {
?>
		<hr>
		<p><? echo stripslashes($row['autor']); ?> (<? echo $row['datum']; ?>)</p>
		<p><? echo stripslashes($row['inhalt']); ?></p>
<?
			} while ($row = mysql_fetch_array($result));
		}
	} else {
		echo "Query Error!";
		exit;
	}
?>
		<hr>
		<form action="news.php?beitrag=<? echo $_GET['beitrag']; ?>" method="post">
			<fieldset>
				<label>Name: <input type="text" name="autor" /></label>
			</fieldset>
			<textarea name="inhalt" rows="20" cols="68"></textarea>
			<input type="submit" name="formaction" value="Add Comment!" />
		</form>
<?
}
}
} else {

	$sql = 'SELECT
		inhalt,
		ueberschrift,
		datum,
		id
	FROM
		cms_news
	ORDER BY
		datum DESC';
	$result = mysql_query($sql);
	if (!$result) {
	    die ('Etwas stimmte mit dem Query nicht');
	}
	$i = 0;
	$pagemarker = 0;

	$skip = 0;
	if (isset($_GET['p']) && ($_GET['p'] != "0")) {
		$skip = $_GET['p'];
	}
	$skip2 = $skip * 5;

	while($row = mysql_fetch_array($result)) {
		$i++;
		if ($skip2 > 0) {
			$skip2--;
			$i--;
		} else {
?>
	<h2><a href="news.php?beitrag=<? echo $row['id']; ?>"><? echo stripslashes($row['ueberschrift']); ?></a></h2>
	<p style="font-size:xx-small"><? echo $row['datum']; ?></p>
	<p><? echo stripslashes($row['inhalt']); ?></p>
	<hr>
<?
		}
		if ($i >= 5) {
			$pagemarker = 1;
			break;
		} else {
			$pagemarker = 0;
		}
	}

	if ($pagemarker == 1) {
?>
	<a href="news.php?p=<? echo ($skip + 1); ?>">Ältere Beiträge</a>
<?
	}
}
?>
	</div>
<?
// #################################
?>
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
