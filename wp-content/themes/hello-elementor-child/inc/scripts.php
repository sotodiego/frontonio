<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

function encolar_contenido_personalizado(){
    wp_enqueue_style('custom_style', get_stylesheet_directory_uri() . '/assets/css/custom-style.css');
    wp_enqueue_script( 'custom_script', get_stylesheet_directory_uri() . '/assets/js/custom-script.js', array( 'jquery' ), '1.0.0', true );
}
add_action("wp_enqueue_scripts","encolar_contenido_personalizado",15);


function add_file_types_to_uploads($file_types){
    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $new_filetypes );
    return $file_types;
    }
add_filter('upload_mimes', 'add_file_types_to_uploads');


