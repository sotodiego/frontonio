<?php

require_once('helpers.php');
require_once(plugin_dir_path(__DIR__).'constants.php');

function enviar_peticion_recogida_rest_cex($datos)
{
        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';
        //sacar de BBDD el usuario y contraseña para meterla en la peticion.
        $url = $wpdb->get_var("SELECT valor
        FROM $table
        WHERE clave= 'MXPS_WSURLREC_REST'");

        //rellenamos los codigos postales con 0 a la izquierda en caso de ser necesario
        $longitud = 5;
        $cprem = $datos['postcode_sender'];
        $cpdest = $datos['postcode_receiver'];

        //$postcode_sender = $this->cex_rellenar_ceros($cprem, $longitud);
        

            //split por -  y recolocar fecha ( FORMATO INCORRECTO. FORMATO VALIDO - DDMMYYYY)
        $fecha= $datos['datepicker'];
        $fechaformat= explode('-', $fecha);

        //$rest_request=$this->sanearString($rest_request);
        $data = array(
            "solicitante"   => $datos['codigo_solicitante'],
            "password"      => "",
            "canalEntrada"  => $datos['canalentrada'],
            "refRecogida"   => $datos['ref_ship'],                       
            "fechaRecogida" => $fechaformat[2].'-'.$fechaformat[1].'-'.$fechaformat[0],
            "horaDesde1"    => $datos['fromHH_sender'].':'.$datos['fromMM_sender'],
            "horaDesde2"    => "",
            "horaHasta1"    => $datos['toHH_sender'].':'.$datos['toMM_sender'],
            "horaHasta2"    => "",
            "clienteRecogida" => $datos['codigo_cliente'],
            "codRemit"      => $datos['codigo_cliente'],
            "nomRemit"      => $datos['name_sender'],
            "nifRemit"      => $datos['nifRte'],
            "dirRecog"      => $datos['address_sender'],
            "poblRecog"     => $datos['city_sender'],
            "cpRecog"       => $cprem,
            "paisISORte"    => $datos['iso_code_remitente'],
            "codPosIntRte"  => $datos['codPosIntRte'],
            "contRecog"     => $datos['contact_sender'],
            "tlfnoRecog"    => $datos['phone_sender'],
            "oTlfnRecog"    => "",
            "emailRecog"    => $datos['email_sender'],
            "observ"        => $datos['note_collect'],
            "tipoServ"      => "",
            "codDest"       => "",
            "nomDest"       => $datos['name_receiver'],
            "nifDest"       => $datos['nifDest'],
            "dirDest"       => $datos['address_receiver'],
            "pobDest"       => $datos['city_receiver'],
            "cpDest"        => "",            
            "paisDest"      => $datos['iso_code'], 
            "cpiDest"       => "",          
            "contacDest"    => $datos['contact_receiver'],
            "telefDest"     => $datos['phone_receiver1'],
            "emailDest"     => $datos['email_receiver'],
            "nEnvio"        => "",
            "refEnvio"      => "",
            "producto"      => $datos['selCarrier'],            
            "kilos"         => $datos['kilos'],
            "bultos"        => $datos['bultos'],
            "volumen"       => $datos['volumen'],
            "alto"          => $datos['alto'],
            "largo"         => $datos['largo'],
            "ancho"         => $datos['ancho'],           
            "tipoPortes"    => $datos['portes'],
            "importReembol" => $datos['payback_val'],
            "valDeclMerc"   => "",
            "infTec"        => "",
            "nSerie"        => "",
            "modelo"        => "",
            "latente"       =>"0"
        );
                
        // Los condicionales no deberian de ir aqui???
        //envios a portugal
       if ($datos['iso_code'] == 'ES') {            
            $data['cpDest']=$cpdest;
            $data['paisDest']=$datos['iso_code'];
            $data['cpiDest']="";
        }elseif ($datos['iso_code'] == 'PT') {
            // envios a portugal
            $data['cpDest']="";
            $data['paisDest']=$datos['iso_code'];
            $data['cpiDest']=$cpdest;        
        }else {
            // internacionales
            $data['cpDest']="";
            $data['paisDest']=$datos['iso_code'];
            $data['cpiDest']=$cpdest;          
        }            

        $retorno = [
        'peticion' => json_encode($data),
        'url'  => $url
        ];
        

    return $retorno;

}

function cex_valor_hideSender(){
    global $wpdb;
    $table = $wpdb->prefix.'cex_customer_options';   
    $hideSender = $wpdb->get_var("SELECT valor
        FROM $table
        WHERE clave= 'MXPS_LABELSENDER'");
    $textoAdicional = $wpdb->get_var("SELECT valor
        FROM $table
        WHERE clave= 'MXPS_LABELSENDER_TEXT'");
   
    if($hideSender == 'true'){
        $retorno = [
            'hideSender' => '1',
            'textoRemiAlternativo'  => $textoAdicional
        ];
    }else{
        $retorno = [
            'hideSender' => '0',
            'textoRemiAlternativo'  => ''
        ];
    }
    return $retorno;
}

function cex_enviar_peticion_envio_rest($datos)
{
    global $wpdb;
    $table = $wpdb->prefix.'cex_customer_options';    
    $url = $wpdb->get_var("SELECT valor
    FROM $table
    WHERE clave= 'MXPS_WSURL_REST'");

    //split por -  y recolocar fecha ( FORMATO INCORRECTO. FORMATO VALIDO - DDMMYYYY)
    $fecha= $datos['datepicker'];
    $fechaformat= explode('-', $fecha);

    //rellenamos los codigos postales con 0 a la izquierda en caso de ser necesario
    $longitud = 5;
    $cprem = $datos['postcode_sender'];
    $cpdest = $datos['postcode_receiver'];   
    $postcode_sender = cex_rellenar_ceros($cprem, $longitud);

    $codigo_cliente= substr($datos["codigo_cliente"], 0, 5);
    $codigo_solicitante = obtenerCodigoSolicitante().$codigo_cliente;

    $data = array(
            "solicitante"   => $codigo_solicitante,
            "canalEntrada"  => "",
            "numEnvio"      => "",
            "ref"           => $datos['ref_ship'],
            "refCliente"    => $datos['ref_ship'],
            "fecha"         => $fechaformat[2].$fechaformat[1].$fechaformat[0],
            "codRte"        => $datos['codigo_cliente'],
            "nomRte"        => $datos['name_sender'],
            "nifRte"        => "",
            "dirRte"        => $datos['address_sender'],
            "pobRte"        => $datos['city_sender'],
            "codPosNacRte"  => $postcode_sender,
            "paisISORte"    => $datos['iso_code_remitente'],
            "codPosIntRte"  => "",
            "contacRte"     => $datos['contact_sender'],
            "telefRte"      => $datos['phone_sender'],
            "emailRte"      => $datos['email_sender'],
            "codDest"       => "",
            "nomDest"       => $datos['name_receiver'],
            "nifDest"       => "",
            "dirDest"       => $datos['address_receiver'],
            "pobDest"       => $datos['city_receiver'],
            "codPosNacDest" => $cpdest,
            "paisISODest"   => $datos['iso_code'],
            "codPosIntDest" => "",
            "contacDest"    => $datos['contact_receiver'],
            "telefDest"     => $datos['phone_receiver1'],
            "emailDest"     => $datos['email_receiver'],
            "contacOtrs"    => "",
            "telefOtrs"     => $datos['phone_receiver2'],
            "emailOtrs"     => "",
            "observac"      => $datos['note_deliver'],
            "numBultos"     => $datos['bultos'],
            "kilos"         => calcularPesoEnKilos($datos['kilos']),
            "volumen"       => "",
            "alto"          => "",
            "largo"         => "",
            "ancho"         => "",
            "producto"      => $datos['selCarrier'],
            "portes"        => "P",
            "reembolso"     => "",
            "entrSabado"    => "",
            "seguro"        => $datos['insured_value'],
            "numEnvioVuelta"=> "",
            "listaBultos"   => [], 
            "codDirecDestino" =>"",
            "password"      => "",    
            "listaInformacionAdicional"=> []
        );
        
         //CP e iso_code Remitentes
        if ($datos['iso_code_remitente'] == 'ES') {            
            $data['codPosNacRte'] = $postcode_sender;
            $data['paisISORte'] = $datos['iso_code_remitente'];
            $data['codPosIntRte'] = "";
        }elseif ($datos['iso_code_remitente'] == 'PT') {
            // envios a portugal
            $data['codPosNacRte'] = ""; 
            $data['paisISORte'] = $datos['iso_code_remitente'];
            $data['codPosIntRte'] = $datos['postcode_sender'];         
        }else {
            // internacionales
            $data['codPosNacRte'] = ""; 
            $data['paisISORte'] = $datos['iso_code_remitente'];
            $data['codPosIntRte'] = $datos['postcode_sender'];       
        }    
        
        //CP e iso_code Destinatarios
        if ($datos['iso_code'] == 'ES') {            
            $data['codPosNacDest']=$datos['postcode_receiver'];
            $data['paisISODest']=$datos['iso_code'];
            $data['codPosIntDest']="";
        }elseif ($datos['iso_code'] == 'PT') {
            // envios a portugal
            $data['codPosNacDest']=""; 
            $data['paisISODest']=$datos['iso_code'];
            $data['codPosIntDest']=$datos['postcode_receiver'];         
        }else {
            // internacionales
            $data['codPosNacDest']=""; 
            $data['paisISODest']=$datos['iso_code'];
            $data['codPosIntDest']=$datos['postcode_receiver'];       
        }     

        if (!empty($datos['payback_val'])) {
            $data['reembolso'] = $datos['payback_val'];
        }
        if ($datos['deliver_sat']=='true') {
            $data['entrSabado'] = 'S';
        }

         //if recogida en oficina
        if ($datos['entrega_oficina']=='true') {
            $data['codDirecDestino'] = $datos['codigo_oficina'];
        }

        //Lista adicional de bultos
        for ($i=1; $i<=$datos['bultos']; $i++) {
            $interior = new stdClass();
            $interior->alto = "";
            $interior->ancho = "";
            $interior->codBultoCli = $i;
            $interior->codUnico = "";
            $interior->descripcion = "";
            $interior->kilos = "";
            $interior->largo = "";
            $interior->observaciones = "";
            $interior->orden = $i;
            $interior->referencia = "";
            $interior->volumen = "";
            $data["listaBultos"][] = $interior;
        }       
        
        

        //listaInformacionAdicional
        
        $data["listaInformacionAdicional"][] = obtener_lista_adicional($datos);

        $retorno = [
            'peticion' => json_encode($data),
            'url'  => $url
        ];
        
        return $retorno;
    
}

function enviar_peticion_masivas_rest($datos){
    global $wpdb;
    $table = $wpdb->prefix.'cex_customer_options';    
    $url = $wpdb->get_var("SELECT valor
    FROM $table
    WHERE clave= 'MXPS_WSURL_REST'");

    //split por -  y recolocar fecha ( FORMATO INCORRECTO. FORMATO VALIDO - DDMMYYYY)
    $fecha= $datos['datepicker'];
    $fechaformat= explode('-', $fecha);

    //rellenamos los codigos postales con 0 a la izquierda en caso de ser necesario
    $longitud = 5;
    $cprem = $datos['postcode_sender'];
    $cpdest = $datos['postcode_receiver'];
    $postcode_sender = cex_rellenar_ceros($cprem, $longitud);  
    
    $codigo_cliente= substr($datos["codigo_cliente"], 0, 5);
    $codigo_solicitante = obtenerCodigoSolicitante().$codigo_cliente;

    $data = array(
            "solicitante"   => $codigo_solicitante,
            "canalEntrada"  => "",
            "numEnvio"      => "",
            "ref"           => $datos['ref_ship'],
            "refCliente"    => $datos['ref_ship'],
            "fecha"         => $fechaformat[2].$fechaformat[1].$fechaformat[0],
            "codRte"        => $datos['codigo_cliente'],
            "nomRte"        => $datos['name_sender'],
            "nifRte"        => "",
            "dirRte"        => $datos['address_sender'],
            "pobRte"        => $datos['city_sender'],
            "codPosNacRte"  => $postcode_sender,
            "paisISORte"    => $datos['iso_code_remitente'],
            "codPosIntRte"  => "",
            "contacRte"     => $datos['contact_sender'],
            "telefRte"      => $datos['phone_sender'],
            "emailRte"      => $datos['email_sender'],
            "codDest"       => "",
            "nomDest"       => $datos['name_receiver'],
            "nifDest"       => "",
            "dirDest"       => $datos['address_receiver'],
            "pobDest"       => $datos['city_receiver'],
            "codPosNacDest" => $cpdest,
            "paisISODest"   => $datos['iso_code'],
            "codPosIntDest" => "",
            "contacDest"    => $datos['contact_receiver'],
            "telefDest"     => $datos['phone_receiver1'],
            "emailDest"     => $datos['email_receiver'],
            "contacOtrs"    => "",
            "telefOtrs"     => $datos['phone_receiver2'],
            "emailOtrs"     => "",
            "observac"      => $datos['note_deliver'],
            "numBultos"     => $datos['bultos'],
            "kilos"         => calcularPesoEnKilos($datos['kilos']),
            "volumen"       => "",
            "alto"          => "",
            "largo"         => "",
            "ancho"         => "",
            "producto"      => $datos['selCarrier'],
            "portes"        => "P",
            "reembolso"     => "",
            "entrSabado"    => "",
            "seguro"        => $datos['insured_value'],
            "numEnvioVuelta"=> "",
            "listaBultos"   => [], 
            "codDirecDestino" =>"",
            "password"      => "",    
            "listaInformacionAdicional"=> []
        );
                
         //CP e iso_code Remitentes
        if ($datos['iso_code_remitente'] == 'ES') {            
            $data['codPosNacRte'] = $postcode_sender;
            $data['paisISORte'] = $datos['iso_code_remitente'];
            $data['codPosIntRte'] = "";
        }elseif ($datos['iso_code_remitente'] == 'PT') {
            // envios a portugal
            $data['codPosNacRte'] = ""; 
            $data['paisISORte'] = $datos['iso_code_remitente'];
            $data['codPosIntRte'] = $postcode_sender;         
        }else {
            // internacionales
            $data['codPosNacRte'] = ""; 
            $data['paisISORte'] = $datos['iso_code_remitente'];
            $data['codPosIntRte'] = $postcode_sender;       
        }    
        
        //CP e iso_code Destinatarios
        if ($datos['iso_code'] == 'ES') {            
            $data['codPosNacDest']=$cpdest;
            $data['paisISODest']=$datos['iso_code'];
            $data['codPosIntDest']="";
        }elseif ($datos['iso_code'] == 'PT') {
            // envios a portugal
            $data['codPosNacDest']=""; 
            $data['paisISODest']=$datos['iso_code'];
            $data['codPosIntDest']=$cpdest;         
        }else {
            // internacionales
            $data['codPosNacDest']=""; 
            $data['paisISODest']=$datos['iso_code'];
            $data['codPosIntDest']=$cpdest;       
        }     

        if (!empty($datos['payback_val'])) {
            $data['reembolso'] = $datos['payback_val'];
        }
        if ($datos['deliver_sat']=='true') {
            $data['entrSabado'] = 'S';
        }

         //if recogida en oficina
        if ($datos['entrega_oficina']=='true' || $datos['entrega_oficina']==true) {
            $data['codDirecDestino'] = $datos['codigo_oficina'];
        }


        //Lista adicional de bultos
        for ($i=1; $i<=$datos['bultos']; $i++) {
            $interior = new stdClass();
            $interior->alto = "";
            $interior->ancho = "";
            $interior->codBultoCli = $i;
            $interior->codUnico = "";
            $interior->descripcion = "";
            $interior->kilos = "";
            $interior->largo = "";
            $interior->observaciones = "";
            $interior->orden = $i;
            $interior->referencia = "";
            $interior->volumen = "";
            $data["listaBultos"][] = $interior;
        }       
        //listaInformacionAdicional
        
        $data["listaInformacionAdicional"][] = obtener_lista_adicional($datos,true);

        $retorno = [
            'peticion' => json_encode($data),
            'url'  => $url
        ];

        return $retorno;
}

function obtener_lista_adicional($datos, $esMasiva=false){   
    $fecha = $datos['datepicker'];
    $fechaformat = explode('-', $fecha);
    $lista = new stdClass();
    $valorHideSender = cex_valor_hideSender();

    if($esMasiva==true){
        $lista->tipoEtiqueta = "";
        $lista->posicionEtiqueta = "";
        $lista->referenciaRecogida = "";
    }else{
        switch($datos['tipoEtiqueta']){
            //ETIQUETA ADHESIVA
            case '1':
            $lista->tipoEtiqueta = "3";
            $lista->posicionEtiqueta = obtener_posicion_etiqueta($datos['posicionEtiqueta']);
            break;
            //ETIQUETA MEDIO FOLIO
            case '2':
            $lista->tipoEtiqueta = "4";
            $lista->posicionEtiqueta = obtener_posicion_etiqueta($datos['posicionEtiqueta']);
            break;
            //ETIQUETA TERMICA
            case '3':
            $lista->tipoEtiqueta = "5";
            break;
            
            default:
            $lista->tipoEtiqueta = "5";
            break;
        }
    
    }
    $lista->hideSender = $valorHideSender['hideSender'];
    $lista->codificacionUnicaB64 = "1";
    $lista->logoCliente = codificar_logo();
    $lista->idioma= obtener_idioma($datos);
    $lista->textoRemiAlternativo = $valorHideSender['textoRemiAlternativo'];
    $lista->etiquetaPDF =  "";

    if(strcmp($datos['grabar_recogida'], 'false') == 0){
        $lista->creaRecogida = 'N';
    }else{
        $lista->creaRecogida = 'S';
        $lista->fechaRecogida = $fechaformat[2].$fechaformat[1].$fechaformat[0];
        $lista->horaDesdeRecogida = $datos['fromHH_sender'].':'.$datos['fromMM_sender'];
        $lista->horaHastaRecogida = $datos['toHH_sender'].':'.$datos['toMM_sender'];
        $lista->referenciaRecogida = "";
    }


    if( !empty($datos['at_portugal']))
        $lista->codigoAT = $datos['at_portugal'];


    return $lista;
    
}

function obtener_idioma($datos){
    $idioma=$datos['idioma'];
    switch($idioma){
        case'en_GB':
            $res= "GB"; 
            break;
        case 'en_US':
            $res="US";
            break;
        case 'ca_ES':
            $res="CA";
            break;
        case 'es_ES':
            $res="ES";
        break;
        case 'pt_PT':
            $res="PT";
            break;
        default:
            $res="ES";
            break;
    }

    return $res;
}


function obtener_posicion_etiqueta($posicionEtiqueta){
    switch ($posicionEtiqueta) {
        case '1':
            return '0';
            break;
        case '2':
            return '1';
            break;
        case '3':
            return '2';
            break;
        
        default:
            return '0';
            break;
    }

}

function cex_enviar_peticion_validación()
{
    global $wpdb;
    $table = $wpdb->prefix.'cex_customer_options';    
    $url = $wpdb->get_var("SELECT valor
    FROM $table
    WHERE clave= 'MXPS_WSURL_REST'");
    $data = array(
        "solicitante"   => "",
        "canalEntrada"  => "",
        "numEnvio"      => "",
        "ref"           => "",
        "refCliente"    => "",
        "fecha"         => "",
        "codRte"        => "",
        "nomRte"        => "",
        "nifRte"        => "",
        "dirRte"        => "",
        "pobRte"        => "",
        "codPosNacRte"  => "",
        "paisISORte"    => "",
        "codPosIntRte"  => "",
        "contacRte"     => "",
        "telefRte"      => "",
        "emailRte"      => "",
        "codDest"       => "",
        "nomDest"       => "",
        "nifDest"       => "",
        "dirDest"       => "",
        "pobDest"       => "",
        "codPosNacDest" => "",
        "paisISODest"   => "",
        "codPosIntDest" => "",
        "contacDest"    => "",
        "telefDest"     => "",
        "emailDest"     => "",
        "contacOtrs"    => "",
        "telefOtrs"     => "",
        "emailOtrs"     => "",
        "observac"      => "",
        "numBultos"     => "",
        "kilos"         => "",
        "volumen"       => "",
        "alto"          => "",
        "largo"         => "",
        "ancho"         => "",
        "producto"      => "",
        "portes"        => "",
        "reembolso"     => "",
        "entrSabado"    => "",
        "seguro"        => "",
        "numEnvioVuelta"=> "",
        "listaBultos"   => [], 
        "codDirecDestino" =>"",
        "password"      => "",    
        "listaInformacionAdicional"=> []
    ); 
       
    $interior = new stdClass();
    $interior->alto = "";
    $interior->ancho = "";
    $interior->codBultoCli = "";
    $interior->codUnico = "";
    $interior->descripcion = "";
    $interior->kilos = "";
    $interior->largo = "";
    $interior->observaciones = "";
    $interior->orden = "";
    $interior->referencia = "";
    $interior->volumen = "";
    $data["listaBultos"][] = $interior;

    $lista = new stdClass();    
    $lista->tipoEtiqueta = "";
    $lista->etiquetaPDF =  "";
    $lista->posicionEtiqueta = "";
    $lista->hideSender = "";
    //-$lista->logoCliente = "";
    $lista->codificacionUnicaB64 = "";       
    $lista->textoRemiAlternativo ="";
    $lista->idioma ="";
    $lista->creaRecogida = "";
    $lista->fechaRecogida = "";
    $lista->horaDesdeRecogida = "";
    $lista->horaHastaRecogida = "";
    $lista->referenciaRecogida ="";
    $data["listaInformacionAdicional"][] = $lista;

    $ret = [
        'peticion' => json_encode($data),
        'url'  => $url,
    ];
    return $ret;  
}

function cex_procesar_curl_rest($peticion, $usuario=false, $password=false)
{
//die(var_dump($peticion));
    $credenciales;
    if(!$usuario && !$password)
        $credenciales = get_user_credentials();
    else{
        $credenciales =  array();
        $credenciales['usuario'] = $usuario;
        $credenciales['password'] = $password;
    }  
         
    // iniciamos y componemos la peticion curl
        $header = array("Charset=\"utf-8\"",
            "Accept"         => "application/json",            
            "Cache-Control"  => "no-cache",
            "Pragma"         => "no-cache",
            "Content-Type"   => "application/json",
            "Content-length" => strlen($peticion['peticion']),
            "Authorization"  => "Basic " . base64_encode( $credenciales['usuario'] . ":" . $credenciales['password'] )
            );             
    
    $options    = array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_SSL_VERIFYHOST  => false,
                    CURLOPT_SSL_VERIFYPEER  => false,
                    CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0)',
                    CURLOPT_URL             => $peticion['url'] ,
                    CURLOPT_USERPWD         => trim($credenciales['usuario']).":".trim($credenciales['password']),
                    CURLOPT_POST            => true ,                    
                    CURLOPT_POSTFIELDS      => $peticion['peticion'],
                    CURLOPT_BINARYTRANSFER  => 1,
                    CURLOPT_SSLVERSION      => CURL_SSLVERSION_SSLv3,                   
                    'headers'               => $header,        
                    'body'                  => $peticion['peticion']
                );  

    $post           = wp_remote_post($peticion['url'], $options );
    $output         = wp_remote_retrieve_body($post);
    $status_code    = wp_remote_retrieve_response_code($post); 
    return $output;
}




function procesar_peticion_recogida_rest_cex($rest, $retorno, $id_orden, $type, $numcollect)
{
    
    $fecha = date("Y-m-d H:i:s");
    global $wpdb;
    $nombreTabla = $wpdb->prefix."cex_history";
    $retornoObj = json_decode($retorno,true);

    $restObj    = json_decode($rest,true);
    $listaInfoAdicionalArray    = $restObj['listaInformacionAdicional'];

    //En la BBDD no guardamos la codificacion del Logo del cliente
    $listaInfoAdicionalArray[0]['logoCliente'] = "";
    $restObj['listaInformacionAdicional'] = $listaInfoAdicionalArray;
    $restString = json_encode($restObj);

    //si retorno vacio el WS no funciono
    if (empty($retorno)) {
        $historyEnv = array(
            'type'                  =>$type ,
            'mensajeRetorno'        =>'RECError [WS] conection',
            'codigoRetorno'         =>'',
            'numShip'               =>'',
            'numCollect'            =>$numcollect,
            'id_order'              =>$id_orden,
            'resultado'             =>'0',
            'envioWS'               =>$restString,
            'respuestaWS'           =>$retorno,
            'fecha'                 =>$fecha,
        );
        $wpdb->insert($nombreTabla, $historyEnv);

        $retorno = [
            'numCollect'        => $numcollect,
            'mensajeRetorno'    => 'RECError [WS] conection',
            'numShip'           => '',
            'resultado'         => '0',
            'id_order'          => $id_orden,           
            ];
        return $retorno;
    }else {

        //DATORESULTADO == NUMSHIP
        $mensajeError       = $retornoObj['mensError'];
        $numRecogida        = $retornoObj['numRecogida'];        
        $codigoError        = $retornoObj['codError'];
        $resultado          = $retornoObj['resultado'];
       

        $retornoAux = json_encode($retornoObj);
        
        //insert en tabla historico
        $historyEnv = array(
            'type'                  =>$type,
            'mensajeRetorno'        =>$mensajeError,
            'codigoRetorno'         =>'0',
            'numShip'               =>$numRecogida,
            'numcollect'            =>$numcollect,
            'id_order'              =>$id_orden,
            'resultado'             =>$resultado,
            'envioWS'               =>$restString,
            'respuestaWS'           =>$retornoAux,
            'fecha'                 =>$fecha,
        );
        
        $wpdb->insert($nombreTabla, $historyEnv);        
       
        $retorno = [
            'resultado'         => $resultado,
            'numRecogida'       => $numRecogida,
            'codError'          => $codigoError,
            'mensError'         => $mensajeError,           
            ];
        return $retorno;
    }
}

function cex_procesar_peticion_envio_rest($rest, $retorno, $id_orden, $type, $numcollect)
{

    $fecha = date("Y-m-d H:i:s");
    global $wpdb;
    $nombreTabla = $wpdb->prefix."cex_history";

    $retornoObj = json_decode($retorno,true);
    $restObj    = json_decode($rest,true);
    $listaInfoAdicionalArray    = $restObj['listaInformacionAdicional'];

    //En la BBDD no guardamos la codificacion del Logo del cliente
    $listaInfoAdicionalArray[0]['logoCliente'] = "";
    $restObj['listaInformacionAdicional'] = $listaInfoAdicionalArray;
    $restString = json_encode($restObj);

        //si retorno vacio el WS no funciono
        // SI DA ERROR 500

    if (empty($retorno)) {
        $historyEnv = array(
            'type'                  =>$type ,
            'mensajeRetorno'        =>'Error [WS] conection',
            'codigoRetorno'         =>'',
            'numShip'               =>'',
            'numCollect'            =>$numcollect,
            'id_order'              =>$id_orden,
            'resultado'             =>'0',
            'envioWS'               =>$restString,
            'respuestaWS'           =>$retorno,
            'fecha'                 =>$fecha,
        );
        $wpdb->insert($nombreTabla, $historyEnv);

        $retorno = [
            'numCollect'        => $numcollect,
            'mensajeRetorno'    => 'Error [WS] conection',
            'numShip'           => '',
            'resultado'         => '0',
            'id_order'          => $id_orden,

        ];
        return $retorno;
    /*}else if(strpos( $retorno, '413 Request Entity Too Large') !== 0){
        $historyEnv = array(
            'type'                  =>$type ,
            'mensajeRetorno'        =>'Error [WS] request too large',
            'codigoRetorno'         =>'',
            'numShip'               =>'',
            'numCollect'            =>$numcollect,
            'id_order'              =>$id_orden,
            'resultado'             =>'0',
            'envioWS'               =>'',
            'respuestaWS'           =>$retorno,
            'fecha'                 =>$fecha,
        );
        $wpdb->insert($nombreTabla, $historyEnv);

        $retorno = [
            'numCollect'        => $numcollect,
            'mensajeRetorno'    => 'Error [WS] request too large',
            'numShip'           => '',
            'resultado'         => '0',
            'id_order'          => $id_orden,

        ];
        return $retorno;*/


    }else {
        if(strcmp($retornoObj['codigoRetorno'], '0')!=0){

            $mensajeRetorno     = $retornoObj['mensajeRetorno'];
            $codigoRetorno      = $retornoObj['codigoRetorno'];
            
            $historyEnv = array(
                'type'                  =>$type ,
                'mensajeRetorno'        =>$mensajeRetorno,
                'codigoRetorno'         =>$codigoRetorno,
                'numShip'               =>'',
                'numCollect'            =>$numcollect,
                'id_order'              =>$id_orden,
                'resultado'             =>'0',
                'envioWS'               =>$restString,
                'respuestaWS'           =>$retorno,
                'fecha'                 =>$fecha,
            );
            $wpdb->insert($nombreTabla, $historyEnv);

            $ret = [
                'numCollect'        => $numcollect,
                'mensajeRetorno'    => $mensajeRetorno,
                'numShip'           => '',
                'resultado'         => '0',
                'id_order'          => $id_orden,

            ];            
            return $ret;
        }else{

            //DATORESULTADO == NUMSHIP
            $mensajeRetorno     = $retornoObj['mensajeRetorno'];
            $codigoRetorno      = $retornoObj['codigoRetorno'];
            $datosResultado     = $retornoObj['datosResultado'];
            $resultado          = $retornoObj['resultado'];
            $listaBultos        = $retornoObj['listaBultos'];
            $etiqueta           = $retornoObj['etiqueta'][0]['etiqueta1'];
            $numRecogida        = $retornoObj['numRecogida'];
            
            unset($retornoObj['etiqueta']);

            $retornoAux = json_encode($retornoObj);        

            $producto_ws = (int)$retornoObj["producto"];

            //insert en tabla historico
            $historyEnv = array(
                'id_order'              =>$id_orden,
                'numCollect'            =>$numcollect,
                'type'                  =>$type,
                'numShip'               =>$datosResultado,
                'resultado'             =>'1',
                'mensajeRetorno'        =>$mensajeRetorno,
                'codigoRetorno'         =>$codigoRetorno,
                'envioWS'               =>$restString,
                'respuestaWS'           =>$retornoAux,
                'fecha'                 =>$fecha,
                'fecha_recogida'        =>null,
                'hora_recogida_desde'   =>null,
                'hora_recogida_hasta'   =>null,                
                'id_bc_ws'              =>$producto_ws,
                'mode_ship_name_ws'     =>modeShipNameByIdBc($producto_ws),
            );

            $wpdb->insert($nombreTabla, $historyEnv);
            
            foreach ($listaBultos as $bulto) {
                $nombreTabla2 = $wpdb->prefix."cex_envios_bultos";
                

                $envio_bultos = array(
                    'id_order'              =>$id_orden,
                    'numCollect'            =>$numcollect,
                    'numShip'               =>$datosResultado,
                    'codUnicoBulto'         =>$bulto['codUnico'],
                    'id_bulto'              =>$bulto['orden'],
                    'fecha'                 =>$fecha,
                );
                //insert
                $wpdb->insert($nombreTabla2, $envio_bultos);
            }

        //LOG  --- COMENTAR NO BORRAR
        //$destino = 'C:\xampp\htdocs\wp\wp-content\plugins\correosExpress\log_tipo_peticion_envio_dump.txt';
        /*$destino = '..\log_tipo_peticion_envio_dump.txt';
        
            ut_contents($destino, print_r($retorno, true));
            ut_contents($destino, "\n\r______________", FILE_APPEND);
            ut_contents($destino, "\n\r El mensaje de retorno :".$mensajeRetorno, FILE_APPEND);
            ut_contents($destino, "\n\r El codigo de retorno :".$codigoRetorno, FILE_APPEND);
            ut_contents($destino, "\n\r Los datos de retorno :".$datosResultado, FILE_APPEND);
            ut_contents($destino, "\n\r El numero de bultos :".var_export($codigosBultos, true), FILE_APPEND);
            ut_contents($destino, "\n\r La respuesta :".$resultado, FILE_APPEND);*/
            
            
            if(strcmp($restObj['listaInformacionAdicional'][0]['creaRecogida'], 'S') == 0){
                
                guardaRecogidaRestHistorico($rest, $retorno, $id_orden, $numcollect);
                $retorno = [
                    'id_order'          => $id_orden,
                    'numCollect'        => $numcollect,
                    'mensajeRetorno'    => $mensajeRetorno,
                    'numShip'           => $datosResultado,
                    'resultado'         => '1',
                    'etiqueta'          => $etiqueta,    
                    'codigoRetorno'    =>  $codigoRetorno,
                    'numRecogida'       => $numRecogida      
                ];
            }else{

                $retorno = [
                    'id_order'          => $id_orden,
                    'numCollect'        => $numcollect,
                    'mensajeRetorno'    => $mensajeRetorno,
                    'numShip'           => $datosResultado,
                    'resultado'         => '1',
                    'etiqueta'          => $etiqueta, 
                    'codigoRetorno'    =>  $codigoRetorno,
                    'numRecogida'       => 'Automatica'       
                ];
            }
            
            return $retorno;
        }
    }
}

function modeShipNameByIdBc($id_bc){
    global $wpdb;    
    $table        = $wpdb->prefix.'cex_savedmodeships';
    $sql          = "SELECT name FROM $table WHERE id_bc = '".$id_bc."'";
    $result       =  $wpdb->get_results($wpdb->prepare($sql, null)); 
    return $result[0]->name;   
        
}   


function devolverFechaRecogida($fecha){
    $dd=substr($fecha, 0,2);  
    $mm=substr($fecha, 2,2);
    $aa=substr($fecha, 4);
    $fechaRecogida=$dd."-".$mm."-".$aa; 
    return $fechaRecogida;
}


function guardaRecogidaRestHistorico($rest, $retorno, $id_orden, $numcollect){
    $fecha = date("Y-m-d H:i:s");
    global $wpdb;
    $nombreTabla = $wpdb->prefix."cex_history";
    $retornoObj = json_decode($retorno,true);

    $restObj    = json_decode($rest,true);
    $listaInfoAdicionalArray    = $restObj['listaInformacionAdicional'];

    //En la BBDD no guardamos la codificacion del Logo del cliente
    $listaInfoAdicionalArray[0]['logoCliente'] = "";
    $restObj['listaInformacionAdicional'] = $listaInfoAdicionalArray;
    $restString = json_encode($restObj);


    if(empty($retorno)) {
        $historyEnv = array(
            'type'                  =>'Recogida',
            'mensajeRetorno'        =>$retornoObj['mensajeRetorno'],
            'codigoRetorno'         =>'',
            'numShip'               =>'',
            'numcollect'            =>$numcollect,
            'id_order'              =>$id_orden,
            'resultado'             =>'0',
            'envioWS'               =>$restString,
            'respuestaWS'           =>$retorno,
            'fecha'                 =>$fecha,
        );
        $wpdb->insert($nombreTabla, $historyEnv);
        
       
    }else{
        //DATORESULTADO == NUMSHIP
        $mensajeRetorno = $retornoObj['mensajeRetorno'];
        $codigoRetorno = $retornoObj['codigoRetorno'];
        $datosResultado = $retornoObj['numRecogida'];        
        $listaBultos = $retornoObj['listaBultos'];            
        $fechaRecogidaRet   = $retornoObj['fechaRecogida'];
        $fechaRecogida      = DateTime::createFromFormat('dmY', $fechaRecogidaRet);       
        $fechaRecogida      = $fechaRecogida->format("Y-m-d");       
        $horaRecogidaDesde  = $retornoObj['horaRecogidaDesde'];
        $horaRecogidaHasta  = $retornoObj['horaRecogidaHasta'];
        unset($retornoObj['etiqueta']);

        //unset($retornoObj['etiqueta']);
        $retornoAux = json_encode($retornoObj);

         //insert en tabla historico
         $historyEnv = array(
            'type'                  =>'Recogida',
            'mensajeRetorno'        =>$mensajeRetorno,
            'codigoRetorno'         =>$codigoRetorno,
            'numShip'               =>$datosResultado,
            'numcollect'            =>$numcollect,
            'id_order'              =>$id_orden,
            'resultado'             =>'1',
            'envioWS'               =>$restString,
            'respuestaWS'           =>$retornoAux,
            'fecha'                 =>$fecha,
            'fecha_recogida'        =>$fechaRecogida,
            'hora_recogida_desde'   =>$horaRecogidaDesde,
            'hora_recogida_hasta'   =>$horaRecogidaHasta,
        );
        $wpdb->insert($nombreTabla, $historyEnv);
    }
}

function cex_procesar_peticion_validacion( $retorno) {        
     $validacion = "false";
        
    switch($retorno['status']){
        case "404":
        case "0":
            $mensaje=array(
                'title'     =>  esc_html(__('Error Validar Credenciales', 'cex_pluggin')),
                'mensaje'   =>  esc_html(__('Servicio temporalmente no disponible, inténtelo más tarde', 'cex_pluggin')),
                'type'      =>  esc_html(__('error', 'cex_pluggin'))
                );
            $validacion=false;
            break;
        case "401":
            $mensaje=array(
                'title'     =>  esc_html(__('Error Validar Credenciales', 'cex_pluggin')),
                'mensaje'   =>  esc_html(__('Las credenciales son incorrectas', 'cex_pluggin')),
                'type'      =>  esc_html(__('error', 'cex_pluggin'))
                );
            $validacion=false;
            break;           
        case "200":
            $mensaje=array(
                'title'     =>  esc_html(__('Credenciales correctas', 'cex_pluggin')),
                'mensaje'   =>  esc_html(__('Guarde sus credenciales en un lugar seguro', 'cex_pluggin')),
                'type'      =>  esc_html(__('success', 'cex_pluggin'))
                );
            $validacion=true;
            break;
        default:
            $mensaje=array(
                'title'     =>  esc_html(__('Error Validar Credenciales', 'cex_pluggin')),
                'mensaje'   =>  esc_html(__('Servicio temporalmente no disponible, inténtelo más tarde', 'cex_pluggin')),
                'type'      =>  esc_html(__('error', 'cex_pluggin'))
                );
            $validacion=false;
            break;

    }  
    $retorno  = array(
        'mensaje'       => $mensaje,
        'validacion'    => $validacion 
    );  

   return $retorno;   
}


function validar_credenciales_rest(){
    $usuario            = $_POST['user'];
    $pass               = $_POST['pass'];
    $rest               = cex_enviar_peticion_validación();             
    $retorno            = cex_procesar_curl_validacion($rest, $usuario, $pass);  
    $respuesta_tracking = cex_procesar_peticion_validacion($retorno);
    
    echo json_encode($respuesta_tracking);    
}

function cex_procesar_curl_validacion($peticion, $usuario=false, $password=false)
{
    $credenciales;
    if(!$usuario && !$password)
        $credenciales = get_user_credentials();
    else{
        $credenciales =  array();
        $credenciales['usuario'] = $usuario;
        $credenciales['password'] = $password;
    }    
    // iniciamos y componemos la peticion curl
     
    $header = array(
        "Charset=\"utf-8\"",
        "Accept"         => "application/json",            
        "Cache-Control"  => "no-cache",
        "Pragma"         => "no-cache",
        "Content-Type"   => "application/json",
        "Content-length" => strlen($peticion['peticion']),
        "Authorization"  => "Basic " . base64_encode( $credenciales['usuario'] . ":" . $credenciales['password'] )
    );     


    $options = array(
        CURLOPT_RETURNTRANSFER  => 1,
        CURLOPT_SSL_VERIFYHOST  => false,
        CURLOPT_SSL_VERIFYPEER  => false,
        CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0)',
        CURLOPT_URL             => $peticion['url'] ,
        CURLOPT_USERPWD         => trim($credenciales['usuario']).":".trim($credenciales['password']),
        CURLOPT_POST            => true ,                    
        CURLOPT_POSTFIELDS      => $peticion['peticion'],
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_BINARYTRANSFER  => 1,
        CURLOPT_SSLVERSION      => CURL_SSLVERSION_SSLv3,                   
        'headers'               => $header,        
        'body'                  => $peticion['peticion']
    );
  
    $post           = wp_remote_post($peticion['url'], $options );
    $result         = wp_remote_retrieve_body($post);
    $status_code    = wp_remote_retrieve_response_code($post);  

    
    $ret = [
        'result' => $result,
        'status' => $status_code,
    ];  

        return $ret;
}

    function enviar_peticion_borrado_recogida($numship,$codigo_cliente)
    {
        $url            = 'https://www.cexpr.es/wsps/apiRestGrabacionRecogidaEnviok8s/json/anularRecogida'; 
        $data = array(
            "solicitante"       => "W".$codigo_cliente,
            "password"          => "",
            "keyRecogida"       => $numship,
            "strTextoAnulacion" => "Anulacion E-Commerce",
            "strUsuario"        => "",
            "strReferencia"     => "",
            "strCodCliente"     => "",
            "strFRecogida"      => "",
            "idioma"            => 'ES'   
        );  
            
        $ret = [
            'peticion' => json_encode($data),
            'url'  => $url,
        ];

        return $ret; 
    }

    
    function enviar_peticion_modificar_recogida(){

        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';
        $url = $wpdb->get_var("SELECT valor
        FROM $table
        WHERE clave= 'MXPS_WSURLMODF_REST'");       
        $data = array(
            "solicitante"      => "",
            "password"         => "",
            "intKeyRecogida"   => "",
            "strCP"            => "",
            "strCPDes"         => "",
            "strClienteDes"    => "",
            "strCodCliente"    => "",
            "strContacto"      => "",
            "strContactoDes"   => "",
            "strCpIntDes"      => "",
            "strDirCalleDes"   => "",
            "strDirNumDes"     => "",
            "strDirRestoDes"   => "",
            "strDireccion"     => "",
            "strF1Desde"       => "",
            "strF1Hasta"       => "",
            "strF2Desde"       => "",
            "strF2Hasta"       => "",
            "strFRecogida"     => "",
            "strNifDni"        => "",
            "strNombre"        => "",
            "strNombreDes"     => "",
            "strObservaciones" => "",
            "strPaisIntDes"    => "",
            "strPoblacion"     => "",
            "strPoblacionDes"  => "",
            "strReferencia"    => "",
            "strTelefono"      => "",
            "strTelefonoDes"   => "",
            "strTexManual"     => "",
            "strUsuario"       => ""
        );  
    }

    function enviar_peticion_anular_recogida(){

        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';
        $url = $wpdb->get_var("SELECT valor
        FROM $table
        WHERE clave= 'MXPS_WSURLANUL_REST'");        
        $data = array(
            "solicitante"       => "",
            "password"          => "",
            "keyRecogida"       => "",
            "strTextoAnulacion" => "",
            "strUsuario"        => "",
            "strReferencia"     => "",
            "strCodCliente"     => "",
            "strFRecogida"      => ""
        );
    }

    

    function cex_gestionar_borrado_pedido()
    {
        $numship       = $_POST['numship']; 
        $numcollect    = $_POST['numcollect'];
        $arrayIdioma = [
            'idioma'    => $_POST['idioma']
        ];



        //miramos el tipo de lo que vamos a borrar
        $ordenes      = obtener_orden_by_numCollect($numcollect);
       
        $peticion   = null;



        $ordenEnvio;
        $ordenRecogida;

        foreach ($ordenes as $orden) {
           if($orden->type == 'Envio'){
                $ordenEnvio = $orden;
           }
           if($orden->type == 'Recogida'){
                $ordenRecogida = $orden;
           }
        }
        $cuantas    = obtener_cuantas_ordenes_by_numShip($ordenRecogida->numship);

        if($cuantas == 1 && strcmp("Automatica", $ordenRecogida->numship)!=0){                 
            //Borramos
            $peticion       = enviar_peticion_anulado_recogida($ordenRecogida->numship, $ordenRecogida->codigo_cliente,$arrayIdioma);
            $retorno        = procesar_curl_borrado_recogida($peticion);
            $respuesta      = procesar_peticion_borrado($retorno, $ordenRecogida->numship, $ordenRecogida->type,$peticion, $numcollect); 
        }
        

        $bandera=borrar_orden_by_numcollect($numcollect);
        $literalEnvio1  = esc_html(__('La petición con referencia: ', 'cex_pluggin'));
        $literalEnvio2  = esc_html(__(' ha sido borrada correctamente', 'cex_pluggin'));
        $literalEnvio   = esc_html(__($literalEnvio1.$numcollect.$literalEnvio2));


        $retorno = [
            'codigoError' => '',
            'mensaje'     => $literalEnvio,
        ]; 
        return $retorno;
    }

    function borrar_orden_by_numcollect($numcollect)
    {  
        global $wpdb;
        $nombreTabla= $wpdb->prefix.'cex_savedships';
        $savedship = array('deleted_at' => date("Y-m-d H:i:s"));
        $where = array('numcollect' => $numcollect);
        $comprobante = $wpdb->update($nombreTabla, $savedship, $where);
        $nombreTablaBultos= $wpdb->prefix.'cex_envios_bultos';
        $bultos = array('deleted_at' => date("Y-m-d H:i:s"));
        $where = array('numcollect' => $numcollect);
        $wpdb->update($nombreTablaBultos, $bultos, $where);
        return $comprobante;
    }
     


    function obtener_orden_by_numCollect($numcollect ){
        global $wpdb;    
        $table        = $wpdb->prefix.'cex_savedships';
        $sql          = "SELECT * FROM $table WHERE numcollect = '".$numcollect."'";
        $result       =  $wpdb->get_results($wpdb->prepare($sql, null));   
        return $result;    
    }

    function obtener_cuantas_ordenes_by_numShip($numship){       
        global $wpdb;        
        $table        = $wpdb->prefix.'cex_savedships';
        $sql          = "SELECT COUNT(*) FROM $table WHERE numship = '".$numship."' AND deleted_at IS NULL";
        $result       =  $wpdb->get_var($wpdb->prepare($sql, null));  
        return $result;    
    }

    function enviar_peticion_anulado_recogida($numship,$codigo_cliente,$arrayIdioma)
    {
        global $wpdb;        
        $table        = $wpdb->prefix.'cex_customer_options';
        $sql          = "SELECT valor FROM $table WHERE clave = 'MXPS_WSURLANUL_REST'";
        $url          =  $wpdb->get_results($wpdb->prepare($sql, null));   
       
          
        $data = array(
            "solicitante"       => "P".$codigo_cliente,
            "password"          => "",
            "keyRecogida"       => $numship,
            "strTextoAnulacion" => "Anulacion E-Commerce",
            "strUsuario"        => "",
            "strReferencia"     => "",
            "strCodCliente"     => "",
            "strFRecogida"      => "",
            "idioma"            => obtener_idioma($arrayIdioma),    
        );            
            
        $ret = [
            'peticion' => json_encode($data),
            'url'  => $url[0]->valor,
        ];

        return $ret; 
    }


    function procesar_curl_borrado_recogida($peticion)
    {
        $credenciales = get_user_credentials();    
    // iniciamos y componemos la peticion curl
     
        $header = array(
            "Charset=\"utf-8\"",
            "Accept"         => "application/json",            
            "Cache-Control"  => "no-cache",
            "Pragma"         => "no-cache",
            "Content-Type"   => "application/json",
            "Content-length" => strlen($peticion['peticion']),
            "Authorization"  => "Basic " . base64_encode( $credenciales['usuario'] . ":" . $credenciales['password'] )
        );    
    
    
        $options = array(
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0)',
            CURLOPT_URL             => $peticion['url'] ,
            CURLOPT_USERPWD         => trim($credenciales['usuario']).":".trim($credenciales['password']),
            CURLOPT_POST            => true ,                    
            CURLOPT_POSTFIELDS      => $peticion['peticion'],
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_BINARYTRANSFER  => 1,
            CURLOPT_SSLVERSION      => CURL_SSLVERSION_SSLv3,                   
            'headers'               => $header,        
            'body'                  => $peticion['peticion']
        );
    
        $post           = wp_remote_post($peticion['url'], $options );
        $result         = wp_remote_retrieve_body($post);
        $status_code    = wp_remote_retrieve_response_code($post);  
    
        return $result;
    }

     
    
    function procesar_peticion_borrado($curl,$numship,$tipo,$rest,$numcollect)
    {
        global $wpdb;
        $nombreTabla  = $wpdb->prefix.'cex_history';
        $fecha        = date("Y-m-d H:i:s");
        $table        = $wpdb->prefix.'cex_savedships';
        $sql          = "SELECT  id_order
                        FROM $table
                        WHERE numship = '".($numship)."'";
        $id_orden     =  $wpdb->query($sql);
        $curlDecode   = json_decode($curl);  
        $codigoError = $curlDecode->codError;     
        $mensajeRetorno = $curlDecode->mensError; 
        $literalRecogida = esc_html(__('La recogida '.$numship.'ha sido anulada correctamente'));
        $resultado = '0';
        if(strcmp($codigoError, "0")==0){   
            $mensajeRetorno=$literalRecogida;
            $resultado = '1';
        }

        $historyEnv = array(
            'type'                  => 'Borrar Recogida' ,
            'mensajeRetorno'        => $mensajeRetorno,
            'codigoRetorno'         => $codigoError,
            'numShip'               => $numship,
            'numCollect'            => $numcollect,
            'id_order'              => $id_orden,
            'resultado'             => $resultado,
            'envioWS'               => $rest['peticion'],
            'respuestaWS'           => $curl,
            'fecha'                 => $fecha,
        );
        $wpdb->insert($nombreTabla, $historyEnv); 
        
        $ret = [
                'codigoError' => $codigoError,
                'mensaje'  => $mensajeRetorno,
        ];           
        return $ret;
    }

    function obtenerCodigoSolicitante(){

        global $wp_version;
        
        
        $version = CORREOSEXPRESS_VERSION_SOLICITANTE;

        $versionCms = str_replace('.', '',$wp_version);
        $versionCms = substr($versionCms, 0, 3);

        $codigo="W".$versionCms."_".$version."_";

        return $codigo;
    }


