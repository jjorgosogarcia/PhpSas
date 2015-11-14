<?php

require "./clases/AutoCarga.php";
$archivo = Request::get("file");
$trozos = pathinfo($archivo);
$extension = $trozos["extension"];
$nss = Request::get("nss");

if ($extension == "jpg") {
    header("Content-type: image/jpeg");
} else {
    header("Content-type: image/$extension");
}
readfile("../../imagenesSas/$nss/$archivo");
