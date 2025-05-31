<?php
class EtiquetasVuelo
{
    public $phone_receiver_local;
    public $entrega_oficina;
    public $bultos;
    public $position       = 0;
    public $cb             = 0; //counter barcodes in page
    public $last_file      = false;
    public $per_page       = 3;
    public $incH           = 88;
    public $offset         = 7;

    /*
    documentation.
    I : send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
    D : send to the browser and force a file download with the name given by name.
    F : save to a local server file with the name given by name.
    S : return the document as a string (name is ignored).
    FI : equivalent to F + I option
    FD : equivalent to F + D option
    E : return the document as base64 mime multi-part email attachment (RFC 2045)

    */

    public function __construct()
    {
        error_reporting(0);
        if (!class_exists('TCPDF_CEX')) {
            require('tcpdf_cex/tcpdf_cex.php');
        }
        require_once('helpers.php');
        error_reporting(0);
    }

    public function cex_generarEtiquetas($numCollect, $tipoEtiqueta, $posicion)
    {
        // este puede ser un array
        $pdf = $this->cex_iniciarEtiquetas($numCollect, $tipoEtiqueta, $posicion);
        $Output = $pdf->Output('etiquetas.pdf', 'E');
        return $Output;
    }

    public function cex_generarResumen($numCollect, $date)
    {
        $pdf =  $this->cex_generarResumenPdf($numCollect, $date);
        $Output = $pdf->Output('lista_envios_'.$date.'.pdf', 'E');

        return $Output;
    }

    private function cex_retornarNombresProductos($order_id)
    {
        global $wpdb;
        $id = $order_id;
        $order = wc_get_order($id);
        $delimitador = ' , ';
        $retorno = '';
        $nameProducts='';
        $idProducts='';
        $skuProducts='';
        $count=1;
        $table = $wpdb->prefix.'cex_customer_options';    
        $products = $order->get_items(); 
        foreach ($products as $producto) {  
            if($count<count($products)){
                $product = $producto->get_product();
                $nameProducts.='#'.$product->get_name().'('.$producto->get_quantity().')'.$delimitador;
                $idProducts.='Ref#'.$product->get_id().$delimitador;
                $skuProducts.='#'.$product->get_sku().$delimitador;
            }else{
                $product = $producto->get_product();
                $nameProducts.='#'.$product->get_name().'('.$producto->get_quantity().')';
                $idProducts.='Ref#'.$product->get_id();
                $skuProducts.='#'.$product->get_sku();
            }
            $count++;
        }    
        $MXPS_NODATAPROTECTION = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_NODATAPROTECTION'");
        $MXPS_DATAPROTECTIONVALUE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_DATAPROTECTIONVALUE'");        
        /*
            1 => ID
            2 => NOMBRE
            3 => VACIO
         */        
        if($MXPS_NODATAPROTECTION == "true"){            
            foreach ($products as $producto) {
                switch($MXPS_DATAPROTECTIONVALUE){
                    case 1:
                        $retorno = $idProducts;
                        break;
                    case 2:
                        $retorno = $nameProducts;
                        break;
                    case 3:
                        $retorno = '';
                        break;
                    case 4:
                        $retorno = $skuProducts;
                        break;
                    default :
                        $retorno = '';
                        break;
                    break; 
                }                
            }
        }//else{                
            //$retorno .= $idProducts;            
        //}                
        return $retorno;
    }

    protected function cex_iniciarEtiquetas($numCollect, $tipo, $posicion)
    {
        $numCollect =  $this->cex_comprobarUnaVariasEtiquetas($numCollect);
        $pdf = $this->cex_obtenerTipoEtiqueta($numCollect, $tipo, $posicion);

        return $pdf;
    }

    protected function cex_comprobarUnaVariasEtiquetas($numCollect)
    {
        // si numCollect tiene ;  es que esta usando el delimitar de numCollects por lo que son varios.
        // retornar el array de numCollects;
        $retorno = '';
        if (is_array($numCollect)) {
            foreach ($numCollect as $paquete) {
                $retorno .= "'".$paquete."'".',';
            }

            $retorno = substr($retorno, 0, -1);
        } else {
            $retorno = "'".$numCollect."'";
        }

        return $retorno;
    }

    protected function cex_comprobarDatosRemitente()
    {
        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';
        $results = $wpdb->get_var("SELECT valor FROM $table where clave = 'MXPS_LABELSENDER'");
        if ($results == 'true') {
            return true;
        } else {
            return false;
        }
    }
 
    protected function cex_obtenerTipoEtiqueta($numCollect, $tipo, $posicion)
    {
        $retorno='';

        switch ($tipo) {
            case 1:
                $retorno =  $this->cex_generarEtiquetaAdhesiva($numCollect, $posicion);
                break;
            case 2:
                $retorno =  $this->cex_generarEtiquetaMedioFolio($numCollect, $posicion);
                break;
            case 3:
                $retorno =  $this->cex_generarEtiquetaTermica($numCollect);
                break;
        }

        return $retorno;
    }

    protected function cex_obtenerDatosOficina($datosoficina)
    {
        //llegan los datos de paquete->oficina-entrega
        if (!empty($datosoficina)) {
            $cod_oficina = explode("#!#", $datosoficina);
            //retornamos array asociativo con los nombres de los campos ya parseados.
            $retorno['cod_oficina_selecionada'] = $cod_oficina[0];
            $retorno['direccion']               = $cod_oficina[1];
            $retorno['nombre_oficina']          = $cod_oficina[2];
            $retorno['codigo_postal_oficina']   = $cod_oficina[3];
            $retorno['pob_oficina']             = $cod_oficina[4];
            return $retorno;
        } else {
            return null;
        }
    }

    protected function obtenerLabelSenderText(){
        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';
        $results = $wpdb->get_var("SELECT valor FROM $table where clave = 'MXPS_LABELSENDER_TEXT'");
        return $results;
    }

    protected function cex_generarEnvioRetorno($paquete)
    {
        $numeroRetorno = '';
        if ($paquete->id_bc==55 || $paquete->id_bc==54) {
            $numship=$paquete->numship;
            $producto = '52';
            $etiquetador = substr($numship, 2, 4);
            $rango = substr($numship, 6, 9);
            $rangoretorno = $rango+1;
            $expedicion = $producto.$etiquetador.$rangoretorno;
            $dcexpedicion= $this->cex_calcularDigitoContolRetorno($expedicion);
            $numeroexpedicion= $expedicion.$dcexpedicion;
            $bulto='01';
            $postalcode=$paquete->sender_postcode;
            $codbulto= $expedicion.$bulto.$postalcode;
            $dcbulto = $this->cex_calcularDigitoContolRetorno($codbulto);
            $bultoretorno = $codbulto.$dcbulto;

            $numeroRetorno= array(
                'expedicion'=> $numeroexpedicion,
                'bulto'     => $bultoretorno,
            );
        }
        return $numeroRetorno;
    }

    protected function cex_calcularDigitoContolRetorno($numero)
    {
        $total = 0;
        $cuantos=strlen($numero);
        if ($cuantos<16) {
            $impar = true;
            for ($i=0; $i<$cuantos; $i++) {
                $num = substr($numero, $i, 1);
                if ($impar) {
                    $num=$num*3;
                }
                $total+=$num;
                $impar=!$impar;
            }
            $round = ceil($total/10)*10;
            $dc = $round-$total;
        } else {
            $impar = false;
            for ($i=0; $i<$cuantos; $i++) {
                $num = substr($numero, $i, 1);
                if ($impar) {
                    $num=$num*3;
                }
                $total+=$num;
                $impar=!$impar;
            }
            $round = ceil($total/10)*10;
            if ($round-$total==10) {
                $dc=0;
            } else {
                $dc = $round-$total;
            }
        }
        return $dc;
    }

    protected function cex_generarEtiquetaAdhesiva($numCollect, $posicion)
    {
        //+++++++++++++++++++++++++++++++++++++++++++++++++
        //        ZONA           +        ZONA            +
        //         1             +          4             +
        //+++++++++++++++++++++++++++++++++++++++++++++++++
        //        ZONA                 +     ZONA         +
        //          2                  +       5          +
        //                             +                  +
        //+++++++++++++++++++++++++++++++++++++++++++++++++
        //        ZONA                 +      ZONA        +
        //          3                  +       6          +
        //                             +                  +
        //++++++++++++++++++++++++++++++++++++++++++++++++++
        //         ZONA 7            +          ZONA 8
        //++++++++++++++++++++++++++++++++++++++++++++++++++
        global $wpdb;

        $pdf = new TCPDF_CEX('P', 'mm', 'A4', true, 'UTF-8', false, false);
            
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();
        $this->cb = $posicion - 1;
            
        $table1 = $wpdb->prefix.'cex_savedships';
        $table2 = $wpdb->prefix.'cex_envios_bultos';
        $table3 = $wpdb->prefix.'cex_history';
        $paquetes = $wpdb->get_results($wpdb->prepare("SELECT *    
                FROM $table1 s 
                LEFT JOIN $table2 e
                    ON e.numcollect = s.numcollect
                LEFT JOIN $table3 h
                    ON h.numcollect = s.numcollect AND h.numShip = s.numship
                WHERE s.type = 'Envio' AND e.numcollect in ($numCollect) AND e.deleted_at is null and s.deleted_at is null",null));     
        // define barcode style
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'arial',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        $i = 0;
            
        foreach ($paquetes as $paquete) {
            // cada linea es un bulto no una orden.
            // Una orden varias lineas.
            if (strcmp($paquete->iso_code, 'PT') == 0) {
                $paquete->receiver_postcode=(int)$paquete->receiver_postcode;
            }            
            $numeroRetorno = $this->cex_generarEnvioRetorno($paquete);
            $remitente  = $this->cex_comprobarDatosRemitente();

            $pdf =  $this->cex_generarEtiquetaAdhesivaZona1($pdf, $paquete, $remitente);
            $pdf =  $this->cex_generarEtiquetaAdhesivaZona2y5($pdf, $paquete, $style);
            $pdf =  $this->cex_generarEtiquetaAdhesivaZona3($pdf, $paquete);
            $pdf =  $this->cex_generarEtiquetaAdhesivaZona4($pdf, $paquete, $numeroRetorno);
            $pdf =  $this->cex_generarEtiquetaAdhesivaZona6($pdf, $paquete);
            $pdf =  $this->cex_generarEtiquetaAdhesivaZona7y8($pdf, $paquete);

            $i++;
            $this->cb++;

            if ($this->cb == $this->per_page) {
                $pdf->AddPage();
                $this->cb = 0;
            }

            if ($paquete->id_bc == 54 || $paquete->id_bc == 55) {
                $this->cex_generarEtiquetaRetornoAdhesiva($pdf, $paquete, $numeroRetorno, $style);
                $i++;
                $this->cb++;

                if ($this->cb == $this->per_page) {
                    $pdf->AddPage();
                    $this->cb = 0;
                }
            }
        }

        if ($this->cb == 0) {
            $pdf->deletePage($pdf->getNumPages());
        };

        return $pdf;
    }

    protected function cex_generarEtiquetaRetornoAdhesiva($pdf, $paquete, $numeroRetorno, $style)
    {
        // adaptar datos.
        $paquetes = $this->cex_generarDatosEtiquetaRetorno($paquete, $numeroRetorno);

        $pdf =  $this->cex_generarEtiquetaAdhesivaZona1($pdf, $paquetes, false);
        $pdf =  $this->cex_generarEtiquetaAdhesivaZona2y5($pdf, $paquetes, $style);
        $pdf =  $this->cex_generarEtiquetaAdhesivaZona3($pdf, $paquetes);
        $pdf =  $this->cex_generarEtiquetaAdhesivaZona4($pdf, $paquetes);
        $pdf =  $this->cex_generarEtiquetaAdhesivaZona6($pdf, $paquetes);
        $pdf =  $this->cex_generarEtiquetaAdhesivaZona7y8($pdf, $paquetes);

        return $pdf;
    }

    protected function cex_generarDatosEtiquetaRetorno($paquete, $numeroRetorno)
    {
        
        //$paqueteRetorno = json_decode( json_encode($paquete), true);
        $paqueteRetorno = new stdClass();

        $paqueteRetorno->numcollect           = $paquete->numcollect;
        $paqueteRetorno->receiver_name        = $paquete->sender_name;
        $paqueteRetorno->receiver_address     = $paquete->sender_address;
        $paqueteRetorno->receiver_postcode    = $paquete->sender_postcode;
        $paqueteRetorno->receiver_city        = $paquete->sender_city;
        $paqueteRetorno->receiver_phone       = $paquete->sender_phone;
        $paqueteRetorno->receiver_contact     = $paquete->sender_contact;
        $paqueteRetorno->receiver_country     = $paquete->sender_country;
        $paqueteRetorno->note_deliver         = $paquete->note_deliver;

        $paqueteRetorno->sender_name          = $paquete->receiver_name;
        $paqueteRetorno->sender_phone         = $paquete->receiver_phone;
        $paqueteRetorno->sender_contact       = $paquete->receiver_contact;
        $paqueteRetorno->sender_address       = $paquete->receiver_address;
        $paqueteRetorno->sender_postcode      = $paquete->receiver_postcode;
        $paqueteRetorno->sender_city          = $paquete->receiver_city;
        $paqueteRetorno->sender_country       = '';
        $paqueteRetorno->note_collect         = $paquete->note_collect;

        $paqueteRetorno->date                 = $paquete->date;
        $paqueteRetorno->id_ship              = $paquete->id_ship;
        $paqueteRetorno->desc_ref_1           = $paquete->desc_ref_1;
        $paqueteRetorno->desc_ref_2           = $paquete->desc_ref_2;
        $paqueteRetorno->deliver_sat          = $paquete->deliver_sat;

        $paqueteRetorno->payback_val          = '';
        $paqueteRetorno->id_bc                = '52';
        $paqueteRetorno->mode_ship_name       = 'Multichrono Retorno';

        $paqueteRetorno->id_bulto             = $paquete->id_bulto;
        $paqueteRetorno->kg                   = $paquete->kg;
        $paqueteRetorno->package              = $paquete->package;
        $paqueteRetorno->id_order             = $paquete->id_order;
        $paqueteRetorno->oficina_entrega      = $paquete->oficina_entrega;

        $paqueteRetorno->numship              = $numeroRetorno['expedicion'];
        $paqueteRetorno->codUnicoBulto        = $numeroRetorno['bulto'];

        //igual ==> numcollect, KG y package
        return $paqueteRetorno;
    }

    protected function cex_generarEtiquetaAdhesivaZona1($pdf, $paquete, $remitente = false)
    {
        $pdf->SetFont('arial', '', 9);
        // set Y to start page o start new etiq (increment)
        $pdf->setY(9+($this->incH*$this->cb)+($this->offset*$this->cb));

        if ($remitente == true) {
            $pdf->MultiCell(100, 4, "\n\n\n\n", 1, 'L', 0);
        } else {
            $pdf->setXY(10, 8+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->SetFont('arial', '', 6);
            $pdf->Cell(72.7, 7, 'Rte: '.strtoupper($paquete->codigo_cliente),0, 1, 'L');

            $pdf->setXY(10, 13+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(72.7, 6.5, strtoupper($paquete->sender_name),0, 1, 'L');

            $pdf->setXY(10, 18+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(72.7, 5.5, strtoupper($paquete->sender_address),0, 1, 'L');

            $pdf->setXY(10, 23+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(72.7, 4.5, $paquete->sender_postcode." ".strtoupper($paquete->sender_city), 0, 1, 'L'); 

            $pdf->setXY(10, 28+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(72.7, 3.5, "Contacto: ".$paquete->sender_phone." ".strtoupper($paquete->sender_contact),0,1,'L');
        }

        //vertical line
        $pdf->Line(99, 9+($this->incH*$this->cb)+($this->offset*$this->cb), 99, 34+($this->incH*$this->cb)+($this->offset*$this->cb));
        //horizontal line     
        $pdf->Line(9.8, 34+($this->incH*$this->cb)+($this->offset*$this->cb), 200, 34+($this->incH*$this->cb)+($this->offset*$this->cb));
 
        return $pdf;
    }

    protected function cex_generarEtiquetaAdhesivaZona2y5($pdf, $paquete, $style)
    {

        // vertical line
        $pdf->Line(50, 65+($this->incH*$this->cb)+($this->offset*$this->cb), 50, 34+($this->incH*$this->cb)+($this->offset*$this->cb));
        
        $pdf->SetFont('arial', '', 8);
        // ship information + barcode
        $pdf->Cell(90, 4, '', 0, 1, 'L');
        $pdf->Cell(90, 4, 'REF.: '.$paquete->numcollect, 0, 1, 'L');
        $pdf->Cell(90, 6, 'PESO: '.$paquete->kg.' kg', 0, 1, 'L');
        $pdf->Cell(90, 6, 'BULTO: '.$paquete->id_bulto, 0, 1, 'L');

        if (!empty($paquete->payback_val) &&  $paquete->payback_val > 0) {
            $pdf->Cell(90, 5, 'REEMBOLSO: '.$paquete->payback_val, 0, 1, 'L');
        }else{
            $pdf->Cell(90, 5, 'REEMBOLSO: 0.0 €', 0, 1, 'L');
        }

        $pdf->Cell(90, 6, 'PORTES: PAGADO', 0, 1, 'L');

        // barcode
        $pdf->write1DBarcode($paquete->codUnicoBulto, 'C128', 50, 34+($this->incH*$this->cb)+($this->offset*$this->cb), 100, 27, 0.4, $style, 'N');

        //barcode vertical
        $pdf->StartTransform();
        $pdf->Rotate(-90);

        // Se desplaza el código de barra vertical hacia la derecha
        $longitud_address_receiver = strlen($paquete->receiver_address);

        if ($longitud_address_receiver <= '70') {
            //Codigo de barra vertical
            $pdf->write1DBarcode($paquete->codUnicoBulto, 'C128', -16, -116+($this->incH*$this->cb)+($this->offset*$this->cb), 30, 25, 0.4, $style, 'N');
        } else {
            $pdf->write1DBarcode($paquete->codUnicoBulto, 'C128', -16, -116+($this->incH*$this->cb)+($this->offset*$this->cb), 30, 25, 0.4, $style, 'N');
        }

        $pdf->StopTransform();
        // code of barcode under image
        $pdf->setXY(97, 59+($this->incH*$this->cb)+($this->offset*$this->cb));

        $pdf->Cell(10, 6, $paquete->codUnicoBulto, 0, 1, 'C');

        // horizontal line
        $pdf->Line(10, 65+($this->incH*$this->cb)+($this->offset*$this->cb), 200, 65+($this->incH*$this->cb)+($this->offset*$this->cb));
        
        return $pdf;
    }

    protected function cex_generarEtiquetaAdhesivaZona3($pdf, $paquete)
    {
        if (!empty($paquete->deliver_sat)) {
            $pdf->Image(plugin_dir_path(__DIR__)."views/img/entregasabado.png", 130, 66+($this->incH*$this->cb)+($this->offset*$this->cb), 18, 17, 'PNG');
        }

        // sender information
        $pdf->setXY(10,67+($this->incH*$this->cb)+($this->offset*$this->cb));        
        $pdf->SetFont('arial', '', 7);
        $pdf->MultiCell(140, 4, $paquete->receiver_name.
        "\nTfno: ".$paquete->receiver_phone.
        ", ".$paquete->receiver_contact, 0, 'L', 0);

        $longitud_address_receiver = strlen($paquete->receiver_address);
        $this->entrega_oficina = $paquete->oficina_entrega;

        $fecha = explode("-", $paquete->date);

        if (!empty($this->entrega_oficina)) {
            $cod_oficina = explode("#!#", $paquete->oficina_entrega);
            $cod_oficina_selecionada = $cod_oficina[0];
            $nombre_oficina         = $cod_oficina[2];
            $dir_oficina            = $cod_oficina[1];
            $codigo_postal_oficina  = $cod_oficina[3];
            $pob_oficina            = $cod_oficina[4];
        }

        $receiver_address = preg_replace("/[\r\n|\n|\r]+/", " ", $paquete->receiver_address);

        // Dependiendo de la longitud
        if ($longitud_address_receiver <= '70') {
            $pdf->SetFont('arial', '', 10);
            $pdf->setXY(10,75+($this->incH*$this->cb)+($this->offset*$this->cb));
            
            if ($paquete->oficina_entrega) {
                $pdf->MultiCell(140, 4, $dir_oficina, 0, 'L', 0);
            } else {
                $pdf->MultiCell(140, 4, strtoupper($receiver_address), 0, 'L', 0);
            }

            $pdf->setXY(10,78+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(140, 6, strtoupper($paquete->receiver_city),0,1,"L");

            $pdf->MultiCell(140,4, $fecha[2]."/".$fecha[1]."/".$fecha[0],0, 'R', 0);

            $pdf->setXY(10,85+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->SetFont('arial', '', 12);

            if ($paquete->oficina_entrega) {
                $pdf->Cell(140, 6, $codigo_postal_oficina.' '.$pob_oficina, 0, 1, 'L');
            } else {                        
                switch($paquete->receiver_country){
                    case'ES':
                    case 'España':
                    case 'Espanha':
                    case 'Spain':
                        $pdf->Cell(140, 6, $paquete->receiver_postcode."    ESPAÑA", 0, 1, 'L'); 
                        break;
                    case 'PT':
                    case 'Portugal':
                        $pdf->Cell(140, 6, $paquete->receiver_postcode."    PORTUGAL", 0, 1, 'L'); 
                        break;
                    default:
                        $pdf->Cell(140, 6, $paquete->receiver_postcode."     ". strtoupper($paquete->receiver_city), 0, 1, 'L'); 
                        break;    
                }
            }
        }else{
            $pdf->SetFont('arial', '', 10);
            if ($longitud_address_receiver > '117') {
                $paquete->receiver_address = substr($paquete->receiver_address, 0, -3);
                $paquete->receiver_address = strtoupper($paquete->receiver_address);
            }

            $pdf->SetFont('arial', '', 10);
            $pdf->setXY(10,75+($this->incH*$this->cb)+($this->offset*$this->cb));
            if ($paquete->oficina_entrega) {
                $pdf->MultiCell(140, 4, $dir_oficina, 0, 'L', 0);
            } else {
                $pdf->MultiCell(140, 4, strtoupper($paquete->receiver_address), 0, 'L', 0);
            }

            $pdf->setXY(10,78+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(140, 6, strtoupper($paquete->receiver_city), 0, 1, 'L');
            
            $pdf->MultiCell(140,4, $fecha[2]."/".$fecha[1]."/".$fecha[0],0, 'R', 0);
            
            $pdf->setXY(10,85+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->SetFont('arial', '', 12);

            if ($paquete->oficina_entrega) {
                $pdf->Cell(140, 6, $codigo_postal_oficina.' '.$pob_oficina, 0, 1, 'L');
            } else {
                switch($paquete->receiver_country){
                    case'ES':
                    case 'España':
                    case 'Espanha':
                        $pdf->Cell(140, 6, $paquete->receiver_postcode."    ESPAÑA", 0, 1, 'L');
                        break;
                    case 'PT':
                    case 'Portugal':
                        $pdf->Cell(140, 6, $paquete->receiver_postcode."    PORTUGAL", 0, 1, 'L'); 
                        break;
                    default:
                        $pdf->Cell(140, 6, $paquete->receiver_postcode."     ". strtoupper($paquete->receiver_city), 0, 1, 'L'); 
                        break;
                }
            }
        }

        // set description references
        $pdf->SetFont('arial', '', 10);
        $pdf->setXY(150, 61+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(30, 6, $paquete->desc_ref_1, 0, 1, 'L');
        $pdf->setXY(150, 81+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(30, 6, $paquete->desc_ref_2, 0, 1, 'L');

        return $pdf;
    }

    protected function cex_generarEtiquetaAdhesivaZona4($pdf, $paquete, $numeroRetorno = '')
    {        
        // logo correos + codigo + retorno
        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';

        $MXPS_CHECKUPLOADFILE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_CHECKUPLOADFILE'");

        if ($MXPS_CHECKUPLOADFILE == "true"){
            $table = $wpdb->prefix.'cex_customer_options';
            $MXPS_UPLOADFILE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_UPLOADFILE'");
            $pdf->Image($MXPS_UPLOADFILE, 150, 10+($this->incH*$this->cb)+($this->offset*$this->cb), 20, 7, substr($rutaLogo,strrpos($MXPS_UPLOADFILE,'.')+1));

        }else{
            $pdf->Image(plugin_dir_path(__DIR__)."views/img/logo-etiquetas.png", 133, 10+($this->incH*$this->cb)+($this->offset*$this->cb), 30, 11, 'PNG');
        }

        $pdf->setXY(107, 20+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 14);

        // obtener numSHIP
        $pdf->Cell(80, 8, "Env: ".$paquete->numship, 0, 1, 'C');
        $pdf->setXY(110, 27+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 8);

        if ($paquete->id_bc == 54 || $paquete->id_bc == 55) {
            $pdf->Cell(80, 3, "Envio retorno:".$numeroRetorno['expedicion'], 0, 1, 'C');
        } else {
            $pdf->Cell(80, 3, "Envio retorno:", 0, 1, 'C');
        }

        if(!empty($paquete->at_portugal)){
            $pdf->SetFont('freeserif', '', 7);
            $pdf->setXY(110, 27+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(80, 10, "Código AT: ".$paquete->at_portugal, 0, 1, 'C');
        }

        return $pdf;
    }

    protected function cex_generarEtiquetaAdhesivaZona6($pdf, $paquete)
    {

        /** DM Country ***/
        $pdf->SetFont('arial', 'B', 12);
        // set iso image
        $pdf->Image(plugin_dir_path(__DIR__)."views/img/etiqueta_AENOR.png", 168, 67+($this->incH*$this->cb)+($this->offset*$this->cb), 18, 22, 'PNG');

        // set description references
        $pdf->SetFont('arial', '', 10);
        $pdf->setXY(150, 61+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(30, 6, $paquete->desc_ref_1, 0, 1, 'L');
        $pdf->setXY(150, 81+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(30, 6, $paquete->desc_ref_2, 0, 1, 'L');

        return $pdf;
    }

    protected function cex_generarEtiquetaAdhesivaZona7y8($pdf, $paquete)
    {
        $cod_oficina_selecionada = "";
        $nombre_oficina          = "";
        $dir_oficina             = "";
        $codigo_postal_oficina   = "";
        $pob_oficina             = "";
        $longitud_address_receiver = strlen($paquete->receiver_address);
        $this->entrega_oficina = $paquete->oficina_entrega;

        if (!empty($this->entrega_oficina)) {
            $cod_oficina = explode("#!#", $paquete->oficina_entrega);
            $cod_oficina_selecionada = $cod_oficina[0];
            $nombre_oficina         = $cod_oficina[2];
            $dir_oficina            = $cod_oficina[1];
            $codigo_postal_oficina  = $cod_oficina[3];
            $pob_oficina            = $cod_oficina[4];
        }

        $pdf->setXY(10, 91+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 8);
        $length_obs= strlen($paquete->note_deliver);

        if ($length_obs<='71') {
            if ($this->entrega_oficina) {
                $pdf->Cell(120, 8, 'Observaciones.: '.$nombre_oficina.' '.$paquete->note_deliver, 0, 1, 'L');
            } else {
                $pdf->Cell(120, 8, 'Observaciones.: '.$paquete->note_deliver, 0, 1, 'L');
            }
        } else {
            $pdf->SetFont('freeserif', '', 7);

            if ($this->entrega_oficina) {
                $pdf->Cell(120, 8, 'Observaciones.: '.$nombre_oficina.' '.$paquete->note_deliver, 0, 1, 'L');
            } else {
                $pdf->Cell(120, 8, 'Observaciones.: '.$paquete->note_deliver, 0, 1, 'L');
            }
        }

        $pdf->setXY(92, 91+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', 'B', 8);
        $pdf->Cell(70, 8, $this->getShortname($paquete->id_bc_ws), 0, 1, 'R');
        
        $pdf->setXY(12, 99+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 7);
        $pdf->Cell(120, 6, "El cliente acepta las condiciones del porte, y estar informado de la posibilidad de seguro a todo riesgo por el valor de la mercancia.", 0, 1, 'L');
        
        // set lines       

        // vertical
        $pdf->Line(10, 9+($this->incH*$this->cb)+($this->offset*$this->cb), 10, 98+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(200, 9+($this->incH*$this->cb)+($this->offset*$this->cb), 200, 98+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(150, 34+($this->incH*$this->cb)+($this->offset*$this->cb), 150, 98+($this->incH*$this->cb)+($this->offset*$this->cb));

        // horizontal
        $pdf->Line(10, 9+($this->incH*$this->cb)+($this->offset*$this->cb), 200, 9+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(10, 91+($this->incH*$this->cb)+($this->offset*$this->cb), 200, 91+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(10, 98+($this->incH*$this->cb)+($this->offset*$this->cb), 200, 98+($this->incH*$this->cb)+($this->offset*$this->cb));


        return $pdf;
    }

    protected function cex_generarEtiquetaMedioFolio($numCollect, $posicion)
    {
        /*
         ************************************************************
         *  Formato de la etiqueta tipo 2 Medio Folio - 2 por paginas
         *
         ***********************************************************

        //+++++++++++++++++++++++++++++++++++++++++++++++++
        //        ZONA           +         ZONA           +
        //          1            +           2            +
        //+++++++++++++++++++++++++++++++++++++++++++++++++
        //                +             +                 +
        //     ZONA3      +     ZONA4   +     ZONA5       +
        //                +             +                 +
        //+++++++++++++++++++++++++++++++++++++++++++++++++
        //                       +                        +
        //      ZONA6            +        ZONA7           +
        //                       +                        +
        //++++++++++++++++++++++++++++++++++++++++++++++++++
        //                       +            +           +   \
        //++++++++++++++++++++++++++++++++++++++++++++++++++   }==>   El footer
                                                      /
        */
        global $wpdb;

        $pdf = new TCPDF_CEX('P', 'mm', 'A4', true, 'UTF-8', false, false); //('L','mm',array(210,115)); //new PDF();
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();

        $pdf->Image(plugin_dir_path(__DIR__).'views/img/Tijeras.png', 12, 137.5+($this->incH*$this->cb)+($this->offset*$this->cb), 180, 7, 'PNG');

        $this->position = 1;
        // generarEtiquetaMedioFolio ==>> $this->cb = $posicion - 1;
        $this->cb = $posicion - 1;//0; //counter barcodes in page
        $this->last_file = false;
        $this->per_page = 2;
        $this->incH = 130;
        $this->offset = 27;

        $table1 = $wpdb->prefix.'cex_savedships';
        $table2 = $wpdb->prefix.'cex_envios_bultos';
        $table3 = $wpdb->prefix.'cex_history';
        $paquetes = $wpdb->get_results($wpdb->prepare("SELECT *    
                FROM $table1 s 
                LEFT JOIN $table2 e
                    ON e.numcollect = s.numcollect
                LEFT JOIN $table3 h
                    ON h.numcollect = s.numcollect AND h.numShip = s.numship
                WHERE s.type = 'Envio' AND e.numcollect in ($numCollect) AND e.deleted_at is null and s.deleted_at is null",null));

        // define barcode style
        $style = array(
        'position' => '',
        'align' => 'L',
        'stretch' => true,
        'fitwidth' => false,
        'cellfitalign' => '',
        'border' => false,
        'hpadding' => 0,
        'vpadding' => 0,
        'fgcolor' => array(0,0,0),
        'bgcolor' => false, //array(255,255,255),
        'text' => false,
        'font' => 'arial',
        'fontsize' => 8,
        'stretchtext' => 4
        );

        $i = 0;

        foreach ($paquetes as $paquete) {
            if (strcmp($paquete->iso_code, 'PT') == 0) {
                $paquete->receiver_postcode=(int)$paquete->receiver_postcode;
            }  
            // cada linea es un bulto no una orden.
            // Una orden varias lineas.
            $numeroRetorno = $this->cex_generarEnvioRetorno($paquete);
            $remitente  = $this->cex_comprobarDatosRemitente();

            $pdf = $this->cex_generarEtiquetaMedioFolioZona1y2($pdf, $paquete, $numeroRetorno, $remitente);
            $pdf = $this->cex_generarEtiquetaMedioFolioZona3y4y5($pdf, $paquete, $style);
            $pdf = $this->cex_generarEtiquetaMedioFolioZona6y7($pdf, $paquete);
            $pdf = $this->cex_generarEtiquetaMedioFolioZonaFooter($pdf, $paquete);

            $i++;
            $this->cb++;

            if ($this->cb == $this->per_page) {
                $pdf->AddPage();
                $this->cb = 0;
            }

            if ($paquete->id_bc == 54 || $paquete->id_bc == 55) {
                $this->cex_generarEtiquetaRetornoMedioFolio($pdf, $paquete, $numeroRetorno, $style);
                $i++;
                $this->cb++;

                if ($this->cb == $this->per_page) {
                    $pdf->AddPage();
                    $this->cb = 0;
                }
            }
        }

        if ($this->cb == 0) {
            $pdf->deletePage($pdf->getNumPages());
        };

        return $pdf;
    }
    
    protected function cex_generarEtiquetaRetornoMedioFolio($pdf, $paquete, $numeroRetorno, $style)
    {

        // adaptar datos.
        $paquetes = $this->cex_generarDatosEtiquetaRetorno($paquete, $numeroRetorno);

        $pdf = $this->cex_generarEtiquetaMedioFolioZona1y2($pdf, $paquetes);
        $pdf = $this->cex_generarEtiquetaMedioFolioZona3y4y5($pdf, $paquetes, $style);
        $pdf = $this->cex_generarEtiquetaMedioFolioZona6y7($pdf, $paquetes);
        $pdf = $this->cex_generarEtiquetaMedioFolioZonaFooter($pdf, $paquetes);

        return $pdf;
    }

    protected function cex_generarEtiquetaMedioFolioZona1y2($pdf, $paquete, $numeroRetorno = '', $remitente = false)
    {

        $this->offset = 7;

        // horizontal line 1
        $pdf->Line(13, 13+($this->incH*$this->cb)+($this->offset*$this->cb), 197, 13+($this->incH*$this->cb)+($this->offset*$this->cb));

        if(!$this->cb == $this->per_page){ 
            $pdf->Image(plugin_dir_path(__DIR__).'views/img/Tijeras.png', 12, 137.5+($this->incH*$this->cb)+($this->offset*$this->cb), 180, 7, 'PNG');
        }

        $pdf->SetFont('arial', '', 6);

        // print sender information
        if ($remitente == true) {
            $pdf->setY(9+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->MultiCell(100, 20, "\n\n\n\n", 1, 'L', 0);
        } else {
            $pdf->setXY(14, 13+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(72.7, 7, 'Rte: '.strtoupper($paquete->codigo_cliente),0, 1, 'L');

            $pdf->setXY(14, 18+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(72.7, 6.5, strtoupper($paquete->sender_name),0, 1, 'L');

            $pdf->setXY(14, 23+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(72.7, 5.5, strtoupper($paquete->sender_address),0, 1, 'L');

            $pdf->setXY(14, 28+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(72.7, 4.5, $paquete->sender_postcode."  ".strtoupper($paquete->sender_city), 0, 1, 'L'); 

            $pdf->setXY(14, 33+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(72.7, 3.5, "Contacto: ".$paquete->sender_phone.", ".strtoupper($paquete->sender_contact),0,1,'L');
            
        }
        
        //vertical line
        $pdf->Line(103, 13+($this->incH*$this->cb)+($this->offset*$this->cb), 103, 37.7+($this->incH*$this->cb)+($this->offset*$this->cb));

        // logo correos + codigo + retorno
        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';

        $MXPS_CHECKUPLOADFILE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_CHECKUPLOADFILE'");
        $MXPS_UPLOADFILE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_UPLOADFILE'");

        if ($MXPS_CHECKUPLOADFILE == "true"){
            $pdf->Image($MXPS_UPLOADFILE, 142, 14+($this->incH*$this->cb)+($this->offset*$this->cb), 20, 11,substr($rutaLogo,strrpos($MXPS_UPLOADFILE,'.')+1));
        }else{
            $pdf->Image(plugin_dir_path(__DIR__)."views/img/logo-etiquetas.png", 140, 14+($this->incH*$this->cb)+($this->offset*$this->cb), 33, 11, 'PNG');
        }

        $pdf->setXY(111, 24+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 13);
        $pdf->Cell(80, 10, "Env: ".$paquete->numship, 0, 1, 'C');

        $pdf->setXY(111, 33+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 6);

        if ($paquete->id_bc == 54 || $paquete->id_bc == 55) {
            $pdf->Cell(80, 3, "Envio retorno:".$numeroRetorno['expedicion'], 0, 1, 'C');
        } else {
            $pdf->Cell(80, 3, "Envio retorno:", 0, 1, 'C');
        }

        // horizontal line
        $pdf->Line(13, 37.7+($this->incH*$this->cb)+($this->offset*$this->cb), 197, 37.7+($this->incH*$this->cb)+($this->offset*$this->cb));

        return $pdf;
    }

    protected function cex_generarEtiquetaMedioFolioZona3y4y5($pdf, $paquete, $style)
    {
        $fechaExplode = explode( '-', $paquete->fecha);
        $fechaDia = $fechaExplode[2];
        $fechaMes = $fechaExplode[1];
        $fechaYear = $fechaExplode[0];

        
        $pdf->SetFont('arial', '', 8);    

        // ship information + barcode
        $pdf->setXY(14, 45+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(90, 4, 'REF: '.$paquete->numcollect, 0, 1, 'L');

        $pdf->setXY(14, 49+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(90, 6, $fechaDia.'/'.$fechaMes.'/'.$fechaYear, 0, 1, 'L');

        $pdf->setXY(14, 53.5+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(90, 6, 'PESO: '.$paquete->kg.' kg', 0, 1, 'L');

        $pdf->setXY(14, 58+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(90, 6, 'BULTO: '.$paquete->id_bulto, 0, 1, 'L');
        $pdf->Cell(90, 2, '', 0, 1, 'L'); //blank
        
        $pdf->SetFont('arial', '', 10);

        // barcode
        $pdf->write1DBarcode($paquete->codUnicoBulto, 'C128', 65, 41+($this->incH*$this->cb)+($this->offset*$this->cb), 77, 37, 0.4, $style, 'N');

        // barcode vertical
        $pdf->StartTransform();
        $pdf->Rotate(-90);
        $pdf->write1DBarcode($paquete->codUnicoBulto, 'C128', -27, -96+($this->incH*$this->cb)+($this->offset*$this->cb), 38, 21, 0.4, $style, 'N');
        $pdf->StopTransform();

        // code of barcode under image
        $pdf->setXY(44, 77+($this->incH*$this->cb)+($this->offset*$this->cb));

        if (count($this->bultos) == 1) {
            $pdf->Cell(120, 6, $paquete->codUnicoBulto, 0, 1, 'C');
        } else {
            $pdf->Cell(120, 6, $paquete->codUnicoBulto, 0, 1, 'C');
        }

        // horizontal line 3
        $pdf->Line(13, 83+($this->incH*$this->cb)+($this->offset*$this->cb), 197, 83+($this->incH*$this->cb)+($this->offset*$this->cb));
        
        // set lines vertical
        $pdf->Line(13, 13+($this->incH*$this->cb)+($this->offset*$this->cb), 13, 125+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(57, 37.7+($this->incH*$this->cb)+($this->offset*$this->cb), 57, 83+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(150, 37.7+($this->incH*$this->cb)+($this->offset*$this->cb), 150, 83+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(110, 83+($this->incH*$this->cb)+($this->offset*$this->cb), 110, 125+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(197, 13+($this->incH*$this->cb)+($this->offset*$this->cb), 197, 125+($this->incH*$this->cb)+($this->offset*$this->cb));

        return $pdf;
    }

    protected function cex_generarEtiquetaMedioFolioZona6y7($pdf, $paquete)
    {
        if (!empty($paquete->deliver_sat)) {
            $pdf->Image(plugin_dir_path(__DIR__)."views/img/entregasabado.png", 94, 94+($this->incH*$this->cb)+($this->offset*$this->cb), 15, 20, 'PNG');
        }

        // sender information
        $pdf->setXY(14, 82+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 10);
        $pdf->Cell(72.7, 6.5, strtoupper($paquete->receiver_name),0, 1, 'L');
        $pdf->setXY(14, 88+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 8);
        $pdf->Cell(72.7, 4.5, "Tfno: ".$paquete->receiver_phone.$this->phone_receiver_local.
        ",".strtoupper($paquete->receiver_contact), 0, 1, 'L');

        $this->entrega_oficina = $paquete->oficina_entrega;

        if (!empty($this->entrega_oficina)) {
            $cod_oficina = explode("#!#", $paquete->oficina_entrega);
            $cod_oficina_selecionada = $cod_oficina[0];
            $nombre_oficina         = $cod_oficina[2];
            $dir_oficina            = $cod_oficina[1];
            $codigo_postal_oficina  = $cod_oficina[3];
            $pob_oficina            = $cod_oficina[4];
        }

        $pdf->setXY(14, 94+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 6);

        if ($this->entrega_oficina) {
            $pdf->MultiCell(100, 4, $dir_oficina, 0, 'L', 0);
        } else {
            $pdf->MultiCell(100, 4, strtoupper($paquete->receiver_address), 0, 'L', 0);
        }

        $pdf->setXY(14, 99+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 8);
        $pdf->Cell(72.7, 4.5, strtoupper($paquete->receiver_address),0, 1, 'L'); 

        $pdf->setXY(14, 105+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 14);

        if ($this->entrega_oficina) {
            $pdf->Cell(100, 10, $codigo_postal_oficina.' '.$pob_oficina, 0, 1, 'L');
        } else {
            switch($paquete->receiver_country){
                case'ES':
                case'España':
                    $pdf->Cell(72.7, 4.5, $paquete->receiver_postcode."   ESPAÑA", 0, 1, 'L'); 
                    break;
                case 'PT':
                case'Portugal':
                    $pdf->Cell(72.7, 4.5, $paquete->receiver_postcode."   PORTUGAL", 0, 1, 'L'); 
                    break;
                default:
                    $pdf->Cell(72.7, 4.5, $paquete->receiver_postcode."   ".strtoupper($paquete->receiver_city), 0, 1, 'L');
                    break;    
            }
        }

        $pdf->Image(plugin_dir_path(__DIR__)."views/img/etiqueta_AENOR.png",174, 94+($this->incH*$this->cb)+($this->offset*$this->cb), 15, 19, 'PNG');

        // set ship name and description references
        $pdf->SetFont('arial', '', 13);
        $pdf->setXY(111, 84+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(70, 12, $this->getShortname($paquete->id_bc), 0, 1, 'L');

        $pdf->setXY(111, 95+($this->incH*$this->cb)+($this->offset*$this->cb));

        if (!empty($paquete->payback_val) &&  $paquete->payback_val > 0) {
            $pdf->Cell(90, 5, 'Reembolso: '.$paquete->payback_val, 0, 1, 'L');   //contrareembolso
        }else{
            $pdf->Cell(90, 5, 'REEMBOLSO: 0.0 €', 0, 1, 'L');   //contrareembolso
        }

        //tipos portes
        $pdf->setXY(111, 101+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Cell(110, 5, 'PORTES: PAGADO', 0, 1, 'L');

        if(!empty($paquete->at_portugal)){
            $pdf->SetFont('arial', '', 8);
            $pdf->setXY(111, 101+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->Cell(110, 23, "Código AT: ".$paquete->at_portugal, 0, 1, 'L');
        }

        return $pdf;
    }

    protected function cex_generarEtiquetaMedioFolioZonaFooter($pdf, $paquete)
    {
        $cod_oficina_selecionada = "";
        $nombre_oficina          = "";
        $dir_oficina             = "";
        $codigo_postal_oficina   = "";
        $pob_oficina             = "";
        $longitud_address_receiver =strlen($paquete->receiver_address);
        $this->entrega_oficina = $paquete->oficina_entrega;

        if (!empty($this->entrega_oficina)) {
            $cod_oficina = explode("#!#", $paquete->oficina_entrega);
            $cod_oficina_selecionada = $cod_oficina[0];
            $nombre_oficina         = $cod_oficina[2];
            $dir_oficina            = $cod_oficina[1];
            $codigo_postal_oficina  = $cod_oficina[3];
            $pob_oficina            = $cod_oficina[4];
        }

        // set footer information
        $pdf->setXY(14,116+($this->incH*$this->cb)+($this->offset*$this->cb));

        $length_obs= strlen($paquete->note_deliver);

        $pdf->SetFont('arial', '', 6);


        if ($length_obs <='62') {
            if ($this->entrega_oficina) {
                $pdf->MultiCell(100, 10, 'Observaciones: '.$nombre_oficina.' '.$paquete->note_deliver,0,'L', 0);
            } else {
                $pdf->MultiCell(100, 10, 'Observaciones: '.$paquete->note_deliver, 0,'L', 0);
            }
        } else {
            $pdf->SetFont('freeserif', '', 6.5);

            if ($this->entrega_oficina) {
                $pdf->MultiCell(100, 10, 'Observaciones: '.$nombre_oficina.' '.$paquete->note_deliver, 0,'L', 0);
            } else {
                $pdf->MultiCell(100, 10, 'Observaciones: '.$paquete->note_deliver, 0,'L', 0);
            }
        }

        $pdf->setXY(111, 116+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->MultiCell(100, 10, "Fecha: ".$paquete->date." "."\n". "Conductor:", 0, 'L', 0);
        
        $pdf->setXY(161, 116+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->MultiCell(100, 10, "Firma y Fecha:\n ", 0, 'L', 0);

        // *** DM ****
        $pdf->setXY(14, 123+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 5);
        $pdf->Cell(120, 10, "El cliente acepta las condiciones del porte, y estar informado de la posibilidad de seguro a todo riesgo por el valor de la mercancia.", 0, 1, 'L');

        // horizontal line 4y5
        $pdf->Line(13, 115+($this->incH*$this->cb)+($this->offset*$this->cb), 197, 115+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(13, 125+($this->incH*$this->cb)+($this->offset*$this->cb), 197, 125+($this->incH*$this->cb)+($this->offset*$this->cb));
        
        // set lines vertical
        $pdf->Line(160, 115+($this->incH*$this->cb)+($this->offset*$this->cb), 160, 125+($this->incH*$this->cb)+($this->offset*$this->cb));

        return $pdf;
    }

    protected function cex_generarEtiquetaTermica($numCollect)
    {
        /*************************************************************
     *  Formato de la etiqueta tipo 3 Termica - 1 por pág.
     *
     ************************************************************/
        //++++++++++++++++++++++++++++++
        //    ZONA1                    +        ZONA2
        //                             +
        //+++++++++++++++++++++++++++++++++++++++++++++++++
        //                             +     +     +      +   \
        //                             ++++++++++++++++++++    > ZONA 4
        //      ZONA3                  +     +     +      +   /
        //                             ++++++++++++++++++++
        //                             +                      \
        //                             +                       > ZONA 5
        //++++++++++++++++++++++++++++++                      /
        //
        //                                                    \
        //                                                     > ZONA6
        //                                                    /
        //
        //++++++++++++++++++++++++++++++++++++++++++++++++++
        global $wpdb;

        $pdf = new TCPDF_CEX('L', 'mm', array(150,100), true, 'UTF-8', false, false);
        $pdf->SetMargins(2, 2, 2);
        $pdf->SetPrintHeader(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();

        $table1 = $wpdb->prefix.'cex_savedships';
        $table2 = $wpdb->prefix.'cex_envios_bultos';
        $table3 = $wpdb->prefix.'cex_history';
        $paquetes = $wpdb->get_results($wpdb->prepare("SELECT *    
                FROM $table1 s 
                LEFT JOIN $table2 e
                    ON e.numcollect = s.numcollect
                LEFT JOIN $table3 h
                    ON h.numcollect = s.numcollect AND h.numShip = s.numship
                WHERE s.type = 'Envio' AND e.numcollect in ($numCollect) AND e.deleted_at is null and s.deleted_at is null",null));

        // define barcode style
        $style = array(
            'position' => '',
            'align' => 'L',
            'stretch' => true,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 0,
            'vpadding' => 0,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'text' => false,
            'font' => 'arial',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        $style2 = array(
            'border' => false,
            'vpadding' => 0,
            'hpadding' => 0,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );

        $i = 0;

        foreach ($paquetes as $paquete) {
            if (strcmp($paquete->iso_code, 'PT') == 0) {
                $paquete->receiver_postcode=(int)$paquete->receiver_postcode;
            }  
            $this->position = 1;
            //generarEtiquetaTermica  ==>> $this->cb = $this->position - 1;
            $this->cb = $this->position - 1; // counter barcodes in page
            $this->last_file = false;
            $this->per_page = 1;
            $this->incH = 130;
            $this->offset = 27;
            $pdf->SetFont('freeserif', '', 9);
            $pdf->SetMargins(2, 2, 2);
            $pdf->SetPrintHeader(false);
            $pdf->SetFooterMargin(0);
            $pdf->SetPrintFooter(false);

            $numeroRetorno = $this->cex_generarEnvioRetorno($paquete);
            $remitente  = $this->cex_comprobarDatosRemitente();
            $pdf =  $this->cex_generarEtiquetaTermicaZona1y2($pdf, $paquete, $numeroRetorno, $remitente);
            $pdf =  $this->cex_generarEtiquetaTermicaZona3($pdf, $paquete);
            $pdf =  $this->cex_generarEtiquetaTermicaZona4($pdf, $paquete);
            $pdf =  $this->cex_generarEtiquetaTermicaZona5($pdf, $paquete);
            $pdf =  $this->cex_generarEtiquetaTermicaZona6($pdf, $paquete, $style, $style2);

            $i++;
            $this->cb++;

            if ($this->cb == $this->per_page) {
                $pdf->AddPage();
                $this->cb = 0;
            }

            if ($paquete->id_bc == 54 || $paquete->id_bc == 55) {
                $this->cex_generarEtiquetaRetornoTermica($pdf, $paquete, $style, $style2, $numeroRetorno);
                $i++;
                $this->cb++;

                if ($this->cb == $this->per_page) {
                    $pdf->AddPage();
                    $this->cb = 0;
                }
            }
        }
        $pdf->deletePage($pdf->getNumPages());

        return $pdf;
    }

    protected function cex_generarEtiquetaRetornoTermica($pdf, $paquete, $style, $style2, $numeroRetorno = '')
    {
        // adaptar datos.
        $paquetes = $this->cex_generarDatosEtiquetaRetorno($paquete, $numeroRetorno);

        $pdf = $this->cex_generarEtiquetaTermicaZona1y2($pdf, $paquetes);
        $pdf = $this->cex_generarEtiquetaTermicaZona3($pdf, $paquetes, '');
        $pdf = $this->cex_generarEtiquetaTermicaZona4($pdf, $paquetes);
        $pdf = $this->cex_generarEtiquetaTermicaZona5($pdf, $paquetes);
        $pdf = $this->cex_generarEtiquetaTermicaZona6($pdf, $paquetes, $style, $style2);

        return $pdf;
    }

    protected function cex_generarEtiquetaTermicaZona1y2($pdf, $paquete, $numeroRetorno = '', $remitente = false)
    {

        $pdf->Line(0.2, 0.2+($this->incH*$this->cb)+($this->offset*$this->cb), 150, 0.2+($this->incH*$this->cb)+($this->offset*$this->cb));

        // set Y to start page o start new etiq (increment)
        $pdf->setXY(2, 2+($this->incH*$this->cb)+($this->offset*$this->cb));
       
        if ($remitente == true) {
            $pdf->Cell(72.7, 4.5, " ", 1, 'L');
        } else {
            $pdf->SetFont('arial', '', 6);
            $pdf->Cell(72.7, 4.5, strtoupper($paquete->codigo_cliente).", ".strtoupper($paquete->sender_name),0, 1, 'L');
            $pdf->SetFont('courier', '', 6);
            $pdf->Cell(72.7, 5, strtoupper($paquete->sender_address),0, 1, 'L');
            $pdf->SetFont('arial', '', 6);
            $pdf->Cell(72.7, 3, $paquete->sender_postcode." ".strtoupper($paquete->sender_city), 0, 1, 'L');                         
            $pdf->setXY(2, 11.5+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->SetFont('arial', '', 6);
            $pdf->Cell(69, 2, "TLFN: ".$paquete->sender_phone,0,1,'R');
        }
 
        $pdf->setXY(71, 2+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', 'B', 14);
        $pdf->Cell(63, 4, "EXP: ".$paquete->numship, 0, 1, 'C');

        $pdf->setXY(50,7+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 6);

        if ($paquete->id_bc == 54 || $paquete->id_bc == 55) {
            $pdf->Cell(63, 9, "Envio retorno:".$numeroRetorno['expedicion'], 0, 1, 'C');
        } else {
            $pdf->Cell(63, 9, "Envio retorno:", 0, 1, 'C');
        }

        if(!empty($paquete->at_portugal)){
            $pdf->setXY(74.5, 10+($this->incH*$this->cb)+($this->offset*$this->cb));
            $pdf->SetFont('freeserif', '', 6);
            $pdf->Cell(63, 9, "Código AT: ".$paquete->at_portugal, 0, 1, 'L');
        }

        // horizontal line
        $pdf->Line(0.2, 17+($this->incH*$this->cb)+($this->offset*$this->cb), 150, 17+($this->incH*$this->cb)+($this->offset*$this->cb));
        
        return $pdf;
    }

    protected function cex_generarEtiquetaTermicaZona3($pdf, $paquete)
    {
        $cod_oficina_selecionada = "";
        $nombre_oficina          = "";
        $dir_oficina             = "";
        $codigo_postal_oficina   = "";
        $pob_oficina             = "";
        $longitud_address_receiver = strlen($paquete->receiver_address);
        $this->entrega_oficina = $paquete->oficina_entrega;
    
        if (!empty($this->entrega_oficina)) {
            $cod_oficina = explode("#!#", $paquete->oficina_entrega);
            $cod_oficina_selecionada = $cod_oficina[0];
            $nombre_oficina         = $cod_oficina[2];
            $dir_oficina            = $cod_oficina[1];
            $codigo_postal_oficina  = $cod_oficina[3];
            $pob_oficina            = $cod_oficina[4];
        }
    
        // sender information
        $codPostal = substr($paquete->receiver_postcode, 0, -2); 
        $codPostalOfi = substr($codigo_postal_oficina, 0, -2);
        
        // sender information
        $pdf->setXY(2, 18+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('courier', '', 10);
        $pdf->Cell(69, 4, strtoupper($paquete->receiver_name), 0, 1, 'L');
        
        $pdf->setXY(2, 23+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('courier', '', 8);
        if ($this->entrega_oficina) {
            $pdf->Cell(69, 5, strtoupper($dir_oficina),0, 1, 'L');
        } else {
            $pdf->Cell(69, 5, strtoupper($paquete->receiver_address),0, 1, 'L');
        }
               
        $pdf->SetFont('arial', 'B', 26);
        $pdf->setXY(2, 27+($this->incH*$this->cb)+($this->offset*$this->cb));
        if ($this->entrega_oficina) {
            $pdf->Cell(69, 6, $codigo_postal_oficina, 0, 1, 'L');
        } else {
            $pdf->Cell(69, 6, $paquete->receiver_postcode,0, 1, 'L');
        }

        $pdf->setXY(2, 36+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('courier', '', 10);   
        if ($this->entrega_oficina) {
            $pdf->Cell(69, 6.5, $codPostalOfi.'-'.strtoupper($pob_oficina), 0, 1, 'L');
        } else {
            $pdf->Cell(69, 6.5, $codPostal.'-'.strtoupper($paquete->receiver_city), 0, 1, 'L');
        }

        $pdf->setXY(2,42+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 12);
        if ($this->entrega_oficina) {
            $pdf->MultiCell(58, 7, $pob_oficina, 0, 'L', 0);
        } else {
            $pdf->MultiCell(58, 7, strtoupper($paquete->receiver_city), 0, 'L', 0);
        }

        
        if (!empty($paquete->deliver_sat)) {
            $pdf->Image(plugin_dir_path(__DIR__)."views/img/entregasabado.png", 55, 25+($this->incH*$this->cb)+($this->offset*$this->cb), 15, 13, 'PNG');
        }
        
        $pdf->setXY(2,47+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('courier', '', 7);
        $pdf->Cell(69, 5, "ATT: ".$paquete->receiver_phone.$this->phone_receiver_local.', '.strtoupper($paquete->receiver_contact), 0, 1, 'L');
            
        $pdf->setXY(2,51+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('courier', '', 7);
        if ($this->entrega_oficina) {
            $pdf->MultiCell(69, 6, 'OBS: '.strtoupper($nombre_oficina).'  '.strtoupper($paquete->note_deliver), 0, 'L', 0);
        } else {
            $pdf->MultiCell(69, 6, 'OBS: '.strtoupper($paquete->note_deliver), 0, 'L', 0);
        }
        
        // horizontal line
        $pdf->Line(0.2, 57+($this->incH*$this->cb)+($this->offset*$this->cb), 73, 57+($this->incH*$this->cb)+($this->offset*$this->cb));
        // vertical line
        $pdf->Line(73, 0.2+($this->incH*$this->cb)+($this->offset*$this->cb), 73, 57+($this->incH*$this->cb)+($this->offset*$this->cb));

    
        return $pdf;
    }

    protected function cex_generarEtiquetaTermicaZona4($pdf, $paquete)
    {
        // horizontal lines
        $pdf->Line(74, 18+($this->incH*$this->cb)+($this->offset*$this->cb), 147, 18+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(74, 26+($this->incH*$this->cb)+($this->offset*$this->cb), 147, 26+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(74, 35+($this->incH*$this->cb)+($this->offset*$this->cb), 147, 35+($this->incH*$this->cb)+($this->offset*$this->cb));

        // vertical lines
        $pdf->Line(74, 18+($this->incH*$this->cb)+($this->offset*$this->cb), 74, 35+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(98, 18+($this->incH*$this->cb)+($this->offset*$this->cb), 98, 35+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->Line(120, 18+($this->incH*$this->cb)+($this->offset*$this->cb), 120, 35+($this->incH*$this->cb)+($this->offset*$this->cb));

        $pdf->setXY(75, 19+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 6);  
        
        // contrareembolso
        if (!empty($paquete->payback_val) &&  $paquete->payback_val > 0) {
            $pdf->Cell(23, 3, "REEMBOLSO:", 0, 1, 'L');
            $pdf->SetFont('arial', 'B', 7);
            $pdf->SetX(75);
            $pdf->Cell(23, 3, $paquete->payback_val, 0, 1, 'L');
        }else{
            $pdf->Cell(23, 3, "REEMBOLSO:", 0, 1, 'L');
            $pdf->SetFont('arial', 'B', 7);
            $pdf->SetX(75);
            $pdf->Cell(23, 3, '0.0 €', 0, 1, 'L');
        }
        // bultos
        $pdf->setXY(101, 19+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 6);
        $pdf->Cell(23, 3, 'BULTO ', 0, 1, 'L');
        $pdf->SetX(101);
        $pdf->SetFont('arial', 'B', 7);
        $pdf->Cell(23, 3, $paquete->id_bulto, 0, 1, 'L');
        // fecha
        $pdf->setXY(122, 19+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 6);
        $pdf->Cell(23, 3, 'FECHA: ', 0, 1, 'L');
        $pdf->SetX(122);
        $pdf->SetFont('arial', 'B', 7);
        // ------ DM -------
        $pdf->Cell(23, 3, $paquete->date, 0, 1, 'L');
        //tipos portes
        $pdf->setXY(75, 27+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 6);
        $pdf->Cell(23, 3, 'TIPO PORTES: ', 0, 1, 'L');
        $pdf->SetX(75);
        $pdf->SetFont('arial', 'B', 7);
        $pdf->Cell(23, 3, 'PAGADO', 0, 1, 'L');
        // peso
        $peso = trim($paquete->kg, '.');
        $pdf->setXY(101, 27+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 6);
        $pdf->Cell(23, 3, 'PESO: ', 0, 1, 'L');
        $pdf->SetX(101);
        $pdf->SetFont('arial', 'B', 7);
        $pdf->Cell(23, 3, $peso.' Kgs', 0, 1, 'L');
        
        //vertical line
        $pdf->Line(147, 18+($this->incH*$this->cb)+($this->offset*$this->cb), 147, 35+($this->incH*$this->cb)+($this->offset*$this->cb));

        return $pdf;
    }

    protected function cex_generarEtiquetaTermicaZona5($pdf, $paquete)
    {
        // ref + bulto cod
        // ship information + barcode
        $pdf->setXY(75, 37+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 9);
        $pdf->Cell(70, 2, 'REF: '.$paquete->numcollect, 0, 1, 'L');

        // code of barcode under image
        $pdf->SetXY(75, 41+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 9);

        if ($paquete->package == 1) {
            $pdf->Cell(70, 6, "COD. BULTO: ".$paquete->codUnicoBulto, 0, 1, 'L');
        } else {
            $pdf->Cell(70, 6, "COD. BULTO: ".$paquete->codUnicoBulto, 0, 1, 'L');
        }

        $pdf->setXY(75, 48+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', 'B', 14);

        $pdf->MultiCell(65, 12, strtoupper($this->getShortname($paquete->id_bc_ws)), 0, 'L', 0);

        $products_name =  $this->cex_retornarNombresProductos($paquete->id_order);

        $pdf->setXY(75, 55+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('arial', '', 8);
        // // Nombre de Productos segun la orden
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->MultiCell(45, 45, $products_name, 0, 'L', false, 8, 107, 50, true, 0, false, true, 0, "L", true);
        $pdf->SetAutoPageBreak(true, 0);
        
        return $pdf;
    }

    protected function cex_generarEtiquetaTermicaZona6($pdf, $paquete, $style, $style2)
    {
        $i=0;
        // create variable content pdf code (417) termic tag
        $content417 = date("d-m-Y")."\n".
            "codigo remitente?"."\n".
            $paquete->sender_name."\n".
            $paquete->sender_address."\n".
            $paquete->sender_postcode."\n".
            $paquete->sender_city."\n".
            $paquete->sender_phone."\n".
            $paquete->receiver_name."\n".
            $paquete->receiver_address."\n".
            $paquete->receiver_postcode."\n".
            $paquete->receiver_city."\n".
            $paquete->receiver_phone."\n".
            $paquete->receiver_contact."\n".
            $paquete->id_ship."\n".
            "codigo barras std?"."\n".
            "delegacion destino?"."\n".
            $paquete->mode_ship_name_ws."\n".
            $paquete->numship."\n".
            $paquete->kg."\n"
            .($i+1)."\n".
            $paquete->package."\n".
            "P"."\n".
            $paquete->payback_val."\n".
            $paquete->note_collect."\n".
            $paquete->note_deliver;


        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';

        $MXPS_CHECKUPLOADFILE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_CHECKUPLOADFILE'");
        $MXPS_UPLOADFILE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_UPLOADFILE'");
        $MXPS_NODATAPROTECTION = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_NODATAPROTECTION'");
        $MXPS_DATAPROTECTIONVALUE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_DATAPROTECTIONVALUE'");

        $extension = strtoupper(explode(".", $MXPS_UPLOADFILE));

        if ($MXPS_CHECKUPLOADFILE == "true"){
            if($MXPS_NODATAPROTECTION == 'false' || ($MXPS_NODATAPROTECTION == 'true' && $MXPS_DATAPROTECTIONVALUE == '3')){
                $pdf->Line(73, 57+($this->incH*$this->cb)+($this->offset*$this->cb), 150, 57+($this->incH*$this->cb)+($this->offset*$this->cb));
                $pdf->Image($MXPS_UPLOADFILE, 100, 61+($this->incH*$this->cb)+($this->offset*$this->cb), 20, 8, $extension);
            }else{
                $pdf->Image($MXPS_UPLOADFILE, 100, 61+($this->incH*$this->cb)+($this->offset*$this->cb), 20, 8, $extension);
            }
        }else{
            if($MXPS_NODATAPROTECTION == 'false' || ($MXPS_NODATAPROTECTION == 'true' && $MXPS_DATAPROTECTIONVALUE == '3')){
                $pdf->Line(73, 57+($this->incH*$this->cb)+($this->offset*$this->cb), 150, 57+($this->incH*$this->cb)+($this->offset*$this->cb));
                $pdf->StartTransform();
                $pdf->Rotate(-90);
                $pdf->Image(plugin_dir_path(__DIR__)."views/img/logo-etiquetas.png", -30, -10+($this->incH*$this->cb)+($this->offset*$this->cb), 20, 8, 'PNG');
                $pdf->StopTransform();
            }else{
                $pdf->StartTransform();
                $pdf->Rotate(-90);
                $pdf->Image(plugin_dir_path(__DIR__)."views/img/logo-etiquetas.png", -30, -10+($this->incH*$this->cb)+($this->offset*$this->cb), 20, 8, 'PNG');
                $pdf->StopTransform();
            }
        }

        /***************************************************************************    ********
        * barcode vertical , de momento se encuentra deshabilitado por la lógica   del módulo
        * ya que no se puede hacer un grabado offline
        ***************************************************************************    ********/
        //CODIGO DE BARRA ENVIO
        $pdf->SetXY(5, 59+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->write1DBarcode($paquete->codUnicoBulto, 'C128', '', 63, 90, 20, 0.4, $style, 'B');
        $pdf->setXY(4, 70+($this->incH*$this->cb)+($this->offset*$this->cb));
        $pdf->SetFont('freeserif', 'B', 14);

        return $pdf;
    }

    public function cex_generarResumenPdf($numCollect, $fecha_resumen)
    {
        global $wpdb;

        $numCollect = $this->cex_comprobarUnaVariasEtiquetas($numCollect);
        $table1 = $wpdb->prefix.'cex_savedships';
        $table2 = $wpdb->prefix.'cex_history';

        $sql    =  "SELECT * 
                    FROM $table1 s 
                    LEFT JOIN $table2 h
                        ON h.numcollect = s.numcollect AND s.numship = h.numship 
                    WHERE s.type = 'Envio' 
                    AND s.status = 'Grabado'
                    AND s.deleted_at IS NULL 
                    AND s.numcollect in ($numCollect)";
                    
        $paquetes = $wpdb->get_results($sql);

        $pdf = new TCPDF_CEX();
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage('L');
        // logo correos
        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';

        $MXPS_CHECKUPLOADFILE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_CHECKUPLOADFILE'");

        if ($MXPS_CHECKUPLOADFILE == "true"){
            $table = $wpdb->prefix.'cex_customer_options';

            $MXPS_UPLOADFILE = $wpdb->get_var("SELECT valor FROM $table WHERE clave = 'MXPS_UPLOADFILE'");

            $pdf->Image($MXPS_UPLOADFILE, 190, 10, 20, 7, substr($rutaLogo,strrpos($MXPS_UPLOADFILE,'.')+1));
        }else{
            $pdf->Image(plugin_dir_path(__DIR__)."views/img/logo-etiquetas.png", 190, 10, 20, 7, 'PNG');
        }

        $pdf->SetFont('arial', '', 18);
        $pdf->SetX(50);
        $pdf->Cell(180, 8, "LISTA DE ENVIOS ".$fecha_resumen, 0, 1, 'C');
        $pdf->SetFont('arial', 'B', 12);
        $pdf->Cell(180, 8, '', 0, 1, 'L');
        $pdf->SetFont('arial', '', 13);
        $contador;
        $array_data = array();
        $array_data_totales = array();

        if ($paquetes) {
            $contador=0;
        }

        $bultos_totales=0;
        $kilos_totales=0;
        $payback_val_totales=0;
        $insured_value_total=0;
        foreach ($paquetes as $paquete) {
            $idship           = $paquete->numship;
            $num_order        = $paquete->numcollect;
            $bultos           = $paquete->package;
            $kilos            = $paquete->kg;
            $payback_val      = number_format($paquete->payback_val, 2);
            $insured_value    = number_format($paquete->insured_value, 2);
            $receiver_contact = $paquete->receiver_contact;
            $receiver_phone   = $paquete->receiver_phone;
            $mode_ship_name   = $paquete->mode_ship_name_ws;

            $receiver_address = $paquete->receiver_address;
            $array_data[]     = array(
                'idship'            => $paquete->numship,
                'num_order'         => $paquete->numcollect,
                'bultos'            => $paquete->package,
                'kilos'             => $kilos,
                'payback_val'       => number_format($paquete->payback_val, 2),
                'insured_value'     => number_format($paquete->insured_value, 2),
                'receiver_contact'  => $paquete->receiver_contact,
                'receiver_phone'    => $paquete->receiver_phone,
                'mode_ship_name'    => $this->getShortname($paquete->id_bc_ws),
                'receiver_address'  => $paquete->receiver_address,
                'receiver_country'  => $paquete->receiver_country);
            
            //Totales
            $contador++;
            $bultos_totales       += $bultos;
            $kilos_totales        += $kilos;
            $payback_val_totales  += $payback_val;
            $insured_value_total  += $insured_value;
        }
        $array_totales[] = array(
            'contador'              => $contador,
            'bultos_totales'        => $bultos_totales,
            'kilos_totales'         => $kilos_totales,
            'payback_val_totales'   => $payback_val_totales,
            'insured_value_total'   => $insured_value_total);
        
        $table_orders = $this->cex_generarHtml($array_data, $array_totales);
        $pdf->writeHTML($table_orders, true, false, false, false, '');

        return $pdf;
    }

    protected function cex_generarHtml($array_data, $array_totales)
    {
        $codigo='';
        $a='';
        $b='';
        $c='';
        $d='';
        $e='';

        $a='<table border="1" cellpadding="2" cellspacing="2" align="center">
        <tr nobr="true" style="background-color:#666666;color:#fff;">
            <td WIDTH="16%">nº Envío</td>
            <td WIDTH="8%">nº Pedido</td>
            <td WIDTH="6%">Bultos</td>
            <td WIDTH="5%">Kilos</td>
            <td WIDTH="10%">Reembolso</td>
            <td WIDTH="11%">V.Asegurado</td>
            <td WIDTH="7%">Nombre Dest.</td>
            <td WIDTH="10%">Teléfono Dest.</td>
            <td WIDTH="8%">Producto de envío</td>
            <td WIDTH="12%">Dirección de entrega</td>
            <td WIDTH="7%">Pais Dest.</td>
        </tr>';

        foreach ($array_data as $array_d) {
            $b.="<tr nobr='true'>".
                "<td>".$array_d['idship']."</td>".
                "<td>".$array_d['num_order']."</td>".
                "<td>".$array_d['bultos']."</td>".
                "<td>".$array_d['kilos']."</td>".
                "<td>".$array_d['payback_val']." &euro;</td>".
                "<td>".$array_d['insured_value']." &euro;</td> ".
                "<td>".$array_d['receiver_contact']."</td>".
                "<td>".$array_d['receiver_phone']."</td>".
                "<td>".$array_d['mode_ship_name']."</td>".
                "<td>".$array_d['receiver_address']."</td>".
                "<td>".$array_d['receiver_country']."</td>".
            "</tr>";
        }

        $c='</table>
        <hr></br>
        <table border="1" cellpadding="2" cellspacing="2" align="center">
        <tr nobr="true" style="background-color:#666666;color:#fff;">
        <td colspan="5"><h1>Totales</h1></td>
        </tr>
        <tr>
        <td>Expediciones</td>
        <td>Bultos</td>
        <td>Kilos</td>
        <td>Reembolso</td>
        <td>V.Asegurado</td>
        </tr>
        <tr>';

        foreach ($array_totales as $array_t) {
            $d.="<td>".$array_t['contador']."</td>

            <td>".$array_t['bultos_totales']."</td>

            <td>".$array_t['kilos_totales']."</td>

            <td>".$array_t['payback_val_totales']." &euro;</td>

            <td>".$array_t['insured_value_total']." &euro;</td>";
        }

        $e="</tr>
        </table>";

        $codigo=$a.$b.$c.$d.$e;
        
        return $codigo;
    }

    protected function getShortname($id_bc_ws)
    {
        global $wpdb;

        $tabla = $wpdb->prefix.'cex_savedmodeships';
        $sql = "SELECT distinct short_name FROM $tabla WHERE id_bc = ".(int)$id_bc_ws."";

        $result = $wpdb->get_var($sql);
        
        return $result;
    }

}?>
