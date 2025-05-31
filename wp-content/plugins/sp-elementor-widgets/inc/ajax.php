<?php
	
	/*
		Espacio para consultar AjAX
		
		define un punto de entrada único y global (se recomiendo usar nonce de wp para aumentar su seguridad)

		//EJEMPLO =>
			if(isset($_REQUEST['test_oper']) && isset($_REQUEST['unik']) && wp_verify_nonce( $_REQUEST['unik'], 'test_oper' )){
				$operacion = $_REQUEST['test_oper'];
				if($operacion == 0){
					//Codigo
				}
			}
		<= EJEMPLO //
		
		Para las respuestas usar para éxito

		Se define un mensaje que llegara y se puede enviar un array de información de manera opcional

		//EJEMPLOS =>
			$this->send_json_success("mensaje");
			$this->send_json_success("mensaje", array());
		<= EJEMPLOS //

		Para las respuestas usar para error

		Se define un mensaje que llegara y se puede enviar un código de error web por defecto sera 404

		//EJEMPLOS =>
			$this->send_json_error("Recurso inexistente");
			$this->send_json_error("Recurso inexistente", 400);
		<= EJEMPLOS //

		NOTA: esto detiene la ejecución 
	*/

	if ( isset($_REQUEST) ){
		
	}

	$this->send_json_error("Recurso inexistente");
