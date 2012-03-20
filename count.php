<?
	$countFile = "counter.txt";
	$currentDay = date("j");

	if (file_exists($countFile)) {
		$hits = file($countFile, FILE_IGNORE_NEW_LINES);
		if ($hits[0] == $currentDay) {
			// Still same day
			$visitCount = $hits[1] + 1;
		} else {
			// New day
			$visitCount = 1;
		}
	} else {
		$visitCount = 1;
	}

	$fp = fopen($countFile, "w");
	fputs($fp, $currentDay."\n".$visitCount);
	fclose($fp);

	echo $visitCount;
?>