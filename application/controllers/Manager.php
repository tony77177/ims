<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * Created by PhpStorm.
 * User: TONY
 * Date: 2016-12-9
 * Time: 21:38
 */
class Manager extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->admin_model->auth_check();
    }

    //首页加载
    public function index()
    {
//        $this->load->view('index');
        $this->load->view('welcome_message');
    }
}