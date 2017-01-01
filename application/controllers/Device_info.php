<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * 局端管理类
 *  包含局端添加、修改及管理员审核等
 * Created by PhpStorm.
 * User: TONY
 * Date: 2016-12-28
 * Time: 22:04
 */
class Device_info extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->admin_model->auth_check();
    }



    //局端添加
    public function device_add_view()
    {
        //小区信息获取
        $get_community_info_sql = "SELECT id,community_name FROM t_community";
        $data['community_info'] = $this->common_model->getDataList($get_community_info_sql, 'default');

        //分前端信息获取
        $get_sr_info_sql = "SELECT id,sr_name FROM t_serverroom";
        $data['sr_info'] = $this->common_model->getDataList($get_sr_info_sql, 'default');

        $this->load->view('device_add', $data);
    }

    //添加单个局端信息
    public function add_single_dev()
    {
        //暂时只过滤XSS情况，后续再增加防止SQL过滤等函数过滤
        $_device_ip_addr = trim($this->input->post('_device_ip_addr', TRUE));//局端IP
        $_device_positional_info = trim($this->input->post('_device_positional_info', TRUE));//局端安装地址
        $_community_info = $this->input->post('_community_info', TRUE);//小区ID
        $_sr_info = $this->input->post('_sr_info', TRUE);//分前端ID
        $_device_mac = $this->input->post('_device_mac', TRUE);//局端MAC，可为空

        //注：branch_id为分公司ID，由于暂时只供观山湖使用，所以此处直接填入观山湖公司ID：1001
        $add_sql = "INSERT INTO t_deviceinfo(ip_addr,positional_info,branch_id,serverroom_id,community_id,dev_mac,update_time) VALUES ";
        $add_sql .="('" . $_device_ip_addr . "','" . $_device_positional_info . "','1001','" . $_sr_info . "','" . $_community_info . "','" . $_device_mac . "','" . date("Y-m-d H:i:s") . "')";
        $result = $this->common_model->execQuery($add_sql, 'default');

        //如果添加成功，则记录log
       if ($result) {
            $this->admin_model->add_log($this->input->ip_address(), $_SESSION['admin_info'] . '  ' . $_SESSION['name'], '添加局端：' . $_device_ip_addr); //记录登录日志
        }
        echo json_encode(array(
            "result" => $result
        ), JSON_UNESCAPED_UNICODE);
    }





    //加载未审核局端列表
    public function dev_unchecked_view()
    {
        //小区信息获取
        $get_community_info_sql = "SELECT id,community_name FROM t_community";
        $data['community_info'] = $this->common_model->getDataList($get_community_info_sql, 'default');

        //分前端信息获取
        $get_sr_info_sql = "SELECT id,sr_name FROM t_serverroom";
        $data['sr_info'] = $this->common_model->getDataList($get_sr_info_sql, 'default');

        $this->load->view('device_unchecked_list', $data);
    }

    //获取未审核局端信息
    public function get_dev_unchecked_list()
    {
        $list_sql = "SELECT * FROM t_unchecked_dev WHERE flag=0 ";
        $search = $_GET['search']['value'];//获取前台传过来的过滤条件
        $search_sql = "";
        if ($search !== '') {
            //mysql CONCAT(str1,str2,…)
            //返回结果为连接参数产生的字符串。如有任何一个参数为NULL ，则返回值为 NULL。
            $search_sql = " AND CONCAT(id,ip_addr,positional_info,add_time) LIKE '%" . $search . "%'";
        }

        //接收前台dropdown列表查询参数
        //1.分前端查询
        $sr_info = $_GET['sr_info'];
        $search_sr_sql = "";
        if ($sr_info != 'all') {
            $search_sr_sql = " AND serverroom_id=" . $sr_info;
        }

        //2.小区查询
        $community_info = $_GET['community_info'];
        $search_community_sql = "";
        if ($community_info != 'all') {
            $search_community_sql = " AND community_id=" . $community_info;
        }

        //条件过滤后记录数 必要
        $draw = $_GET['draw'];//这个值作者会直接返回给前台
        //过滤的数据条数
        $recordsFiltered = 0;
        //表的总记录数 必要
        $recordsTotal = 0;
        $total_num_sql = "SELECT COUNT(*) AS num FROM t_unchecked_dev WHERE flag=0 ";
        $recordsTotal = $this->common_model->getTotalNum($total_num_sql, 'default');
        $recordsFiltered = count($this->common_model->getDataList($list_sql . $search_sql . $search_sr_sql . $search_community_sql, 'default'));

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
                    $orderSql = " ORDER BY add_time " . $order_dir;
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
        $data = $this->common_model->getDataList($list_sql . $search_sql . $search_sr_sql . $search_community_sql . $orderSql . $limitSql, 'default');

        echo(json_encode(array(
            "draw" => intval($draw),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $data
        ), JSON_UNESCAPED_UNICODE));
    }

    //局端审核通过操作
    public function dev_checked_operation()
    {
        $dev_id = $this->input->post('_dev_id', TRUE);
        $dev_id = implode(',', $dev_id);//将数组转成字符串，利用 Mysql中WHERE IN 实现批量更新

//        //审核标志：0不通过，
//        $flag = $this->input->post('_flag', TRUE);

        //批量更新局端为已审核状态，同时添加审核人名称及审核时间
        $checked_sql = "UPDATE t_unchecked_dev SET flag=1,checked_user='" . $_SESSION['admin_info'] . " " . $_SESSION['name'] . "',checked_date='" . date("Y-m-d H:i:s") . "' WHERE id IN (" . $dev_id . ")";
        $result = $this->common_model->execQuery($checked_sql, 'default');

        if ($result) {
            $this->admin_model->add_log($this->input->ip_address(), $_SESSION['admin_info'] . '  ' . $_SESSION['name'], '审核局端'); //记录登录日志
            /////准备添加数据到t_device正式表
        }

        echo json_encode(array(
            "result" => $result
        ), JSON_UNESCAPED_UNICODE);
    }

    //加载已审核局端列表
    public function dev_checked_view()
    {
        //小区信息获取
        $get_community_info_sql = "SELECT id,community_name FROM t_community";
        $data['community_info'] = $this->common_model->getDataList($get_community_info_sql, 'default');

        //分前端信息获取
        $get_sr_info_sql = "SELECT id,sr_name FROM t_serverroom";
        $data['sr_info'] = $this->common_model->getDataList($get_sr_info_sql, 'default');

        $this->load->view('device_checked_list', $data);
    }

    //获取已审核局端信息
    public function get_dev_checked_list()
    {
        $list_sql = "SELECT * FROM t_unchecked_dev WHERE flag=1 ";
        $search = $_GET['search']['value'];//获取前台传过来的过滤条件
        $search_sql = "";
        if ($search !== '') {
            //mysql CONCAT(str1,str2,…)
            //返回结果为连接参数产生的字符串。如有任何一个参数为NULL ，则返回值为 NULL。
            $search_sql = " AND CONCAT(id,ip_addr,positional_info,add_user,add_time,checked_user,checked_date) LIKE '%" . $search . "%'";
        }

        //接收前台dropdown列表查询参数
        //1.分前端查询
        $sr_info = $_GET['sr_info'];
        $search_sr_sql = "";
        if ($sr_info != 'all') {
            $search_sr_sql = " AND serverroom_id=" . $sr_info;
        }

        //2.小区查询
        $community_info = $_GET['community_info'];
        $search_community_sql = "";
        if ($community_info != 'all') {
            $search_community_sql = " AND community_id=" . $community_info;
        }

        //条件过滤后记录数 必要
        $draw = $_GET['draw'];//这个值作者会直接返回给前台
        //过滤的数据条数
        $recordsFiltered = 0;
        //表的总记录数 必要
        $recordsTotal = 0;
        $total_num_sql = "SELECT COUNT(*) AS num FROM t_unchecked_dev WHERE flag=1 ";
        $recordsTotal = $this->common_model->getTotalNum($total_num_sql, 'default');
        $recordsFiltered = count($this->common_model->getDataList($list_sql . $search_sql . $search_sr_sql . $search_community_sql, 'default'));

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
                    $orderSql = " ORDER BY community_id " . $order_dir;
                    break;
                case 4;
                    $orderSql = " ORDER BY add_user " . $order_dir;
                    break;
                case 5;
                    $orderSql = " ORDER BY add_time " . $order_dir;
                    break;
                case 6;
                    $orderSql = " ORDER BY checked_user " . $order_dir;
                    break;
                case 7;
                    $orderSql = " ORDER BY checked_date " . $order_dir;
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
        $data = $this->common_model->getDataList($list_sql . $search_sql . $search_sr_sql . $search_community_sql . $orderSql . $limitSql, 'default');

        echo(json_encode(array(
            "draw" => intval($draw),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $data
        ), JSON_UNESCAPED_UNICODE));
    }

}