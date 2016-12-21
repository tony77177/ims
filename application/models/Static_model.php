<?php

/**
 * 分类信息类，包含小区、分公司及分前端信息
 * Created by PhpStorm.
 * User: TONY
 * Date: 2016-12-21
 * Time: 21:29
 */
class Static_model extends CI_Model
{

    function __construct() {
        parent::__construct();
        $this->load->model('common_model');
    }

    

}