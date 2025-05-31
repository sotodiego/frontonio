<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SP_Widget_Antes_Despues extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sp_widget_antes_despues';
    }

    public function get_title() {
        return __( 'Comparación Primera y Segunda', 'sp-elementor-widgets' );
    }

    public function get_icon() {
        return 'fa fa-arrows-h';
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

        // Control para la imagen "Primera"
        $this->add_control(
            'imagen_primera',
            [
                'label'   => __( 'Imagen Primera', 'sp-elementor-widgets' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        // Control para la imagen "Segunda"
        $this->add_control(
            'imagen_segunda',
            [
                'label'   => __( 'Imagen Segunda', 'sp-elementor-widgets' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings         = $this->get_settings_for_display();
        $imagen_primera_url = $settings['imagen_primera']['url'];
        $imagen_segunda_url = $settings['imagen_segunda']['url'];

        // Generamos un ID único para evitar conflictos si se usa más de una vez el widget
        $widget_id = 'sp-before-after-' . $this->get_id();
        ?>
        <!-- Estilos -->
        <style>
            .elementor-widget-sp_widget_antes_despues{
                width: 100%;
            }
            /* Contenedor responsive usando padding-bottom para mantener la proporción */
            #<?php echo esc_attr( $widget_id ); ?> {
                position: relative;
                width: 100%;
                max-width: 1140px;
                margin: auto;
                border-radius: 10px;
                overflow: hidden;
                height: 100%;
            }

            /* Las imágenes se posicionan absolutamente para ocupar todo el contenedor */
            #<?php echo esc_attr( $widget_id ); ?> img {
                position: absolute;
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: left;
            }

            /* La imagen "primera" inicia ocupando el 50% del ancho */
            #<?php echo esc_attr( $widget_id ); ?> .imagen-primera {
                width: 50%;
            }

            /* Estilos del slider */
            #<?php echo esc_attr( $widget_id ); ?> .slider {
                position: absolute;
                top: 0;
                left: 50%;
                width: 5px;
                height: 100%;
                background: white;
                cursor: ew-resize;
                transform: translateX(-50%);
            }

            #<?php echo esc_attr( $widget_id ); ?> .slider::before {
                content: "";
                position: absolute;
                top: 50%;
                left: -10px;
                width: 20px;
                height: 20px;
                background: white;
                border-radius: 50%;
                border: 2px solid black;
                transform: translateY(-50%);
            }

            /* Evitar la selección de texto e imágenes */
            #<?php echo esc_attr( $widget_id ); ?> * {
                user-select: none;
                -webkit-user-drag: none;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
            }

            #<?php echo esc_attr( $widget_id ); ?> img {
                pointer-events: none;
            }
        </style>

        <!-- Estructura HTML -->
        <div id="<?php echo esc_attr( $widget_id ); ?>">
            <img src="<?php echo esc_url( $imagen_segunda_url ); ?>" class="imagen imagen-segunda" alt="Segunda">
            <img src="<?php echo esc_url( $imagen_primera_url ); ?>" class="imagen imagen-primera" alt="Primera">
            <div class="slider"></div>
        </div>

        <!-- Script para la funcionalidad -->
        <script>
    (function(){
        var container = document.getElementById('<?php echo esc_js( $widget_id ); ?>');
        var slider = container.querySelector(".slider");
        var imagenPrimera = container.querySelector(".imagen-primera");

        var isDragging = false;

        // Función para actualizar la posición según la coordenada x
        function actualizarPosicion(x) {
            var width = container.offsetWidth;
            if (x < 0) { x = 0; }
            if (x > width) { x = width; }
            imagenPrimera.style.width = x + "px";
            slider.style.left = x + "px";
        }

        // Reinicia la posición al 50% (en porcentaje) cuando se cambia el tamaño de la ventana
        function resetearPosicion() {
            imagenPrimera.style.width = "50%";
            slider.style.left = "50%";
        }

        // Eventos para desktop
        slider.addEventListener("mousedown", function(e) {
            isDragging = true;
        });

        document.addEventListener("mouseup", function(e) {
            isDragging = false;
        });

        document.addEventListener("mousemove", function(e) {
            if (!isDragging) return;
            var rect = container.getBoundingClientRect();
            var x = e.clientX - rect.left;
            actualizarPosicion(x);
        });

        // Eventos para dispositivos táctiles (móviles y tabletas)
        slider.addEventListener("touchstart", function(e) {
            isDragging = true;
            e.preventDefault();
        });

        document.addEventListener("touchend", function(e) {
            isDragging = false;
        });

        document.addEventListener("touchmove", function(e) {
            if (!isDragging) return;
            var touch = e.touches[0];
            var rect = container.getBoundingClientRect();
            var x = touch.clientX - rect.left;
            actualizarPosicion(x);
        });

        // Reinicia la posición al 50% al cambiar el tamaño de la ventana
        window.addEventListener("resize", function() {
            resetearPosicion();
        });
        })();
    </script>

        <?php
    }
}
