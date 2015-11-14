<?php

require "./clases/AutoCarga.php";
$archivos = FileUpload::transformar("imagen");
$contadorSubidas = 0;
$contadorFallo = 0;
$nss = Request::post("id_us");
$dir = "../../imagenesSas";
if (!file_exists($dir)) {
    mkdir("$dir");
}
foreach ($archivos as $archivo) {
    $subir = new FileUpload("imagen", $archivo);
    $subir->setDestino("$dir/");
    $subir->setNombre($nss);
    if ($subir->upload()) {
        $contadorSubidas++;
    } else {
        $contadorFallo++;
    }
}
header("Location:fotosUsuario.php?nss=$nss&subidas=$contadorSubidas&noSubidas=$contadorFallo");
exit();
