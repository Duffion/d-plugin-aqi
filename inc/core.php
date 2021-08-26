<?php

/**
 * Our primary CORE plugin handler
 */

use  D\DUFFION\TRAITS\PRIME as D_PRIME;
use  D\DUFFION\TRAITS\TEMPLATES as D_TEMPLATES;
use D\DUFFION\CRONS\d_crons as CRON;

class d_core
{
    use D_PRIME, D_TEMPLATES;

    // [ 'directory-namespace' => 'directory folder' ]
    private $auto_dirs = [
        'modules', 'widgets'
    ];

    private $api = [
        'purpleair' => [
            'base_uri' => 'https://www.purpleair.com/json',
            'params' => [
                'show' => 'purple_air_id' // this will autofill based on the option given //
            ]
        ],
        'wunderground' => []
    ];

    function __construct()
    {
        $this->cron = new CRON;
        $this->_define();
        // Register our Actions and Filters //
        $this->_actions($this->actions);
        $this->_filters($this->filters);
    }

    function register_cron()
    {
        $proption = get_option('purple_air_id');
        if ($proption) {
            add_action('d_aqi__run_purple_air', [$this, 'purple_air_job']);
            $this->cron->schedule('d_aqi__run_purple_air', 'one_minute');
        }
    }

    function _define()
    {
        $this->actions = [
            'd-register-menu' => [
                'hook' => 'admin_menu',
                'function' => 'add_admin_menu'
            ],
            'manual-job' => [
                'hook' => 'init',
                'function' => 'run_manual_job'
            ],
            'd-register-cron' => [
                'hook' => 'init',
                'function' => 'register_cron'
            ],
            'd-register-settings' => [
                'hook' => 'admin_init',
                'function' => 'settings_init'
            ],
            'd-widget-aqi' => [
                'hook' => 'widgets_init',
                'function' => 'load_aqi_widget'
            ]
            // 'd-register-meta-box' => [
            //     'hook' => 'add_meta_boxes',
            //     'function' => 'add_dynamic_product_fields'
            // ],
        ];

        $this->filters = [
            'd-put-widget-in-menu' => [
                'hook' => 'wp_nav_menu_items',
                'function' => 'widget_to_menu',
                'priority' => 10,
                'accepted_args' => 2
            ]
        ];
    }

    function load_aqi_widget()
    {
        // load in the widget file //
        require_once('widgets/aqi.php');
        register_widget('aqinfo_widget');
    }

    // Action targets //
    function setup()
    {
        // Lets setup our autoloaders and build out our core plugin needs //
        $this->autoloader($this->auto_dirs);
    }

    function __widget()
    {
        ob_start();
        the_widget('aqinfo_widget', ['purple_air_index' => 0]);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    function widget_to_menu($items, $args)
    {
        if ($args->theme_location === 'primary') {
            $widget = $this->__widget();
            $items .= '<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-6528">' . $widget . '</li>';
        }
        return $items;
    }

    function add_admin_menu()
    {
        // Register our Dashboard area //
        $menu = [
            'primary' => [
                'page_title' => __('AQI Configuration - Dashboard', 'd-text'),
                'menu_title' => 'AQI Config',
                'capability' => 'manage_options',
                'menu_slug' => 'd-aqi',
                'function' => 'view_dashboard',
                'icon_url' => '',
                'position' => 40,
                'subpages' => [
                    // 'settings' => [
                    //     'page_title' => __('Plugin Settings', 'd-text'),
                    //     'menu_title' => 'Settings',
                    //     'capability' => 'manage_options',
                    //     'menu_slug' => 'settings',
                    //     'function' => 'view_settings',
                    //     'position' => 9
                    // ]
                ]
            ],
        ];

        // $subpages = ;
        // Lets add in a filter to allow us to append more sub pages via our modules //
        $this->register_settings($menu);
    }

    public function purple_air_container($args)
    {
    }

    public function text_input_cb($args)
    {
        // Get the value of the setting we've registered with register_setting()
        $label_for = $args['label_for'];
        $key = get_option($label_for);
        echo '<input type="text" id="' . $label_for . '" name="' . $label_for . '" value="' . $key . '">';
    }

    // register settings forms and page //
    function settings_init()
    {
        // Register the settings for "settings" page
        register_setting('d-aqi', 'purple_air_id');


        // Register an API section in the Settings page
        add_settings_section(
            'purple_air_settings',
            __('Purple Air Settings', 'd-text'),
            [&$this, 'purple_air_container'],
            'd-aqi'
        );

        // Register both a staging and production API field in the API section
        add_settings_field(
            'purple-air-id',
            __('Device ID', 'd-text'),
            array(&$this, 'text_input_cb'),
            'd-aqi',
            'purple_air_settings',
            array(
                'label_for'         => 'purple_air_id',
            )
        );
    }


    function view_dashboard()
    {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        $nonce = $this->create_nonce();
        $options = [
            'pa-id' => get_option('d_aqi_purpleair_id')
        ];

        // load the partial for output view
        $this->partial('modules', 'aqi-config', ['nonce' => $nonce, 'options' => $options]);
    }

    function run_manual_job()
    {
        $request = $_REQUEST;
        $jobs = [];
        if (is_admin() && isset($request['run']) && $request['page'] === 'd-aqi') {

            switch ($request['run']) {
                case 'purpleair':
                    $jobs[$request['run']] = $this->purple_air_job();
                    break;

                case 'wunderground':

                    break;
            }
        }
        // wpp($jobs) . die;
    }

    function purple_air_job()
    {
        // we need to pull down the json from the purple air api endpoint and cache it into our database //
        $id = get_option('purple_air_id');
        $api = $this->api['purpleair'];
        $status = '&success=false&error=ID was not specified properly.';
        if ($id && $id !== '' && $id !== 0 && $api) {

            $url = $api['base_uri'] . '?show=' . $id;

            // lets grab the contents //
            $results = file_get_contents($url);

            if ($results) {
                $data = json_decode($results);
                // now that we have our data we need to package it up properly and save it to our database //
                if (isset($data->results) && count($data->results) > 0) {
                    $cache = [
                        'data' => $data->results,
                        'updated' => time(),
                        'updated_timestamp' => date('Y-m-d H:i:s'),
                        'pa-id' => $id
                    ];

                    $updated = update_option('d_aqi__purple_air', $cache);
                    // now lets log anything else we need //

                    // make sure we updated our option //
                    if ($updated) {
                        $status = '&success=true';
                    }
                } else {
                    // There was an error we need to make sure to return that //
                    $status = '&success=false&error=Could not connect to Purple Air.';
                }
            }
        }
        wp_safe_redirect($_SERVER['HTTP_REFERER'] . $status);
    }
}

if (!function_exists('d__init_core')) {
    function d__init_core()
    {
        global $d__core;

        return (!isset($d__core) ? new d_core() : $d__core);
    }
}

$d__core = d__init_core();
$d__core->setup();
