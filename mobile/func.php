<?
function listNews() {
	$sql = 'SELECT ueberschrift, datum, id
	FROM cms_news
	ORDER BY datum DESC';
	$result = mysql_query($sql);
	if (!$result) {
		die("Query Error");
	}
	while ($row = mysql_fetch_array($result)) {
?>	<ul class="pageitem">
		<li class="menu">
			<a href="index.php?news=<? echo $row['id'] ?>">
				<span class="name"><? echo $row['ueberschrift']." (".$row['datum'].")"; ?></span>
				<span class="arrow"></span>
			</a>
		</li>
	</ul>
<?	}
}

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