<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - Parameter
 * Plugin URI: http://github.com/isleshocky77/ninja-forms-parameter
 * Description: Add ability to pass a Field Value as a parameter from a shortcode and pull them back using MergeTags
 * Version: 3.0.0
 * Requires at least: 4.3
 * Tested up to: 4.7
 * Author: Stephen Ostrow <stephen@ostrow.tech>
 * Author URI: http://ostrow.tech
 * Text Domain: ninja-forms-parameter
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright 2017 Stephen Ostrow .
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    //include 'deprecated/ninja-forms-parameter.php';

} else {

    /**
     * Class NF_Parameter_Add_On
     */
    final class NF_Parameter_Add_On
    {
        const VERSION = '3.0.0';
        const SLUG    = 'parameter';
        const NAME    = 'Parameter';
        const AUTHOR  = 'Stephen Ostrow <stephen@ostrow.tech>';
        const PREFIX  = 'NF_Parameter_Add_On';

        /**
         * @var NF_Parameter_Add_On
         * @since 3.0
         */
        private static $instance;

        /**
         * Plugin Directory
         *
         * @since 3.0
         * @var string $dir
         */
        public static $dir = '';

        /**
         * Plugin URL
         *
         * @since 3.0
         * @var string $url
         */
        public static $url = '';

        /**
         * Main Plugin Instance
         *
         * Insures that only one instance of a plugin class exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 3.0
         * @static
         * @static var array $instance
         * @return NF_Parameter_Add_On NF_Parameter_Add_On Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof NF_Parameter_Add_On)) {
                self::$instance = new NF_Parameter_Add_On();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                /*
                 * Register our autoloader
                 */
                spl_autoload_register(array(self::$instance, 'autoloader'));
            }

            return self::$instance;
        }

        public function __construct()
        {
            add_action( 'admin_init', array( $this, 'setup_license') );

            add_filter( 'ninja_forms_register_merge_tags', array($this, 'register_merge_tags'));

            add_shortcode('ninja-forms-parameter', [$this, 'display_form']);
        }

        public function register_merge_tags($merge_tags)
        {
            $merge_tags['parameter'] = new NF_Parameter_Add_On_MergeTags_Parameter();

            return $merge_tags;
        }

        /*
         * Optional methods for convenience.
         */

        public function autoloader($class_name)
        {
            if (class_exists($class_name)) return;

            if ( false === strpos( $class_name, self::PREFIX ) ) return;

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

            if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }

        /*
         * Required methods for all extension.
         */
        public function setup_license()
        {
            if ( ! class_exists( 'NF_Extension_Updater' ) ) return;

            new NF_Extension_Updater( self::NAME, self::VERSION, self::AUTHOR, __FILE__, self::SLUG );
        }

        /**
         * Displays a form after setting parameters
         *
         * @param array $atts
         * @return string
         */
        public function display_form($atts = [])
        {
            if( ! isset( $atts[ 'id' ] ) ) return $this->display_no_id();

            /** @var NF_Parameter_Add_On_MergeTags_Parameter $parameterMergeTag */
            $parameterMergeTag = !isset(Ninja_Forms()->merge_tags['parameter']) ?: Ninja_Forms()->merge_tags['parameter'];

            foreach ($atts as $name => $value) {
                if ($name === 'id') {
                    continue;
                }
                $parameterMergeTag->set_merge_tags($name, $value);
            }

            ob_start();
            Ninja_Forms()->display( $atts['id'] );
            return ob_get_clean();
        }

        /**
         * TODO: Extract output to template files.
         * @return string
         */
        private function display_no_id()
        {
            $output = __( 'Notice: Ninja Forms shortcode used without specifying a form.', 'ninja-forms' );

            // TODO: Maybe support filterable permissions.
            if( ! current_user_can( 'manage_options' ) ) return "<!-- $output -->";

            // TODO: Log error for support reference.
            // TODO: Maybe display notice if not logged in.
            trigger_error( __( 'Ninja Forms shortcode used without specifying a form.', 'ninja-forms' ) );

            return "<div style='border: 3px solid red; padding: 1em; margin: 1em auto;'>$output</div>";
        }
    }

    /**
     * The main function responsible for returning The Parameter Plugin
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * @since 3.0
     * @return NF_Parameter_Add_On Parameter Instance
     */
    function NF_Parameter_Add_On()
    {
        return NF_Parameter_Add_On::instance();
    }

    NF_Parameter_Add_On();
}
