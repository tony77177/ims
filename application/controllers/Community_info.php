<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 小区信息类
 * Created by PhpStorm.
 * User: TONY
 * Date: 2016-12-28
 * Time: 21:47
 */
class Community_info extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->admin_model->auth_check();
        $this->admin_model->check_is_manager();
    }

    //小区信息view加载
    public function index()
    {
        //分前端信息获取
        if (!isset($_SESSION['sr_info'])) {
            $get_sr_info_sql = "SELECT id,sr_name FROM t_serverroom";
            $_SESSION['sr_info'] = $this->common_model->getDataList($get_sr_info_sql, 'default');
        }
        $data['sr_info'] = $_SESSION['sr_info'];
        $this->load->view('community_info', $data);
    }

    //小区信息获取，返回JSON格式数据
    public function get_community_info()
    {
        $list_sql = "SELECT * FROM t_community";
        $search = $_GET['search']['value'];//获取前台传过来的过滤条件
        $search_sql = "";
        if ($search !== '') {
            //mysql CONCAT(str1,str2,…)
            //返回结果为连接参数产生的字符串。如有任何一个参数为NULL ，则返回值为 NULL。
            $search_sql = " WHERE CONCAT(id,community_name,update_time) LIKE '%" . $search . "%'";
        }


        //条件过滤后记录数 必要
        $draw = $_GET['draw'];//这个值作者会直接返回给前台

        //表的总记录数 必要
        $recordsTotal = 0;
        $total_num_sql = "SELECT COUNT(*) AS num FROM t_community";
        $recordsTotal = $this->common_model->getTotalNum($total_num_sql, 'default');

        //过滤的数据条数
        $recordsFiltered = 0;
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
                    $orderSql = " ORDER BY community_name " . $order_dir;
                    break;
                case 2;
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

    //添加小区司信息
    public function add_community_info()
    {
        //暂时只过滤XSS情况，后续再增加防止SQL过滤等函数过滤
        $community_name = trim($this->input->post('_community_name', TRUE));
        $sr_id = trim($this->input->post('_sr_id', TRUE));
        $add_sql = "INSERT INTO t_community(community_name,sr_id,update_time) VALUES ('" . $community_name . "','" . $sr_id . "','" . date("Y-m-d H:i:s") . "')";
        $result = $this->common_model->execQuery($add_sql, 'default');
        //添加成功，则删除已存在session，从DB获取最新数据
        if ($result) {
            unset($_SESSION['community_info']);
        }
        echo json_encode(array(
            "result" => $result
        ), JSON_UNESCAPED_UNICODE);
    }

}