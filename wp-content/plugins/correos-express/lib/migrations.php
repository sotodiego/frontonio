<?php
function install_db()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    //$wpdb->base_prefix=strtolower($wpdb->base_prefix);
    
    $table =$wpdb->base_prefix.'cex_migrations';
    $result= $wpdb->get_results("SHOW TABLES LIKE '$table'");
    if (empty($result)) {
        $tabla0 = cex_migracion0($wpdb);
    }
    $tabla1 = cex_migracion1($wpdb);
    $tabla2 = cex_migracion2($wpdb);
    $tabla3 = cex_migracion3($wpdb);
    $poblado1 = cex_seeding_saved_modeships($wpdb);
    $tabla4 = cex_migracion4($wpdb);
    $tabla5 = cex_migracion5($wpdb);
    $poblado2 = cex_seeding_customer_options($wpdb);
    $tabla6 = cex_migracion6($wpdb);
    $tabla7 = cex_migracion7($wpdb);
    $tabla8 = cex_migracion8($wpdb);
    $tabla9 = cex_migracion9($wpdb);
    $tabla10 = cex_migracion10($wpdb);
    $poblado3 = cex_seeding_entrega_oficina($wpdb);
    $poblado4 = cex_seeding_saved_modeships_2($wpdb);
    $tabla11 = cex_migracion11($wpdb);
    $tabla12 = cex_migracion12($wpdb);
    $tabla13 = cex_migracion13($wpdb);
    $poblado5 = cex_seeding_customer_deliver_option($wpdb);
    $tabla14 = cex_migracion14($wpdb);
    $tabla15 = cex_migracion15($wpdb);
    $tabla16 = cex_migracion16($wpdb);
    $tabla17 = cex_migracion17($wpdb);
    $tabla18 = cex_migracion18($wpdb);
    
    /********************
     * CREACION DE TABLAS CON EL PREFIJO "CEX" Y LA CODIFICACIÓN DEFINA POR WORDPRESS
     * MIGRACIÓN DE DATOS DE LAS TABLAS VIEJAS A LAS NUEVAS
    ********************/
    
    $collate = '';
    $pref= $wpdb->prefix.'cex_';
    if ($wpdb->has_cap('collation')) {
        $collate = $wpdb->get_charset_collate();
    }
    $tabla1 = cex_migracion19($wpdb, $collate, $pref);
    $tabla2 = cex_migracion20($wpdb, $collate, $pref);
    $tabla3 = cex_migracion21($wpdb, $collate, $pref);
    $tabla4 = cex_migracion22($wpdb, $collate, $pref);
    $tabla5 = cex_migracion23($wpdb, $collate, $pref);
    $tabla6 = cex_migracion24($wpdb, $collate, $pref);
    $tabla7 = cex_migracion25($wpdb, $collate, $pref);
    $tabla8 = cex_migracion26($wpdb, $collate, $pref);
    $tabla9 = cex_migracion27($wpdb, $collate, $pref);
    $tabla10 = cex_migracion28($wpdb);
    $tabla11 = cex_migracion29($wpdb);
    $tabla12 = cex_migracion30($wpdb);
    $tabla13 = cex_migracion31($wpdb);
    //$tabla14 = cex_migracion32($wpdb);
    $tabla15 = cex_migracion33($wpdb);
    $tabla16 = cex_migracion34($wpdb);
    $tabla17 = cex_migracion34Rest($wpdb);
    $tabla18 = cex_migracion35($wpdb);
    $tabla19 = cex_migracion36($wpdb);
    $tabla20 = cex_migracion37($wpdb);
    $tabla21 = cex_migracion38($wpdb);
    $pobladoIslas = cex_seeding_saved_modeships_3($wpdb);
    $tabla22 = cex_migracion39($wpdb);
    $tabla23 = cex_migracion40($wpdb);
    $tabla24 = cex_migracion41($wpdb);
    $tabla25 = cex_migracion42($wpdb);
    $tabla26 = cex_migration43($wpdb);
    $tabla27 = cex_migration44($wpdb);
    $tabla28 = cex_migration45($wpdb);
    $tabla29 = cex_migration46($wpdb);
    $tabla30 = cex_migration47($wpdb);
}

function comprobar_ejecucion_migracion($wpdb, $nombre_migracion)
{
    $nombreTabla = $wpdb->prefix.'cex_migrations';
    //ejecutar una select sobre la tabla de migraciones pa ver si existe el metodo.
    $cuantos = $wpdb->get_var("SELECT  count(*)
                            FROM $nombreTabla
                            WHERE metodoEjecutado = '$nombre_migracion'");
    //retornamos true si ya ha sido ejecutada
    //retornamos false si aun no ha sido ejecuta.
    if ($cuantos >= 1) {
        return true;
    } else {
        return false;
    }
}

function registrar_ejecucion_migracion($wpdb, $nombreMigracion, $nombreTabla = 'cex_migrations')
{
    $migraciones = $wpdb->prefix.$nombreTabla;
    $data = array(
        'metodoEjecutado' => $nombreMigracion,
        'created_at' => date("Y-m-d H:i:s")
    );
    if ($wpdb->insert($migraciones, $data)) {
        return true;
    } else {
        return false;
    }
}

function cex_migracion0($wpdb)
{
    $nombreTabla = $wpdb->prefix.'cex_migrations';
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
        id INT NOT NULL AUTO_INCREMENT,
        metodoEjecutado VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id)    
    )";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA MIGRATONS";
}

function cex_migracion1($wpdb)
{
    $nombreTabla = $wpdb->prefix.'savedships';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
        id_ship INT(11) NOT NULL AUTO_INCREMENT,
        date DATE NOT NULL,
        numcollect VARCHAR(50) NOT NULL,
        numship VARCHAR(20) NULL DEFAULT NULL,
        collectfrom VARCHAR(50) NOT NULL,
        postalcode VARCHAR(10) NOT NULL,
        id_order INT(11) NOT NULL,
        id_mode INT(11) NULL DEFAULT NULL,
        id_sender INT(11) NULL DEFAULT NULL,
        type ENUM('Recogida','Envio','Recogida Agrupada','Envio Agrupado') NULL DEFAULT NULL,
        kg DECIMAL(7,2) NOT NULL,
        package INT(11) NOT NULL,
        payback_val DECIMAL(20,2) NULL DEFAULT NULL,
        insured_value DECIMAL(20,2) NULL DEFAULT NULL,
        id_bc INT(11) NOT NULL,
        mode_ship_name VARCHAR(50) NOT NULL,
        status ENUM('Creado','Guardado','Grabado','Agrupado','Caducado') NOT NULL DEFAULT 'Creado',
        id_ship_expired INT(11) NULL DEFAULT NULL,
        id_group INT(11) NULL DEFAULT NULL,
        note_collect LONGTEXT NULL,
        note_deliver LONGTEXT NULL,
        iso_code VARCHAR(10) NOT NULL,
        devolution BOOLEAN NULL DEFAULT NULL,
        deliver_sat BOOLEAN NULL DEFAULT NULL,
        mailLabel BOOLEAN NULL DEFAULT NULL,
        desc_ref_1 VARCHAR(50) NULL DEFAULT NULL,
        desc_ref_2 VARCHAR(50) NULL DEFAULT NULL,
        from_hour VARCHAR(50) NOT NULL,
        from_minute VARCHAR(50) NOT NULL,
        to_hour VARCHAR(50) NOT NULL,
        to_minute VARCHAR(50) NOT NULL,
        sender_name VARCHAR(50) NOT NULL,
        sender_contact VARCHAR(50) NOT NULL,
        sender_address VARCHAR(120) NOT NULL,
        sender_postcode VARCHAR(11) NOT NULL,
        sender_city VARCHAR(50) NOT NULL,
        sender_phone VARCHAR(50) NOT NULL,
        sender_country VARCHAR(20) NOT NULL,
        sender_email VARCHAR(50) NOT NULL,
        receiver_name VARCHAR(50) NOT NULL,
        receiver_contact VARCHAR(50) NOT NULL,
        receiver_address VARCHAR(120) NOT NULL,
        receiver_postcode VARCHAR(11) NOT NULL,
        receiver_city VARCHAR(50) NOT NULL,
        receiver_phone VARCHAR(50) NOT NULL,
        receiver_phone2 VARCHAR(50) NOT NULL,
        receiver_email VARCHAR(50) NOT NULL,
        receiver_country VARCHAR(20) NOT NULL,
        codigo_cliente VARCHAR(50) NOT NULL,
        oficina_entrega TEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id_ship),
        UNIQUE INDEX referencias_unicas (numcollect, type)
    )";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA SAVEDSHIPS";
}

function cex_migracion2($wpdb)
{
    $nombreTabla = $wpdb->prefix.'savedsenders';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla(
        id_sender INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        name VARCHAR(50) NOT NULL,
        address VARCHAR(90) NOT NULL,
        postcode VARCHAR(11) NOT NULL,
        city VARCHAR(20) NOT NULL,
        contact VARCHAR(30) NOT NULL,
        phone VARCHAR(14) NOT NULL,
        from_hour INT(2) NOT NULL,
        from_minute INT(2) NOT NULL,
        to_hour INT(2) NOT NULL,
        to_minute INT(2) NOT NULL,
        iso_code_pais VARCHAR(4) NOT NULL,
        email VARCHAR(50) NOT NULL,
        id_cod_cliente INT(3) NOT NULL,
        UNIQUE INDEX Senders_unicos_completos(name, address, postcode,
        city, contact, phone,
        from_hour,from_minute,to_hour,
        to_minute)
    )";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA SAVEDSENDERS";
}

function cex_migracion3($wpdb)
{
    $nombreTabla = $wpdb->prefix.'savedmodeships';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
        id_mode INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        name VARCHAR(50) NOT NULL,
        id_bc VARCHAR(5) NOT NULL, 
        id_carrier VARCHAR(255) NULL,
        checked BOOLEAN NULL
    )";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA SAVEDMODESHIPS";
}

function cex_migracion4($wpdb)
{
    $nombreTabla = $wpdb->prefix.'officedeliverycorreo';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
        id_officeDeliveryCorreo INT(11) NOT NULL AUTO_INCREMENT,
        id_cart INT(11) NULL DEFAULT NULL,
        id_carrier INT(11) NULL DEFAULT NULL,
        id_customer INT(11) NULL DEFAULT NULL,
        codigo_oficina VARCHAR(255) NULL DEFAULT NULL,
        PRIMARY KEY (id_officeDeliveryCorreo)
    )";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA OFFICEDELIVERYCORREO";
}

function cex_migracion5($wpdb)
{
    $nombreTabla = $wpdb->prefix.'customer_options';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
    id INT(11) NOT NULL AUTO_INCREMENT,
    clave VARCHAR(50) NOT NULL,
    valor VARCHAR(100) NOT NULL,
    PRIMARY KEY (id))";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA CUSTOMER_OPTIONS";
}

function cex_migracion6($wpdb)
{
    $nombreTabla = $wpdb->prefix.'customer_codes';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
    id INT(3) NOT NULL AUTO_INCREMENT,
    customer_code VARCHAR(30) NOT NULL,
    code_demand VARCHAR(50) NOT NULL,
    PRIMARY KEY (id))";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA customer_codes";
}

function cex_migracion7($wpdb)
{
    $nombreTabla = $wpdb->prefix.'cex_history';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    id_order INT NOT NULL,
    numCollect VARCHAR(15),
    type ENUM('Recogida','Envio','Tracking','Envio Recogida Oficina') NULL DEFAULT NULL,
    numShip VARCHAR(20) NULL,
    resultado VARCHAR(20) NULL,
    mensajeRetorno VARCHAR(255) NULL,
    codigoRetorno INT NULL, 
    envioWS TEXT NOT NULL,
    respuestaWS TEXT NOT NULL,
    fecha DATETIME NOT NULL)";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA CEX_HISTORY";
}

function cex_migracion8($wpdb)
{
    $nombreTabla = $wpdb->prefix.'envios_bultos';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    id_order INT NOT NULL,
    numcollect VARCHAR(255) NOT NULL,
    numShip VARCHAR(20) NOT NULL,
    codUnicoBulto VARCHAR(30) NOT NULL,
    id_bulto INT NOT NULL,
    fecha DATETIME NOT NULL)";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA ENVIOS_BULTOS";
}

function cex_migracion9($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'savedships';
    $nombreMigracion = $nombreTabla." ADD WS_ESTADO_TRACKING";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    //añadir nuevo campo para el tracking de pedidos.
    $sql = "ALTER TABLE $nombreTabla
            ADD WS_ESTADO_TRACKING integer
            DEFAULT 0";
    $created = $wpdb->query($sql);
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    //return "SE HA ACTUALIZADO LA TABLA DE SAVEDSHIPS";
}

function cex_migracion10($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'customer_options';
    $nombreMigracion = $nombreTabla." DROP CRON STATES";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    //añadir nuevo campo para el tracking de pedidos.
    $sql = "DELETE FROM $nombreTabla WHERE clave in ('MXPS_ENABLESENDCRON','MXPS_SENDCRONSTATE1','MXPS_SENDCRONSTATE2','MXPS_DELIVERCRONSTATE2','MXPS_DELIVERCRONSTATE1','MXPS_ENABLEDELIVERCRON')";
    $created = $wpdb->query($sql);
    //insertamos el nuevo campo AutoTracking
    $wpdb->insert($nombreTabla, array('clave' => "MXPS_TRACKINGCEX", 'valor' => ''));
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}

function cex_migracion11($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'customer_options';
    $nombreMigracion = $nombreTabla." ADD MXPS_CHANGESTATUS";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    //añadir nuevo campo para el tracking de pedidos.
    $wpdb->insert($nombreTabla, array('clave' => "MXPS_CHANGESTATUS", 'valor' => ''));
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}

function cex_migracion12($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'savedships';
    $nombreMigracion = $nombreTabla." CHANGE ENUM";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    //añadir nuevo campo para el tracking de pedidos.
    $sql="ALTER TABLE $nombreTabla MODIFY COLUMN status enum(
        'Creado','Guardado','Grabado','Agrupado','Caducado','Enviado','Entregado','Anulado','Devuelto')";
    $execute = $wpdb->query($sql);
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    //return "SE HA ACTUALIZADO LA TABLA DE SAVEDSHIPS";
}

function cex_migracion13($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'savedships';
    $nombreMigracion = $nombreTabla." ADD DELETED_AT";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    //añadir nuevo campo para el tracking de pedidos.
    $sql = "ALTER TABLE $nombreTabla
            ADD deleted_at DATETIME
            DEFAULT NULL";
    $created = $wpdb->query($sql);
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    //return "SE HA ACTUALIZADO LA TABLA DE SAVEDSHIPS";
}

function cex_migracion14($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'customer_options';
    $nombreMigracion = $nombreTabla." ADD Estados Cron Orden";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $wpdb->insert($nombreTabla, array('clave' => "MXPS_RECORDSTATUS", 'valor' => ''));
    $wpdb->insert($nombreTabla, array('clave' => "MXPS_SENDINGSTATUS", 'valor' => ''));
    $wpdb->insert($nombreTabla, array('clave' => "MXPS_DELIVEREDSTATUS", 'valor' => ''));
    $wpdb->insert($nombreTabla, array('clave' => "MXPS_CANCELEDSTATUS", 'valor' => ''));
    $wpdb->insert($nombreTabla, array('clave' => "MXPS_RETURNEDSTATUS", 'valor' => ''));
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}

function cex_migracion15($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'customer_options';
    $nombreMigracion = $nombreTabla." ADD Quitar remitente etiqueta";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $wpdb->insert($nombreTabla, array('clave' => "MXPS_LABELSENDER", 'valor' => ''));
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}

function cex_migracion16($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'customer_options';
    $nombreMigracion = $nombreTabla." ADD MXPS_SAVEDSTATUS";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $wpdb->insert($nombreTabla, array('clave' => "MXPS_SAVEDSTATUS", 'valor' => ''));
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}

function cex_migracion17($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'envios_bultos';
    $nombreMigracion = $nombreTabla." ADD DELETED_AT";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    //añadir nuevo campo para el tracking de pedidos.
    $sql = "ALTER TABLE $nombreTabla
            ADD deleted_at DATETIME
            DEFAULT NULL";
    $created = $wpdb->query($sql);
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    //return "SE HA ACTUALIZADO LA TABLA DE ENVIOS_BULTOS";
}

function cex_migracion18($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'savedships';
    $nombreMigracion = $nombreTabla." ADD INDEX";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    //añadir nuevo campo para el tracking de pedidos.
    $sql = "ALTER TABLE $nombreTabla DROP INDEX referencias_unicas";
    $query = $wpdb->query($sql);
    $sql = "ALTER TABLE $nombreTabla ADD UNIQUE INDEX referencias_unicas (numcollect, type, created_at)";
    $query = $wpdb->query($sql);
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;
    //return "SE HA ACTUALIZADO LA TABLA DE ENVIOS_BULTOS";
}

/*CREACIÓN DE LAS NUEVAS TABLAS*/
function cex_migracion19($wpdb, $collate, $pref)
{
    $nombreTabla = $pref.'savedships';
    $nombreTablaOrigen = $wpdb->prefix.'savedships';
    $nombreTablaMigracion = 'CREATE TABLE '.$nombreTabla;
    if (comprobar_ejecucion_migracion($wpdb, $nombreTablaMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE $nombreTabla (
        id_ship int(11) NOT NULL AUTO_INCREMENT,
        date date NOT NULL,
        numcollect varchar(50) NOT NULL,
        numship varchar(20) DEFAULT NULL,
        collectfrom varchar(50) NOT NULL,
        postalcode varchar(10) NOT NULL,
        id_order int(11) NOT NULL,
        id_mode int(11) DEFAULT NULL,
        id_sender int(11) DEFAULT NULL,
        type enum('Recogida','Envio','Recogida Agrupada','Envio Agrupado') DEFAULT NULL,
        kg decimal(7,2) NOT NULL,
        package int(11) NOT NULL,
        payback_val decimal(20,2) DEFAULT NULL,
        insured_value decimal(20,2) DEFAULT NULL,
        id_bc int(11) NOT NULL,
        mode_ship_name varchar(50) NOT NULL,
        status enum('Creado','Guardado','Grabado','Agrupado','Caducado','Enviado','Entregado','Anulado','Devuelto') DEFAULT NULL,
        id_ship_expired int(11) DEFAULT NULL,
        id_group int(11) DEFAULT NULL,
        note_collect longtext,
        note_deliver longtext,
        iso_code varchar(10) NOT NULL,
        devolution tinyint(1) DEFAULT NULL,
        deliver_sat tinyint(1) DEFAULT NULL,
        mailLabel tinyint(1) DEFAULT NULL,
        desc_ref_1 varchar(50) DEFAULT NULL,
        desc_ref_2 varchar(50) DEFAULT NULL,
        from_hour varchar(50) NOT NULL,
        from_minute varchar(50) NOT NULL,
        to_hour varchar(50) NOT NULL,
        to_minute varchar(50) NOT NULL,
        sender_name varchar(50) NOT NULL,
        sender_contact varchar(50) NOT NULL,
        sender_address varchar(120) NOT NULL,
        sender_postcode varchar(11) NOT NULL,
        sender_city varchar(50) NOT NULL,
        sender_phone varchar(50) NOT NULL,
        sender_country varchar(20) NOT NULL,
        sender_email varchar(50) NOT NULL,
        receiver_name varchar(50) NOT NULL,
        receiver_contact varchar(50) NOT NULL,
        receiver_address varchar(120) NOT NULL,
        receiver_postcode varchar(11) NOT NULL,
        receiver_city varchar(50) NOT NULL,
        receiver_phone varchar(50) NOT NULL,
        receiver_phone2 varchar(50) NOT NULL,
        receiver_email varchar(50) NOT NULL,
        receiver_country varchar(20) NOT NULL,
        codigo_cliente varchar(50) NOT NULL,
        oficina_entrega text,
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        WS_ESTADO_TRACKING int(11) DEFAULT '0',
        deleted_at datetime DEFAULT NULL,  
        modificacionAutomatica int(1) DEFAULT 0,   
        PRIMARY KEY (id_ship),
        UNIQUE INDEX referencias_unicas (numcollect,type,created_at)
    ) $collate ";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);

    if (comprobar_ejecucion_migracion($wpdb, "migrarDatos  --".$nombreTablaMigracion)) {
        return false;
    }
    migrarDatos($wpdb, $nombreTablaOrigen, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA SAVEDSHIPS";
}

function cex_migracion20($wpdb, $collate, $pref)
{
    $nombreTabla = $pref.'savedsenders';
    $nombreTablaOrigen = $wpdb->prefix.'savedsenders';
    $nombreTablaMigracion = 'CREATE TABLE '.$nombreTabla;
    if (comprobar_ejecucion_migracion($wpdb, $nombreTablaMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla(
        id_sender INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        name VARCHAR(50) NOT NULL,
        address VARCHAR(90) NOT NULL,
        postcode VARCHAR(11) NOT NULL,
        city VARCHAR(20) NOT NULL,
        contact VARCHAR(30) NOT NULL,
        phone VARCHAR(14) NOT NULL,
        from_hour INT(2) NOT NULL,
        from_minute INT(2) NOT NULL,
        to_hour INT(2) NOT NULL,
        to_minute INT(2) NOT NULL,
        iso_code_pais VARCHAR(4) NOT NULL,
        email VARCHAR(50) NOT NULL,
        id_cod_cliente INT(3) NOT NULL,
        UNIQUE INDEX Senders_unicos_completos(name, address, postcode,
        city, contact, phone,
        from_hour,from_minute,to_hour,
        to_minute)
    ) $collate";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);

    if (comprobar_ejecucion_migracion($wpdb, "migrarDatos  --".$nombreTablaMigracion)) {
        return false;
    }
    migrarDatos($wpdb, $nombreTablaOrigen, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA SAVEDSENDERS";
}

function cex_migracion21($wpdb, $collate, $pref)
{
    $nombreTabla = $pref.'savedmodeships';
    $nombreTablaOrigen = $wpdb->prefix.'savedmodeships';
    $nombreTablaMigracion = 'CREATE TABLE '.$nombreTabla;
    if (comprobar_ejecucion_migracion($wpdb, $nombreTablaMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
        id_mode INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        name VARCHAR(50) NOT NULL,
        id_bc VARCHAR(5) NOT NULL, 
        id_carrier VARCHAR(255) NULL,
        checked BOOLEAN NULL
    ) $collate";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);

    if (comprobar_ejecucion_migracion($wpdb, "migrarDatos  --".$nombreTablaMigracion)) {
        return false;
    }
    migrarDatos($wpdb, $nombreTablaOrigen, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA SAVEDMODESHIPS";
}

function cex_migracion22($wpdb, $collate, $pref)
{
    $nombreTabla = $pref.'officedeliverycorreo';
    $nombreTablaOrigen = $wpdb->prefix.'officedeliverycorreo';
    $nombreTablaMigracion = 'CREATE TABLE '.$nombreTabla;
    if (comprobar_ejecucion_migracion($wpdb, $nombreTablaMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
        id_officeDeliveryCorreo INT(11) NOT NULL AUTO_INCREMENT,
        id_cart INT(11) NULL DEFAULT NULL,
        id_carrier INT(11) NULL DEFAULT NULL,
        id_customer INT(11) NULL DEFAULT NULL,
        codigo_oficina VARCHAR(50) NULL DEFAULT NULL,
        PRIMARY KEY (id_officeDeliveryCorreo)
    ) $collate";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);

    if (comprobar_ejecucion_migracion($wpdb, "migrarDatos  --".$nombreTablaMigracion)) {
        return false;
    }
    migrarDatos($wpdb, $nombreTablaOrigen, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA OFFICEDELIVERYCORREO";
}

function cex_migracion23($wpdb, $collate, $pref)
{
    $nombreTabla = $pref.'customer_options';
    $nombreTablaOrigen = $wpdb->prefix.'customer_options';
    $nombreTablaMigracion = 'CREATE TABLE '.$nombreTabla;
    if (comprobar_ejecucion_migracion($wpdb, $nombreTablaMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
    id INT(11) NOT NULL AUTO_INCREMENT,
    clave VARCHAR(50) NOT NULL,
    valor VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)) $collate";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);
    migrarDatos($wpdb, $nombreTablaOrigen, $nombreTabla);

    $result=$wpdb->insert($nombreTabla, array( 'clave' => "MXPS_NODATAPROTECTION", 'valor' => false));
    $result2=$wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DATAPROTECTIONVALUE", 'valor' => '0'));
    if (!$result || !$result2) {
        return false;
    } else {
        registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);
        return true;
    }
    //return "SE HA CREADO LA TABLA CUSTOMER_OPTIONS";
}

function cex_migracion24($wpdb, $collate, $pref)
{
    $nombreTabla = $pref.'customer_codes';
    $nombreTablaOrigen = $wpdb->prefix.'customer_codes';
    $nombreTablaMigracion = 'CREATE TABLE '.$nombreTabla;
    
    if (comprobar_ejecucion_migracion($wpdb, $nombreTablaMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
    id INT(3) NOT NULL AUTO_INCREMENT,
    customer_code VARCHAR(30) NOT NULL,
    code_demand VARCHAR(50) NOT NULL,
    PRIMARY KEY (id)) $collate";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);

    if (comprobar_ejecucion_migracion($wpdb, "migrarDatos  --".$nombreTablaMigracion)) {
        return false;
    }
    migrarDatos($wpdb, $nombreTablaOrigen, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA CUSTOMER_OPTIONS";
}

function cex_migracion25($wpdb, $collate, $pref)
{
    $nombreTabla = $pref.'envios_bultos';
    $nombreTablaOrigen = $wpdb->prefix.'envios_bultos';
    $nombreTablaMigracion = 'CREATE TABLE '.$nombreTabla;

    if (comprobar_ejecucion_migracion($wpdb, $nombreTablaMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    id_order INT NOT NULL,
    numcollect VARCHAR(255) NOT NULL,
    numShip VARCHAR(20) NOT NULL,
    codUnicoBulto VARCHAR(30) NOT NULL,
    id_bulto INT NOT NULL,
    fecha DATETIME NOT NULL,
    deleted_at DATETIME DEFAULT NULL) $collate";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);
    if (comprobar_ejecucion_migracion($wpdb, "migrarDatos  --".$nombreTablaMigracion)) {
        return false;
    }
    migrarDatos($wpdb, $nombreTablaOrigen, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA ENVIOS_BULTOS";
}

function cex_migracion26($wpdb, $collate, $pref)
{
    $nombreTablaVieja = $wpdb->prefix.'cex_history';
    $nombreTablaAuxiliar = $pref.'aux_history';
    $nombreTablaMigracion = 'CREATE TABLE '.$nombreTablaAuxiliar;
    $nombreTabla = $pref.'history';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTablaMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTablaAuxiliar(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    id_order INT NOT NULL,
    numCollect VARCHAR(15),
    type ENUM('Recogida','Envio','Tracking','Envio Recogida Oficina') NULL DEFAULT NULL,
    numShip VARCHAR(20) NULL,
    resultado VARCHAR(20) NULL,
    mensajeRetorno VARCHAR(255) NULL,
    codigoRetorno INT NULL, 
    envioWS TEXT NOT NULL,
    respuestaWS TEXT NOT NULL,
    fecha DATETIME NOT NULL) $collate";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);
    if (actualizarTablasPrincipales($wpdb, $nombreTablaVieja, $nombreTablaAuxiliar, $nombreTabla)) {
        return true;
    } else {
        return false;
    }
    return true;
    //return "SE HA CREADO LA TABLA CEX_HISTORY";
}

function cex_migracion27($wpdb, $collate, $pref)
{
    $nombreTablaVieja = $wpdb->prefix.'cex_migrations';
    $nombreTablaAuxiliar = $pref.'aux_migrations';
    $nombreTablaMigracion = 'CREATE TABLE '.$nombreTablaAuxiliar;
    $nombreTabla = $pref.'migrations';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTablaMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTablaAuxiliar (
        id INT NOT NULL AUTO_INCREMENT,
        metodoEjecutado VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id)    
    ) $collate";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTablaMigracion);
    if (actualizarTablasPrincipales($wpdb, $nombreTablaVieja, $nombreTablaAuxiliar, $nombreTabla)) {
        return true;
    } else {
        return false;
    }
    return true;
    //return "SE HA CREADO LA TABLA MIGRATONS";
}

function cex_migracion28($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'cex_customer_options';    
    $nombreMigracion = $nombreTabla." ADD MXPS_REFETIQUETAS - Referencia orden";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_REFETIQUETAS", 'valor' => '0'));
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;    
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}
function cex_migracion29($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'cex_customer_options';    
    $nombreMigracion = $nombreTabla." ADD MXPS_CRONINTERVAL";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_CRONINTERVAL", 'valor' => '4'));
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;    
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}
function cex_migracion30($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'cex_customer_options';    
    $nombreMigracion = $nombreTabla." ADD MXPS_CRYPT";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_CRYPT", 'valor' => base64_encode(uniqid(mt_rand()))));
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;    
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}

function cex_migracion31($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'cex_customer_options';    
    $nombreMigracion = $nombreTabla." ENCRYPT MXPS_USER AND MXPS_PASS";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
       
    $user = $wpdb->get_var($wpdb->prepare("SELECT valor FROM $nombreTabla where clave='MXPS_USER'", null));	
    $pass = $wpdb->get_var($wpdb->prepare("SELECT valor FROM $nombreTabla where clave='MXPS_PASSWD'", null));    
    if ($pass!='' && strlen($pass)<12) {
        if ($user!='') {
            $user=cex_encrypt_decrypt('encrypt',$user);
            $update  = $wpdb->prepare("UPDATE $nombreTabla SET valor='$user' WHERE clave='MXPS_USER'", null);        
            $comprobante1=$wpdb->query($update);
        }
        $pass=cex_encrypt_decrypt('encrypt',$pass);
        $update2  = $wpdb->prepare("UPDATE $nombreTabla SET valor='$pass' WHERE clave='MXPS_PASSWD'", null);        
        $comprobante2=$wpdb->query($update2);
    }        
    
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);    
    
    return true;    
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}

// function cex_migracion32($wpdb)
// {
//     //alter tabla savedships
//     $nombreTabla = $wpdb->prefix.'cex_customer_options';    
//     $nombreMigracion = $nombreTabla." ADD WEB_SERVICES_REST";
//     if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
//         return false;
//         //return "$nombreTabla -- YA HA SIDO EJECUTADA";
//     }
//     $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_DEFAULTWS", 'valor' => 'REST'));
//     $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_WSURL_REST", 'valor' => 'https://www.correosexpress.com/wpsc/apiRestGrabacionEnvio/json/grabacionEnvio'));
//     $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_WSURLREC_REST", 'valor' => 'https://www.correosexpress.com/wpsc/apiRestGrabacionRecogida/json/grabarRecogida'));
//     $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_WSURLSEG_REST", 'valor' => 'http://www.test.cexpr.es/wsps/apiRestListaEnvios/json/listaEnvios'));
//     registrar_ejecucion_migracion($wpdb, $nombreMigracion);
//     return true;    
//     //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
// }
function cex_migracion33($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'cex_customer_options';    
    $nombreMigracion = $nombreTabla." ADD IMAGE_LOGO";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_CHECKUPLOADFILE", 'valor' => 'false'));
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_UPLOADFILE", 'valor' => 'undefined'));
    
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;    
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}


function cex_migracion34($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'cex_customer_options';    
    $nombreMigracion = $nombreTabla." ADD LABELSENDER_TEXT";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $wpdb->insert($nombreTabla, array( 'clave' => "MXPS_LABELSENDER_TEXT", 'valor' => ''));

    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    return true;    
    //return "SE HA ACTUALIZADO LA TABLA DE CUSTOMER_OPTIONS";
}

function cex_migracion34Rest($wpdb){
    $nombreTabla = $wpdb->prefix.'cex_customer_options';  
    $nombreMigracion    = $nombreTabla." ADD REST_CONF";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }
    $wpdb->insert($nombreTabla, array(
        'clave'     => "MXPS_DEFAULTWS",
        'valor'     => 'REST',
    ));
        
    $wpdb->insert($nombreTabla, array(
        'clave'     => "MXPS_WSURL_REST",
        'valor'     => 'https://www.cexpr.es/wspsc/apiRestGrabacionEnviok8s/json/grabacionEnvio',
    ));

    $wpdb->insert($nombreTabla, array(
        'clave'     => "MXPS_WSURLSEG_REST",
        'valor'     => 'https://www.cexpr.es/wspsc/apiRestListaEnvios/json/listaEnvios',
    ));

    $wpdb->insert($nombreTabla, array(
        'clave'     => "MXPS_WSURLREC_REST",
        'valor'     => 'https://www.correosexpress.com/wpsc/apiRestSeguimientoEnvios/rest/seguimientoEnvio',
    )); 

    if (!$wpdb) {
        return false;
    }

    registrar_ejecucion_migracion($wpdb,$nombreMigracion);
    return true;
}

function cex_migracion35($wpdb){
    $nombreTabla        = $wpdb->prefix.'cex_history';
    $nombreMigracion    = $nombreTabla." ADD DATE_HOURS PICK UP";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }

    $sql = "ALTER TABLE $nombreTabla 
            ADD fecha_recogida DATE NULL, 
            ADD hora_recogida_desde TIME NULL,
            ADD hora_recogida_hasta TIME NULL";

    $query = $wpdb->query($sql);

    if (!$query) {
        return false;
    }

    registrar_ejecucion_migracion($wpdb,$nombreMigracion);
    return true;
}

function cex_migracion36($wpdb){
    $nombreTabla        = $wpdb->prefix.'cex_history';
    $nombreMigracion    = $nombreTabla." CHANGE ENUM";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }

    $sql = "ALTER TABLE $nombreTabla
    MODIFY type ENUM('Recogida','Envio','Tracking',
    'Envio Recogida Oficina','Borrar Recogida', 'Modificar Recogida', 
    'Borrar Envio');";

    $query = $wpdb->query($sql);

    if (!$query) {
        return false;
    }

    registrar_ejecucion_migracion($wpdb,$nombreMigracion);
    return true;
}

function cex_migracion37($wpdb){  
    $nombreTabla        = $wpdb->prefix.'cex_customer_options';
    $nombreMigracion    = $nombreTabla." ADD_URL_ANUL_MODF_REC";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }
    $wpdb->insert($nombreTabla, array(
        'clave'     => "MXPS_WSURLMODF_REST",
        'valor'     => 'https://www.cexpr.es/wspsc/apiRestGrabacionRecogidaEnviok8s/json/modificarRecogida',
    ));

    $wpdb->insert($nombreTabla, array(
        'clave'     => "MXPS_WSURLANUL_REST",
        'valor'     => 'https://www.cexpr.es/wspsc/apiRestGrabacionRecogidaEnviok8s/json/anularRecogida',
    )); 

    if (!$wpdb) {
        return false;
    }

    registrar_ejecucion_migracion($wpdb,$nombreMigracion);
    return true;   
}

function cex_migracion38($wpdb)
{
    //alter tabla savedships
    $nombreTabla = $wpdb->prefix.'cex_customer_options';    
    $nombreMigracion = $nombreTabla." UPDATE APIRESTOFI";
    if (comprobar_ejecucion_migracion($wpdb, $nombreMigracion)) {
        return false;
    }

    $urlApiRestNew = "https://www.cexpr.es/wspsc/apiRestOficina/v1/oficinas/listadoOficinasCoordenadas";
         
    $update  = $wpdb->prepare("UPDATE $nombreTabla SET valor='$urlApiRestNew' WHERE clave='MXPS_APIRESTOFI'", null);
    $comprobante1=$wpdb->query($update);       
    
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);    
    
    return true;    
}

function cex_migracion39($wpdb){
    $nombreTabla        = $wpdb->prefix.'cex_history';
    $nombreMigracion    = $nombreTabla." ADD CEX PRODUCT WS";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }

    $sql = "ALTER TABLE $nombreTabla 
            ADD id_bc_ws INTEGER NULL, 
            ADD mode_ship_name_ws VARCHAR(50) NULL";

    $query = $wpdb->query($sql);

    if (!$query) {
        return false;
    }

    registrar_ejecucion_migracion($wpdb,$nombreMigracion);
    return true;
}

function cex_migracion40($wpdb){
    $nombreTabla        = $wpdb->prefix.'cex_savedsenders';
    $nombreMigracion    = $nombreTabla." ALTER CITY SAVEDSENDERS";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }

    $sql = "ALTER TABLE $nombreTabla 
            MODIFY COLUMN city VARCHAR(40)";

    $query = $wpdb->query($sql);

    if (!$query) {
        return false;
    }

    registrar_ejecucion_migracion($wpdb,$nombreMigracion);
    return true;
}

function cex_migracion41($wpdb)
{     
    $nombreTabla        = $wpdb->prefix.'cex_savedmodeships';
    $nombreMigracion    = $nombreTabla." UPDATE SAVEDMODESHIPS";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }

    $sql = "ALTER TABLE $nombreTabla 
            ADD short_name VARCHAR(30) null";
    
    $result  = $wpdb->query($sql);
    
    if (!$result) {
        return false;
    }

    $updates = array(
                        [
                            'name'=>'Islas Express',
                            'id_bc'=>'26',
                            'short_name' => 'ISEXP'
                        ],
                        [
                            'name'=>'Campaña Cex',
                            'id_bc'=>'27',
                            'short_name' => 'CCEX'
                        ],
                        [
                            'name'=>'Entrega en Oficina',
                            'id_bc'=>'44',
                            'short_name' => 'EOFEL'
                        ],
                        [
                            'name'=>'Islas Documentación',
                            'id_bc'=>'46',
                            'short_name' => 'ISDOC'
                        ],
                        [
                            'name'=>'Entrega Plus',
                            'id_bc'=>'54',
                            'short_name' => '54ER'
                        ],
                        [
                            'name'=>'Entrega Plus con manipulación',
                            'id_bc'=>'55',
                            'short_name' => '55ERM'
                        ],
                        [
                            'name'=>'Paq 10',
                            'id_bc'=>'61',
                            'short_name' => 'PAQ10'
                        ],
                        [
                            'name'=>'Paq 14',
                            'id_bc'=>'62',
                            'short_name' => 'PAQ14'
                        ],
                        [
                            'name'=>'Paq 24',
                            'id_bc'=>'63',
                            'short_name' => 'PAQ24'
                        ],
                        [
                            'name'=>'Baleares Express',
                            'id_bc'=>'66',
                            'short_name' => 'BAL'
                        ],
                        [
                            'name'=>'Canarias Express',
                            'id_bc'=>'67',
                            'short_name' => 'CANE'
                        ],
                        [
                            'name'=>'Canarias Aéreo',
                            'id_bc'=>'68',
                            'short_name' => 'CANA'
                        ],
                        [
                            'name'=>'Canarias Marítimo',
                            'id_bc'=>'69',
                            'short_name' => 'CANM'
                        ],
                        [
                            'name'=>'Portugal Óptica',
                            'id_bc'=>'73',
                            'short_name' => 'CEXPOROPT'
                        ],
                        [
                            'name'=>'Paquetería Ópticas',
                            'id_bc'=>'76',
                            'short_name' => 'PQOP'
                        ],
                        [
                            'name'=>'Islas Marítimo',
                            'id_bc'=>'79',
                            'short_name' => 'ISEST'
                        ],
                        [
                            'name'=>'Internacional Estándar',
                            'id_bc'=>'90',
                            'short_name' => 'IE'
                        ],
                        [
                            'name'=>'Internacional Express',
                            'id_bc'=>'91',
                            'short_name' => 'IEX'
                        ],
                        [
                            'name'=>'Paq Empresa 14',
                            'id_bc'=>'92',
                            'short_name' => 'PAQE14'
                        ],
                        [
                            'name'=>'ePaq 24',
                            'id_bc'=>'93',
                            'short_name' => 'ePAQ24'
                        ],
                    );

    $count = 0;
    foreach ($updates as $update) {
        $short_name = $update['short_name'];
        $name       = $update['name'];
        $id_bc      = $update['id_bc'];

        $query = "UPDATE $nombreTabla s SET s.short_name ='$short_name', s.name='$name' WHERE s.id_bc=$id_bc;";
        $result  = $wpdb->query($query);
        $count += $result;
    }

    if ($count == 20) {
        registrar_ejecucion_migracion($wpdb,$nombreMigracion);
        return true;
    } else {
        return false;
    }
}

function cex_migracion42($wpdb)
{
    $nombreTabla        = $wpdb->prefix.'cex_customer_options';
    $nombreMigracion    = $nombreTabla." MODIFY MXPS_WSURL_REST";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }

    $MXPS_WSURL_NEW = "https://www.cexpr.es/wspsc/apiRestGrabacionEnviok8s/json/grabacionEnvio/V2";
    $clave          = "MXPS_WSURL_REST";

    $sql = "UPDATE $nombreTabla o 
                        SET o.valor ='$MXPS_WSURL_NEW'
                        WHERE o.clave='$clave';";

    $result  = $wpdb->query($sql);

    if (!$result) {
        return false;
    } else {
        registrar_ejecucion_migracion($wpdb,$nombreMigracion);
        return true;
    }
}

function cex_migration43($wpdb){
    $nombreTabla        = $wpdb->prefix.'cex_savedships';
    $nombreMigracion    = $nombreTabla." ADD AT PORTUGAL";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }

    $sql = "ALTER TABLE $nombreTabla
            ADD at_portugal VARCHAR(50) NULL";
    $query = $wpdb->query($sql);
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);
    
    if (!$query) {
        return false;
    }

    return true;
}

function cex_migration44($wpdb){
    $nombreTabla        = $wpdb->prefix.'cex_savedships';
    $nombreMigracion    = $nombreTabla." CHANGE DECIMAL KG";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }

    $sql = "ALTER TABLE $nombreTabla
            MODIFY COLUMN kg DECIMAL(10,3)";
    $query = $wpdb->query($sql);
    registrar_ejecucion_migracion($wpdb, $nombreMigracion);

    if (!$query) {
        return false;
    }

    return true;
}

function cex_migration45($wpdb){  
    $nombreTabla        = $wpdb->prefix.'cex_customer_options';
    $nombreMigracion    = $nombreTabla." ADD_CHECK_LOG";

    if (comprobar_ejecucion_migracion($wpdb,$nombreMigracion)) {
        return true;
    }
    $wpdb->insert($nombreTabla, array(
        'clave'     => "MXPS_CHECK_LOG",
        'valor'     => 'true',
    ));   

    if (!$wpdb) {
        return false;
    }
    registrar_ejecucion_migracion($wpdb,$nombreMigracion);
    return true;   
}

function cex_migration46($wpdb){  

    $nombreTabla = $wpdb->prefix.'cex_envio_cron';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
        id_envio_cron INT(11) NOT NULL AUTO_INCREMENT,
        peticion_envio LONGTEXT NOT NULL,
        respuesta_envio TEXT NULL,
        codError INT(11) NULL,
        descError VARCHAR(80) NULL,
        codCliente VARCHAR(20) NULL, 
        created_at DATETIME NOT NULL,
        updated_at DATETIME NULL,
        deleted_at DATETIME NULL,
        PRIMARY KEY (id_envio_cron)       
        )";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA SAVEDSHIPS";
}

function cex_migration47($wpdb){  

    $nombreTabla = $wpdb->prefix.'cex_respuesta_cron';
    $nombreTablaPadre = $wpdb->prefix.'cex_envio_cron';
    if (comprobar_ejecucion_migracion($wpdb, $nombreTabla)) {
        return false;
        //return "$nombreTabla -- YA HA SIDO EJECUTADA";
    }
    $sql= "CREATE TABLE IF NOT EXISTS $nombreTabla (
        id_respuesta_cron INT(11) NOT NULL AUTO_INCREMENT,
        nEnvioCliente LONGTEXT NOT NULL,
        referencia LONGTEXT NOT NULL,
        codigoIncidencia INT(11) NULL, 
        descripcionIncidencia VARCHAR(80) NULL, 
        codigoEstado INT(11) NULL, 
        descripcionEstado VARCHAR(80) NULL, 
        estadoAntiguo INT(11) NULL,        
        created_at DATETIME NOT NULL,
        updated_at DATETIME NULL,
        deleted_at DATETIME NULL,
        id_envio_cron INT(11) NOT NULL,      
        PRIMARY KEY (id_respuesta_cron),
        FOREIGN KEY(id_envio_cron) REFERENCES $nombreTablaPadre(id_envio_cron)
        )";
    $created = dbDelta($sql);
    registrar_ejecucion_migracion($wpdb, $nombreTabla);
    return true;
    //return "SE HA CREADO LA TABLA SAVEDSHIPS";
}

function actualizarTablasPrincipales($wpdb, $nombreTablaVieja, $nombreTablaAuxiliar, $nombreTabla)
{
    if (migrarDatos($wpdb, $nombreTablaVieja, $nombreTablaAuxiliar)) {
        //registrar_ejecucion_migracion($wpdb, $nombreTablaAuxiliar);
        if (cambiarNombreTabla($wpdb, $nombreTablaAuxiliar, $nombreTabla)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function migrarDatos($wpdb, $nombreTablaVieja, $nombreTablaAuxiliar)
{
    registrar_ejecucion_migracion($wpdb, "migrarDatos  --".$nombreTablaVieja." ==> ".$nombreTablaAuxiliar);
    try {
        $datos = $wpdb->get_results("SELECT * FROM $nombreTablaVieja", ARRAY_A);
        if (isset($datos) && count($datos)>0) {
            $i=1;
            foreach ($datos as $dato) {
                if ($wpdb->insert($nombreTablaAuxiliar, $dato)) {
                    if (count($datos)<=$i) {
                        registrar_ejecucion_migracion($wpdb, 'MIGRACION DATOS DE '.$nombreTablaVieja.' A '.$nombreTablaAuxiliar);
                        if ($wpdb->query("DROP TABLE IF EXISTS $nombreTablaVieja")) {
                            if ($nombreTablaVieja!=$wpdb->prefix.'cex_migrations') {
                                registrar_ejecucion_migracion($wpdb, 'DROP TABLE IF EXISTS '.$nombreTablaVieja);
                            } else {
                                registrar_ejecucion_migracion($wpdb, 'DROP TABLE IF EXISTS '.$nombreTablaVieja.',cex_aux_migrations');
                            }
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
                $i++;
            }
        } else {
            if ($wpdb->query("DROP TABLE IF EXISTS $nombreTablaVieja")) {
                registrar_ejecucion_migracion($wpdb, 'DROP TABLE IF EXISTS '.$nombreTablaVieja);
                return true;
            } else {
                return false;
            }
            return false;
        }
    } catch (Exception $e) {
        registrar_ejecucion_migracion($wpdb, 'EXCEPTION -- DROP TABLE IF EXISTS '.$nombreTablaVieja);

        return false;
    }
}

function cambiarNombreTabla($wpdb, $nombreTablaAuxiliar, $nombreTabla)
{
    if ($wpdb->query("RENAME TABLE $nombreTablaAuxiliar TO $nombreTabla;")) {
        registrar_ejecucion_migracion($wpdb, 'Cambio de nombre: '.$nombreTablaAuxiliar.' a '.$nombreTabla);
        return true;
    } else {
        return false;
    }
}


