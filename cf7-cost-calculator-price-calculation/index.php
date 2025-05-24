<?php
/**
* Plugin Name: Cost Calculator for Contact Form 7 - Price Calculation
* Plugin URI: https://add-ons.org/plugin/contact-form-7-cost-calculator/
* Requires Plugins: contact-form-7
* Description: Create forms with field values calculated based in other form field values for contact form 7
* Author: add-ons.org
* Version: 10.1
* Domain Path: /languages
* Author URI: https://add-ons.org/
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define( 'CT_7_COST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CT_7_COST_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
class Yeeaddons_CF7_Cost_Calculator_Init{
    function __construct(){
        add_action( 'plugins_loaded', array($this,'loads') );
    }
    function loads(){
        include CT_7_COST_PLUGIN_PATH."backend/index.php";
        include CT_7_COST_PLUGIN_PATH."backend/checkbox.php";
        include CT_7_COST_PLUGIN_PATH."backend/select.php";
        include CT_7_COST_PLUGIN_PATH."backend/number_format.php";
        include CT_7_COST_PLUGIN_PATH."frontend/index.php";
        include_once CT_7_COST_PLUGIN_PATH."yeekit/document.php";
    }
}
new Yeeaddons_CF7_Cost_Calculator_Init;