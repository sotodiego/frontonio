<?php

    if ( !class_exists('admin_panel_s3')){

        class admin_panel_s3 {

            const bucketName = ADPNSY_S3_BUKET;
            const key        = ADPNSY_S3_KEY;
            const secret     = ADPNSY_S3_SECRET;
            const region     = ADPNSY_S3_REGION;

            private $s3;

            public function __construct(){

                require 'aws/aws-autoloader.php';

                $credentials = [
                    'key'    => self::key,
                    'secret' => self::secret,
                    'region' => self::region,
                ];
        
                $this->s3 = new Aws\S3\S3Client([
                    'version' => 'latest',
                    'region' => $credentials['region'],
                    'credentials' => $credentials
                ]);
            }

            public function listarCarpeta($carpeta = ''){
                try {
                    $objects = $this->s3->listObjects([
                        'Bucket'    => self::bucketName,
                        'Prefix'    => $carpeta,
                        //'Delimiter' => '/'
                    ]);
                    return $objects['Contents'];
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    return [];
                }
            }

            public function existeElemento($carpeta, $archivo = ''){
                try {
                    $this->s3->headObject([
                        'Bucket'    => self::bucketName,
                        'Key'       =>  $carpeta . $archivo,
                    ]);
                    return true;
                } catch (Aws\Exception\AwsException $e) {
                    return false;
                }
            }

            public function crearCarpeta($carpeta){
                try {
                    $this->s3->putObject([
                        'Bucket'    => self::bucketName,
                        'Key'       =>  $carpeta,
                        'Body'      => '',
                    ]);
                    return true;
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    return false;
                }
            }

            public function NuevoArchivo($carpeta, $nombre, $contenido){
                try {
                    $r = $this->s3->putObject([
                        'Bucket'        => self::bucketName,
                        'Key'           => $carpeta . $nombre,
                        'SourceFile'    => $contenido,
                    ]);
                    return md5_file($contenido) === str_replace('"', '', $r['ETag']);
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    return false;
                }
            }

            public function obtenerUrlPublica($carpeta, $nombre){
                try {
                    $url = $this->s3->getObjectUrl(self::bucketName, $carpeta . $nombre);
                    return $url;
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    return false;
                }
            }

            public function NuevoArchivoData($carpeta, $nombre, $contenido){
                try {
                    $this->s3->putObject([
                        'Bucket'    => self::bucketName,
                        'Key'       => $carpeta . $nombre,
                        'Body'      => $contenido,
                    ]);
                    return true;
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    return false;
                }
            }

            public function DescargarArchivo($carpeta, $nombre, $mime = false, $extra = false){
                try {
                    if($mime === false) $mime = $this->mime_content_type_extra($nombre);
                    $file = $this->s3->getObject([
                        'Bucket'        => self::bucketName,
                        'Key'           => $carpeta . $nombre,
                    ]);
                    header('Content-Type: ' . $mime);
                    header('Content-Length: '.$file['ContentLength']);
                    header('Cache-Control: public, must-revalidate, max-age=0');
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
                    if($extra === true){
                        header('Content-Disposition: inline; filename="' . $nombre . '"');
                    }else{
                        header('Content-Disposition: attachment; filename="' . $nombre . '"');
                    }
                    echo $file['Body'];
                    if($extra === 3) $this->eliminarElemento($carpeta, $nombre);
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    echo "Archivo no disponible";
                }
                exit;
            }

            public function ObtenerArchivo($carpeta, $nombre){
                try {
                    $file = $this->s3->getObject([
                        'Bucket'        => self::bucketName,
                        'Key'           => $carpeta . $nombre,
                    ]);
                    return $file;
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    return false;
                }
            }

            public function iniciarArchivo($carpeta, $nombre, $contenido){
                try {
                    $this->s3->putObject([
                        'Bucket' => self::bucketName,
                        'Key'    => $carpeta . $nombre,
                        'Body'   => $contenido
                    ]);
                    return true;
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    return false;
                }
            }

            public function actualizarArchivo($carpeta, $nombre, $contenidoNuevo){
                try {
                    $file = $this->s3->getObject([
                        'Bucket'        => self::bucketName,
                        'Key'           => $carpeta . $nombre,
                    ]);
                    $contenidoActual     = $file['Body'];
                    $contenidoModificado = $contenidoActual . $contenidoNuevo;
                    $this->s3->putObject([
                        'Bucket' => self::bucketName,
                        'Key'    => $carpeta . $nombre,
                        'Body'   => $contenidoModificado
                    ]);
                    return true;
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    return false;
                }
            }

            public function eliminarElemento($carpeta, $archivo = ''){
                try {
                    $this->s3->deleteObject([
                        'Bucket'    => self::bucketName,
                        'Key'       =>  $carpeta . $archivo,
                    ]);
                    return true;
                } catch (Aws\Exception\AwsException $e) {
                    error_log("Error: " . $e->getMessage() . "\n");
                    return false;
                }
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