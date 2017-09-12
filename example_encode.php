<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");

	$archivo=file_get_contents("./pruebas/extremes/largests.txt");
	var_dump(unserialize( $archivo));
	// echo json_encode($archivo);