<? include('auth.php'); ?>
<!DOCTYPE HTML>
<html lang="de">
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="style.css" media="screen" />
<link rel="stylesheet" href="print.css" media="print" />
<link rel="author" href="mailto:taucher.bodensee@googlemail.com" />
<link rel="shortcut icon" href="http://www.xythobuz.org/favicon.ico" />
<meta name="author" content="Thomas Buck">
<?
include('sql.php');
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Konnte keine Verbindung zur Datenbank aufbauen');
}
?>
<title>xythobuz.org CMS Administration</title>
</head>
<body>
<div class="admin">
<?
if (!isset($_GET['p'])) {
?>
<h1>xythobuz.org CMS Admin-Area</h1>
<a href="admin.php?p=add">Add Page</a><br>
<a href="admin.php?p=edit">Edit Page</a><br>
<a href="admin.php?p=delete">Delete Page</a><br>
<a href="admin.php?p=addnews">Add News</a><br>
<a href="admin.php?p=deletenews">Delete News</a><br>
<a href="admin.php?p=deletecomment">Delete Comment</a><br>
<a href="admin.php?p=user">Manage Users</a><br>
<hr>
<a href="/piwik/">Piwik Statistics</a><br>
<?
	// Update sitemap
	$sitemap = "<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\nxmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\nxsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\nhttp://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n\n<url>\n\t<loc>http://www.xythobuz.org/index.php</loc>\n</url>\n<url>\n\t<loc>http://www.xythobuz.org/news.php</loc>\n</url>\n\n";

	$sql = 'SELECT
				kuerzel
			FROM
				cms
			ORDER BY
				id';
	$result = mysql_query($sql);
	if (!$result) {
		die ('Query-Error!');
	}
	while ($row = mysql_fetch_array($result)) {
		$sitemap = $sitemap."<url>\n\t<loc>";
		$sitemap = $sitemap."http://www.xythobuz.org/index.php?p=";
		$sitemap = $sitemap.$row['kuerzel'];
		$sitemap = $sitemap."</loc>\n</url>\n\n";
	}
	$sitemap = $sitemap."</urlset>";

	$path = 'sitemap.xml';
	if(!file_exists($path)) {
		if (touch($path)) {
			print "Created $path...<br>\n";
		} else {
			print "Could not create $path...<br>\n";
			exit;
		}
	}
	if (!$handle = fopen($path, "w")) {
		print "Kann $path nicht öffnen!<br>\n";
		exit;
	}
	if (!fwrite($handle, $sitemap)) {
		print "Kann nicht in $path schreiben!<br>\n";
		exit;
	}
	fclose($handle);
	print "Saved $path...<br>\n";

	$sql = 'SELECT
				datum,
				ueberschrift,
				inhalt,
				id
			FROM
				cms_news
			ORDER BY
				datum DESC';
	$result = mysql_query($sql);
	if (!$result) {
		die ("Error!!");
	}
	$rss = '<?xml version="1.0"?>'."\n";
	$rss = $rss.'<rss version="2.0">'."\n";
	$rss = $rss."\t<channel>\n\t\t<title>xythobuz.org Blog</title>\n";
	$rss = $rss."\t\t<link>http://www.xythobuz.org/news.php</link>\n";
	$rss = $rss."\t\t<description>xythobuz.org - Artikel und Links von xythobuz</description>\n";
	$rss = $rss."\t\t<language>de-de</language>\n";
	$rss = $rss."\t\t<generator>xythobuz.org CMS</generator>\n";
	while ($row = mysql_fetch_array($result)) {
		$rss = $rss."\t\t<item>\n";
		$rss = $rss."\t\t\t<title>".str_replace('>', '&gt;', str_replace('<', '&lt;', stripslashes($row['ueberschrift'])))."</title>\n";

		$rss = $rss."\t\t\t<link>http://www.xythobuz.org/news.php?beitrag=".$row['id']."</link>\n";

		$rss = $rss."\t\t\t<description>".str_replace('>', '&gt;', str_replace('<', '&lt;', stripslashes($row['inhalt'])))."</description>\n";
		$rss = $rss."\t\t\t<pubDate>".date(DATE_RSS, strtotime($row['datum']))."</pubDate>\n";
		$rss = $rss."\t\t\t<guid>http://www.xythobuz.org/news.php?beitrag=".$row['id']."</guid>\n";
		$rss = $rss."\t\t</item>\n";
	}
	$rss = $rss."\t</channel>\n</rss>";

	$path = 'rss.xml';
	if(!file_exists($path)) {
		if (touch($path)) {
			print "Created $path...<br>\n";
		} else {
			print "Could not create $path...<br>\n";
			exit;
		}
	}
	if (!$handle = fopen($path, "w")) {
		print "Kann $path nicht öffnen!<br>\n";
		exit;
	}
	if (!fwrite($handle, $rss)) {
		print "Kann nicht in $path schreiben!<br>\n";
		exit;
	}
	fclose($handle);
	print "Saved $path...<br>\n";

} else {
	if ($_GET['p'] == 'add') {
		if(!isset($_POST['content'])) {
?>
<h1>Add Page</h1>
<p>Enter content in text area below. Use html tags! * = mandatory</p>
<form action="admin.php?p=add" method="post">
	<fieldset>
		<legend>Meta</legend>
		<label>Short-Name: *<input type="text" name="short" /></label>
		<label>Parent: <select name="parent">
			<option value="0">0 -&gt; Root</option>
			<?
			$sql = 'SELECT
				id, linktext
			FROM
				cms';
			$result = mysql_query($sql);
			if (!$result) {
				echo "Query Error!";
				exit;
			}
			while ($row = mysql_fetch_array($result)) {
				if ($row['id'] != $_POST['id']) {
					echo '<option value="';
					echo $row['id'];
					echo '">';
					echo $row['linktext'];
					echo "</option>\n";
				}
			}
?>
		</select></label><br>
		<label>Description: *<input type="text" name="desc" /></label>
		<label>Link Text: *<input type="text" name="link" /></label>
		<label>Order: <input type="text" name="order" /></label>
	</fieldset>
	<textarea name="content" rows="20" cols="80">Hier Inhalt eingeben</textarea>
	<textarea name="content_en" rows="20" cols="80">No translation available...</textarea>
	<input type="submit" name="formaction" value="Add Page!" />
</form>
<?
		} else {
			if ($_POST['parent'] == "") {
				$Pparent = 0;
			} else {
				if (!is_numeric($_POST['parent'])) {
					echo "Parent NaN!";
					exit;
				}
				if ($_POST['parent'] != 0) {
					$sql = 'SELECT id FROM cms WHERE id = "'.$_POST['parent'].'"';
					$result = mysql_query($sql);
					if (!$result) {
						echo "Parent doesn't exist!";
						exit;
					}
					if (!($row = mysql_fetch_array($result))) {
						echo "Parent doesn't exist.";
						exit;
					}
				}
				$Pparent = mysql_real_escape_string($_POST['parent']);
			}
			if ($_POST['order'] != "") {
				if (!is_numeric($_POST['order'])) {
					echo "Order NaN!";
					exit;
				}
				$Porder = $_POST['order'];
			} else {
				$sql = 'SELECT order FROM cms ORDER BY DESC ord';
				$result = mysql_query($sql);
				if (!$result) {
					die ('Query-Error!');
				}
				$row = mysql_fetch_array($result);
				$Porder = $row['ord'] + 1;
			}
			if ( (!isset($_POST['short'])) || (!isset($_POST['desc'])) || (!isset($_POST['link'])) ) {
				print "Left mandatory fields blank!";
				exit;
			}
			$sql = 'SELECT kuerzel FROM cms WHERE kuerzel = "'.mysql_real_escape_string($_POST['short']).'"';
			$result = mysql_query($sql);
			if (!$result) {
				echo "Query error!";
				exit;
			}

			if (isset($_REQUEST['content'])) {
				$conte = str_replace("\r\n", "\n", $_REQUEST['content']);
				$conte = mysql_real_escape_string($conte);
			} else {
				print "No Content!";
				exit;
			}
			if (isset($_REQUEST['content_en'])) {
				$conte2 = str_replace("\r\n", "\n", $_REQUEST['content_en']);
				$conte2 = mysql_real_escape_string($conte2);
			} else {
				print "No Content!";
				exit;
			}
			
			$sql = 'INSERT INTO
					cms(kuerzel, beschreibung, linktext, kategorie, ord, inhalt, inhalt_en)
				VALUES
					("'.mysql_real_escape_string($_POST['short']).'",
					"'.mysql_real_escape_string($_POST['desc']).'",
					"'.mysql_real_escape_string($_POST['link']).'",
					"'.$Pparent.'",
					"'.$Porder.'",
					"'.$conte.'",
					"'.$conte2.'" )';
			$result = mysql_query($sql);
			if (!$result) {
				echo "Query Error(6)!";
				exit;
			}
			echo "Entry added successfully...!<br>\n";
		}
	}
	
	if ($_GET['p'] == 'addnews') {
		if (!isset($_POST['ueber'])) {
?>
<h1>Add News</h1>
<form action="admin.php?p=addnews" method="post">
	<fieldset>
		<label>Überschrift: <input type="text" name="ueber"></label><br>
		<textarea name="content" rows="20" cols="80"></textarea><br>
		<input type="submit" name="formaction" value="Add" />
	</fieldset>
</form>
<?
		} else {
			$sql = 'INSERT INTO
				cms_news(datum, ueberschrift, inhalt)
			VALUES
				(FROM_UNIXTIME('.time().'),
				"'.mysql_real_escape_string($_POST['ueber']).'",
				"'.mysql_real_escape_string($_POST['content']).'")';
			$result = mysql_query($sql);
			if (!$result) {
				echo "Error!";
				exit;
			}
			echo "News added!";
		}
	}

	if ($_GET['p'] == 'edit') {
?>
<h1>Edit Page</h1>
<?
		// kuerzel, linktext, beschreibung, pfad, indent
		if ( (!isset($_POST['id'])) && (!isset($_GET['id'])) ) {
?>
<p>Select Page:</p>
<form action="admin.php?p=edit" method="post">
	<select name="id">
<?
			$sql = 'SELECT
				id,
				linktext
			FROM
				cms
			ORDER BY
				id';
			$result = mysql_query($sql);
			if (!$result) {
				echo "Querry Error!";
				exit;
			}
			while ($row = mysql_fetch_array($result)) {
				echo '<option value="';
				echo $row['id'];
				echo '">';
				echo $row['linktext'];
				echo "</option>\n";
			}
?>
	</select>
	<input type="submit" name="formaction" value="Edit" />
</form>
<?
		} else if (isset($_POST['id'])) {
			$sql = 'SELECT
				kuerzel,
				linktext,
				beschreibung,
				ord,
				inhalt,
				inhalt_en
			FROM
				cms
			WHERE id = '.mysql_real_escape_string($_POST['id']);
			$result = mysql_query($sql);
			if (!$result) {
				echo "Query-Error";
				exit;
			}
			$row = mysql_fetch_array($result);
?>
	<form action="admin.php?p=edit&amp;id=<? echo $_POST['id']; ?>" method="post">
	<fieldset>
		<label>Linktext: <input type="text" name="link" value="<?
			echo $row['linktext'];
?>"></label><br>
		<label>Beschreibung: <input type="text" name="desc" value="<?
			echo $row['beschreibung'];
?>"></label><br>
		<label>Kategorie: <select name="kat">
			<option value="0">0 -&gt; Root</option>
			<?
			$sql = 'SELECT
				id, linktext
			FROM
				cms';
			$result = mysql_query($sql);
			if (!$result) {
				echo "Query Error!";
				exit;
			}
			while ($row2 = mysql_fetch_array($result)) {
				if ($row2['id'] != $_POST['id']) {
					echo '<option value="';
					echo $row2['id'];
					echo '">';
					// echo $row2['id'];
					// echo " -&gt; ";
					echo $row2['linktext'];
					echo "</option>\n";
				}
			}
?>
		</select></label><br>
		<label>Order: <input type="text" name="ord" value="<?
			echo $row['ord'];
?>"></label><br>
		<textarea name="content" rows="20" cols="80"><?
			echo stripslashes($row['inhalt']);
?></textarea><br>
		<textarea name="content_en" rows="20" cols="80"><?
			echo stripslashes($row['inhalt_en']);
?></textarea><br>
		<input type="submit" name="formaction" value="Save" />
	</fieldset>
</form>
<?
		} else {
			if ( ($_POST['link'] == "") || ($_POST['desc'] == "") || ($_POST['kat'] == $_GET['id']) || ($_POST['ord'] == "") || ($_POST['content'] == "") || ($_POST['content_en'] == "") ) {
				echo "Empty Field!\n";
				exit;
			}
			$link = mysql_real_escape_string($_POST['link']);
			$desc = mysql_real_escape_string($_POST['desc']);
			$kat = mysql_real_escape_string($_POST['kat']);
			$ord = mysql_real_escape_string($_POST['ord']);
			$content = mysql_real_escape_string($_POST['content']);
			$content_en = mysql_real_escape_string($_POST['content_en']);
			$id = mysql_real_escape_string($_GET['id']);

			$sql = 'UPDATE
				cms
			SET
				linktext = "'.$link.'",
				beschreibung = "'.$desc.'",
				kategorie = '.$kat.',
				ord = '.$ord.',
				inhalt = "'.$content.'",
				inhalt_en = "'.$content_en.'"
			WHERE
				id = '.mysql_real_escape_string($_GET['id']);
			$result = mysql_query($sql);
			if (!$result) {
				echo "Query Error. Nothing updated!";
				exit;
			}
			echo "Edited ";
			echo $link;
			echo "successfully!";
		}
	}

	if ($_GET['p'] == 'delete') {
		if (!isset($_GET['w'])) {
			$sql = 'SELECT
				id,
				kuerzel,
				linktext,
				beschreibung,
				kategorie,
				ord
			FROM
				cms
			ORDER BY
				id';
			$result = mysql_query($sql);
			if (!$result) {
				die ('Query-Error!');
			}
?>
<table border="1">
<tr><th>ID</th>
<th>Kürzel</th>
<th>LinkText</th>
<th>Beschreibung</th>
<th>Kategorie (Parent)</th>
<th>Order</th>
<th>Löschen?</th></tr>
<?
			while ($row = mysql_fetch_array($result)) {
				echo "<tr>";
				echo "<td>".$row['id']."</td>\n";
				echo "<td>".$row['kuerzel']."</td>\n";
				echo "<td>".$row['linktext']."</td>\n";
				echo "<td>".$row['beschreibung']."</td>\n";
				echo "<td>".$row['kategorie']."</td>\n";
				echo "<td>".$row['ord']."</td>\n";
				echo '<td><a href="admin.php?p=delete&amp;w='.$row['kuerzel'].'">Löschen</a></td></tr>'."\n";
			}
?>
</table>
<?
		} else {
			$sql = 'DELETE FROM cms
				WHERE kuerzel = "'.$_GET['w'].'"';
			$result = mysql_query($sql);
			if (!$result) {
				print "Database delete failed! (4)<br>\n";
				exit;
			}
			print "Deleted Database Entry...<br>\n";
		}
	}

	if ($_GET['p'] == 'deletenews') {
		if (!isset($_GET['w'])) {
			$sql = 'SELECT
				id,
				ueberschrift
			FROM
				cms_news
			ORDER BY
				id';
			$result = mysql_query($sql);
			if (!$result) {
				die ('Query-Error!');
			}
?>
<table border="1">
<tr><th>ID</th>
<th>Überschrift</th>
<th>Löschen?</th></tr>
<?
			while ($row = mysql_fetch_array($result)) {
				echo "<tr>";
				echo "<td>".$row['id']."</td>\n";
				echo "<td>".$row['ueberschrift']."</td>\n";
				echo '<td><a href="admin.php?p=deletenews&amp;w='.$row['id'].'">Löschen</a></td></tr>'."\n";
			}
?>
</table>
<?
		} else {
			$sql = 'DELETE FROM cms_news
				WHERE id = '.mysql_real_escape_string($_GET['w']).'';
			$result = mysql_query($sql);
			if (!$result) {
				print "Database delete failed! (42)<br>\n";
				exit;
			}
			print "Deleted Database Entry...<br>\n";
		}
	}
	
	if ($_GET['p'] == 'deletecomment') {
		if (!isset($_GET['w'])) {
			$sql = 'SELECT
				id,
				autor,
				inhalt
			FROM
				cms_comments
			ORDER BY
				datum ASC';
			$result = mysql_query($sql);
			if (!$result) {
				die ('Query-Error!');
			}
?>
<table border="1">
<tr><th>Autor</th>
<th>Inhalt</th>
<th>Löschen?</th></tr>
<?
			while ($row = mysql_fetch_array($result)) {
				echo "<tr>";
				echo "<td>".$row['autor']."</td>\n";
				echo "<td>".$row['inhalt']."</td>\n";
				echo '<td><a href="admin.php?p=deletecomment&amp;w='.$row['id'].'">Löschen</a></td></tr>'."\n";
			}
?>
</table>
<?
		} else {
			$sql = 'DELETE FROM cms_comments
				WHERE id = '.mysql_real_escape_string($_GET['w']).'';
			$result = mysql_query($sql);
			if (!$result) {
				print "Database delete failed! (44)<br>\n";
				exit;
			}
			print "Deleted Database Entry...<br>\n";
		}
	}

	if ($_GET['p'] == 'user') {
		if (!isset($_GET['user'])) {
?>
<table border="1">
<tr><th>Username</th>
<th>Old Pass</th>
<th>New Pass</th>
<th>New Pass</th>
<th>Send</th>
<th>Delete</th></tr>
<?
			$sql = 'SELECT username, password FROM cms_user';
			$result = mysql_query($sql);
			if (!$result) {
				echo "Query Error";
				exit;
			}
			while ($row = mysql_fetch_array($result)) {
				echo "<tr>";
				echo "<td>".$row['username']."</td>\n";
?>
<form action="admin.php?p=user&amp;user=<? echo $row['username']; ?>" method="post">
	<td><input type="password" name="oldpass" /></td>
	<td><input type="password" name="newpass1" /></td>
	<td><input type="password" name="newpass2" /></td>
	<td><input type="submit" name="formaction" value="Change" /></td>
</form>
<?
				echo "<td><a href=\"admin.php?p=deluser&amp;name=";
				echo $row['username'];
				echo "\">Löschen</a></td>";
				echo "</tr>\n";
			}
?>
</table>
<form action="admin.php?p=newuser" method="post">
	<fieldset>
		<legend>New User</legend>
		<label>Username: <input type="text" name="username" /></label><br>
		<label>Password: <input type="password" name="pass1" /></label><br>
		<label>Password: <input type="password" name="pass2" /></label><br>
		<input type="submit" value="Save" />
	</fieldset>
</form>
<?
		} else {
			if ($_POST['newpass1'] != $_POST['newpass2']) {
				echo "Passwords not equal!";
				exit;
			}
			$sql = 'SELECT username, password FROM cms_user WHERE username = "'.mysql_real_escape_string($_GET['user']).'"';
			$result = mysql_query($sql);
			if (!$result) {
				echo "Query Error!";
				exit;
			}
			$row = mysql_fetch_array($result);
			if ($row['username'] != $_GET['user']) {
				echo "Username Error?!";
				exit;
			}
			if (md5($_POST['oldpass']) != $row['password']) {
				echo "Old Password incorrect!";
				exit;
			}
			$sql = 'UPDATE cms_user SET password = "'.md5($_POST['newpass1']).'" WHERE username = "'.$_GET['user'].'"';
			$request = mysql_query($sql);
			if (!$request) {
				echo "Query Error!";
				exit;
			}
			echo "Password for ".$_GET['user']." changed successfully!";
		}
	}

	if ($_GET['p'] == "newuser") {
		if ($_POST['pass1'] != $_POST['pass2']) {
			echo "New Passwords not equal!";
			exit;
		}
		$sql = 'SELECT username FROM cms_user';
		$result = mysql_query($sql);
		if (!$result) {
			echo "Query-Error";
			exit;
		}
		while ($row = mysql_fetch_array($result)) {
			if ($_POST['username'] == $row['username']) {
				echo "Username already existing!";
				exit;
			}
		}
		$sql = 'INSERT INTO cms_user(username, password) VALUES ( "'.mysql_real_escape_string($_POST['username']).'", "'.md5($_POST['pass1']).'" )';
		$result = mysql_query($sql);
		if (!$result) {
			echo "Query Error!";
			exit;
		}
		echo "User created!<br>\n";
	}

	if ($_GET['p'] == "deluser") {
		$sql = 'SELECT username FROM cms_user';
		$result = mysql_query($sql);
		if (!$result) {
			echo "Query Error";
			exit;
		}
		$found = "";
		while ($row = mysql_fetch_array($result)) {
			if ($_GET['name'] == $row['username']) {
				$found = $row['username'];
			}
		}
		if ($found == "") {
			echo "No such user!";
			exit;
		}
		$sql = 'DELETE FROM cms_user WHERE username = "'.$found.'"';
		$result = mysql_query($sql);
		if (!$result) {
			echo "Query Error";
			exit;
		}
		echo "Deleted user!<br>\n";
	}
}

mysql_close($db);
?>
<hr>
<a href="logout.php">Logout</a><br>
<a href="index.php">Home</a><br>
<a href="admin.php">Admin</a><br>
</div>
</body>
</head>
