<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SP_Widget_Ejemplo extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sp_widget_ejemplo';
    }

    public function get_title() {
        return __( 'Widget Ejemplo', 'sp-elementor-widgets' );
    }

    public function get_icon() {
        return 'fa fa-star';
    }

    public function get_categories() {
        return [ 'sp-elementor-widgets' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Contenido', 'sp-elementor-widgets' ),
            ]
        );

        $this->add_control(
            'text_example',
            [
                'label'   => __( 'Texto de ejemplo', 'sp-elementor-widgets' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Hola Mundo', 'sp-elementor-widgets' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        echo '<div class="SP-widget-ejemplo">';
        echo esc_html( $settings['text_example'] );
        echo '</div>';
    }
}
