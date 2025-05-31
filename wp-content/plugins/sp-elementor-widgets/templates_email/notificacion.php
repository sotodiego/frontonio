<?php

	$remplace = array(
		"{{url}}" 			=> get_option( "siteurl" ),
		"{{logo}}" 			=> KWKRS_URL . "app-assets/img/logo.png",
		"{{title}}" 		=> get_option( "blogname" ),
		"{{titulo}}" 		=> "Notificación de BASE",
		"{{contenido}}" 	=> $_data->mensaje,
		"{{coping}}" 		=> "Copyright © ".date("Y")." BASE",
		"{{info_footer}}" 	=> "BASE",
		"{{address}}" 		=> "El contenido de este correo electrónico es confidencial, es parte de una conversación entre $_data->Nombre y AgenciaSP. Está estrictamente prohibido compartir cualquier parte de este mensaje con terceros, sin el consentimiento escrito del remitente. Si ha recibido este mensaje por error, por favor responda a este mensaje y prosiga con su eliminación, para que podamos asegurarnos de que este error no ocurra otra vez en el futuro.",
		"{{site}}" 			=> "www.agenciasp.com",
	);

	$_config = array(
		"Nombre"		=> "[BASE] - Notificación",
		"Correo"		=> "no-reply@agenciasp.com"
	);