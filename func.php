<?
clearstatcache();
if (file_exists("setup.php")) {
	die("Please setup xythobuzCMS Installation first!");
}

// Patch for bloated log warnings
if (!isset($_GET['beitrag'])) {
	$_GET['beitrag'] = "";
}
if (!isset($_GET['lang'])) {
	$_GET['lang'] = "";
}
if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "";
}

function header1() {
	include("config.php");
	if ($_GET['lang'] == "en") {
		$tmpstring = $xythobuzCMS_lang2;
	} else {
		$tmpstring = $xythobuzCMS_lang;
	}
?><!DOCTYPE HTML>
<html lang="<? echo $tmpstring ?>">
<head>
<meta charset="utf-8" />
<? if (isset($_GET['css']) && !empty($_GET['css'])) { ?>
<link rel="stylesheet" href="<? echo $xythobuzCMS_root; ?>/<? echo $_GET['css']; ?>.css" media="screen" type="text/css" />
<? } else { ?>
<link rel="stylesheet" href="<? echo $xythobuzCMS_root; ?>/style.css" media="screen" type="text/css" />
<? } ?>
<link rel="stylesheet" href="<? echo $xythobuzCMS_root; ?>/print.css" media="print" type="text/css" />
<link rel="author" href="<? echo $xythobuzCMS_authormail; ?>" />
<link rel="shortcut icon" href="<? echo $xythobuzCMS_root; ?>/favicon.ico" />
<meta name="author" content="<? echo $xythobuzCMS_author; ?>">
<?
}

function body1($s = "content") {
	body2();
	body3($s);
}

function body2() {
	include("config.php");
?><title><? echo $xythobuzCMS_title; ?></title>
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
?></head>
<?
	if (isset($xythobuzCMS_onload)) {
		echo "<body onload=\"".$xythobuzCMS_onload."\">\n";
	} else {
		echo "<body>\n";
	}
}

function body3($s = "content") {
?><div class="container">

<div class="<? echo $s; ?>">
<?
}

function prefered_language ($available_languages,$http_accept_language="auto") {
	// if $http_accept_language was left out, read it from the HTTP-Header
	if ($http_accept_language == "auto") $http_accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

	// standard  for HTTP_ACCEPT_LANGUAGE is defined under
	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
	// pattern to find is therefore something like this:
	//    1#( language-range [ ";" "q" "=" qvalue ] )
	// where:
	//    language-range  = ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
	//    qvalue         = ( "0" [ "." 0*3DIGIT ] )
	//            | ( "1" [ "." 0*3("0") ] )
	preg_match_all("/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?" .
			"(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i",
			$http_accept_language, $hits, PREG_SET_ORDER);

	// default language (in case of no hits) is the first in the array
	$bestlang = $available_languages[0];
	$bestqval = 0;

	foreach ($hits as $arr) {
		// read data from the array of this hit
		$langprefix = strtolower ($arr[1]);
		if (!empty($arr[3])) {
			$langrange = strtolower ($arr[3]);
			$language = $langprefix . "-" . $langrange;
		}
		else $language = $langprefix;
		$qvalue = 1.0;
		if (!empty($arr[5])) $qvalue = floatval($arr[5]);

		// find q-maximal language 
		if (in_array($language,$available_languages) && ($qvalue > $bestqval)) {
			$bestlang = $language;
			$bestqval = $qvalue;
		}
		// if no direct hit, try the prefix only but decrease q-value by 10% (as http_negotiate_language does)
		else if (in_array($langprefix,$available_languages) && (($qvalue*0.9) > $bestqval)) {
			$bestlang = $langprefix;
			$bestqval = $qvalue*0.9;
		}
	}
	return $bestlang;
}

function bottom1($lang) {
	include("config.php");
	if ($lang == "en") {
?>- <a href="news.php?lang=en">Blog</a><br>
<?
	} else {
?>- <a href="news.php">Blog</a><br>
<?
	}
?><br><form action="search.php" method="get"><input type="text" name="term">
<input type="submit" value="<? if ($_GET['lang'] == "en") { echo "Search"; } else { echo "Suche"; } ?>"></form>
<p><?
	if (isset($xythobuzCMS_logo)) {
		if (!isset($xythobuzCMS_logoLink)) {
			$tmp = $xythobuzCMS_root;
		} else {
			$tmp = $xythobuzCMS_logoLink;
		}
?><a href="<? echo $tmp; ?>"><img src="<? echo $xythobuzCMS_logo; ?>" alt="Logo"></a><br>
<?
	}
	echo $xythobuzCMS_author."<br>";
	if (isset($xythobuzCMS_birth)) {
?>Age: <?
	}
}

function bottom2() {
	include("config.php");
	$db2 = mysql_connect($sql_host, $sql_username, $sql_password);
	mysql_select_db($sql_database);
	if (mysql_errno()) {
		die ('Konnte keine Verbindung zur Datenbank aufbauen');
	}
	if (isset($xythobuzCMS_birth)) {
		$birth = $xythobuzCMS_birth;
		$now = time();
		$diff = $now - $birth;
		$age = 0;
		while ($diff > 31536000) {
			$diff -= 31536000; // One year
			$age += 1;
		}
		echo $age;
	}
?></p><p>
<? 
	$sql = 'SELECT
		url, title, nofollow
	FROM
		cms_links
	ORDER BY
		ord ASC';
	$result = mysql_query($sql);
	if (!$result) {
		die ("Could not connect to database!");
	}
	while ($row = mysql_fetch_array($result)) {
?><a href="<? echo htmlspecialchars($row['url']); ?>"<?
		if ($row['nofollow'] == 1) {
			echo " rel=\"nofollow\"";
		}
?>><? echo $row['title']; ?></a><br>
<?
	}
?><br><a href="mailto:<? echo $xythobuzCMS_authormail; ?>"><? echo $xythobuzCMS_authormail; ?></a><br>
<? if (isset($xythobuzCMS_twitterNick)) {
?><a href="https://twitter.com/<? echo $xythobuzCMS_twitterNick; ?>" class="twitter-follow-button" data-button="grey" data-text-color="#FFFFFF" data-link-color="#00AEFF" data-show-count="false">Follow @<? echo $xythobuzCMS_twitterNick; ?></a><br>
<? } ?>
</p><p style="font-size:xx-small">
<?
	// Create String with Link to current site.
	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	if (strpos($_SERVER['PHP_SELF'], "news.php", 1) != FALSE) {
		// we are in news.php
		if ($_GET['beitrag'] != '') {
			$url = $url.'?beitrag='.$_GET['beitrag'];
		}
	} else {
		if (isset($_GET['p'])) {
			$url = $url.'?p='.$_GET['p'];
		}
	}

	$url = str_replace('<', '&lt;', $url);
	$url = str_replace('>', '&gt;', $url);
	$url = str_replace('"', '&quot;', $url);
	$url = str_replace("'", '&#39;', $url);
?>Permalink: <a href="<? echo $url; ?>"><? echo $url; ?></a><br>
<a href="pwd.php">Admin</a><br>
</p></nav>

<div class="footer">
<?

	$sql = 'SELECT
		inhalt
	FROM
		cms_codenav
	ORDER BY
		id ASC';
	$result = mysql_query($sql);
	if (!$result) {
		die ("Error");
	}
	while ($row = mysql_fetch_array($result)) {
		echo stripslashes($row['inhalt'])."\n";
	}

	if (isset($xythobuzCMS_flattr)) {
?><a href="<? echo $xythobuzCMS_flattr; ?>" target="_blank"><img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this"></a><br>
<?
	}
	if (isset($xythobuzCMS_adcode)) {
		echo $xythobuzCMS_adcode."\n";
	}

	echo "<p style=\"font-size:x-small\">";
	include("count.php");
	echo " visitors / pageviews today.</p>\n";

?></div></div>
<?
	// Print custom code
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
</html>
<?
	mysql_close();
}

function sort2d_asc(&$arr, $key){
	//we loop through the array and fetch a value that we store in tmp
	for($i = 0; $i < count($arr); $i++){
		$tmp = $arr[$i];
		$pos = false;
		//we check if the key we want to sort by is a string
		$str = is_numeric($tmp[$key]);
		if(!$str){
			//we loop the array again to compare against the temp value we have
			for($j = $i; $j < count($arr); $j++){
				if(strcasecmp($arr[$j][$key], $tmp[$key]) < 0){
					$tmp = $arr[$j];
					$pos = $j;
				}
			} 
		}else{
			for($j = $i; $j < count($arr); $j++){
				if($arr[$j][$key] < $tmp[$key]){
					$tmp = $arr[$j];
					$pos = $j;
				}
			}
		}
		if($pos !== false){
			$arr[$pos] = $arr[$i];
			$arr[$i] = $tmp;
		}
	}
}

// für jede auszugebende id nach anderen ids suchen, die als kategorie die erste id haben -> ausgeben
function printID($r, $pos, $numOfInvocations, $lang) {
	$found = false;
	$newPosition = array();
	$results = array();
	$resPos = 0;
	foreach ($r as $i => $v) {
		if ($v['kategorie'] == $r[$pos]['id']) {
			$found = true;
			$newPosition[$resPos] = $i;
			$results[$resPos] = $v;
			$resPos++;
		}
	}
	if ($numOfInvocations == 0) {
		if ($pos != 0) {
			echo "<br>";
		}
	}
	echo "<li>";
	for ($i = 1; $i < ($numOfInvocations+1); $i++) {
		echo "- ";
	}

	$printLink = 1;
	if (isset($r[$pos]['nolink'])) {
		if (($r[$pos]['nolink'] == 1)) {
			$printLink = 0;
		}
	}

	if ($printLink == 1) {
		echo "<a href=\"index.php?p=";
		echo $r[$pos]['kuerzel'];
		if ($lang == "en") {
			echo "&amp;lang=en";
		}
		echo "\">";
	}	
	echo $r[$pos]['linktext'];
	if ($printLink == 1) {
		echo "</a>";
	}

	echo "</li>\n";
	if ($found == true) {
		sort2d_asc($results, 'id');
		$resPos = 0;
		foreach ($results as $i) {
			printID($r, $newPosition[$resPos], ($numOfInvocations+1), $lang);
			$resPos++;
		}
	}
}

function checkComment($comment) {
	$filter = file("spam.txt", FILE_IGNORE_NEW_LINES);
	foreach ($filter as $key=>$val) {
		if (stripos($comment, $val) !== false) {
			return $val;
		}
	}
	return -1;
}

?>
