<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");

	$archivo=file_get_contents("./Chat de WhatsApp con Santa 78. Luigi Bosco.txt");
	$re="#(\d+\/\d+\/\d+, \d+:\d+ - )#";
	$chat_array=preg_split($re, $archivo, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$chats=[];
	$i=0;

	foreach ($chat_array as $key => $value) {
		if ($key % 2 == 0) {
			$chats[$i]=$value;
		} else {
			// $chats[$i].=$value;
			$value=preg_replace('#[^A-Za-záéíóúñÁÉÍÓÚÑ0-9\-\s\:]#', '', $value);
			$value = preg_replace('/\x{0089}/u','', $value );
			$chats[$i].=utf8_encode($value);
			$i++;
		}
		
	}
	$topx=5;
	function lsort($a,$b){
		return strlen($b)-strlen($a);
	}
	usort($chats,'lsort');

	$largests=array_slice($chats, 0,$topx);
	file_put_contents("./pruebas/extremes/largests.txt", json_encode($largests));

	$shortests=array_slice($chats, max(array_keys($chats))-$topx, $topx);

	function ssort($a,$b){
		return strlen($a)-strlen($b);
	}
	usort($shortests,'ssort');
	file_put_contents("./pruebas/extremes/shortests.txt", json_encode($shortests,JSON_PARTIAL_OUTPUT_ON_ERROR));
?>