<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="style.css" media="screen" type="text/css" />
<title>xythobuz.org CMS Setup</title>
</head>
<body><div class="admin">
<h1>xythobuz.org CMS Setup</h1>

<?
$configFile = 'config.php';

if (isset($_POST['Host'])) {

	// Form is filled. Process it!

	// Create Main Table
	$db = mysql_connect($_POST['Host'], $_POST['Username'], $_POST['Password']);
	mysql_select_db($_POST['Database']);
	$sql = 'CREATE TABLE cms (
		id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
		kategorie INT,
		ord INT,
		kuerzel VARCHAR(50) NOT NULL,
		linktext VARCHAR(50) NOT NULL,
		beschreibung VARCHAR(100) NOT NULL,
		inhalt TEXT NOT NULL,
   		inhalt_en TEXT NOT NULL	)';
	$result = mysql_query($sql);
	if ($result == NULL) {
		die ("Could not create table 'cms'");
	}

	// Create Table for blog entries
	$sql = 'CREATE TABLE cms_news (
		id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
		datum DATETIME NOT NULL,
		ueberschrift VARCHAR(100) NOT NULL,
		inhalt TEXT NOT NULL )';
	$result = mysql_query($sql);
	if ($result == NULL) {
		die ("Could not create table 'cms_news'");
	}

	// Create table for users
	$sql = 'CREATE TABLE cms_user (
		username VARCHAR(50),
		password VARCHAR(50) )';
	$result = mysql_query($sql);
	if (!$result) {
		die("Could not create table 'cms_user'");
	} 

	// Create table for links
	$sql = 'CREATE TABLE cms_links (
		id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
		url TEXT NOT NULL,
		title TEXT NOT NULL,
		ord INT )';
	$result = mysql_query($sql);
	if (!$result) {
		die ("Could not create table 'cms_links'");
	}

	$sql = 'CREATE TABLE cms_code (
		id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
		inhalt TEXT NOT NULL)';
	$result = mysql_query($sql);
	if (!$result) {
		die("Could not create table 'cms_code'");
	}

	$sql = 'CREATE TABLE cms_codenav (
		id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
		inhalt TEXT NOT NULL)';
	$result = mysql_query($sql);
	if (!$result) {
		die("Could not create table 'cms_codenav'");
	}
	
	// Create table for comments
	$sql = 'CREATE TABLE cms_comments (
		id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
		datum DATETIME NOT NULL,
		parent INT NOT NULL,
		autor VARCHAR(100) NOT NULL,
		inhalt TEXT NOT NULL,
		frei BOOL)';
	$result = mysql_query($sql);
	if ($result == NULL) {
		die ("Could not create table cms_comments");
	} else {
		?>
Tables created successfully!<br>
		<?
		
		// Create example blog entry
		$sql = 'INSERT INTO
				cms_news(id, datum, ueberschrift, inhalt)
			VALUES
				(1, FROM_UNIXTIME('.time().'), "Hello World", "Hello World!")';
		$result = mysql_query($sql);
		if (!$result) {
			die("Could not create example blog entry!");
		}
		echo "Created blog entry...<br>\n";

		$sql = 'INSERT INTO
				cms_codenav(id, inhalt)
			VALUES
				(1, "'.mysql_real_escape_string('<a href="http://validator.w3.org/check?uri=referer"><img src="img/valid_html5.png" alt="Valid HTML 4.01 Transitional"></a>').'")';
		$result = mysql_query($sql);
		if (!$result) {
			die("Could not add nav pics!");
		}
		$sql = 'INSERT INTO
				cms_codenav(id, inhalt)
			VALUES
				(2, "'.mysql_real_escape_string('<a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS ist valide!"></a>').'")';
		$result = mysql_query($sql);
		if (!$result) {
			die("Could not add nav pics!");
		}
		$sql = 'INSERT INTO
				cms_codenav(id, inhalt)
			VALUES
				(3, "'.mysql_real_escape_string('<a href="http://feed1.w3.org/check.cgi?url='.$_POST['root'].'/rss.xml"><img src="img/valid_rss.png" alt="Valides RSS"></a>').'")';
		$result = mysql_query($sql);
		if (!$result) {
			die("Could not add nav pics!");
		}
		echo "Added nav pics<br>\n";

		// Create example page
		$sql = 'INSERT INTO
				cms(id, kuerzel, beschreibung, linktext, kategorie, ord, inhalt, inhalt_en)
			VALUES
				(1, "home", "xythobuz\\'."'".'s Elektronik und Software Projekte", "Home", 0, 0, "<h1>Herzlich Willkommen</h1>
<p>Hier findest du (immer mehr) Informationen zu Projekten von mir. Gerne höre ich Feedback über E-Mail. Viel Spaß!
</p>", "No translation available!")';
		$result = mysql_query($sql);
		if (!$result) {
			die("Could not create example page!");
		}
		echo "Created example page!<br>\n";
		
		$sql = 'INSERT INTO
				cms_links(url, title, ord)
			VALUES
				("'.mysql_real_escape_string("http://www.xythobuz.org").'", "powered by xythobuzCMS", 99)';
		$result = mysql_query($sql);
		if (!$result) {
			die("Could not create example link!");	
		}
		echo "Created example link!<br>\n";

		// Create first user
		if (strcmp($_POST['pass1'], $_POST['pass2']) != 0) {
			die("Password not equal!");
		}
		$pass = md5($_POST['pass1']);
		$sql = 'INSERT INTO cms_user(username, password)
			VALUES
				("'.mysql_real_escape_string($_POST['user']).'", "'.$pass.'")';
		$result = mysql_query($sql);
		if (!$result) {
			die("Could not create user!");
		}
		echo "Created user!<br>\n";

		// Create config file
		$content = "<?\n\$sql_host = '".$_POST['Host']."';\n";
		$content = $content."\$sql_username = '".$_POST['Username']."';\n";
		$content = $content."\$sql_password = '".$_POST['Password']."';\n";
		$content = $content."\$sql_database = '".$_POST['Database']."';\n";
		$content = $content."\$xythobuzCMS_root = '".$_POST['root']."';\n";
		$content = $content."\$xythobuzCMS_author = '".$_POST['author']."';\n";
		$content = $content."\$xythobuzCMS_authormail = '".$_POST['authormail']."';\n";
		$content = $content."\$xythobuzCMS_lang = '".$_POST['lang']."';\n";
		$content = $content."\$xythobuzCMS_lang2 = '".$_POST['lang2']."';\n";
		$content = $content."\$xythobuzCMS_title = '".$_POST['title']."';\n";
		$content = $content."\$xythobuzCMS_logo = '".$_POST['logo']."';\n";
		$content = $content."\$xythobuzCMS_com = '".$_POST['com']."';\n";
		if ($_POST['birth'] != '') {
			$content = $content."\$xythobuzCMS_birth = ".$_POST['birth'].";\n";
		}
		if ($_POST['flattr'] != '') {
			$content = $content."\$xythobuzCMS_flattr = '".$_POST['flattr']."';\n";
		}
		if ($_POST['piwiktoken'] != '') {
			$content = $content."\$xythobuzCMS_piwiktoken = '".$_POST['piwiktoken']."';\n";
		}
		if ($_POST['adcode'] != '') {
			$content = $content."\$xythobuzCMS_adcode = \"".str_replace("\n", '\n', str_replace('"', '\"', $_POST['adcode']))."\";\n";
		}
		if ($_POST['feed'] != '') {
			$content = $content."\$xythobuzCMS_customFeed = \"".$_POST['feed']."\";\n";
		}
		if ($_POST['nick'] != '') {
			$content = $content."\$xythobuzCMS_twitterNick = \"".$_POST['nick']."\";\n";
		}
		if ($_POST['capt_priv'] != '') {
			if ($_POST['capt_pub'] != '') {
				$content = $content."\$xythobuzCMS_captcha_pub = \"".$_POST['capt_pub']."\";\n";
				$content = $content."\$xythobuzCMS_captcha_priv = \"".$_POST['capt_priv']."\";\n";
			}
		}
		if ($_POST['UNname'] != '') {
			if ($_POST['UNpass'] != '') {
				$content = $content."\$xythobuzCMS_UNname = \"".$_POST['UNname']."\";\n";
				$content = $content."\$xythobuzCMS_UNpass = \"".$_POST['UNpass']."\";\n";
			}
		}
		$content = $content."?>";

		if (!file_exists($configFile)) {
			if (touch($configFile)) {
				print "Config-Datei angelegt!<br>\n";
			} else {
				print "Konnte Config-Datei nicht anlegen!<br>\n";
				exit;
			}
		}

		if (!$handle = fopen($configFile, "w")) {
			print "Kann Datei $configFile nicht öffnen<br>\n";
			exit;
		}
		if (!fwrite($handle, $content)) {
			print "Kann nicht in $configFile schreiben<br>\n";
			exit;
		}
		fclose($handle);
		print "Config erfolgreich gespeichert!<br>\n";
	}
	mysql_close($db);
?>
<h1>Delete setup.php!</h1>
<a href="index.php">Zur Startseite</a><br>
<?
} else {
	// Show form
?>
<form action="setup.php" method="post">
	<table>
		<tr><th>MySQL Credentials:</th><th></th><th></th></tr>
		<tr><td>Host</td><td><input type="text" name="Host" /></td><td></td></tr>
		<tr><td>Username</td><td><input type="text" name="Username" /></td><td></td></tr>
		<tr><td>Password</td><td><input type="password" name="Password" /></td><td></td></tr>
		<tr><td>Database</td><td><input type="text" name="Database" /></td><td></td></tr>
		
		<tr><th>User Informations:</th><th></th><th></th></tr>
		<tr><td>CMS Username</td><td><input type="text" name="user" /></td><td></td></tr>
		<tr><td>CMS Password</td><td><input type="password" name="pass1" /></td><td></td></tr>
		<tr><td>CMS Pass again</td><td><input type="password" name="pass2" /></td><td></td></tr>
		
		<tr><th>Misc. Infos:</th><th></th><th></th></tr>
		<tr><td>Root URL</td><td><input type="text" name="root" /></td><td>with http:// but without / at the end!</td></tr>
		<tr><td>Author Name</td><td><input type="text" name="author" /></td><td></td></tr>
		<tr><td>Author Mail</td><td><input type="text" name="authormail" /></td><td></td></tr>
		<tr><td>Language Code 1</td><td><input type="text" name="lang" /></td><td>de, en... For meta tag</td></tr>
		<tr><td>Language Code 2</td><td><input type="text" name="lang2" /></td><td>Works with any language combination!</td></tr>
		<tr><td>Page Title</td><td><input type="text" name="title" /></td><td></td></tr>
		<tr><td>Logo URL</td><td><input type="text" name="logo" /></td><td></td></tr>
		<tr><td>Comments standard visible?</td><td><input type="text" name="com" /></td><td>TRUE or FALSE</td></tr>

		<tr><th>Optional:</th><th></th><th></th></tr>
		<tr><td>Birthdate</td><td><input type="text" name="birth" /></td><td>As Unix timestamp. Displays age.</td></tr>
		<tr><td>Flattr URL</td><td><input type="text" name="flattr" /></td><td>Adds Flattr Button.</td></tr>
		<tr><td>Piwik API Token</td><td><input type="text" name="piwiktoken" /></td><td>Shows visitor count, adds Piwik API Code</td></tr>
		<tr><td>Ad Code (AdSense etc.)</td><td><input type="text" name="adcode" /></td><td>Gets in navbar</td></tr>
		<tr><td>Alternative RSS URL</td><td><input type="text" name="feed" /></td><td>If you want to use Feedburner</td></tr>
		<tr><td>Twitter nickname</td><td><input type="text" name="nick" /></td><td>Adds a follow button</td></tr>
		<tr><td>reCaptcha Public</td><td><input type="text" name="capt_pub" /></td><td>Put recaptchalib.php in same folder as index.php</td></tr>
		<tr><td>reChaptcha Private</td><td><input type="text" name="capt_priv" /></td><td></td></tr>
		<tr><td>UltimateNotifier Username</td><td><input type="text" name="UNname" /></td><td>Sends notifications when comments are added</td></tr>
		<tr><td>UltimateNotifier Password</td><td><input type="text" name="UNpass" /></td><td></td></tr>
		<tr><td></td><td><input type="submit" name="formaction" value="Save" /></td><td></td></tr>
	</table>
</form>
<?
}
?>
</div></body>
</html>
