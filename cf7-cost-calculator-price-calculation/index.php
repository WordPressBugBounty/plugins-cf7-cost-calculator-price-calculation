<?php
/**
* Plugin Name: Contact Form 7 Cost Calculator - Price Calculation
* Plugin URI: https://add-ons.org/plugin/contact-form-7-cost-calculator/
* Requires Plugins: contact-form-7
* Description: Create forms with field values calculated based in other form field values for contact form 7
* Author: add-ons.org
* Version: 9.6.7
* Domain Path: /languages
* Text Domain: cf7-cost-calculator-price-calculation
* Author URI: https://add-ons.org/
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define( 'CT_7_COST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CT_7_COST_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
include CT_7_COST_PLUGIN_PATH."backend/index.php";
include CT_7_COST_PLUGIN_PATH."backend/checkbox.php";
include CT_7_COST_PLUGIN_PATH."backend/select.php";
include CT_7_COST_PLUGIN_PATH."backend/number_format.php";
include CT_7_COST_PLUGIN_PATH."frontend/index.php";
include CT_7_COST_PLUGIN_PATH."superaddons/check_purchase_code.php";
new Superaddons_Check_Purchase_Code( 
    array(
        "plugin" => "cf7-cost-calculator-price-calculation/index.php",
        "id"=>"1515",
        "pro"=>"https://add-ons.org/plugin/contact-form-7-cost-calculator/",
        "plugin_name"=> "Contact Form 7 Cost Calculator",
        "document"=>"https://add-ons.org/document-contact-form-7-cost-calculator/"
    )
);
if(!class_exists('Superaddons_List_Addons')) {  
    include CT_7_COST_PLUGIN_PATH."add-ons.php"; 
}