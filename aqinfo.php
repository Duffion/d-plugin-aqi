<?php

/**
 *
 * @package AQInfo Widget Plugin
 * @version 1.0
 */

namespace D\DUFFION;


/*
 * Plugin Name: Duffion - AQI Widget Plugin
 * Plugin URI: https://duffion.com
 * Description: This is a custom AQI tool that integrates both Wunderground and Purple air data into displayable widgets for the frontend
 * Version: 1.0
 * Author: Chris "Duffs" Crevling
 * Text Domain: d-aqinfo-plugin
 * Author URI: https://duffion.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */



if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('D_AQI')) :

    // Load in global vars //
    $d_plugin_dirs = [];
    $d_modules = [];

    class D_AQI
    {

        var $version = '1.0';

        public $settings = [];

        public $modules = [];

        private $updater = [];

        public $dirs = [
            'partials' => 'templates/partials',
            'modules' => 'inc/modules',
            'inc' => 'inc',
            'traits' => 'inc/traits',
            'vendors' => 'inc/vendors',
            'assets' => 'assets',
            'scripts' => 'assets/js',
            'styles' => 'assets/css',
            'templates' => 'templates',
            'modules' => 'inc/modules',
            'templates-modules' => 'templates/modules',
        ];

        // [ 'filename without php' => 'name of dir from above config' ] //
        private $_loading = [
            'core' => 'inc',
            'enqueue' => 'inc'
        ];

        private $instance = [];

        /**
         * __construct - []
         *
         */
        function __construct()
        {
            $this->_define();
        }

        function _git_updater()
        {
            require $this->dirs['plugin'] . '/' . $this->dirs['vendors'] . '/plugin-update-checkers/plugin-update-checker.php';

            $config = [
                'git' => 'https://github.com/Duffion/d-plugin-aqi/',
                'target_branch' => 'production'
            ];

            $this->updater = \Puc_v4_Factory::buildUpdateChecker(
                $config['git'],
                __FILE__,
                'fulcrum'
            );

            //Set the branch that contains the stable release.
            $this->updater->setBranch($config['target_branch']);
        }

        /**
         * _load - []
         * We need to load in all the required core files / traits
         */
        function _load()
        {
            global $d_instance, $d_loaded;
            // Lets create a global instance to make sure we only load items not already loaded //
            $d_loaded = [];
            $d_instance = (!isset($d_instance) ? [] : $d_instance);
            $this->_define();

            require_once $this->dirs['plugin'] . '/' . $this->dirs['inc'] . '/util.php';
            require_once $this->dirs['plugin'] . '/' . $this->dirs['traits'] . '/d-primary.php';
            require_once $this->dirs['plugin'] . '/' . $this->dirs['traits'] . '/d-templates.php';
            require_once $this->dirs['plugin'] . '/' . $this->dirs['inc'] . '/d-crons.php';
            require_once $this->dirs['plugin'] . '/' . $this->dirs['inc'] . '/vendors.php';

            $this->_git_updater();

            // Lets now load in our other flies with the util loader //
            if ($this->_loading && count($this->_loading) > 0) {
                foreach ($this->_loading as $file => $dir_name) {
                    $file_loc = (isset($this->dirs[$dir_name]) ? $this->dirs['plugin'] . '/' . $this->dirs[$dir_name] . '/' . $file . '.php' : false);
                    if ($file_loc) d_req($file_loc);

                    $this->instance['loaded'] = $d_loaded;
                }
            }
        }

        /**
         * _define - []
         *
         */
        function _define($r = false)
        {
            global $d_plugin_dirs;

            $this->dirs['plugin'] = rtrim(plugin_dir_path(__FILE__), '/');

            $d_plugin_dirs = $this->dirs;
        }

        /**
         * init - []
         *
         */
        function init()
        {
            // Load in any needed configs or passable globals here so loaded items can use properly //

            // Lets manually load in our starting files //
            $this->_load();

            // Do anything extra after we have loaded in the core //

        }
    }

    /**
     * Global Functionset - D_FULCRUM() - only run once []
     *
     */
    function D_AQI()
    {
        global $d_aqi;

        if (!isset($d_aqi)) {
            $d_aqi = new d_aqi();
            $d_aqi->init();
        }

        return $d_aqi;
    }

    // Instantiate
    D_AQI();

endif;
