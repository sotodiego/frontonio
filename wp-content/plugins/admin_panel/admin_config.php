<?php 
	$colores = array("red","pink","purple","deep-purple","indigo","blue","light-blue","cyan","teal","green","light-green","lime","yellow","amber","orange","deep-orange","brown","grey","blue-grey", "black", "white", "transparent");
	
	$tonos = array("lighten-5","lighten-4","lighten-3","lighten-2","lighten-1","darken-1","darken-2","darken-3","darken-4","accent-1","accent-2","accent-3","accent-4");

?>

<div class="wrap">
	<h1>Configuración de template</h1>
	<form method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="titulo">Titulo del sitio</label>
					</th>
					<td>
						<input autocomplete="off" required name="titulo" type="text" id="titulo" value="<?=$info->titulo?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="description">Descripción del sitio</label>
					</th>
					<td>
						<input autocomplete="off" required name="description" type="text" id="description" value="<?=$info->description?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="autor">Autor del sitio</label>
					</th>
					<td>
						<input autocomplete="off" required name="autor" type="text" id="autor" value="<?=$info->autor?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="logo">Logo Color (100x47)</label>
					</th>
					<td>
						<input name="logo" id="logo_v" type="text" value="<?=$info->logo;?>" class="regular-text" readonly>
						<input name="logo_vn" id="logo_vn" type="hidden" value="<?=$info->logo_vn;?>">
						<button class="button" id="logo">Seleccionar Imagen</button>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="logo_blanco">Logo Menú (100x47)</label>
					</th>
					<td>
						<input name="logo_blanco" id="logo_blanco_v" type="text" value="<?=$info->logo_blanco;?>" class="regular-text" readonly>
						<input name="logo_blanco_vn" id="logo_blanco_vn" type="hidden" value="<?=$info->logo_blanco_vn?>">
						<button class="button" id="logo_blanco">Seleccionar Imagen</button>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="logo_texto">Logo Texto</label>
					</th>
					<td>
						<input autocomplete="off" required name="logo_texto" type="text" id="logo_texto" value="<?=$info->logo_texto?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="icon_apple">Apple touch ico (1024x1024)</label>
					</th>
					<td>
						<input name="icon_apple" id="icon_apple_v" type="text" value="<?=$info->icon_apple?>" class="regular-text" readonly>
						<input name="icon_apple_vn" id="icon_apple_vn" type="hidden" value="<?=$info->icon_apple_vn?>">
						<button class="button" id="icon_apple">Seleccionar Imagen</button>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="favicon">Favicon (32x32)</label>
					</th>
					<td>
						<input name="favicon" id="favicon_v" type="text" value="<?=$info->favicon?>" class="regular-text" readonly>
						<input name="favicon_vn" id="favicon_vn" type="hidden" value="<?=$info->favicon_vn?>">
						<button class="button" id="favicon">Seleccionar Imagen</button>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="bglogin">Imagen de fondo para login (1920x1080)</label>
					</th>
					<td>
						<input name="bglogin_vn" id="bglogin_vn" type="text" value="<?=$info->bglogin_vn?>" class="regular-text" readonly>
						<input name="bglogin" id="bglogin_v" type="hidden" value="<?=$info->bglogin?>">
						<button class="button" id="bglogin">Seleccionar Imagen</button>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="lglogin">Logo para login (310x60)</label>
					</th>
					<td>
						<input name="lglogin_vn" id="lglogin_vn" type="text" value="<?=$info->lglogin_vn?>" class="regular-text" readonly>
						<input name="lglogin" id="lglogin_v" type="hidden" value="<?=$info->lglogin?>">
						<button class="button" id="lglogin">Seleccionar Imagen</button>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Página principal</label>
					</th>
					<td>
						<select class="select2 regular-text" name='login' id="login">
							<?php  foreach ($pages as $p) {$s = "";if($info->login == $p->ID) $s=" selected";echo "<option value='{$p->ID}'$s>{$p->post_title}</option>";}?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Página de Registro</label>
					</th>
					<td>
						<select class="select2 regular-text" name='Register' id="Register">
							<option value="0">--Desactivado--</option>
							<?php  foreach ($pages_rg as $p) {$s = "";if($info->Register == $p->ID) $s=" selected";echo "<option value='{$p->ID}'$s>{$p->post_title}</option>";}?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Página de recuperación</label>
					</th>
					<td>
						<select class="select2 regular-text" name='Recovery' id="Recovery">
							<option value="0">--Desactivado--</option>
							<?php  foreach ($pages_rc as $p) {$s = "";if($info->Recovery == $p->ID) $s=" selected";echo "<option value='{$p->ID}'$s>{$p->post_title}</option>";}?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Página de políticas</label>
					</th>
					<td>
						<select class="select2 regular-text" name='Politicas' id="Politicas">
							<option value="0">--Desactivado--</option>
							<?php  foreach ($pages_al as $p) {$s = "";if($info->Politicas == $p->ID) $s=" selected";echo "<option value='{$p->ID}'$s>{$p->post_title}</option>";}?>
						</select>
					</td>
				</tr>
				<tr style="border-top: 1px solid #ddd">
					<th scope="row">
						<label>Estilo de botón</label>
					</th>
					<td><button id="test_button" class="btn <?=$info->botton_color?> <?=$info->botton_color_t?> <?=$info->botton_fondo?> <?=$info->botton_tono?>">Prueba de color</button></td>
				</tr>
				<tr>
					<th scope="row">
						<label>Fondo</label>
					</th>
					<td>
						<select class="select2 regular-text" name='botton_fondo' id="botton_fondo">
							<option></option>
							<?php foreach ($colores as $c) {$s = "";if($info->botton_fondo == $c) $s=" selected";echo "<option$s>$c</option>";}?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Tono</label>
					</th>
					<td>
						<select class="select2 regular-text" name="botton_tono" id="botton_tono">
							<option></option>
							<?php foreach ($tonos as $c) {$s = "";if($info->botton_tono == $c) $s=" selected";echo "<option$s>$c</option>";}?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label>Color de texto</label>
					</th>
					<td>
						<select class="select2 regular-text" name='botton_color' id="botton_color">
							<option></option>
							<?php foreach ($colores as $c) {$s = "";if($info->botton_color == $c . "-text") $s=" selected";echo "<option$s>$c-text</option>";}?>
						</select>
					</td>
				</tr>

				<tr style="border-bottom: 1px solid #ddd">
					<th scope="row">
						<label>Tono de texto</label>
					</th>
					<td>
						<select class="select2 regular-text" name='botton_color_t' id="botton_color_t">
							<option></option>
							<?php foreach ($tonos as $c) {$s = "";if($info->botton_color_t == "text-" . $c) $s=" selected";echo "<option$s>text-$c</option>";}?>
						</select>
					</td>
				</tr>
				
			</tbody>
		</table>
		<input class="button button-primary" type="submit" value="Guardar">
	</form>
</div>