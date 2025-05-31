<?php

    $remplace = array(
        "{{url}}"           => get_option("siteurl"),
        "{{logo}}"          => $info->lglogin_vn,
        "{{bg}}"            => $info->bglogin_vn,
        "{{title}}"         => get_option("blogname"),
        "{{titulo}}"        => "Restaura tu contraseña",
        "{{contenido}}"     => "
            ¡Hola $_data->Nombre! ¿Has solicitado restaurar la contraseña?
            <br>
            Sí es así por favor haz <a href='$_data->Link' style='color:#B41F2F;text-decoration:underline;'>clic aquí</a><br>
			<br>
			Si no has solicitado restaurar la contraseña de tu cuenta de ".get_option("blogname").", puedes ignorar este mensaje.
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
