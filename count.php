<?
	$countFile = "counter.txt";
	$currentDay = date("j");
	$currentIp = $_SERVER['REMOTE_ADDR'];
	$addIp = 1;

	if (file_exists($countFile)) {
		$hits = file($countFile, FILE_IGNORE_NEW_LINES);
		if ($hits[0] == $currentDay) {
			// Still same day
			for ($i = 2; $i < count($hits); $i++) {
				if ($_SERVER['REMOTE_ADDR'] == $hits[$i]) {
					$addIp = 0;
					break;
				}
			}
			if ($addIp == 1) {
				$visitCount = $hits[1] + 1;
			} else {
				$visitCount = $hits[1];
			}
		} else {
			// New day
			$visitCount = 1;
		}
	} else {
		$visitCount = 1;
	}

	$fp = fopen($countFile, "w");
	fputs($fp, $currentDay."\n".$visitCount);
	for ($i = 2; $i < count($hits); $i++) {
		fputs($fp, "\n".$hits[$i]);
	}
	if ($addIp == 1) {
		fputs($fp, "\n".$_SERVER['REMOTE_ADDR']);
	}
	fclose($fp);
	echo $visitCount;
?>