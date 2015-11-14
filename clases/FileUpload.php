<?php

class FileUpload {

    private $destino = "./", $nombre = "", $tamañoMaximo = 100000000;
    private $parametro, $error = false, $politica = self::RENOMBRAR,
            $errorSubida, $tamaño, $tmpName;

    const CONSERVAR = 1, REEMPLAZAR = 2, RENOMBRAR = 3;

    private $arrayDeTipos = array(
        "jpg" => 1,
        "gif" => 1,
        "png" => 1,
        "jpeg" => 1
    );
    private $extension;

    function __construct($parametro, $multi = null) {
        if ($multi !== null) {
            if (isset($multi[$parametro]) && $multi[$parametro]["name"] !== "") {
                $this->parametro = $parametro;
                $this->extension = pathinfo($multi[$this->parametro]["name"])["extension"];
                $this->nombre = pathinfo($multi[$this->parametro]["name"])["filename"];
                $this->errorSubida = $multi[$this->parametro]["error"];
                $this->tamaño = $multi[$this->parametro]["size"];
                $this->tmpName = $multi[$this->parametro]["tmp_name"];
            } else {
                $this->error = true;
            }
        } else {
            if (isset($_FILES[$parametro]) && $_FILES[$parametro]["name"] !== "") {
                $this->parametro = $parametro;
                $this->extension = pathinfo($_FILES[$this->parametro]["name"])["extension"];
                $this->nombre = pathinfo($_FILES[$this->parametro]["name"])["filename"];
                $this->errorSubida = $_FILES[$this->parametro]["error"];
                $this->tamaño = $_FILES[$this->parametro]["size"];
                $this->tmpName = $_FILES[$this->parametro]["tmp_name"];
            } else {
                $this->error = true;
            }
        }
    }

    public function getDestino() {
        return $this->destino;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getTamaño() {
        return $this->tamaño;
    }

    public function getPolitica() {
        return $this->politica;
    }

    public function setDestino($destino) {
        $this->destino = $destino;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setTamaño($tamaño) {
        $this->tamaño = $tamaño;
    }

    public function setPolitica($politica) {
        $this->politica = $politica;
    }

    function upload() {
        $nss = Request::post("id_us");
        $nombre = $this->nombre;
        if ($this->error) {
            return false;
        }
        if ($this->errorSubida != UPLOAD_ERR_OK) {
            return false;
        }
        if ($this->tamaño > $this->tamañoMaximo) {
            return false;
        }
        if (!$this->isTipo($this->extension)) {
            return false;
        }
        if (!is_dir($this->destino) || substr($this->destino, -1) !== "/") {
            return false;
        }
        if ($this->politica === self::CONSERVAR && file_exists($this->destino . $this->nombre . "." . $this->extension)) {
            return false;
        }
        if ($this->politica === self::RENOMBRAR && file_exists($this->destino . $nss . "/" . $this->nombre . "." . $this->extension)) {
            $nombre = $this->renombrar($nombre, $nss);
        }
        if (!self::directory($nss)) {
            return mkdir("../../imagenesSas/" . $nombre) + move_uploaded_file($this->tmpName, $this->destino . $nss . "/" . $nombre . "." . $this->extension);
        } else {
            return move_uploaded_file($this->tmpName, $this->destino . $nss . "/" . $nombre . "." . $this->extension);
        }
    }

    private function renombrar($nombre, $nss) {
        $i = 1;
        while (file_exists($this->destino . $nss . "/" . $nombre . "_" . $i . "." . $this->extension)) {
            $i++;
        }
        return $nombre . "_" . $i;
    }

    public function addTipo($tipo) {
        if (!$this->isTipo($tipo)) {
            $this->arrayDeTipos[$tipo] = 1;
            return true;
        }
        return false;
    }

    public function removeTipo($tipo) {
        if ($this->isTipo($tipo)) {
            unset($this->arrayDeTipos[$tipo]);
            return true;
        }
        return false;
    }

    public function isTipo($tipo) {
        return isset($this->arrayDeTipos[$tipo]);
    }

    private static function directory($nss) {
        if (file_exists("../../imagenesSas/" . $nss)) {
            return true;
        } else {
            return false;
        }
    }

    /* PERMITIR SUBIR UN ARCHIVO Y VARIOS ARCHIVOS */

    public static function transformar($parametro) {
        $array = array();
        $numeroArchivos = count($files['name']);
        for ($i = 0; $i < count($_FILES[$parametro]['name']); $i++) {
            $array[$i][$parametro]['name'] = $_FILES[$parametro]['name'][$i];
            $array[$i][$parametro]['type'] = $_FILES[$parametro]['type'][$i];
            $array[$i][$parametro]['tmp_name'] = $_FILES[$parametro]['tmp_name'][$i];
            $array[$i][$parametro]['error'] = $_FILES[$parametro]['error'][$i];
            $array[$i][$parametro]['size'] = $_FILES[$parametro]['size'][$i];
        }
        return $array;
    }
}
