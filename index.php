<?php
    /*
        Plugin Name: Payment Gateway for Denizbank
        Plugin URI: https://tr.wordpress.org/plugins/wc-denizbank-sanal-pos/
        Description: Denizbank için ödeme yöntemi.
        Version: 1.1
        Author: garsoft
        Author URI: https://www.garsoft.com.tr/
        License: GNU
        Text Domain: denizpos
        Domain Path: /languages
        */

    if (!defined('ABSPATH')) {
        exit;
    }



    define("pgfd_plugin_url", plugin_dir_url(__FILE__));
    define("pgfd_plugin_dir", plugin_dir_path(__FILE__));

    add_action('init', 'denizpos_load_textdomain');
    function denizpos_load_textdomain()
    {
        load_plugin_textdomain('denizpos', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    add_filter('woocommerce_payment_gateways', 'denizpos_add_gateway_class');
    function denizpos_add_gateway_class($gateways)
    {
        $gateways[] = 'WC_DenizPos_Gateway';
        return $gateways;
    }

    /*
     * The class itself, please note that it is inside plugins_loaded action hook
     */
    add_action('plugins_loaded', 'denizpos_init_gateway_class');
    function denizpos_init_gateway_class()
    {
        require_once "class/class-wc_denizpos_gateway.php";
    }


