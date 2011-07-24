<?
	if ((isset($_POST['username'])) && (isset($_POST['passwort'])) && ($_POST['username'] != "") && ($_POST['passwort'] != "")) {
		session_start();
		include('sql.php');
		$db = mysql_connect($sql_host, $sql_username, $sql_password);
		mysql_select_db($sql_database);
		if (mysql_errno()) {
			die ('Konnte keine Verbindung zur Datenbank aufbauen');
		}
		$username = mysql_real_escape_string($_POST['username']);
		$passwort = $_POST['passwort'];
		$hostname = $_SERVER['HTTP_HOST'];
		$path = dirname($_SERVER['PHP_SELF']);
		// Benutzername und Passwort werden überprüft
		$login = false;		
		$sql = 'SELECT
				password
			FROM
				cms_user
			WHERE
				username = "'.$username.'"';
		$result = mysql_query($sql);
		if (!result) {
			echo "Query Error!\n";
			exit;
		}

		if (!($row = mysql_fetch_array($result))) {
			echo "Username <i>".$username."</i> unknown!\n";
			exit;
		}
		mysql_close($db);
		$passhash = md5($passwort);
		if ($row['password'] == $passhash) {
			$_SESSION['angemeldet'] = true;
			if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
				if (php_sapi_name() == 'cgi') {
					header('Status: 303 See Other');
				} else {
					header('HTTP/1.1 303 See Other');
				}
			}
			header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/admin.php');
			exit;
		}
	}
?>
<!DOCTYPE HTML>
<html lang="de">
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="style.css" media="screen" />
<link rel="stylesheet" href="print.css" media="print" />
<link rel="author" href="mailto:taucher.bodensee@googlemail.com" />
<link rel="shortcut icon" href="http://www.xythobuz.org/favicon.ico" />
<meta name="author" content="Thomas Buck">
<title>xythobuz.org CMS Administration</title>
</head>
<body>
<div class="admin">
<form action="pwd.php" method="post">
	<fieldset>
		<label>Username: <input type="text" name="username" /></label><br>
		<label>Password: <input type="password" name="passwort" /></label><br>
		<input type="submit" value="Login" />
	</fieldset>
</form>
<a href="index.php">Home</a>
</div>
</body>
</html>
