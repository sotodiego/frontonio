<?php
/**
 * This program is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/.
 */
/**
 * Clase que prefiltra el tpl de Smarty antes de pasársela a la función $smarty->display.
 * Con esta clase se puede adaptar el contenido de una plataforma a otra.
 */
class Prefilter
{

    /**
     *   Transforma las variables de tipo {l s='settings'  mod='correosoficial'} a variables
     *   válidas tipo de Smarty {$settings}
     *
     *   CASOS:
     *   {$variable_traducida}<br>
     *   {l s='settings'  mod='correosoficial'}<br>
     *   {l s='prueba' mod='correosoficial'}
     *   {l s='pruebamodcorreosoficial'   mod='correosoficial'}
     *   {l s=' CUSTOMER DATA' mod='correosoficial'}
     */
    public static function preFilterConstants($tpl_output, Smarty_Internal_Template $template)
    {
        global $co_page;
        global $post;

        $input = $tpl_output;

        /**
         * Transforma  {l s='settings'  mod='correosoficial'} en {$settings}
         * y entrega la salida a Smarty.
         */
        $tpl_output = preg_replace_callback(
            "/{l s=\'(.*?)\'.*?mod=\'correosoficial\'}/s",
            function ($m) {
                $str = str_replace("/", "_", $m[1]);
                $str = str_replace("'", "", $str);
                $str = str_replace(array("\r", "\n"), '', $str);
                $id = "{" . "$" . preg_replace('/[^a-zA-Z\r\n]/', '_', $str) . "}";
                return $id;
            },
            $tpl_output
        );

        // Encuentra ocurrencias de {l s=''} y los almacena en $matches
        preg_match_all("/{l s='(.*?)'}/s", $input, $matches);

        for ($i = 0; $i < count($matches[0]); $i++) {
            if (!strstr($matches[0][$i], "{l s=")) {
                unset($matches[0][$i]);
            }
        }

        /**
         * Crea las sentencias $tpl->assign con los literales para Smarty
         */
        $assign = '';
        foreach ($matches[1] as $match) {
            $match = str_replace(" mod='correosoficial", '', $match);

            $str = str_replace("/", "_", $match);
            $str = str_replace("'", "", $str);
            $str = str_replace(array("\r", "\n"), '', $str);
            $id = preg_replace('/[^a-zA-Z\r\n]/', '_', $str);

            $assign .= "\n" . '$this->smarty->assign("' . $id . '", __("' . $str . '","correosoficial"));' . "\n";
        }

        $page = isset($_GET['page'])?$_GET['page']:null;

        // Escribimos el assign que será leído por los controllers (Home, Settings, Utilites)
        if ($page == 'home') {
            $fileLang = plugin_dir_path(__DIR__) . '../langs/homeLang.php';
        } elseif ($page == 'settings' || $page == 'correosoficial') {
            $fileLang = plugin_dir_path(__DIR__) . '../langs/settingsLang.php';
        } elseif ($page == 'utilities') {
            $fileLang = plugin_dir_path(__DIR__) . '../langs/utilitysLang.php';
        } elseif ($co_page == 'checkout') { // WIP
            $fileLang = plugin_dir_path(__DIR__) . '../langs/checkoutLang.php';
        } elseif (preg_match('/admin.*\.tpl/', $template->compiled->filepath)) {
            $fileLang = plugin_dir_path(__DIR__) . '../langs/orderLang.php';
        } elseif ($co_page == 'my_account') { // WIP
            $fileLang = plugin_dir_path(__DIR__) . '../langs/orderDetailLang.php';
        } elseif ($page == 'notifications') {
            $fileLang = plugin_dir_path(__DIR__) . '../langs/notificationsLang.php';
        } else {
            return $tpl_output;
        }
        $fp = fopen($fileLang, "a+");

        /** Se añade <?php al inicio dle fichero */
        if (!strstr(file_get_contents($fileLang), "<?php")) {
            fwrite($fp, "<?php\r\n");
        }

        fwrite($fp, $assign);
        fclose($fp);

        // Se limpia el fichero de repeticiones
        self::cleanAssignFile($fileLang);

        return $tpl_output;
    }

    /**
     * Elimina las repeticiones de los ficheros de asignaciones langs/*
     * @param $file ruta del fichero a limpiar
     */
    private static function cleanAssignFile($file)
    {
        $lines = file($file);
        $lines = array_unique($lines);
        file_put_contents($file, implode($lines));
    }
}
