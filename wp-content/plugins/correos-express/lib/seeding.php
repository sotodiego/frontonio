<?php
function cex_seeding_saved_modeships($wpdb)
{
    $nombreTabla = $wpdb->prefix.'savedmodeships';

    if (comprobar_ejecucion_migracion($wpdb, 'seeding - '.$nombreTabla)) {
        return false;
    }

    $wpdb->insert($nombreTabla, array( 'name' => "PAQ 10",                 'id_bc' => 61,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "PAQ 14",                 'id_bc' => 62,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "PAQ 24",                 'id_bc' => 63,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Baleares",               'id_bc' => 66,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Canarias Express",       'id_bc' => 67,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Canarias Aéreo",         'id_bc' => 68,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Canarias Marítimo",      'id_bc' => 69,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "CEX Portugal Óptica",    'id_bc' => 73,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Paquetería Óptica",      'id_bc' => 76,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Internacional Express",  'id_bc' => 91,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Internacional Estandard",'id_bc' => 90,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Paq Empresa 14",         'id_bc' => 92,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "ePaq 24",                'id_bc' => 93,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Campaña CEX",            'id_bc' => 27,      'id_carrier'=>''));

    registrar_ejecucion_migracion($wpdb, 'seeding - '.$nombreTabla);

    return true;
}

function cex_seeding_customer_options($wpdb)
{
    $nombreTabla = $wpdb->prefix.'customer_options';

    if (comprobar_ejecucion_migracion($wpdb, 'seeding - '.$nombreTabla)) {
        return false;
    }

    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_ENABLEOFFICEDELIVERY", 'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_ORDER_PROCESS_TYPE", 'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_MSG_DELIVERY_OFFICE", 'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_CHECK_ENABLESHIPPINGTRACK", 'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_MSG_ALERT_OFICINACORREOS", 'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_CLASS_CONTRAREEMBOLSO", 'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_USER",             'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_PASSWD",           'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_WSURL",
        'valor' => 'https://www.correosexpress.com/wpsc/services/GrabacionEnvio?wsdl'));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_WSURLREC",
        'valor' => 'https://www.correosexpress.com/wpsc/services/GrabacionRecogida?wsdl'));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_WSURLSEG",
        'valor' => 'https://www.correosexpress.com/wpsc/services/SeguimientoEnvio?wsdl'));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_APIRESTOFI",
        'valor' => 'https://www.cexpr.es/wspsc/apiRestOficina/v1/oficinas/listadoOficinasCoordenadas'));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DEFAULTKG",        'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_ENABLEWEIGHT",     'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_ENABLESHIPPINGTRACK", 'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DEFAULTBUL",       'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DEFAULTPDF",       'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DEFAULTSEND",      'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DEFAULTPAYBACK",   'valor' => ''));

    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_ENABLESENDCRON",   'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_SENDCRONSTATE1",   'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_SENDCRONSTATE2",   'valor' => ''));


    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_ENABLEDELIVERCRON", 'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DELIVERCRONSTATE1", 'valor' => ''));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DELIVERCRONSTATE2", 'valor' => ''));    

    registrar_ejecucion_migracion($wpdb, 'seeding - '.$nombreTabla);

    return true;
}


function cex_seeding_entrega_oficina($wpdb)
{
    //realizado el 28-05-2018
    $nombreTabla = $wpdb->prefix.'savedmodeships';

    if (comprobar_ejecucion_migracion($wpdb, 'seeding entrega en oficina - '.$nombreTabla)) {
        return false;
    }

    $wpdb->insert($nombreTabla, array( 'name' => "Entrega en Oficina",                 'id_bc' => 44,      'id_carrier'=>''));
    registrar_ejecucion_migracion($wpdb, 'seeding entrega en oficina - '.$nombreTabla);

    return true;
}



function cex_seeding_saved_modeships_2($wpdb)
{
    $nombreTabla = $wpdb->prefix.'savedmodeships';

    if (comprobar_ejecucion_migracion($wpdb, 'seeding - Multichrono')) {
        return false;
    }

    $wpdb->insert($nombreTabla, array( 'name' => "Entrega + Recogida Multichrono",                 'id_bc' => 54,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Entrega + recogida + Manip Multichrono",         'id_bc' => 55,      'id_carrier'=>''));

    registrar_ejecucion_migracion($wpdb, 'seeding - Multichrono');

    return true;
}

function cex_seeding_customer_deliver_option($wpdb)
{
    $nombreTabla = $wpdb->prefix.'customer_options';

    if (comprobar_ejecucion_migracion($wpdb, 'seeding deliver - '.$nombreTabla)) {
        return false;
    }


    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DEFAULTDELIVER", 'valor' => ''));

    registrar_ejecucion_migracion($wpdb, 'seeding deliver - '.$nombreTabla);

    return true;
}



function cex_seeding_saved_modeships_3($wpdb)
{
    $nombreTabla = $wpdb->prefix.'cex_savedmodeships';

    if (comprobar_ejecucion_migracion($wpdb, 'seeding - Islas - '.$nombreTabla)) {
        return false;
    }

    $wpdb->insert($nombreTabla, array( 'name' => "Islas Express",          'id_bc' => 26,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Islas Docs",             'id_bc' => 46,      'id_carrier'=>''));
    $wpdb->insert($nombreTabla, array( 'name' => "Islas Marítimo",         'id_bc' => 79,      'id_carrier'=>''));

    registrar_ejecucion_migracion($wpdb, 'seeding - Islas - '.$nombreTabla);

    return true;
}



