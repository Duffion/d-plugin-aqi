<?php
/*
    Module: Product Category Scraper - Fulcrum
    Goal: Create a targeted list of Categories (product_category) that have special terms within them that will be compaired against PUBLISHED products and specific fields of those products in order to copy the matching term into its own category (or subcategory of that products primary category {opt in from admin setting}) or into a product tag. This newly created category / subcat / tag will allow for a frontend developer / content creator to split out the inventory organization on the frontend

    Author: Christopher "Duffs" Crevling
    Company: Duffion LLC


    */

use  D\DUFFION\TRAITS\PRIME as D_PRIME;
use  D\DUFFION\TRAITS\TEMPLATES as D_TEMPLATES;
use D\DUFFION\CRONS\d_crons as CRON;

class duffion_ex
{

    use D_PRIME, D_TEMPLATES;

    var $menu_item = [];

    function __construct()
    {
        $this->cron = new CRON;
    }

    function init()
    {
        $this->_define();

        add_action('wp_ajax_nopriv_ex_ajax', [$this, 'ajax__handler_function']);
        add_action('wp_ajax_ex_ajax', [$this, 'ajax__handler_function']);


        $this->_actions($this->actions);
        $this->_filters($this->filters);

        // we need to now register our cronjobs
        $this->register_cron();
    }

    function register_cron()
    {
        add_action('ex__example_cron_hook', [$this, 'run_cron']);
        // wpp($this->cron) . die;
        // $this->cron->print_tasks();
        $this->cron->schedule('ex__example_cron_hook', 'three_minutes');
    }

    function _define()
    {
        $this->validations = [
            [
                'key' => 'example-text',
                'rules' => [
                    'not_empty' => true,
                    'required' => true
                ]
            ]
        ];

        $this->menu_item = [
            'parent_slug' => 'duffion',
            'page_title' => 'Duffion Example Module',
            'menu_title' => 'Example Module',
            'capability' => 'manage_options',
            'menu_slug' => 'module-ex',
            'function' => [&$this, 'view_ex'],
            'position' => 1
        ];

        $this->filters = [
            'add_subpage' => [
                'hook' => 'd-add-subpages--primary',
                'function' => 'add_submenu',
                'args' => 1
            ]
        ];

        $this->actions = [];
    }

    function view_ex()
    {

        $nonce = $this->create_nonce();
        // pull the terms that the user has already put into the system //
        $jobs = ['array' => 'stuff'];
        // load the partial for output view
        $this->partial('modules', 'module-example', ['jobs' => $jobs, 'nonce' => $nonce]);
    }



    function ajax__handler_function()
    {
        $p = $this->validate_request(true);
        // validate and run our response //
        $response = ['example' => 'module'];
        wp_send_json($response, 200);
    }


    function run_cron()
    {
        // This is a cron job running
    }
}


if (!function_exists('d__start_ex')) {
    function d__start_ex()
    {
        global $d_modules;

        return (!isset($d_modules['EX']) ? new duffion_ex() : $d_modules['EX']);
    }
}

if (!isset($d_modules['EX'])) $d_modules['EX'] = [];

$d_modules['EX'] = d__start_ex();
$d_modules['EX']->init();
