<?php

    if ( !class_exists('admin_panel_files')){

        class admin_panel_files {

            public function listarCarpeta($carpeta = ''){
                $resultado = array();
                try {
                    $iterator = new DirectoryIterator($carpeta);
                    foreach ($iterator as $archivo) {
                        if ($archivo->isDot()) continue;
                        if ($archivo->isDir()) {
                            $resultado[] = $archivo->getFilename();
                        } else {
                            $resultado[] = $archivo->getFilename();
                        }
                    }
                } catch (Exception $e) {
                    return false;
                }
                return $resultado;
            }

            public function ExisteElemento($carpeta, $archivo = ''){
                if(!$archivo) return is_dir($carpeta);
                return file_exists($carpeta . $archivo);
            }

            public function crearCarpeta($carpeta){
                return mkdir($carpeta);
            }

            public function NuevoArchivo($carpeta, $nombre, $contenido){
                if(!$this->EliminarElemento($carpeta, $nombre)) return false;
                if(!$this->ExisteElemento($carpeta) && !$this->crearCarpeta($carpeta)) return false;
                return move_uploaded_file($contenido, $carpeta . $nombre);
            }

            public function NuevoArchivoData($carpeta, $nombre, $contenido){
                if(!$this->EliminarElemento($carpeta, $nombre)) return false;
                if(!$this->ExisteElemento($carpeta) && !$this->crearCarpeta($carpeta)) return false;
                return file_put_contents($carpeta . $nombre, $contenido) !== false;
            }

            public function DescargarArchivo($carpeta, $nombre, $mime = false, $extra = false){
                if($file = $this->ObtenerArchivo($carpeta . $nombre)){
                    if($mime === false) $mime = $this->mime_content_type_extra($nombre);
                    $file = file_get_contents($carpeta . $nombre);
                    header('Content-Type: ' . $mime);
                    header('Content-Length: '.filesize($carpeta . $nombre));
                    header('Cache-Control: public, must-revalidate, max-age=0');
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
                    if($extra === true){
                        header('Content-Disposition: inline; filename="' . $nombre . '"');
                    }else{
                        header('Content-Disposition: attachment; filename="' . $nombre . '"');
                    }
                    echo $file;
                    if($extra === 3) $this->eliminarElemento($carpeta, $nombre);
                } else {
                    echo "Archivo no disponible";
                }
                exit;
            }

            public function ObtenerArchivo($carpeta, $nombre){
                if($this->ExisteElemento($carpeta, $nombre)){
                    return file_get_contents($carpeta . $nombre);
                } else {
                    return false;
                }
            }

            public function IniciarArchivo($carpeta, $nombre, $contenido){
                return file_put_contents($carpeta . $nombre, $contenido) !== false;
            }

            public function ActualizarArchivo($carpeta, $nombre, $contenidoNuevo){
                return file_put_contents($carpeta . $nombre, $contenido, FILE_APPEND) !== false;
            }

            public function EliminarElemento($carpeta, $archivo = ''){
                if(!$this->ExisteElemento($carpeta, $archivo)) return true;
                if($archivo) return unlink($carpeta . $archivo);
                return rmdir($carpeta);
            }

            public function GuardarArchivoPorURL($url, $carpeta, $nombre){
                $response = wp_remote_get($url);
                if (is_wp_error($response)) return false;
                $data = wp_remote_retrieve_body($response);
                return $this->NuevoArchivoData($carpeta, $nombre, $data);
            }

            public function EliminarContenido($carpeta){
                if(!is_dir($carpeta)) return false;
                $carpetacontenido = opendir($carpeta);
                if (!$carpetacontenido) return false;
                while (($archivo = readdir($carpetacontenido)) !== false) {
                    if ($archivo != '.' && $archivo != '..') {
                        $ubicacion = $carpeta . DIRECTORY_SEPARATOR . $archivo;
                        if (is_dir($ubicacion)) {
                            $this->EliminarContenido($ubicacion);
                            rmdir($ubicacion);
                        } else {
                            unlink($ubicacion);
                        }
                    }
                }
                closedir($carpetacontenido);
                return true;
            }

            public function mime_content_type_extra($filename) {
                $mime_types = array(

                    'txt' => 'text/plain',
                    'htm' => 'text/html',
                    'html' => 'text/html',
                    'php' => 'text/html',
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'json' => 'application/json',
                    'xml' => 'application/xml',
                    'swf' => 'application/x-shockwave-flash',
                    'flv' => 'video/x-flv',

                    // images
                    'png' => 'image/png',
                    'jpe' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'jpg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'bmp' => 'image/bmp',
                    'ico' => 'image/vnd.microsoft.icon',
                    'tiff' => 'image/tiff',
                    'tif' => 'image/tiff',
                    'svg' => 'image/svg+xml',
                    'svgz' => 'image/svg+xml',

                    // archives
                    'zip' => 'application/zip',
                    'rar' => 'application/x-rar-compressed',
                    'exe' => 'application/x-msdownload',
                    'msi' => 'application/x-msdownload',
                    'cab' => 'application/vnd.ms-cab-compressed',

                    // audio/video
                    'mp3' => 'audio/mpeg',
                    'qt' => 'video/quicktime',
                    'mov' => 'video/quicktime',

                    // adobe
                    'pdf' => 'application/pdf',
                    'psd' => 'image/vnd.adobe.photoshop',
                    'ai' => 'application/postscript',
                    'eps' => 'application/postscript',
                    'ps' => 'application/postscript',

                    // ms office
                    'doc' => 'application/msword',
                    'rtf' => 'application/rtf',
                    'xls' => 'application/vnd.ms-excel',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'docx' => 'application/msword',
                    'xlsx' => 'application/vnd.ms-excel',
                    'pptx' => 'application/vnd.ms-powerpoint',


                    // open office
                    'odt' => 'application/vnd.oasis.opendocument.text',
                    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
                );

                $ext = explode('.',$filename);
                $ext = array_pop($ext);
                $ext = strtolower($ext);

                if(array_key_exists($ext, $mime_types)){ 
                    return $mime_types[$ext]; 
                }else { 
                    return 'application/octet-stream'; 
                } 
            }

        }
    }