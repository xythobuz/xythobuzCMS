<?

function header1() {
	include("sql.php");
?>
<!DOCTYPE HTML>
<html lang="<? echo $xythobuzCMS_lang; ?>">
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="style.css" media="screen" type="text/css" />
<link rel="stylesheet" href="print.css" media="print" type="text/css" />
<link rel="author" href="<? echo $xythobuzCMS_authormail; ?>" />
<link rel="shortcut icon" href="<? echo $xythobuzCMS_root; ?>/favicon.ico" />
<meta name="author" content="<? echo $xythobuzCMS_author; ?>">
<?
}

function body1() {
	body2();
	body3();
}

function body2() {
	include("sql.php");
?>
<title><? echo $xythobuzCMS_title; ?></title>
</head>
<body>
<?
	if (eregi("MSIE",getenv("HTTP_USER_AGENT")) || eregi("Internet Explorer",getenv("HTTP_USER_AGENT"))) {
?>
	<div class="bar">Du verwendest Internet Explorer. Dieser Browser unterstützt aktuelle Web Standards nur unzureichend. Wechsle doch zu <a href="http://www.mozilla.com/">Firefox, einem freien Browser</a>!</div>
<?
	}	
}

function body3() {
?>
<div class="container">
	<div class="content">
<?
}

/*
  determine which language out of an available set the user prefers most
 
  $available_languages        array with language-tag-strings (must be lowercase) that are available
  $http_accept_language    a HTTP_ACCEPT_LANGUAGE string (read from $_SERVER['HTTP_ACCEPT_LANGUAGE'] if left out)
  By: anonymous on http://www.php.net/manual/en/function.http-negotiate-language.php
*/
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
        else if (in_array($languageprefix,$available_languages) && (($qvalue*0.9) > $bestqval)) {
            $bestlang = $languageprefix;
            $bestqval = $qvalue*0.9;
        }
    }
    return $bestlang;
}

function bottom1($lang) {
	include("sql.php");
	if ($lang == "en") {
?>
		<br>-&gt; <a href="news.php?lang=en">Blog</a><br>
<?
	} else {
?>
		<br>-&gt; <a href="news.php">Blog</a><br>
<?
	}
?>
		<p>
		<img src="<? echo $xythobuzCMS_logo; ?>" alt="Me"><br>
		<? echo $xythobuzCMS_author; ?><br>
		Age: 
<?
}

function bottom2() {
include("sql.php");
$birth = $xythobuzCMS_birth;
$now = time();
$diff = $now - $birth;
$age = 0;
while ($diff > 31536000) {
	$diff -= 31536000; // One year
	$age += 1;
}
echo $age;
?>
		</p><p>
<? include("links.php"); ?>
		</p><p style="font-size:xx-small">
<?
// Create String with Link to current site.
$url = 'http://' . $_SERVER['HTTP_HOST']; //. $_SERVER['PHP_SELF'];
if ($_GET['p'] != '') {
	$url = $url . $_SERVER['PHP_SELF'] . '?p=' . $_GET['p'];
}
$url = str_replace('<', '&lt;', $url);
$url = str_replace('>', '&gt;', $url);
$url = str_replace('"', '&quot;', $url);
$url = str_replace("'", '&#39;', $url);
?>
		Permalink: <a href="<? echo $url; ?>"><? echo $url; ?></a><br>
		<a href="pwd.php">Admin</a><br>
		</p>
		<a href="http://validator.w3.org/check?uri=referer"><img
        src="img/valid_html5.png"
		alt="Valid HTML 4.01 Transitional"></a>
		<a href="http://jigsaw.w3.org/css-validator/check/referer">
        <img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS ist valide!">
		</a>
		<a href="http://feed1.w3.org/check.cgi?url=<? echo str_replace(":", "%3A", $xythobuzCMS_root); ?>/rss.xml"><img src="img/valid_rss.png" alt="Valides RSS"></a><br>
		<a href="<? echo $xythobuzCMS_flattr; ?>" target="_blank"><img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this"></a><br>
<?
$curl_handle = curl_init();
curl_setopt($curl_handle,CURLOPT_URL, $xythobuzCMS_root.'/piwik/index.php?module=API&method=VisitsSummary.getUniqueVisitors&idSite=1&period=day&date=today&format=xml&token_auth='.$xythobuzCMS_piwiktoken);
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
$buffer = curl_exec($curl_handle);
curl_close($curl_handle);

if (!empty($buffer)) {
	$s_array = array("<result>", "</result>", "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n");
	echo "<p style=\"font-size:x-small\">";
	echo str_replace($s_array, "", $buffer);
	echo " Besucher heute</p>\n";
}
?>
	</nav>
</div>
<!-- Piwik --> 
 <script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "<? echo $xythobuzCMS_root; ?>/piwik/" : "<? echo $xythobuzCMS_root; ?>/piwik/");
 document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
 </script><script type="text/javascript">
 try {
 var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
 piwikTracker.trackPageView();
 piwikTracker.enableLinkTracking();
 } catch( err ) {}
 </script><noscript><p><img src="<? echo $xythobuzCMS_root; ?>/piwik/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
 <!-- End Piwik Tracking Code -->
</body>
</html>
<?
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
	for ($i = 0; $i < ($numOfInvocations+1); $i++) {
		echo "-&gt; ";
	}
	echo "<a href=\"index.php?p=";
	echo $r[$pos]['kuerzel'];
	if ($lang == "en") {
		echo "&amp;lang=en";
	}
	echo "\">";
	echo $r[$pos]['linktext'];
	echo "</a><br>\n";
	if ($found == true) {
		sort2d_asc($results, 'id');
		$resPos = 0;
		foreach ($results as $i) {
			printID($r, $newPosition[$resPos], ($numOfInvocations+1), $lang);
			$resPos++;
		}
	}
}

?>
