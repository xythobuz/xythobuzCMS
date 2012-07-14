<?
	// Do not call this directly! We need a opened DB connection.
	if (basename($_SERVER['PHP_SELF']) == "render.php") {
		include("config.php");
		header('Location: '.$xythobuzCMS_root.'/stats.php');
		exit;
	}

	// We create a diagram of the monthly visitors
	header("Content-type: image/png");
	// Custom or preset width / height
	if ((isset($_GET['w'])) && (isset($_GET['h']))) {
		define("WIDTH", $_GET['w']);
		define("HEIGHT", $_GET['h']);
	} else if (isset($_GET['w'])) {
		define("WIDTH", $_GET['w']);
		define("HEIGHT", floor(WIDTH * 0.85));
	} else if (isset($_GET['h'])) {
		define("HEIGHT", $_GET['h']);
		define("WIDTH", floor(HEIGHT * 1.16));
	} else {
		define("WIDTH", 350);
		define("HEIGHT", 300);
	}
	define("XSTART", 60);
	define("XEND", (WIDTH - 25));
	define("YSTART", (HEIGHT - 30));
	define("YEND", 50);
	if (WIDTH <= 300) {
		define("RADIUS", 6);
	} else {
		define("RADIUS", 8);
	}

	// Used colors
	$img = @imagecreate(WIDTH, HEIGHT)
			or die ("Can't create GD Stream!");
	$white = imagecolorallocate($img, 255, 255, 255); // Set white background
	$black = imagecolorallocate($img, 0, 0, 0);
	$grey = imagecolorallocate($img, 176, 176, 69);
	$blue = imagecolorallocate($img, 42, 42, 242);

	if (isset($_GET['d'])) { // d is the number of days to show, ending today
		$d = $_GET['d'];
		if (date('d') < $d) { // Can't go past current month
			$d = date('d');
		}
		$start = (date('d') - $d);
		$printDays = 1; // Print no days, cause it's fucked up
		$clause = 'day > SUBDATE(NOW(), '.$d.')';
	} else {
		$d = 0;
		$start = 1;
		$clause = 'MONTH(day) = '.date('m');
		$printDays = 0; // Print days on y-axis
	}
	$day = date('d');

	if ($renderVisitors) {
		$max = maxThisMonthB($d);
	} else if ($renderBots) {
		$max = maxThisMonthC($d);
	} else {
		$max = maxThisMonth($d);
	}

	// Render datapoints
	if ($renderVisitors) {
		$sql = 'SELECT count(ip) AS visitors, day
		FROM cms_visit
		GROUP BY day
		HAVING '.$clause.'
		ORDER BY day ASC';
	} else if ($renderBots) {
		$sql = 'SELECT day, bots AS visitors
		FROM cms_bots
		WHERE '.$clause.'
		ORDER BY day ASC';
	} else {
		$sql = 'SELECT day, visitors
		FROM cms_visitors
		WHERE '.$clause.'
		ORDER BY day ASC';
	}

	$result = mysql_query($sql);
	if (!$result) {
		// Show error, exit
		imagestring($img, 5, (WIDTH / 2), (HEIGHT / 2), "Database Error!", $blue);
		imagestring($img, 2, (WIDTH - 145), (HEIGHT - 20), "rendered by xythobuzCMS", $grey);
		imagepng($img);
		exit;
	}

	// Display data points
	$oldPoint = 0;

	while ($row = mysql_fetch_array($result)) {
		$date = str_replace(date('Y-m-'), "", $row['day']); // Strip month and year
		if ($oldPoint == 0) {
			$oldPoint = drawPoint($date, $row['visitors'], $max, $day, $img, $blue, $black, NULL, $printDays, $start);
		} else {
			$oldPoint = drawPoint($date, $row['visitors'], $max, $day, $img, $blue, $black, $oldPoint, $printDays, $start);
		}
	}

	// Draw the usual stuff
	imagefilledrectangle($img, 0, 0, WIDTH-1, 1, $black); // Line top
	imagefilledrectangle($img, 0, 0, 1, HEIGHT-1, $black); // Line left
	imagefilledrectangle($img, WIDTH-1, 0, WIDTH-2, HEIGHT-1, $black); // Line right
	imagefilledrectangle($img, WIDTH-1, HEIGHT-1, 0, HEIGHT-2, $black); // Line bottom
	if ($renderVisitors) {
		imagestring($img, 4, 3, 3, "Visitors ".date('m.Y')." ".$xythobuzCMS_title, $black);
	} else if ($renderBots) {
		imagestring($img, 4, 3, 3, "Bots ".date('m.Y')." ".$xythobuzCMS_title, $black);
	} else {
		imagestring($img, 4, 3, 3, "Pageviews ".date('m.Y')." ".$xythobuzCMS_title, $black);
	}
	imagefilledrectangle($img, 50, 30, 52, (HEIGHT - 15), $black); // Y-Axis
	imagefilledrectangle($img, 40, (HEIGHT - 25), (WIDTH - 15), (HEIGHT - 27), $black); // X-Axis

	imagefilledrectangle($img, 42, 50, 60, 52, $black); // Max mark on y axis
	imagefilledrectangle($img, 42, ((diff(YSTART, YEND) / 2) + YEND - 2),
					60, ((diff(YSTART, YEND) / 2) + YEND), $black); // Half mark on y axis

	imagestring($img, 3, 5, 45, $max, $black); // Max Number
	
	if (($max / 2) < 1000) {
		$str = number_format($max / 2, 1, ".", "");
	} else {
		$str = number_format($max / 2, 0, ".", "");
	}
	imagestring($img, 3, 5, ((diff(YSTART, YEND) / 2) + YEND - 8),
					$str, $black); // Half number

	if (HEIGHT >= 200) {
		// Large enough that we want to render quarter marks
		imagefilledrectangle($img, 42, ((diff(YSTART, YEND) / 4) + YEND - 2),
					60, ((diff(YSTART, YEND) / 4) + YEND), $black); // 3 Quarter mark on y axis
		imagefilledrectangle($img, 42, ((diff(YSTART, YEND) * 3 / 4) + YEND - 2),
					60, ((diff(YSTART, YEND) * 3 / 4) + YEND), $black); // Quarter mark on y axis

		imagefilledrectangle($img, 42, ((diff(YSTART, YEND) / 8) + YEND),
					60, ((diff(YSTART, YEND) / 8) + YEND), $black); // 7/8 mark on y axis
		imagefilledrectangle($img, 42, ((diff(YSTART, YEND) * 7 / 8) + YEND),
					60, ((diff(YSTART, YEND) * 7 / 8) + YEND), $black); // 1/8 mark on y axis
		imagefilledrectangle($img, 42, ((diff(YSTART, YEND) * 3 / 8) + YEND),
					60, ((diff(YSTART, YEND) *3 / 8) + YEND), $black); // 5/8 mark on y axis
		imagefilledrectangle($img, 42, ((diff(YSTART, YEND) * 5 / 8) + YEND),
					60, ((diff(YSTART, YEND) * 5 / 8) + YEND), $black); // 3/8 mark on y axis

		if (($max / 2) < 1000) {
			$str = number_format($max / 4, 1, ".", "");
		} else {
			$str = number_format($max / 4, 0, ".", "");
		}
		imagestring($img, 3, 5, ((diff(YSTART, YEND) * 3 / 4) + YEND - 8), $str, $black); // Quarter number
		if (($max * 3 / 4) < 1000) {
			$str = number_format($max * 3 / 4, 1, ".", "");
		} else {
			$str = number_format($max * 3 / 4, 0, ".", "");
		}
		imagestring($img, 3, 5, ((diff(YSTART, YEND) / 4) + YEND - 8), $str, $black); // 3 Quarter number
	}

	if (WIDTH >= 200) {
		// Large enough that we want to render arrows at the axis tops
		imageline($img, 51, 30, 41, 40, $black); // Y-Axis, to the left
		imageline($img, 51, 30, 61, 40, $black); // Y-Axis, to the right
		imageline($img, (WIDTH - 15), (HEIGHT - 26), (WIDTH - 20), (HEIGHT - 34), $black); // X-Axis, to the top
		imageline($img, (WIDTH - 15), (HEIGHT - 26), (WIDTH - 20), (HEIGHT - 18), $black); // X-Axis, to the bottom
	}
	
	imagestring($img, 2, 4, 17, "rendered by xythobuzCMS", $grey);
	imagepng($img);

	exit;

	// ------------------------------------
	// --------- Functions follow ---------
	// ------------------------------------

	// x = days, from 1 to $maxDay
	// y = views, from 0 to $maxViews
	// printDays: 0 if you want days to be printed
	function drawPoint($x, $y, $maxViews, $maxDay, $img, $color1, $color2, $oldPoint = NULL, $printDays = 0, $minDay = 1) {
		// Beauty fix for first day of month
		if ($maxDay <= 1) {
			$maxDay = 2;
		}

		// Draw point
		$minDay = $minDay - 1;
		$a = ((diff(XSTART, XEND) * ($x - $minDay) / ($maxDay - $minDay + 1)) + XSTART); // X Point
		$b = ((diff(YSTART, YEND) * ($maxViews - $y) / $maxViews) + YEND); // Y Point
		imagefilledellipse($img, $a, $b, RADIUS, RADIUS, $color1); // Point
		imagefilledrectangle($img, $a-1, HEIGHT-20, $a, HEIGHT-32, $color2); // Line on x-Axis
		
		if (($printDays == 0) && ((($x - $minDay) == $maxDay) || (($x - $minDay) == ($maxDay / 2)))) {
			imagestring($img, 3, $a-7, HEIGHT-17, $x, $color2); // Day
		}

		// Draw line to last point
		if (isset($oldPoint) && (!is_null($oldPoint) && ($x != 1))) {
			imageline($img, $oldPoint[0], $oldPoint[1], $a, $b, $color1);
		}

		// Prepare return array
		$ret = array();
		$ret[0] = $a;
		$ret[1] = $b;
		return $ret;
	}


	// The highest daily pageviews this month, or last $d days
	function maxThisMonth($d = 0) {
		$sql = 'SELECT day, visitors FROM cms_visitors WHERE ';
		if ($d == 0) {
			$sql .= 'MONTH(day) = '.date('m');
		} else {
			$sql .= 'day > SUBDATE(NOW(), '.$d.')';
		}
		$result = mysql_query($sql);
		if ($result) {
			$maxVisitors = 0;
			while ($row = mysql_fetch_array($result)) {
				if ($row['visitors'] > $maxVisitors) {
					$maxVisitors = $row['visitors'];
				}
			}
			return $maxVisitors;
		} else {
			return 0;
		}
	}

	function maxThisMonthB($d = 0) {
		$sql = 'SELECT count(ip) AS count FROM cms_visit GROUP BY day HAVING ';
		if ($d == 0) {
			$sql .='MONTH(day) = '.date('m');
		} else {
			$sql .= 'day > SUBDATE(NOW(), '.$d.')';
		}
		$result = mysql_query($sql);
		if ($result) {
			$maxVisitors = 0;
			while ($row = mysql_fetch_array($result)) {
				if ($row['count'] > $maxVisitors) {
					$maxVisitors = $row['count'];
				}
			}
			return $maxVisitors;
		} else {
			return 0;
		}
	}

	function maxThisMonthC($d = 0) {
		$sql = 'SELECT day, bots FROM cms_bots WHERE ';
		if ($d == 0) {
			$sql .='MONTH(day) = '.date('m');
		} else {
			$sql .= 'day > SUBDATE(NOW(), '.$d.')';
		}
		$result = mysql_query($sql);
		if ($result) {
			$maxVisitors = 0;
			while ($row = mysql_fetch_array($result)) {
				if ($row['bots'] > $maxVisitors) {
					$maxVisitors = $row['bots'];
				}
			}
			return $maxVisitors;
		} else {
			return 0;
		}
	}

	function diff($a, $b) {
		if ($a > $b) {
			return $a - $b;
		} else {
			return $b - $a;
		}
	}
?>