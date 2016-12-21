<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: TONY
 * Date: 2016-12-14
 * Time: 17:56
 */
class Log_info extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->admin_model->auth_check();
        $this->load->model('common_model');
//        $this->load->library(array('common_class', 'pagination'));
//        $this->load->model('device_model');
    }

    //日志信息加载
    public function index()
    {
//        $this->load->view('index');
        $offset = 0; //偏移量
        $where = "";

//        if ($this->input->get('per_page')) {
//            $offset = ((int)$this->input->get('per_page') - 1) * $this->per_page; //计算偏移量
//        }

//        $count = $this->device_model->get_list_total_num($where); //总条数

        //初始化分页数据
//        $config = $this->common_class->getPageConfigInfo('/manager/?', $count, $this->per_page, $this->uri_segment);
//        $this->pagination->initialize($config);
//        $data['pagination'] = $this->pagination->create_links();


//        $list_sql = "SELECT * FROM t_log $where ORDER BY log_datetime DESC LIMIT $offset,10";
//        $data['log_info'] = $this->common_model->getDataList($list_sql, 'default');
//        $data['log_list'] = $this->device_model->get_list($offset, $this->per_page, $where);
        $this->load->view('log_info');
    }

    //日志信息获取，根据对应搜索条件反应json格式结果
    public function get_log_info()
    {
        $list_sql = "SELECT * FROM t_log";
        $search = $_GET['search']['value'];//获取前台传过来的过滤条件
        $search_sql = "";
        if ($search !== '') {
            //mysql CONCAT(str1,str2,…)
            //返回结果为连接参数产生的字符串。如有任何一个参数为NULL ，则返回值为 NULL。
            $search_sql = " WHERE CONCAT(id,log_username,ip_addr,log_content,log_datetime) LIKE '%" . $search . "%'";
        }


        //条件过滤后记录数 必要
        $draw = $_GET['draw'];//这个值作者会直接返回给前台
        //过滤的数据条数
        $recordsFiltered = 0;
        //表的总记录数 必要
        $recordsTotal = 0;
        $total_num_sql = "SELECT COUNT(*) AS num FROM t_log";
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
                    $orderSql = " ORDER BY log_username " . $order_dir;
                    break;
                case 2;
                    $orderSql = " ORDER BY ip_addr " . $order_dir;
                    break;
                case 3;
                    $orderSql = " ORDER BY log_content " . $order_dir;
                    break;
                case 4;
                    $orderSql = " ORDER BY log_datetime " . $order_dir;
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