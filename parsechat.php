<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_http.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_mysql.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_mail.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_parse.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_parse_urls.php");
include("/Users/francisco/gdrive/PV_7puentes/fuentes/extract/catalogos/lib/LIB_arrays.php");

	$archivo=file_get_contents("./Chat de WhatsApp con Santa 78. Luigi Bosco.txt");
	// echo($archivo);
	$re="#(\d+\/\d+\/\d+, \d+:\d+ - )#";
	$chat_array=preg_split($re, $archivo, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	print_r($chat_array);

	// file_put_contents("./pruebas/output/chat1.txt", $chat_array[0]." ".$chat_array[1]);

	foreach ($chat_array as $key => $content_pair) {
		if ($key % 2 == 0) {
			$file_output_name="";
			echo "par: ".$key."\n";
			$fecha_hora=preg_split("/,/", $content_pair);
			$fecha=preg_split("#\/#", $fecha_hora[0]);
			$str_fecha_inv="";
			for ($i=2; $i >= 0 ; --$i) {
				if (strlen($fecha[$i])<2) {
					$str_fecha_inv.= "0".$fecha[$i];
				} else {
					$str_fecha_inv.=$fecha[$i];
				}
			}
			$file_output_name = trim($str_fecha_inv."_".str_replace("-", "", trim(str_replace(":", "", $fecha_hora[1]))));
			$file_output_name.="_".str_pad($key, 8, '0', STR_PAD_LEFT);
		} else {
			echo "impar: ".$key;
			file_put_contents("./pruebas/output/".$file_output_name.".txt", $content_pair);
		}
		echo "\n";
	}