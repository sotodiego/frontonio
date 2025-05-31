<?php
class CorreosOficialLogManager {
    public $logFile;
    public $filePath;
    private $startTime;
    private $buffer = []; // Buffer para optimización

    public function __construct($filePath) {
        $this->filePath = $filePath;
        
        // Verificar y crear el archivo principal si no existe.
        if (!file_exists($this->filePath)) {
            if (!touch($this->filePath)) {
                throw new Exception("No se pudo crear el archivo de log: " . $this->filePath);
            }
        }

        // Verificar y crear updates.log
        $updatesLogPath = get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/sql/updates.log";
        if (!file_exists($updatesLogPath)) {
            if (!touch($updatesLogPath)) {
                error_log("No se pudo crear el archivo updates.log en: " . $updatesLogPath);
            }
        }

        $this->open();
    }

    public function open() {
        $this->logFile = fopen($this->filePath, "a");
        if ($this->logFile === false) {
            throw new Exception("No se pudo abrir el archivo de log: " . $this->filePath);
        }
    }

    // Permite escribir con nivel y añade el mensaje al buffer.
    public function write($line, $level = 'INFO') {
        $now = new DateTime();
        $dateTime = $now->format("Y-m-d H:i:s");
        $logMessage = sprintf("[%s] [%s] %s%s", $dateTime, strtoupper($level), $line, PHP_EOL);
        $this->buffer[] = $logMessage;

        if (fwrite($this->logFile, $logMessage) === false) {
            error_log("Error escribiendo en el archivo de log: " . $this->filePath);
        }
    }
    
    // Inicia una sección y devuelve el tiempo de inicio.
    public function startSection($sectionName) {
        $this->write("------------------ INICIANDO " . $sectionName . " ------------------", 'INFO');
        return microtime(true);
    }
    
    // Finaliza una sección, mide el tiempo y lo registra.
    public function endSection($sectionName, $startTime) {
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $this->write("Se termina " . $sectionName . " (Tiempo: " . number_format($executionTime, 3) . " seg.)", 'INFO');
        $this->write("------------------------------------------------------", 'INFO');
    }
    
    public function close() {
        if ($this->logFile) {
            if (fclose($this->logFile) === false) {
                error_log("Error cerrando el archivo de log: " . $this->filePath);
            }
        }
    }
}