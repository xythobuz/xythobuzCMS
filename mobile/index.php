<?
include('../config.php');
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Could not connect to database!');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<link rel="apple-touch-icon" href="images/icon.png" />
<link rel="apple-touch-startup-image" href="images/load.png" />
<title><? echo $xythobuzCMS_title; ?></title>
<?
$sql = 'SELECT
	inhalt
FROM
	cms_codehead
ORDER BY
	id ASC';
$result = mysql_query($sql);
if (!$result) {
	die ("Error");
}
while ($row = mysql_fetch_array($result)) {
	echo stripslashes($row['inhalt'])."\n";
}
?>
<script src="javascript/functions.js" type="text/javascript"></script>
<? if (isset($xythobuzCMS_onload)) { ?>
<script type="text/javascript">
window.onDomReady(onReady);
function onReady() {
	<? echo $xythobuzCMS_onload; ?>
}
</script>
<? } ?>
</head>
<body>
<div id="topbar">
<? if (isset($_GET['p']) || isset($_GET['search'])) {
	if (strpos($_SERVER['HTTP_REFERER'], $xythobuzCMS_root."/index.php") === 0) { ?>
	<div id="leftnav"><a href="index.php"><img src="images/home.png" alt="Home" /></a></div>
	<? } else { ?>
	<div id="leftnav"><a href="javascript:history.back();">Back</a></div>
	<? }
} else if (isset($_GET['news'])) { ?>
	<div id="leftnav"><a href="index.php"><img src="images/home.png" alt="Home" /></a></div>
<? } else { ?>
	<div id="leftnav"><a class="noeffect" href="../index.php?desktop"><img alt="Desktop Version" src="images/pc.png" /></a></div>
<? } ?>
	<div id="title"><? echo $xythobuzCMS_title; ?></div>
<? if (isset($_GET['p'])) {
	if (isset($_GET['lang'])) { // Link to lang 1 ?>
	<div id="rightnav"><a href="index.php?p=<? echo $_GET['p']; ?>"><img alt="Change language" src="../img/flags/<? echo $xythobuzCMS_lang; ?>.png"></a></div>
<?	} else { // Link to lang 2 ?>
	<div id="rightnav"><a href="index.php?p=<? echo $_GET['p']; ?>&amp;lang"><img alt="Change language" src="../img/flags/<? echo $xythobuzCMS_lang2; ?>.png"></a></div>
<?	}
} ?>
</div>
<? if (isset($_GET['lang'])) {
	$inhaltLanguage = "inhalt_en";
} else {
	$inhaltLanguage = "inhalt";
} ?>
<? if ((!isset($_GET['search'])) && (!isset($_GET['news']))) { ?>
<div class="searchbox">
	<form action="index.php" method="get">
		<fieldset>
			<input id="search" placeholder="search" type="text" name="search" />
			<input id="submit" type="hidden" />
		</fieldset>
	</form>
</div>
<? } ?>
<div id="content">
<? if (isset($_GET['p'])) { // Page:
	$sql = "SELECT inhalt, inhalt_en
	FROM cms
	WHERE kuerzel = '".mysql_real_escape_string($_GET['p'])."'";
	$result = mysql_query($sql);
	if (!$result) {
		die("Database Error");
	}
	$row = mysql_fetch_array($result);
?>	<ul class="pageitem">
		<li class="textbox">
<?
	$content = stripslashes($row[$inhaltLanguage]);
	$content = preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.$xythobuzCMS_root.'/$2"',$content);
	echo $content;
?>		</li>
	</ul>
<?
} else if (isset($_GET['search'])) {  // Search:
	searchInCms($_GET['search']);
} else if (isset($_GET['news'])) {
	include("php/rss.php");
} else { // Navigation: ?>
	<span class="graytitle">Navigation</span>
	<ul class="pageitem">
		<li class="menu">
			<a href="index.php?news">
				<span class="name">Blog</span>
				<span class="arrow"></span>
			</a>
		</li>
	<? printPage(0, 0); ?>
	</ul>
	<? if (isset($xythobuzCMS_logo)) { ?>
	<span class="graytitle">Logo</span>
	<ul class="pageitem">
		<li class="textbox">
			<img src="../<? echo $xythobuzCMS_logo; ?>" alt="Logo">
		</li>
	</ul>
<? 	} ?>
	<span class="graytitle">Administration</span>
	<ul class="pageitem">
		<li class="menu">
			<a href="../admin.php">
				<span class="name">Login</span>
				<span class="arrow"></span>
			</a>
		</li>
	</ul>
<? } ?>
</div>
<div id="footer">
	<!-- Support iWebKit by sending us traffic; please keep this footer on your page, consider it a thank you for my work :-) -->
	<a class="noeffect" href="http://snippetspace.com">iPhone site powered by iWebKit</a></div>
<?
$sql = 'SELECT
	inhalt
FROM
	cms_code
ORDER BY
	id ASC';
$result = mysql_query($sql);
if (!$result) {
	die ("Error");
}
while ($row = mysql_fetch_array($result)) {
	echo stripslashes($row['inhalt'])."\n";
}
?>
</body>
<? mysql_close(); ?>
</html>
<?
function printPage($parent, $depth) {
	$sql2 = "SELECT id, linktext, kuerzel FROM cms WHERE kategorie = ".mysql_real_escape_string($parent);
		$sql2 = $sql2." ORDER BY ord ASC";
		$result2 = mysql_query($sql2);
		if (!$result2) {
			die ("Database Error");
		}
		while ($row2 = mysql_fetch_array($result2)) {
			echo "<li class=\"menu\">";
			echo "<a href=\"index.php?p=".$row2['kuerzel'];
			if (isset($_GET['lang'])) {
				echo "&amp;lang=en";
			}
			echo "\">";
			echo "<span class=\"name\">";
			for ($i = 0; $i < $depth; $i++) {
				echo "--&gt; ";
			}
			echo $row2['linktext'];
			echo "</span>";
			echo "<span class=\"arrow\"></span>";
			echo "</a>";
			echo "</li>\n";
			printPage($row2['id'], $depth + 1);
		}
}

function searchInCms($searchTerm) {
	$resultKuerzel = array();
	$resultLink = array();
	$resultDesc = array();
	$resultVal = array();
	$i = 0;
	$sql = 'SELECT
		inhalt, inhalt_en, kuerzel, linktext, beschreibung
	FROM
		cms';
	$result = mysql_query($sql);
	if (!$result) {
		die("Database Error");
	}
	while ($row = mysql_fetch_array($result)) {
		$res = substr_count($row['inhalt'], $searchTerm);
		if ($res != 0) {
			$resultVal[$i] = $res;
			$resultKuerzel[$i] = $row['kuerzel'];
			$resultLink[$i] = $row['linktext'];
			$resultDesc[$i] = $row['beschreibung'];
			$i++;
		}
	}
	if ($i != 0) {
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
?>	<ul class="pageitem">
<?
		for($j = 0; $j < $i; $j++) {
?>		<li class="menu">
			<a href="index.php?p=<? echo $resultKuerzel[$resultOrder[$j]];
			if (isset($_GET['lang'])) { echo "&amplang=en"; } ?>">
				<span class="name"><? echo $resultLink[$resultOrder[$j]]; ?></span>
				<span class="arrow"></span>
			</a>
		</li>
<?
		}
?>	</ul>
<?
	} else {
?><ul class="pageitem">
		<li class="menu">
			<a href="index.php">
				<span class="name">No results!</span>
			</a>
		</li>
	</ul>
<?
	}
}
?>