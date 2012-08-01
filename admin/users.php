<? include('../auth.php');
include('../config.php');
include('../func.php');
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Konnte keine Verbindung zur Datenbank aufbauen');
}
header1();
?>
<title>xythobuz.org - Manage Users</title>
</head>
<body>
<div class="admin">
<?

// Create a new user
if (isset($_GET['p']) && ($_GET['p'] == "newuser")) {
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
} else if (isset($_GET['p']) && ($_GET['p'] == "deluser")) {
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
} else if (!isset($_GET['user'])) {
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
<form action="users.php?user=<? echo $row['username']; ?>" method="post">
	<td><input type="password" name="oldpass" /></td>
	<td><input type="password" name="newpass1" /></td>
	<td><input type="password" name="newpass2" /></td>
	<td><input type="submit" name="formaction" value="Change" /></td>
</form>
<?
		echo "<td><a href=\"users.php?p=deluser&amp;name=";
		echo $row['username'];
		echo "\">Löschen</a></td>";
		echo "</tr>\n";
	}
?>
</table>
<form action="users.php?p=newuser" method="post">
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

mysql_close($db);
?>
<hr>
<a href="<? echo $xythobuzCMS_root; ?>/logout.php">Logout</a><br>
<a href="<? echo $xythobuzCMS_root; ?>/index.php">Home</a><br>
<a href="<? echo $xythobuzCMS_root; ?>/admin.php">Admin</a><br>
</div>
</body>
</html>
