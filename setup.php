<!DOCTYPE HTML>
<html lang="de">
<head>
<meta charset="utf-8" />
<title>xythobuz.org CMS Setup</title>
</head>
<body>
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
	
	// Create table for comments
	$sql = 'CREATE TABLE cms_comments (
		id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
		datum DATETIME NOT NULL,
		parent INT NOT NULL,
		autor VARCHAR(100) NOT NULL,
		inhalt TEXT NOT NULL )';
	$result = mysql_query($sql);
	if ($result == NULL) {
		die ("Could not create table cms_comments");
	} else {
		?>
Tables created successfully!<br>
		<?
		
		// Create example page
		$sql = 'INSERT INTO
				cms(id, kuerzel, beschreibung, linktext, kategorie, ord, inhalt)
			VALUES
				(1, "home", "xythobuz\\'."'".'s Elektronik und Software Projekte", "Home", 0, 0, "<h1>Herzlich Willkommen</h1>
<p>Hier findest du (immer mehr) Informationen zu Projekten von mir. Gerne höre ich Feedback über E-Mail. Viel Spaß!
</p>")';
		$result = mysql_query($sql);
		if (!$result) {
			die("Could not create example page!");
		}
		echo "Created example page!<br>\n";
		
		$sql = 'INSERT INTO
				cms_links(url, title, ord)
			VALUES
				("'.mysql_real_escape_string("http://www.xythobuz.org").'", "powered by xythobuzCMS", 99)'

		// Create first user
		if ($_POST['pass1'] != $_POST['pass2']) {
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
		$content = $content."\$xythobuzCMS_title = '".$_POST['title']."';\n";
		$content = $content."\$xythobuzCMS_logo = '".$_POST['logo']."';\n";
		if ($_POST['birth'] != '') {
			$content = $content."\$xythobuzCMS_birth = ".$_POST['birth'].";\n";
		}
		if ($_POST['flattr'] != '') {
			$content = $content."\$xythobuzCMS_flattr = '".$_POST['flattr']."';\n";
		}
		if ($_POST['piwiktoken'] != '') {
			$content = $content."\$xythobuzCMS_piwiktoken = '".$_POST['piwiktoken']."';\n";
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
	<fieldset>
		<legend>MySQL Credentials:</legend>
		<label>Host: <input type="text" name="Host" /></label><br>
		<label>Username: <input type="text" name="Username" /></label><br>
		<label>Password: <input type="password" name="Password" /></label><br>
		<label>Database: <input type="text" name="Database" /></label><br>
		<legend>User Informations:</legend>
		<label>CMS Username: <input type="text" name="user" /></label><br>
		<label>CMS Password: <input type="password" name="pass1" /></label><br>
		<label>CMS Pass again: <input type="password" name="pass2" /></label><br>
		<legend>Misc. Infos:</legend>
		<label>Root URL: <input type="text" name="root" /></label><br>
		<label>Author Name: <input type="text" name="author" /></label><br>
		<label>Author Mail: <input type="text" name="authormail" /></label><br>
		<label>Language Code (de, en...) <input type="text" name="lang" /></label><br>
		<label>Page Title: <input type="text" name="title" /></label><br>
		<label>Logo Path (relative): <input type="text" name="logo" /></label><br>
		<legend>Optional:</legend>
		<label>Birthdate as Unix Timestamp: <input type="text" name="birth" /></label><br>
		<label>Flattr URL: <input type="text" name="flattr" /></label><br>
		<label>Piwik API Token: <input type="text" name="piwiktoken" /></label><br>
		<input type="submit" name="formaction" value="Save" />
	</fieldset>
</form>
<?
}
?>
</body>
</html>
