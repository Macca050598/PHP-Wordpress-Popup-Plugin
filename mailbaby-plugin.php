<?php
/**
 * Plugin Name: MailBaby Plugin
 * Description: MailBaby is a custom mailing list plug-in for Rattles & Rockers, it gives you the ability to customize the form and email sent to users who sign up, and store submissions within wordpress. For any issues please contact mackenzie.williams@mjweb.ltd
 * Author: MJWeb Ltd 
 * Author URI:  https://mjweb.ltd
 * Version: 1.0.2
 * 
 */

if (!defined('ABSPATH')) {
    die('You cannot be here');
}

if (!class_exists('MailBabyPlugin')) {

    class MailBabyPlugin
    {

        public function __construct()
        {
            define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
            define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));
            require_once(MY_PLUGIN_PATH . '/vendor/autoload.php');
        }

        public function initialize()
        {
            include_once MY_PLUGIN_PATH . 'includes/utilities.php';
            include_once MY_PLUGIN_PATH . 'includes/options-page.php';

            include_once MY_PLUGIN_PATH . 'includes/mailbaby-form.php';
        }

    }

    $mailBabyPlugin = new MailBabyPlugin;
    $mailBabyPlugin->initialize();
}
