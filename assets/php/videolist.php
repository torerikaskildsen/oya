<?php

	header('Content-Type: application/json');

	error_reporting(E_ALL);

	define('VIDEO_DIR', __DIR__ . '/../video');
	// define('JSON_FILE', __DIR__ . '/data/videos.json');

	if (!file_exists(VIDEO_DIR)) {
		die("Unable to find video dir: ". VIDEO_DIR);
	}


	$reply = array('videos' => array(), 'DEBUG' => array());
	$files = glob(VIDEO_DIR."/*");
	$baseUri = "assets/video/";

	$videoCount = 0;
	// default format
	$which = "widescreen";

	$reply['DEBUG'][] = "starting...";

	$width = 448;

	if (isset($_GET['width']) && isset($_GET['height'])) {
		$width = $_GET['width'];
		$reply['DEBUG'][] = "updating aspect from GET params";
		$aspect = $_GET['height'] ? $_GET['width']/$_GET['height'] : 1;
		if ($aspect < 1) {
			$which = "tallscreen";
			$width = "448";
			//tallscreen
		}
		else {
			//widescreen
			$which = "widescreen";
			$width = "672";
		}
	}

	$reply['DEBUG'][] = "which : $which";



	foreach ($files as $filename) {
		$pathinfo = pathinfo($filename);

		if (strpos(basename($filename), $width) !== 0) {
			// don't double count
			$reply['DEBUG'][] = "Skipping : " . basename($filename);
			continue;
		}

		$file = array();
		$file["filename"] = $pathinfo['basename'];
		$file['filesize'] = filesize($filename);
		$file['filetime'] = filemtime($filename);

		$file['uri'] = $baseUri . basename($filename);
		$file['id'] = count($reply['videos']);

		array_push($reply['videos'], $file);
	}

	// if (count($reply['videos']) != $videoCount) {
	// 	$reply['DEBUG'][] = "Updating JSON_FILE : ". count($reply['videos']) . ", " . $videoCount . " | " . JSON_FILE;
	// 	file_put_contents(JSON_FILE, json_encode($reply['videos'], JSON_PRETTY_PRINT));
	// }

	echo json_encode($reply, JSON_PRETTY_PRINT);

?>