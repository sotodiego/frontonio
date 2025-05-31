<?php
require_once('helpers.php');
/*function sanear_string($string)
{
    $string = trim($string);
 
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ã', 'ª', 'Á', 'À', 'Â', 'Ä', 'Ã'),
        array('a', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A'),
        $string
    );
        
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
    
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
    
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'õ', 'Ó', 'Ò', 'Ö', 'Ô', 'Õ'),
        array('o', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'O'),
        $string
    );
    
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
    
    $string = str_replace(
            array('ñ', 'Ñ', 'ç','Ç','º','&','ª','°','ń','—','№'),
            array('n', 'N', 'c','C','','','','', 'n','-','N'),
            $string
     );
 
    return $string;
}*/




function enviar_peticion_recogida_soap_cex($datos)
{

    //coger valores para el curl
    //sacar de la tabla customer_options
    // - MXPS_WSURLREC
    global $wpdb;
    $datos = array_map('htmlspecialchars', $datos);
    $table = $wpdb->prefix.'cex_customer_options';
    $column  = 'MXPS_WSURLREC';
    $url = $wpdb->get_var(" SELECT valor
        FROM $table 
        WHERE clave = '$column'");
    
    //rellenamos los codigos postales con 0 a la izquierda en caso de ser necesario
    $longitud = 5;
    $cprem = $datos['postcode_sender'];
    $cpdest = $datos['postcode_receiver'];

    if ($datos['iso_code_remitente'] == 'ES') {
        $postcode_sender = cex_rellenar_ceros($cprem, $longitud);
    }else {
        $postcode_sender = $cprem;
    }


    //split por -  y recolocar fecha ( FORMATO INCORRECTO. FORMATO VALIDO - DDMMYYYY)
    $fecha= $datos['datepicker'];
    $fechaformat= explode('-', $fecha);

    //Esta parte rellenar con $datos
    //generacion del xml
    $soap_request = '
    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:es="es.chx.ws.recogidas">
    <soapenv:Header/>
    <soapenv:Body>
    <es:grabarRecogida>
    <es:solicitante>'.$datos['codigo_solicitante'].'</es:solicitante>
    <es:password></es:password>
    <es:canalEntrada></es:canalEntrada>
    <es:refRecogida>'.$datos['ref_ship'].'</es:refRecogida>
    <es:fechaRecogida>'.$fechaformat[2].'-'.$fechaformat[1].'-'.$fechaformat[0].'</es:fechaRecogida>
    <es:horaDesde1>'.$datos['fromHH_sender'].':'.$datos['fromMM_sender'].'</es:horaDesde1>
    <es:horaHasta1>'.$datos['toHH_sender'].':'.$datos['toMM_sender'].'</es:horaHasta1>
    <es:horaDesde2></es:horaDesde2>
    <es:horaHasta2></es:horaHasta2>
    <es:clienteRecogida>'.$datos['codigo_cliente'].'</es:clienteRecogida>
    <es:codRemit></es:codRemit>
    <es:NomRemit>'.$datos['name_sender'].'</es:NomRemit>
    <es:nifRemit></es:nifRemit>
    <es:dirRecog>'.$datos['address_sender'].'</es:dirRecog>
    <es:poblRecog>'.$datos['city_sender'].'</es:poblRecog>    
    <es:cpRecog>'.$postcode_sender.'</es:cpRecog>
    <es:contRecog>'.$datos['contact_sender'].'</es:contRecog>
    <es:tlfnoRecog>'.$datos['phone_sender'].'</es:tlfnoRecog>
    <es:emailRecog>'.$datos['email_sender'].'</es:emailRecog>
    <es:observ>'.$datos['note_collect'].'</es:observ>
    <es:tipoServ></es:tipoServ>
    <es:codDest></es:codDest>
    <es:nomDest>'.$datos['name_receiver'].'</es:nomDest>
    <es:nifDest></es:nifDest>
    <es:dirDest>'.$datos['address_receiver'].'</es:dirDest>
    <es:pobDest>'.$datos['city_receiver'].'</es:pobDest>';
  
    if ($datos['iso_code'] == 'ES') {
        $soap_request .= '<es:cpDest>'.cex_rellenar_ceros($cpdest,5).'</es:cpDest>';        
        $soap_request .= '<es:paisDest>'.$datos['iso_code'].'</es:paisDest>';
        $soap_request .= '<es:cpiDest></es:cpiDest>';        
    // envios a portugal
    }elseif ($datos['iso_code'] == 'PT') {
        $soap_request .= '<es:cpDest></es:cpDest>';
        $soap_request .= '<es:paisDest>'.$datos['iso_code'].'</es:paisDest>';
        $soap_request .= '<es:cpiDest>'.cex_rellenar_ceros($cpdest,4).'</es:cpiDest>';
    }else {
        // internacionales
        //Por aqui solo internacionales, si hay error, lo da el WS
        //if ($datos['selCarrier'] == '90' || $datos['selCarrier'] == '91') {
            $soap_request .= '<es:cpDest></es:cpDest>';
            $soap_request .= '<es:paisDest>'.$datos['iso_code'].'</es:paisDest>';
            $soap_request .= '<es:cpiDest>'.$cpdest.'</es:cpiDest>';
        //}
    }


    $soap_request .= '<es:contactoDest>'.$datos['contact_receiver'].'</es:contactoDest>
    <es:tlfnoDest>'.$datos['phone_receiver1'].'</es:tlfnoDest>
    <es:emailDest>'.$datos['email_receiver'].'</es:emailDest>
    <es:nEnvio></es:nEnvio>
    <es:refEnvio></es:refEnvio>
    <es:producto>'.$datos['selCarrier'].'</es:producto>
    <es:kilos>'.calcularPesoEnKilos($datos['kilos']).'</es:kilos>
    <es:bultos>'.$datos['bultos'].'</es:bultos>
    <es:volumen></es:volumen>
    <es:tipoPortes>P</es:tipoPortes>';
    if (!empty($datos['payback_val'])) {
        $soap_request .= '<es:importReembol>'.$datos['payback_val'].'</es:importReembol>';
    }
    $soap_request .= '<es:valDeclMerc></es:valDeclMerc>
    <es:infTec></es:infTec>
    <es:nSerie></es:nSerie>
    <es:modelo></es:modelo>
    </es:grabarRecogida>
    </soapenv:Body>
    </soapenv:Envelope>';

    //$soap_request=sanear_string($soap_request);

    $retorno = [
        'soap' => $soap_request,
        'url'  => $url
    ];

    return $retorno;
}



function cex_enviar_peticion_envio_soap($datos)
{
    global $wpdb;
    $datos = array_map('htmlspecialchars', $datos);
    $table = $wpdb->prefix.'cex_customer_options';
    $column  = 'MXPS_WSURL';
    $url = $wpdb->get_var(" SELECT valor
        FROM $table 
        WHERE clave = '$column'");

    $fecha= $datos['datepicker'];
    $fechaformat= explode('-', $fecha);

    //rellenamos los codigos postales con 0 a la izquierda en caso de ser necesario
    $longitud = 5;
    $cprem = $datos['postcode_sender'];
    $cpdest = $datos['postcode_receiver'];

    if ($datos['iso_code_remitente'] == 'ES') {
        $postcode_sender = cex_rellenar_ceros($cprem, $longitud);
    }else {
        $postcode_sender = $cprem;
    }


    $soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"                    xmlns:mes="messages.envios.ws.chx.es" xmlns:xsd="http://pojo.envios.ws.chx.es/xsd">
    <soapenv:Header/>
    <soapenv:Body>';

    //if recogida en oficina
    if ($datos['entrega_oficina']=='true') {
        $soap_request .= '<mes:grabarEnvioEntregaOficina>';
    }else {
        $soap_request .= '<mes:grabarEnvio>';
    }

    $soap_request .='<mes:solicitante>'.$datos['codigo_solicitante'].'</mes:solicitante>
    <mes:canalEntrada></mes:canalEntrada>
    <mes:ref>'.$datos['ref_ship'].'</mes:ref>
    <mes:refCli></mes:refCli>
    <mes:fecha>'.$fechaformat[2].'-'.$fechaformat[1].'-'.$fechaformat[0].'</mes:fecha>
    <mes:codRte>'.$datos['codigo_cliente'].'</mes:codRte>
    <mes:nomRte>'.$datos['name_sender'].'</mes:nomRte>
    <mes:dirRte>'.$datos['address_sender'].'</mes:dirRte>
    <mes:pobRte>'.$datos['city_sender'].'</mes:pobRte>';
    
    if ($datos['iso_code_remitente'] == 'ES') {
        $soap_request .= '<mes:codPosNacRte>'.$postcode_sender.'</mes:codPosNacRte>';
        $soap_request .= '<mes:paisISORte>'.$datos['iso_code_remitente'].'</mes:paisISORte>';
        $soap_request .= '<mes:codPosIntRte></mes:codPosIntRte>';
    }elseif ($datos['iso_code_remitente'] == 'PT') {
        $soap_request .= '<mes:codPosNacRte></mes:codPosNacRte>';
        $soap_request .= '<mes:paisISORte>'.$datos['iso_code_remitente'].'</mes:paisISORte>';
        $soap_request .= '<mes:codPosIntRte>'.$postcode_sender.'</mes:codPosIntRte>';
    }else {
        // internacionales
        $soap_request .= '<mes:codPosIntRte>'.$postcode_sender.'</mes:codPosIntRte>';
        $soap_request .= '<mes:paisISORte>'.$datos['iso_code_remitente'].'</mes:paisISORte>';
    }


    $soap_request .= '<mes:contacRte>'.$datos['contact_sender'].'</mes:contacRte>
    <mes:telefRte>'.$datos['phone_sender'].'</mes:telefRte>
    <mes:emailRte>'.$datos['email_sender'].'</mes:emailRte>
    <mes:nomDest>'.$datos['name_receiver'].'</mes:nomDest>
    <mes:dirDest>'.$datos['address_receiver'].'</mes:dirDest>
    <mes:pobDest>'.$datos['city_receiver'].'</mes:pobDest>';

    if ($datos['iso_code'] == 'ES') {
        $soap_request .= '<mes:codPosNacDest>'.cex_rellenar_ceros($cpdest,5).'</mes:codPosNacDest>';        
        $soap_request .= '<mes:paisISODest>'.$datos['iso_code'].'</mes:paisISODest>';
        $soap_request .= '<mes:codPosIntDest></mes:codPosIntDest>';        
    // envios a portugal
    }elseif ($datos['iso_code'] == 'PT') {
        $soap_request .= '<mes:codPosNacDest></mes:codPosNacDest>';
        $soap_request .= '<mes:paisISODest>'.$datos['iso_code'].'</mes:paisISODest>';
        $soap_request .= '<mes:codPosIntDest>'.cex_rellenar_ceros($cpdest,4).'</mes:codPosIntDest>';
    }else {
        // internacionales
        //Por aqui solo internacionales, si hay error, lo da el WS
        //if ($datos['selCarrier'] == '90' || $datos['selCarrier'] == '91') {
            $soap_request .= '<mes:codPosNacDest></mes:codPosNacDest>';
            $soap_request .= '<mes:paisISODest>'.$datos['iso_code'].'</mes:paisISODest>';
            $soap_request .= '<mes:codPosIntDest>'.$cpdest.'</mes:codPosIntDest>';
        //}
    }

    $soap_request .= '<mes:contacDest>'.$datos['contact_receiver'].'</mes:contacDest>
    <mes:telefDest>'.$datos['phone_receiver1'].'</mes:telefDest>
    <mes:emailDest>'.$datos['email_receiver'].'</mes:emailDest>
    <mes:telefOtrs>'.$datos['phone_receiver2'].'</mes:telefOtrs>
    <mes:observac>'.$datos['note_deliver'].'</mes:observac>
    <mes:numBultos>'.$datos['bultos'].'</mes:numBultos>
    <mes:kilos>'.calcularPesoEnKilos($datos['kilos']).'</mes:kilos>
    <mes:producto>'.$datos['selCarrier'].'</mes:producto>
    <mes:portes>P</mes:portes>';

    if (!empty($datos['payback_val'])) {
        $soap_request .= '<mes:reembolso>'.$datos['payback_val'].'</mes:reembolso>';
    }
    if ($datos['deliver_sat']=='true') {
        $soap_request .= '<mes:entrSabado>S</mes:entrSabado>';
    }

    //Valor Asegurado
    $soap_request .='<mes:seguro>'.$datos['insured_value'].'</mes:seguro>';

    for ($i=1;$i<=$datos['bultos'];$i++) {
        $soap_request .= '<mes:listaBultos>
        <xsd:alto></xsd:alto>
        <xsd:ancho></xsd:ancho>
        <xsd:codBultoCli>'.$i.'</xsd:codBultoCli>
        <xsd:codUnico></xsd:codUnico>
        <xsd:descripcion></xsd:descripcion>
        <xsd:kilos></xsd:kilos>
        <xsd:largo></xsd:largo>
        <xsd:observaciones></xsd:observaciones>
        <xsd:orden>'.$i.'</xsd:orden>
        <xsd:referencia></xsd:referencia>
        <xsd:volumen></xsd:volumen>
        </mes:listaBultos>';
    }
    //if recogida en oficina
    if ($datos['entrega_oficina']=='true') {
        $soap_request .= '<mes:coddirecDestino>'.$datos['codigo_oficina'].'</mes:coddirecDestino>';
        $soap_request .= '</mes:grabarEnvioEntregaOficina></soapenv:Body></soapenv:Envelope>';
    }else {
        $soap_request .= '</mes:grabarEnvio></soapenv:Body></soapenv:Envelope>';
    }

    //$soap_request=sanear_string($soap_request);

    $retorno = [
        'soap' => $soap_request,
        'url'  => $url

    ];

    return $retorno;
}

function cex_enviar_peticion_tracking($codigo_solicitante, $numship)
{
    //coger la url de BBDD
    global $wpdb;
    $table = $wpdb->prefix.'cex_customer_options';
    $column  = 'MXPS_WSURLSEG';
    $url = $wpdb->get_var(" SELECT valor
        FROM $table 
        WHERE clave = '$column'");

    $soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mes="messages.seguimientoEnvio.ws.chx.es">
    <soapenv:Header/>
    <soapenv:Body>
    <mes:seguimientoEnvio>
    <mes:solicitante>'.$codigo_solicitante.'</mes:solicitante>
    <mes:dato>'.$numship.'</mes:dato>
    <!--Optional:-->
    <mes:password></mes:password>
    </mes:seguimientoEnvio>
    </soapenv:Body>
    </soapenv:Envelope>';

    $retorno = [
        'soap' => $soap_request,
        'url'  => $url
    ];
    return $retorno;
}

function cex_procesar_curl($peticion, $usuario=false, $password=false)
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
    $header = array("Content-type"=> "text/xml;charset=\"utf-8\"",
        "Accept" => "text/xml",
        "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "SOAPAction: \"".$peticion['url']."\"",
        "Authorization" => "Basic " . base64_encode( $credenciales['usuario'] . ":" . $credenciales['password'] ));

    

    $options    = array(
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_SSL_VERIFYHOST  => false,
                    CURLOPT_SSL_VERIFYPEER  => false,
                    CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0)',
                    CURLOPT_URL             => $peticion['url'] ,
                    CURLOPT_USERPWD         => $credenciales['usuario'].":".$credenciales['password'],
                    CURLOPT_POST            => true ,
                    CURLOPT_POSTFIELDS      => $peticion['soap'],
                    'headers' => $header,        
                    'body' => $peticion['soap']
                );


    $output = wp_remote_retrieve_body(wp_remote_post( $peticion['url'],$options));
    return $output;
}
add_action('wp_ajax_procesar_curl_oficina_rest', 'procesar_curl_oficina_rest');
add_action('wp_ajax_nopriv_procesar_curl_oficina_rest', 'procesar_curl_oficina_rest');

function procesar_curl_oficina_rest()
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce-user')) {
        die('Security procesar_curl_oficina_rest');
    }
    $credenciales = get_user_credentials();

    $data = array("cod_postal" => sanitize_text_field(trim($_POST["cod_postal"])), "poblacion" => sanitize_text_field(trim($_POST["poblacion"])));
    $data_json = json_encode($data);
    
    global $wpdb;
    $table = $wpdb->prefix.'cex_customer_options';
    $column  = 'MXPS_APIRESTOFI';
    $url = $wpdb->get_var(" SELECT valor
                            FROM $table 
                            WHERE clave = '$column'");

    //API REST que recibe las oficinas de Correos    
    $header = array("Content-type"=> "application/json;charset=\"utf-8\"",       
        "Content-length"=> strlen($data_json),
        "Authorization" => "Basic " . base64_encode( $credenciales['usuario'] . ":" . $credenciales['password'] ));
    $options=array(
        CURLOPT_URL => $url,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLINFO_HEADER_OUT => true,
        CURLOPT_POSTFIELDS => $data_json,
        CURLOPT_RETURNTRANSFER=> true,
        CURLOPT_RETURNTRANSFER => true,
        //CURLOPT_HTTPAUTH=> CURLAUTH_BASIC,        
        CURLOPT_SSL_VERIFYPEER => false,
        'headers' => $header,
        'body' => $data_json,        
    );        
    $peticion = wp_remote_post( $url, $options );    
    $result = wp_remote_retrieve_body( $peticion );
    $status_code = wp_remote_retrieve_response_code($result);//get status code
    $info = wp_remote_post( $url);
    

    if ($result == null) {
        echo '';
        exit;
    }else {
        //recorrer result y ver que hay en ello
        $retorno = json_decode($result);
        $oficinas = $retorno->oficinas;

        echo json_encode($oficinas);
        exit;
    }
}
add_action('wp_ajax_procesar_curl_oficina_rest', 'procesar_curl_oficina_rest');
add_action('wp_ajax_nopriv_procesar_curl_oficina_rest', 'procesar_curl_oficina_rest');

function procesar_peticion_recogida_soap_cex($soap, $retorno, $id_orden, $type, $numcollect)
{
    $fecha              = date("Y-m-d H:i:s");
    global $wpdb;
    $nombreTabla = $wpdb->prefix."cex_history";
    //si retorno vacio el WS no funciono
    if (empty($retorno)) {
        $historyRec = array(
            'type'                  =>'Recogida',
            'mensajeRetorno'        =>'Error [WS] conection',
            'codigoRetorno'         =>'',
            'numShip'               =>'',
            'numcollect'            =>  $numcollect,
            'id_order'              =>  $id_orden,
            'resultado'             =>'0',
            'envioWS'               =>$soap,
            'respuestaWS'           =>$retorno,
            'fecha'                 =>$fecha,
        );
        $wpdb->insert($nombreTabla, $historyRec);

        $retorno = [
            'mensajeRetorno'    => 'Error [WS] conection',
            'numShip'           => '',
            'resultado'         => '0',
            ];
        return $retorno;
    }

    $DOM = new DOMDocument('1.0', 'utf-8');
    $DOM->loadXML($retorno);

    //numRecogida == NUMSHIP
    $mensajeRetorno     = $DOM->getElementsByTagName('mensajeRetorno')->item(0)->nodeValue;
    $codigoRetorno      = $DOM->getElementsByTagName('codigoRetorno')->item(0)->nodeValue;
    $numRecogida        = $DOM->getElementsByTagName('numRecogida')->item(0)->nodeValue;
    $resultado          = $DOM->getElementsByTagName('resultado')->item(0)->nodeValue;
    $fecha              = date("Y-m-d H:i:s");

    if ($numRecogida == '') {
        $coincidencias;
        preg_match('/(?<=KEY RECOGIDA: )(.*)(?= FECHA)/', $retorno, $coincidencias);
        $aux = $coincidencias['0'];
        if (!empty($aux)) {
            $aux = str_replace('KEY RECOGIDA: ', '', $aux);
            $aux = str_replace(' FECHA', '', $aux);
            $numRecogida= $aux;
        }
    }

    //LOG  --- COMENTAR NO BORRAR
    //$destino= 'C:\xampp\htdocs\wp\wp-content\plugins\correosExpress\log_tipo_peticion_recogida_dump.txt';
    $destino='..\log_tipo_peticion_recogida_dump.txt';
    /*
        file_put_contents($destino, print_r($retorno, true));
        file_put_contents($destino,"\n\r______________", FILE_APPEND);
        file_put_contents($destino,"\n\r El mensaje de retorno :".$mensajeRetorno, FILE_APPEND);
        file_put_contents($destino,"\n\r El codigo de retorno :".$codigoRetorno, FILE_APPEND);
        file_put_contents($destino,"\n\r El numero de recogida es :".$numRecogida, FILE_APPEND);
        file_put_contents($destino,"\n\r El resultado :".$resultado, FILE_APPEND);
    */
    //hacer un insert de esa peticion y todo lo que se envio
    $historyRec = array(
        'type'                  =>  'Recogida',
        'mensajeRetorno'        =>  $mensajeRetorno,
        'codigoRetorno'         =>  $codigoRetorno,
        'numShip'               =>  $numRecogida,
        'numcollect'            =>  $numcollect,
        'id_order'              =>  $id_orden,
        'resultado'             =>  $resultado,
        'envioWS'               =>  $soap,
        'respuestaWS'           =>  $retorno,
        'fecha'                 =>  $fecha,
    );
    $wpdb->insert($nombreTabla, $historyRec);
    $retorno = [
        'mensajeRetorno'    => $mensajeRetorno,
        'numShip'           => $numRecogida,
        'resultado'         => $resultado,
    ];
    return $retorno;
}

function cex_procesar_peticion_envio_soap($soap, $retorno, $id_orden, $type, $numcollect)
{
    $fecha              = date("Y-m-d H:i:s");
    global $wpdb;
    $nombreTabla = $wpdb->prefix."cex_history";
    //si retorno vacio el WS no funciono
    if (empty($retorno)) {
        $historyEnv = array(
            'type'                  =>$type ,
            'mensajeRetorno'        =>'Error [WS] conection',
            'codigoRetorno'         =>'',
            'numShip'               =>'',
            'numCollect'            =>$numcollect,
            'id_order'              =>$id_orden,
            'resultado'             =>'0',
            'envioWS'               =>$soap,
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
    }else {
        $DOM = new DOMDocument('1.0', 'utf-8');
        $DOM->loadXML($retorno);

        //DATORESULTADO == NUMSHIP
        $mensajeRetorno     = $DOM->getElementsByTagName('mensajeRetorno')->item(0)->nodeValue;
        $codigoRetorno      = $DOM->getElementsByTagName('codigoRetorno')->item(0)->nodeValue;
        $datosResultado     = $DOM->getElementsByTagName('datosResultado')->item(0)->nodeValue;
        $resultado          = $DOM->getElementsByTagName('resultado')->item(0)->nodeValue;
        $listaBultos        = $DOM->getElementsByTagName('listaBultos');

        $codigosBultos;

        //insert en tabla historico
        $historyEnv = array(
            'type'                  =>$type,
            'mensajeRetorno'        =>$mensajeRetorno,
            'codigoRetorno'         =>$codigoRetorno,
            'numShip'               =>$datosResultado,
            'numcollect'            =>$numcollect,
            'id_order'              =>$id_orden,
            'resultado'             =>$resultado,
            'envioWS'               =>$soap,
            'respuestaWS'           =>$retorno,
            'fecha'                 =>$fecha,
        );
        $wpdb->insert($nombreTabla, $historyEnv);

        foreach ($listaBultos as $bulto) {
            $nombreTabla2 = $wpdb->prefix."cex_envios_bultos";
            $hijo = $bulto->childNodes;
            $auxiliar = $hijo->item(1)->nodeValue;// el id del bulto
            $codigosBultos[$auxiliar] = $hijo->item(0)->nodeValue;// el codigo del bulto

            $envio_bultos = array(
                    'id_order'              =>$id_orden,
                    'numCollect'            =>$numcollect,
                    'numShip'               =>$datosResultado,
                    'codUnicoBulto'         =>$hijo->item(0)->nodeValue,
                    'id_bulto'              =>$hijo->item(1)->nodeValue,
                    'fecha'                 =>$fecha,
            );
            //insert
            $wpdb->insert($nombreTabla2, $envio_bultos);
        }

        //LOG  --- COMENTAR NO BORRAR
        //$destino = 'C:\xampp\htdocs\wp\wp-content\plugins\correosExpress\log_tipo_peticion_envio_dump.txt';
        $destino = '..\log_tipo_peticion_envio_dump.txt';
        /*
            file_put_contents($destino, print_r($retorno, true));
            file_put_contents($destino, "\n\r______________", FILE_APPEND);
            file_put_contents($destino, "\n\r El mensaje de retorno :".$mensajeRetorno, FILE_APPEND);
            file_put_contents($destino, "\n\r El codigo de retorno :".$codigoRetorno, FILE_APPEND);
            file_put_contents($destino, "\n\r Los datos de retorno :".$datosResultado, FILE_APPEND);
            file_put_contents($destino, "\n\r El numero de bultos :".var_export($codigosBultos, true), FILE_APPEND);
            file_put_contents($destino, "\n\r La respuesta :".$resultado, FILE_APPEND);
        */

        $retorno = [
            'id_order'              =>$id_orden,
            'numCollect'            =>$numcollect,
            'mensajeRetorno'    => $mensajeRetorno,
            'numShip'           => $datosResultado,
            'resultado'         => $resultado           
        ];
        return $retorno;
    }
}

function cex_procesar_peticion_tracking($soap, $retorno) {        
    if (strpos($retorno, '<?xml') === 0) {
        $DOM = new DOMDocument('1.0', 'utf-8');
        $DOM->loadXML($retorno);
        $codigoEstado = $DOM->getElementsByTagName('codEstado')->item(0)->nodeValue;
        if (empty($codigoEstado)) {
            return 0;
        }
    } else {
        $codigoEstado = 'invalido';
    }

    return $codigoEstado;
}







