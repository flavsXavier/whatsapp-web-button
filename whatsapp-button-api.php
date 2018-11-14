<?php
    defined('ABSPATH') or die("Sem scripts kids, por favor.");
    
    /*
    * Plugin name: WhatsApp Button API
    * Description: Adiciona um botão do whatsapp configurável e estilizável para desktop e mobile. Linkado para o WhatsApp Web.
    * Version: 1.0
    * Author: Flaviano Xavier
    * Author URI: https://github.com/flavisXavier
    */
    
    define('WBAPI_URL', plugins_url('', __FILE__));
    define('WBAPI_DIR', plugin_dir_path(__FILE__));

    $wbapi = new WBAPI_Engine();
    class WBAPI_Engine {

        function __construct() {
            add_action('admin_menu', array($this, 'add__wbapi_options_page'));
            add_action('admin_init', array($this, 'load__scripts_js'));
            add_action('admin_init', array($this, 'register__wbapi_settings'));
            add_action('wp_enqueue_scripts', array($this, 'load__front_scripts_css'));
            add_action("wp_footer", array($this, 'add__wbapi_section'));
        }

        function add__wbapi_options_page() {
            add_menu_page( 'WhatsApp Button API', 'WhatsApp Button API', 'edit_posts', 'wbapi-options', array($this, 'wbapi__options_page'), 'dashicons-forms', 80 );
        }

        function wbapi__options_page() {
            require WBAPI_DIR . 'settings.php';
        }

        function load__scripts_js() {
            wp_deregister_script('jquery');
            wp_enqueue_script('jquery-wbapi', WBAPI_URL . '/js/jquery-3.3.1-min.js', array(), null, false);
            wp_enqueue_script('scripts-wbapi-page', WBAPI_URL . '/js/wbapi.scripts.js', array('jquery'), null, false);
            wp_enqueue_script('maskedinput-wbapi', WBAPI_URL . '/js/jquery.maskedinput.js', array('jquery'), null, false);
        }

        function load__front_scripts_css() {
            wp_enqueue_style('wbapi-css-icones', WBAPI_URL . '/css/wbapi.style.css', array(), null, false);
            wp_enqueue_style('wbapi-css-icones');
        }

        function register__wbapi_settings() {
            register_setting('settings__wbapi_page', 'wpp__active');
            register_setting('settings__wbapi_page', 'wpp__telefone');
            register_setting('settings__wbapi_page', 'wpp__img');
            register_setting("settings__wbapi_page", "wpp__file", "handle_file_upload");
            register_setting("settings__wbapi_page", "wpp__style");
            register_setting("settings__wbapi_page", "wpp__style_mb");
            register_setting("settings__wbapi_page", "wpp__tooltip");
            register_setting("settings__wbapi_page", "wpp__tooltip_pos");
            register_setting("settings__wbapi_page", "wpp__multi_act");
        }

        function handle_file_upload($option) {
            if(!empty($_FILES["wpp__file"]["tmp_name"])) {
                $urls = wp_handle_upload($_FILES["wpp__file"], array('test_form' => FALSE));
                $temp = $urls["url"];
                return $temp;
            }
            return $option;
        }

        
        function add__wbapi_section() {
            function unmask__wbapi_tel($numero) {
                $tel = array();
                $tel[0] = substr_replace($numero, "", 0, 1);
                $tel[1] = substr_replace($tel[0], "", 2, 1);
                $tel[2] = substr_replace($tel[1], "", 2, 1);
                $tel[3] = substr_replace($tel[2], "", 6, 1);
                return $tel[3];
            }
            if (get_option('wpp__active') === 'active') :
                $html = "";
                $telefone = unmask__wbapi_tel(get_option('wpp__telefone'));
                if ((get_option('wpp__tooltip')) && !(get_option('wpp__tooltip_pos') == 'none')) :
                    $tooltip = "data-toggle='tooltip' data-placement='". get_option('wpp__tooltip_pos') ."' title='". get_option('wpp__tooltip') ."'";
                endif;
                if (!(get_option('wpp__style') == 'none')) :
                    $html .= "<a href='https://web.whatsapp.com/send?phone=+55$telefone' target='_blank' class='d-none d-md-block'>";
                    if ($wpp__icon = get_option('wpp__file')) :
                        $html .= "<span class='wpp__section desktop ". get_option('wpp__style') ."'><img src='$wpp__icon' $tooltip alt=''></span>";
                    elseif (get_option('wpp__img') == 'wpp__padrao') :
                        $html .= "<span class='wpp__section fab desktop ". get_option('wpp__style') ."' $tooltip><i class='fa fa-whatsapp' aria-hidden='true'></i></span>";
                    else: 
                        $html .= "<span class='wpp__section desktop ". get_option('wpp__style') ."'><img src='". WBAPI_URL . "/images/whatsapp/wpp__businesst.png" ."' $tooltip alt=''></span>";
                    endif;
                    $html .= "</a>";
                endif;
                if (!(get_option('wpp__style_mb') == 'none')) :
                    $html .= "<a href='https://web.whatsapp.com/send?phone=+55$telefone' target='_blank' class='d-md-none'>";
                    if ($wpp__icon = get_option('wpp__file')) :
                        $html .= "<span class='wpp__section mobile ". get_option('wpp__style_mb') ."'><img src='$wpp__icon' $tooltip alt=''></span>";
                        elseif (get_option('wpp__img') == 'wpp__padrao') :
                            $html .= "<span class='wpp__section fab mobile ". get_option('wpp__style_mb') ."' $tooltip><i class='fa fa-whatsapp' aria-hidden='true'></i></span>";
                        else: 
                            $html .= "<span class='wpp__section mobile ". get_option('wpp__style_mb') ."'><img src='". WBAPI_URL . "/images/whatsapp/wpp__business.png" ."' $tooltip alt=''></span>";
                        endif;
                    $html .= "</a>";
                endif;
            endif;
            ?>
            <script>
                $(function () {
                    $('[data-toggle="tooltip"]').tooltip();
                });
            </script>
            <?php
            echo $html;
        }

    }

?>