<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * 分前端信息类
 * Created by PhpStorm.
 * User: TONY
 * Date: 2016-12-28
 * Time: 21:44
 */
class Serverroom_info extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->admin_model->auth_check();
        $this->admin_model->check_is_manager();
    }

    //分前端信息view加载
    public function index()
    {
        $this->load->view('serverroom_info');
    }

    //分前端信息获取，返回JSON格式数据
    public function get_serverroom_info()
    {
        $list_sql = "SELECT * FROM t_serverroom";
        $search = $_GET['search']['value'];//获取前台传过来的过滤条件
        $search_sql = "";
        if ($search !== '') {
            //mysql CONCAT(str1,str2,…)
            //返回结果为连接参数产生的字符串。如有任何一个参数为NULL ，则返回值为 NULL。
            $search_sql = " WHERE CONCAT(id,sr_name,update_time) LIKE '%" . $search . "%'";
        }


        //条件过滤后记录数 必要
        $draw = $_GET['draw'];//这个值作者会直接返回给前台

        //表的总记录数 必要
        $recordsTotal = 0;
        $total_num_sql = "SELECT COUNT(*) AS num FROM t_serverroom";
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
                    $orderSql = " ORDER BY sr_name " . $order_dir;
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

    //添加分前端司信息
    public function add_serverroom_info()
    {
        //暂时只过滤XSS情况，后续再增加防止SQL过滤等函数过滤
        $sr_name = trim($this->input->post('_sr_name', TRUE));
        $branch_id = '1001';//分公司ID，目前暂时只支持观山湖，故此处直接增加为观山湖公司ID
        $add_sql = "INSERT INTO t_serverroom(sr_name,branch_id,update_time) VALUES ('" . $sr_name . "','" . $branch_id . "','" . date("Y-m-d H:i:s") . "')";
        $result = $this->common_model->execQuery($add_sql, 'default');
        //添加成功，则删除已存在session，从DB获取最新数据
        if ($result) {
            unset($_SESSION['sr_info']);
        }
        echo json_encode(array(
            "result" => $result
        ), JSON_UNESCAPED_UNICODE);
    }

}