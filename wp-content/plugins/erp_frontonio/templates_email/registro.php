<?php

    $remplace = array(
        "{{url}}"           => get_option("siteurl"),
        "{{logo}}"          => $info->lglogin_vn,
        "{{bg}}"            => $info->bglogin_vn,
        "{{title}}"         => get_option("blogname"),
        "{{titulo}}"        => "Bienvenido a " . get_option("blogname"),
        "{{contenido}}"     => "
            ¡Hola $_data->Nombre!
            <br>
            Nos complace informarte que tu cuenta ha sido creada exitosamente en " . get_option("blogname") . ".<br>
            <br>
            Por favor, establece tu contraseña haciendo <a href='$_data->Link' style='color:#B41F2F;text-decoration:underline;'>clic aquí</a><br>
			<br>
			<br>
			<b>NOTA:</b>Si tienes problema con el enlace, por favor copia y pega el siguiente enlace: $_data->Link.
            <br>
            <br>
            Recibe un cordial saludo de todo el equipo de ".get_option("blogname").".<br><br>",
        "{{coping}}"        => "Copyright © " . date("Y") . " " . get_option("blogname"),
        "{{info_footer}}"   => "",
        "{{address}}"       => "El contenido de este correo electrónico es confidencial, es parte de una conversación usted y " . get_option("blogname") . ". Está estrictamente prohibido compartir cualquier parte de este mensaje con terceros, sin el consentimiento escrito del remitente. Si has recibido este mensaje por error, por favor responde a este mensaje y procede con su eliminación, para que podamos asegurarnos de que este error no vuelva a ocurrir en el futuro.",
        "{{site}}"          => get_option("siteurl"),
    );

    $_config = array(
        "Nombre"        => "[AJE ".get_option("blogname")."]",
        "Correo"        => "no-reply@bodegasfrontonio.com"
    );
?>
