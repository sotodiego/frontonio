<?php

    class admin_panel_twilio {
        protected $account_sid;
        protected $auth_token;
        protected $verify_service_sid;
        protected $whatsapp_number;

        public function __construct() {
            $this->account_sid = ADPNSY_TWILIO_ID;
            $this->auth_token = ADPNSY_TWILIO_TOKEN;
            $this->verify_service_sid = ADPNSY_TWILIO_SERVICIOS;
            $this->whatsapp_number = "whatsapp:" . ADPNSY_TWILIO_WHATSAPP;
        }

        /**
         * Envía un código de verificación sin necesidad de un número de Twilio
         * @param string $phone_number Número de teléfono con código de país (ej. +1234567890)
         * @return string Respuesta del servidor
         */
        public function enviarCodigo($phone_number, $channel = 'sms') {
            $url = "https://verify.twilio.com/v2/Services/{$this->verify_service_sid}/Verifications";
            $data = http_build_query([
                "To" => $phone_number,
                "Channel" => $channel
            ]);

            return $this->sendRequest($url, $data);
        }

        /**
         * Verifica si el código ingresado por el usuario es correcto
         * @param string $phone_number Número de teléfono con código de país
         * @param string $code Código ingresado por el usuario
         * @return string Respuesta del servidor
         */
        public function verificarCodigo($phone_number, $code) {
            $url = "https://verify.twilio.com/v2/Services/{$this->verify_service_sid}/VerificationCheck";
            $data = http_build_query([
                "To" => $phone_number,
                "Code" => $code
            ]);

            return $this->sendRequest($url, $data);
        }

        /**
         * Envía un mensaje vía WhatsApp a un número dado
         * @param string $phone_number Número de teléfono con código de país (ej. +1234567890)
         * @param string $mensaje Mensaje a enviar
         * @return string Respuesta del servidor
         */
        public function enviarMensajeWhatsApp($phone_number, $mensaje) {
            // URL para enviar mensajes (API de mensajes de Twilio)
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->account_sid}/Messages.json";
            
            $data = http_build_query([
                "To"   => "whatsapp:" . $phone_number,
                "From" => $this->whatsapp_number,
                "Body" => $mensaje
            ]);
            
            return $this->sendRequest($url, $data);
        }
        
        /**
         * Método genérico para hacer peticiones a la API de Twilio
         */
        protected function sendRequest($url, $data) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->account_sid}:{$this->auth_token}");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                $response = "Error en la petición: " . curl_error($ch);
            }
            curl_close($ch);

            return $response;
        }
    }