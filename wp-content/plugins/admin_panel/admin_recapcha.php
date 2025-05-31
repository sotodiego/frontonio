<?php

class admin_panel_recapcha {
    private $projectId;
    private $recaptchaSecretKey;
    private $recaptchaSiteKey;

    /**
     * Constructor de la clase
     *
     * @param string $projectId ID del Proyecto en Google Cloud
     * @param string $recaptchaSecretKey Clave Secreta de reCAPTCHA Enterprise
     * @param string $recaptchaSiteKey Clave Pública de reCAPTCHA Enterprise
     */
    public function __construct() {
        $this->projectId = ADPNSY_RE_PROJECT;
        $this->recaptchaSecretKey = ADPNSY_RE_SECRET;
        $this->recaptchaSiteKey = ADPNSY_RE_PUBLIC;
    }

    /**
     * Verifica el token de reCAPTCHA Enterprise con Google API
     *
     * @param string $token Token generado en el frontend
     * @param string $expectedAction Acción esperada (ejemplo: "submit")
     * @param float $threshold Umbral mínimo de confianza (por defecto 0.5)
     * @return array Resultado de la validación con score y éxito
     */
    public function validateToken($token, $expectedAction = "submit", $threshold = 0.5) {
        $url = "https://recaptchaenterprise.googleapis.com/v1/projects/{$this->projectId}/assessments?key={$this->recaptchaSecretKey}";

        $data = [
            "event" => [
                "token"   => $token,
                "siteKey" => $this->recaptchaSiteKey,
                "expectedAction" => $expectedAction
            ]
        ];

        $response = $this->sendRequest($url, $data);

        if (isset($response['riskAnalysis']['score'])) {
            $score = $response['riskAnalysis']['score'];
            return [
                "success" => $score >= $threshold,
                "score" => $score,
                "reason" => $response['riskAnalysis']['reasons'] ?? []
            ];
        }

        return [
            "success" => false,
            "score" => 0,
            "reason" => "Error en la validación de reCAPTCHA"
        ];
    }

    /**
     * Envía una solicitud HTTP POST a la API de Google reCAPTCHA Enterprise
     *
     * @param string $url URL de la API
     * @param array $data Datos a enviar en formato JSON
     * @return array Respuesta decodificada en JSON
     */
    private function sendRequest($url, $data) {
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => json_encode($data),
                'ignore_errors' => true
            ]
        ];
        
        $context  = stream_context_create($options);
        $result   = file_get_contents($url, false, $context);
        return json_decode($result, true);
    }
}

?>
