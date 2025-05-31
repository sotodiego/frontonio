<?php


	class adpnsy_redsys  {
	    private array $vars_pay = [
	    	"DS_MERCHANT_MERCHANTCODE" => ADPNSY_REDSYS_ID,
			"DS_MERCHANT_CURRENCY" => "978",
			"DS_MERCHANT_TRANSACTIONTYPE" => "0",
			"DS_MERCHANT_TERMINAL" => ADPNSY_REDSYS_TERMINAL,
			"DS_MERCHANT_MERCHANTURL" => "",
	    ];

	    public function crearOrden($cantidad, $orden, $url){
	    	$this->setParameter("DS_MERCHANT_AMOUNT",intval($cantidad*100));
			$this->setParameter("DS_MERCHANT_ORDER",$orden);
			$this->setParameter("DS_MERCHANT_URLOK",$url);
			$this->setParameter("DS_MERCHANT_URLKO",$url);

			return [
				"Ds_url" => (ADPNSY_REDSYS_TEST ? 'https://sis-t.redsys.es:25443/sis/realizarPago' : 'https://sis.redsys.es/sis/realizarPago'),
				"Ds_MerchantParameters" => $this->createMerchantParameters(),
				"Ds_Signature" => $this->createMerchantSignature(ADPNSY_REDSYS_CLAVE),
				"Ds_SignatureVersion" => "HMAC_SHA256_V1"
			];
	    }

	    public function validarFirma($data){
	    	$version = $data["Ds_SignatureVersion"];
			$datos = $data["Ds_MerchantParameters"];
			$signatureRecibida = $data["Ds_Signature"];
			$firma = $this->createMerchantSignatureNotif(ADPNSY_REDSYS_CLAVE,$datos);
			return ($firma === $signatureRecibida);
	    }

	    public function reconstruirDatos($data){
	    	$_decode = $this->decodeMerchantParameters($data);
	    	return json_decode($_decode);
	    }

	    function setParameter(string $key, string $value): void {
	        $this->vars_pay[$key] = $value;
	    }

	    function getParameter(string $key): ?string {
	        return $this->vars_pay[$key] ?? null;
	    }

	    private function encrypt3DES(string $message, string $key): string {
	        $l = ceil(strlen($message) / 8) * 8;
	        return substr(openssl_encrypt($message . str_repeat("\0", $l - strlen($message)), 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, "\0\0\0\0\0\0\0\0"), 0, $l);
	    }

	    private function base64UrlEncode(string $input): string {
	        return strtr(base64_encode($input), '+/', '-_');
	    }

	    private function base64UrlDecode(string $input): string {
	        return base64_decode(strtr($input, '-_', '+/'));
	    }

	    private function encodeBase64(string $data): string {
	        return base64_encode($data);
	    }

	    private function decodeBase64(string $data): string {
	        return base64_decode($data);
	    }

	    private function mac256(string $ent, string $key): string {
	        return hash_hmac('sha256', $ent, $key, true);
	    }

	    function getOrder(): ?string {
	        return $this->vars_pay['DS_MERCHANT_ORDER'] ?? $this->vars_pay['Ds_Merchant_Order'] ?? null;
	    }

	    function arrayToJson(): string {
	        return json_encode($this->vars_pay);
	    }

	    function createMerchantParameters(): string {
	        return $this->encodeBase64($this->arrayToJson());
	    }

	    function createMerchantSignature(string $key): string {
	        $key = $this->decodeBase64($key);
	        $ent = $this->createMerchantParameters();
	        $key = $this->encrypt3DES($this->getOrder(), $key);
	        return $this->encodeBase64($this->mac256($ent, $key));
	    }

	    function getOrderNotif(): ?string {
	        return $this->vars_pay['Ds_Order'] ?? $this->vars_pay['DS_ORDER'] ?? null;
	    }

	    private function extractTagValue(string $data, string $tag): ?string {
	        $posIni = strpos($data, "<$tag>");
	        $posFin = strpos($data, "</$tag>");
	        if ($posIni === false || $posFin === false) return null;
	        return substr($data, $posIni + strlen("<$tag>"), $posFin - ($posIni + strlen("<$tag>")));
	    }

	    function getOrderNotifSOAP(string $datos): ?string {
	        return $this->extractTagValue($datos, 'Ds_Order');
	    }

	    function getRequestNotifSOAP(string $datos): ?string {
	        return $this->extractTagValue($datos, 'Request');
	    }

	    function getResponseNotifSOAP(string $datos): ?string {
	        return $this->extractTagValue($datos, 'Response');
	    }

	    private function stringToArray(string $data): void {
	        $this->vars_pay = json_decode($data, true) ?? [];
	    }

	    function decodeMerchantParameters(string $datos): string {
	        $decodec = $this->base64UrlDecode($datos);
	        $this->stringToArray($decodec);
	        return $decodec;
	    }

	    function createMerchantSignatureNotif(string $key, string $datos): string {
	        $key = $this->decodeBase64($key);
	        $decodec = $this->base64UrlDecode($datos);
	        $this->stringToArray($decodec);
	        $key = $this->encrypt3DES($this->getOrderNotif(), $key);
	        return $this->base64UrlEncode($this->mac256($datos, $key));
	    }

	    function createMerchantSignatureNotifSOAPRequest(string $key, string $datos): string {
	        $key = $this->decodeBase64($key);
	        $datos = $this->getRequestNotifSOAP($datos);
	        $key = $this->encrypt3DES($this->getOrderNotifSOAP($datos), $key);
	        return $this->encodeBase64($this->mac256($datos, $key));
	    }

	    function createMerchantSignatureNotifSOAPResponse(string $key, string $datos, string $numPedido): string {
	        $key = $this->decodeBase64($key);
	        $datos = $this->getResponseNotifSOAP($datos);
	        $key = $this->encrypt3DES($numPedido, $key);
	        return $this->encodeBase64($this->mac256($datos, $key));
	    }
	}
