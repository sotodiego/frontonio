<?php

	$remplace = array(
		"{{url}}" 			=> get_option( "siteurl" ),
		"{{logo}}" 			=> $info->lglogin_vn,
		"{{bg}}"			=> $info->bglogin_vn,
		"{{title}}" 		=> get_option( "blogname" ),
		"{{titulo}}" 		=> "",
		"{{contenido}}" 	=> "
			Hola $_data->Nombre,<br>
			<br>
			¿Has solicitado la recuperación de contraseña?, Sí es así por favor haga click <a href='$_data->Link' style='color:#e40248;text-decoration:underline;'>aquí</a><br>
			<br>
			Si por el contrario no solicitó ningún cambio de contraseña, por favor ignore el presente mensaje.<br>
			<br>
			<b>NOTA:</b>Si tienes problema con el enlace por favor copia y pega el siguiente enlace: $_data->Link en tu navegador",
		"{{coping}}" 		=> "Copyright © ".date("Y")." La Finestra Lectora",
		"{{info_footer}}" 	=> "",
		"{{address}}" 		=> "El contenido de este correo electrónico es confidencial, es parte de una conversación entre $_data->Nombre y La Finestra Lectora. Está estrictamente prohibido compartir cualquier parte de este mensaje con terceros, sin el consentimiento escrito del remitente. Si ha recibido este mensaje por error, por favor responda a este mensaje y prosiga con su eliminación, para que podamos asegurarnos de que este error no ocurra otra vez en el futuro.",
		"{{site}}" 			=> "intranet.lafinestralectora.cat/",
	);

	$_config = array(
		"Nombre"		=> "[LaFinestraLectora] - Recuperación de cuenta",
		"Correo"		=> "no-reply@lafinestralectora.cat"
	);

