<?php

if (!class_exists('admin_panel_imagens')) {

    class admin_panel_imagens {

        private $imagick;
        private $status;
        private $error;

        public function __construct($file) {
            if (!class_exists('Imagick')) {
                $this->error = 'La extensión Imagick no está instalada en el servidor.';
                $this->status = false;
                return;
            }

            if (!file_exists($file) || !is_readable($file)) {
                $this->error = 'El archivo de imagen no existe o no se puede leer.';
                $this->status = false;
                return;
            }

            try {
                $this->imagick = new Imagick($file);
                $this->status = true;
            } catch (Exception $e) {
                $this->error = "Error al cargar la imagen: " . $e->getMessage();
                $this->status = false;
            }
        }

        public function get_status() {
            return $this->status;
        }

        public function get_error() {
            return $this->error;
        }

        public function optimize($new_imagen, $quality = 75) {
            if (!$this->status) {
                $this->error = "No se puede optimizar la imagen: " . ($this->error ?: "Estado inválido");
                return false;
            }

            try {
                $this->imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
                $this->imagick->setImageCompressionQuality($quality);
                $this->imagick->setImageFormat('jpeg');
                $this->imagick->writeImage($new_imagen);
                return true;
            } catch (Exception $e) {
                $this->error = "Error al optimizar la imagen: " . $e->getMessage();
                return false;
            }
        }

        public function crop($new_imagen, $width, $height, $quality = 75) {
            if (!$this->status) {
                $this->error = "No se puede recortar la imagen: " . ($this->error ?: "Estado inválido");
                return false;
            }

            try {
                $this->imagick->thumbnailImage($width, $height, true);
                $this->imagick->cropImage($width, $height, ($this->imagick->getImageWidth() - $width) / 2, ($this->imagick->getImageHeight() - $height) / 2);
                $this->imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
                $this->imagick->setImageCompressionQuality($quality);
                $this->imagick->setImageFormat('jpeg');
                $this->imagick->writeImage($new_imagen);
                return true;
            } catch (Exception $e) {
                $this->error = "Error al recortar la imagen: " . $e->getMessage();
                return false;
            }
        }

        public function close() {
            if ($this->status && $this->imagick) {
                $this->imagick->clear();
                $this->imagick->destroy();
            }
        }
    }
}
