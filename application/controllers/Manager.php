<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * Created by PhpStorm.
 * User: TONY
 * Date: 2016-12-9
 * Time: 21:38
 */
class Manager extends CI_Controller{

    private $per_page = 20; //每页显示数据条数
    private $uri_segment = 2; //分页方法自动测定你 URI 的哪个部分包含页数

    function __construct()
    {
        parent::__construct();
        $this->admin_model->auth_check();
//        $this->load->model('device_model');
    }

    //首页加载
    public function index()
    {
        $this->load->view('index');
    }

    //局列表首页
    public function device_list()
    {
        $this->load->view('device_list');
    }

    //局端列表信息获取
    public function get_device_info()
    {

        $list_sql = "SELECT * FROM t_deviceinfo";
        $search = $_GET['search']['value'];//获取前台传过来的过滤条件
        $search_sql = "";
        if ($search !== '') {
            //mysql CONCAT(str1,str2,…)
            //返回结果为连接参数产生的字符串。如有任何一个参数为NULL ，则返回值为 NULL。
            $search_sql = " WHERE CONCAT(id,ip_addr,positional_info,update_time) LIKE '%" . $search . "%'";
        }


        //条件过滤后记录数 必要
        $draw = $_GET['draw'];//这个值作者会直接返回给前台
        //过滤的数据条数
        $recordsFiltered = 0;
        //表的总记录数 必要
        $recordsTotal = 0;
        $total_num_sql = "SELECT COUNT(*) AS num FROM t_deviceinfo";
        $recordsTotal = $this->common_model->getTotalNum($total_num_sql, 'default');
        $recordsFiltered = count($this->common_model->getDataList($list_sql . $search_sql, 'default'));

        //排序
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//ase desc 升序或者降序
        //拼接排序sql
        $orderSql = "";
        if (isset($order_column)) {
            $i = intval($order_column);
            switch ($i) {
                case 0;
                    $orderSql = " ORDER BY id " . $order_dir;
                    break;
                case 1;
                    $orderSql = " ORDER BY positional_info " . $order_dir;
                    break;
                case 2;
                    $orderSql = " ORDER BY ip_addr " . $order_dir;
                    break;
                case 3;
                    $orderSql = " ORDER BY update_time " . $order_dir;
                    break;
                default;
                    $orderSql = '';
            }
        }

        //分页
        $start = $_GET['start'];//从多少开始
        $length = $_GET['length'];//数据长度
        $limitSql = '';
        $limitFlag = isset($_GET['start']) && $length != -1;
        if ($limitFlag) {
            $limitSql = " LIMIT " . intval($start) . ", " . intval($length);
        }

        //拼接搜索条件、排序后得到的最终数据集
        $data = $this->common_model->getDataList($list_sql . $search_sql . $orderSql . $limitSql, 'default');

        echo(json_encode(array(
            "draw" => intval($draw),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $data
        ), JSON_UNESCAPED_UNICODE));

    }
}