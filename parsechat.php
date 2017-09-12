<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_http.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_mysql.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_mail.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_parse.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_parse_urls.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_arrays.php");

	// $archivo=file_get_contents("./pruebas/chat_corto.txt");
	$archivo=file_get_contents("./Chat de WhatsApp con Santa 78. Luigi Bosco.txt");
	$re="#(\d+\/\d+\/\d+, \d+:\d+ - )#";
	$chat_array=preg_split($re, $archivo, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	print_r($chat_array);
	$chunk_size=3000;
	$j=0;
	$chunks=ceil(sizeof($chat_array)/$chunk_size);
	$file_output_name="_".str_pad('0', 8, '0', STR_PAD_LEFT);
	foreach ($chat_array as $key => $content_pair) {
		echo $j."\n";
		if ($j==$chunk_size) {
			$file_output_name="_".str_pad($key, 8, '0', STR_PAD_LEFT);
			$j=0;
			file_put_contents("./pruebas/output/".$file_output_name.".txt", $content_pair);
			$j++;
			echo($j.": ");
			echo("\n".$file_output_name."\n");
		} else {
			$j++;
			file_put_contents("./pruebas/output/".$file_output_name.".txt", $content_pair, FILE_APPEND);
		}
	}