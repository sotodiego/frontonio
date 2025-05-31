<?php

$user_id = get_current_user_id();
$user_data = get_userdata($user_id);
$user_roles = $user_data->roles;
?>
<!DOCTYPE html>
<html class="loading" lang="es" data-textdirection="ltr">
	<head>
	    <?=$adpnsy->header($info, false);?>
	</head>
	<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns" data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">
        <header class="page-topbar" id="header">
            <div class="navbar navbar-fixed">
                <nav class="navbar-main navbar-color nav-collapsible sideNav-lock navbar-light">
                    <div class="nav-wrapper">
                        <ul class="navbar-list right">
                            <li>
                                <?php 
                                    if ( function_exists('pll_the_languages') ) {
                                        $languages = pll_the_languages(array('raw' => 1));
                                        $link = '<a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="language-dropdown">';
                                        $menu = '<ul class="dropdown-content" id="language-dropdown">';
                                        
                                        foreach($languages as $language) {
                                            $translated_url = '';
                                            if (is_page()) {
                                                $translated_id = pll_get_post(get_the_ID(), $language['slug']);
                                                if ($translated_id) {
                                                    $translated_url = get_permalink($translated_id);
                                                }
                                            } elseif (is_front_page()) {
                                                $translated_url = pll_home_url($language['slug']);
                                            }

                                            $url = $translated_url ? $translated_url : $language['url'];

                                            if ($language['current_lang']) {
                                                $link .= '<img src="' . $language['flag'] . '" alt="' . $language['name'] . '" /></a>';
                                            } else {
                                                $menu .= '<li><a href="' . $url . '"><img src="' . $language['flag'] . '" alt="' . $language['name'] . '" /></a></li>';
                                            }
                                        }

                                        echo $link . $menu . "</ul>";
                                    }
                                ?>
                            </li>
                            <li>
                                <a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="profile-dropdown">
                                    <span class="avatar-status avatar-online">
                                        <img src="<?=$adpnsy->avatar();?>" alt="avatar">
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <?php do_action("menu_perfil_adps"); ?>
                    </div>
                </nav>
            </div>
        </header>
        <aside class="sidenav-main nav-expanded nav-lock nav-collapsible sidenav-dark sidenav-active-rounded">
            <div class="brand-sidebar">
                <h1 class="logo-wrapper">
                    <a class="brand-logo darken-1" href="<?=get_site_url();?>">
                        <img class="hide-on-med-and-down" id="menu_toggle_sp_logo_full" src="<?=$info->logo;?>" alt="<?= get_bloginfo('name'); ?>" />
                        <img class="hide-on-med-and-down" id="menu_toggle_sp_logo" src="<?=$info->favicon;?>" alt="<?= get_bloginfo('name'); ?>" />
                        <img class="show-on-medium-and-down hide-on-med-and-up" src="<?=$info->logo;?>" alt="<?= get_bloginfo('name'); ?>" />
                        <span class="logo-text hide-on-med-and-down"><?=$info->logo_texto;?></span>
                    </a>
                </h1>
            </div>
            <?php  do_action("menu_adps"); ?>
                <div class="navigation-background"></div>
                <a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only" href="#" data-target="slide-out">
                    <i class="material-icons">menu</i>
                </a>
            <div class="hide-on-med-and-down"  id="menu_toggle_sp"><i class="material-icons">play_circle</i></div>
        </aside>
        <div id="main">
        	<?=$adpnsy->contenido();?>
        </div>

        <footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow">
            <div class="footer-copyright">
                <div class="container">
                    <span>&copy; <?= date("Y") ?> 
                        <a href="<?= esc_url(home_url('/')); ?>" target="_blank"><?= get_bloginfo('name'); ?></a> 
                        All rights reserved.
                    </span>
                    <span class="right hide-on-small-only">
                        <img class="footer-logo" src="<?= ADPNSY_URL; ?>/app-assets/img/favicon-asp.png" />
                        Diseñado y desarrollado por 
                        <a href="https://agenciasp.com/" target="_blank">Agencia SP S.L</a>
                    </span>
                </div>
            </div>
        </footer>
        
        <?=$adpnsy->footer(false);?>
	</body>
</html>