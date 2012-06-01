<?
	// Do not call this directly!
	if (basename($_SERVER['PHP_SELF']) == "render.php") {
		include("config.php");
		header('Location: '.$xythobuzCMS_root.'/stats.php?img');
		exit;
	}

	// We create a diagram of the monthly visitors
	header("Content-type: image/png");
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
	define("RADIUS", 12);

	$img = @imagecreate(WIDTH, HEIGHT)
			or die ("Can't create GD Stream!");
	$white = imagecolorallocate($img, 255, 255, 255); // Set white background
	$black = imagecolorallocate($img, 0, 0, 0);
	$grey = imagecolorallocate($img, 176, 176, 69);
	$blue = imagecolorallocate($img, 42, 42, 242);

	imagefilledrectangle($img, 0, 0, WIDTH-1, 1, $black); // Line top
	imagefilledrectangle($img, 0, 0, 1, HEIGHT-1, $black); // Line left
	imagefilledrectangle($img, WIDTH-1, 0, WIDTH-2, HEIGHT-1, $black); // Line right
	imagefilledrectangle($img, WIDTH-1, HEIGHT-1, 0, HEIGHT-2, $black); // Line bottom
	if ($renderVisitors) {
		imagestring($img, 4, 3, 3, "Visitors ".date('m.Y')." ".$xythobuzCMS_title, $black);
	} else {
		imagestring($img, 4, 3, 3, "Pageviews ".date('m.Y')." ".$xythobuzCMS_title, $black);
	}

	imagefilledrectangle($img, 50, 30, 52, (HEIGHT - 15), $black); // Y-Axis
	imagefilledrectangle($img, 40, (HEIGHT - 25), (WIDTH - 15), (HEIGHT - 27), $black); // X-Axis

	if ($renderVisitors) {
		$max = maxThisMonthB();
	} else {
		$max = maxThisMonth();
	}
	imagefilledrectangle($img, 42, 50, 60, 52, $black); // Max mark on y axis
	imagefilledrectangle($img, 42, ((diff(YSTART, YEND) / 2) + YEND - 2), 60, ((diff(YSTART, YEND) / 2) + YEND), $black); // Half mark on y axis
	imagestring($img, 3, 15, 45, $max, $black); // Max Number
	imagestring($img, 3, 15, ((diff(YSTART, YEND) / 2) + YEND - 8), floor($max / 2), $black);

	$day = date('d'); // For maximum x value

	if ($renderVisitors) {
		$sql = 'SELECT count(ip) AS count, day
		FROM cms_visit
		GROUP BY day
		HAVING MONTH(day) = '.date('m').'
		ORDER BY day ASC';
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
				$oldPoint = drawPoint($date, $row['count'], $max, $day, $img, $blue, $black);
			} else {
				$oldPoint = drawPoint($date, $row['count'], $max, $day, $img, $blue, $black, $oldPoint);
			}
		}
	} else {
		$sql = 'SELECT day, visitors
			FROM cms_visitors
			WHERE MONTH(day) = '.date('m').'
			ORDER BY day ASC';
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
				$oldPoint = drawPoint($date, $row['visitors'], $max, $day, $img, $blue, $black);
			} else {
				$oldPoint = drawPoint($date, $row['visitors'], $max, $day, $img, $blue, $black, $oldPoint);
			}
		}
	}

	// Finish
	imagestring($img, 2, 4, 17, "rendered by xythobuzCMS", $grey);
	imagepng($img);

	exit;

	// ------------------------------------
	// --------- Functions follow ---------
	// ------------------------------------

	// x = days, from 1 to $maxDay
	// y = views, from 0 to $max
	function drawPoint($x, $y, $maxViews, $maxDay, $img, $color1, $color2, $oldPoint) {
		// Beauty fix for first day of month
		if ($maxDay == 1) {
			$maxDay = 2;
		}

		// Draw point
		$a = ((diff(XSTART, XEND) * $x / $maxDay) + XSTART); // X Point
		$b = ((diff(YSTART, YEND) * ($maxViews - $y) / $maxViews) + YEND); // Y Point
		imagefilledellipse($img, $a, $b, RADIUS, RADIUS, $color1); // Point
		imagefilledrectangle($img, $a-1, HEIGHT-20, $a, HEIGHT-32, $color2); // Line on x-Axis
		if (($x == $maxDay) || ($x == ($maxDay / 2))) {
			imagestring($img, 3, $a-17, HEIGHT-17, $x.".".date('m'), $color2);
		}

		// Draw line to last point
		if (isset($oldPoint)) {
			imageline($img, $oldPoint[0], $oldPoint[1], $a, $b, $color1);
		}

		// Prepare return array
		$ret = array();
		$ret[0] = $a;
		$ret[1] = $b;
		return $ret;
	}

	// The highest daily pageviews this month
	function maxThisMonth() {
		$sql = 'SELECT day, visitors FROM cms_visitors WHERE MONTH(day) = '.date('m');
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

	function maxThisMonthB() {
		$sql = 'SELECT count(ip) AS count FROM cms_visit GROUP BY day HAVING MONTH(day) = '.date('m');
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

	function diff($a, $b) {
		if ($a > $b) {
			return $a - $b;
		} else {
			return $b - $a;
		}
	}
?>