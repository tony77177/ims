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
        //获取设备总数
        $dev_total_num = 0;
        if (!isset($_SESSION['dev_total_num'])) {
            $get_dev_num_sql = "SELECT COUNT(*) AS num FROM t_deviceinfo";
            $_SESSION['dev_total_num'] = $this->common_model->getTotalNum($get_dev_num_sql, 'default');
        }
        $dev_total_num = $_SESSION['dev_total_num'];

        //分公司个数
        $branches_total_num = 0;
        if (!isset($_SESSION['branches_total_num'])) {
            $get_branches_num_sql = "SELECT COUNT(*) AS num FROM t_branch";
            $_SESSION['branches_total_num'] = $this->common_model->getTotalNum($get_branches_num_sql, 'default');
        }
        $branches_total_num = $_SESSION['branches_total_num'];


        //分前端个数
        $sr_total_num = 0;
        if (!isset($_SESSION['sr_total_num'])) {
            $get_sr_num_sql = "SELECT COUNT(*) AS num FROM t_serverroom";
            $_SESSION['sr_total_num'] = $this->common_model->getTotalNum($get_sr_num_sql, 'default');
        }
        $sr_total_num = $_SESSION['sr_total_num'];


        //小区个数
        $community_total_num = 0;
        if (!isset($_SESSION['community_total_num'])) {
            $get_community_num_sql = "SELECT COUNT(*) AS num FROM t_community";
            $_SESSION['community_total_num'] = $this->common_model->getTotalNum($get_community_num_sql, 'default');
        }
        $community_total_num = $_SESSION['community_total_num'];


        //加载首页数量
        $data = array(
            "dev_total_num" => $dev_total_num,
            "branches_total_num" => $branches_total_num,
            "sr_total_num" => $sr_total_num,
            "community_total_num" => $community_total_num
        );
        $this->load->view('index', $data);
    }

    //局列表首页
    public function device_list()
    {
        //小区信息获取
        $get_community_info_sql = "SELECT * FROM t_community";
        $data['community_info'] = $this->common_model->getDataList($get_community_info_sql, 'default');

        //分前端信息获取
        $get_sr_info_sql = "SELECT id,sr_name FROM t_serverroom";
        $data['sr_info'] = $this->common_model->getDataList($get_sr_info_sql, 'default');

        $this->load->view('device_list', $data);
    }

    //局端列表信息获取
    public function get_device_info()
    {

        $list_sql = "SELECT * FROM t_deviceinfo WHERE 1=1 ";
        $search = $_GET['search']['value'];//获取前台传过来的过滤条件
        $search_sql = "";
        if ($search !== '') {
            //mysql CONCAT(str1,str2,…)
            //返回结果为连接参数产生的字符串。如有任何一个参数为NULL ，则返回值为 NULL。
            $search_sql = " AND CONCAT(id,ip_addr,positional_info,update_time) LIKE '%" . $search . "%'";
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
        $total_num_sql = "SELECT COUNT(*) AS num FROM t_deviceinfo";
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
        $data = $this->common_model->getDataList($list_sql . $search_sql . $search_sr_sql . $search_community_sql . $orderSql . $limitSql, 'default');

        echo(json_encode(array(
            "draw" => intval($draw),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $data
        ), JSON_UNESCAPED_UNICODE));
    }

    /**
     * 添加局端信息：
     *  此处仅仅添加到待审核局端表，需要管理员审核通过后才添加到正式表
     */
    public function add_uncheck_dev()
    {
        //暂时只过滤XSS情况，后续再增加防止SQL过滤等函数过滤
        $_device_ip_addr = trim($this->input->post('_device_ip_addr', TRUE));//局端IP
        $_device_positional_info = trim($this->input->post('_device_positional_info', TRUE));//局端安装地址
        $_community_info = $this->input->post('_community_info', TRUE);//小区ID
        $_sr_info = $this->input->post('_sr_info', TRUE);//分前端ID
        $_device_mac = $this->input->post('_device_mac', TRUE);//局端MAC，可为空


        //检测局端是否存在
        $check_dev_is_exist_sql = "SELECT COUNT(*) AS num FROM t_deviceinfo WHERE ip_addr='" . $_device_ip_addr . "'";
        $check_result = $this->common_model->getTotalNum($check_dev_is_exist_sql, 'default');
        if ($check_result > 0) {
            echo json_encode(array(
                "result" => 'false'
            ), JSON_UNESCAPED_UNICODE);
        } else {

            log_message('info', '添加待审核局端：' . $_SESSION['admin_info'] . '  ' . $_SESSION['name'] . ',IP地址：' . $_device_ip_addr);
            //注：branch_id为分公司ID，由于暂时只供观山湖使用，所以此处直接填入观山湖公司ID：1001
            $add_sql = "INSERT INTO t_unchecked_dev(ip_addr,positional_info,branch_id,serverroom_id,community_id,dev_mac,add_user,add_time,flag) VALUES ";
            $add_sql .= "('" . $_device_ip_addr . "','" . $_device_positional_info . "','1001','" . $_sr_info . "','" . $_community_info . "','" . $_device_mac . "','" . $_SESSION['admin_info'] . '  ' . $_SESSION['name'] . "','" . date("Y-m-d H:i:s") . "','0')";
            $result = $this->common_model->execQuery($add_sql, 'default');
            log_message('info', '添加待审核局端SQL：' . $add_sql);
            log_message('info', '添加待审核局端结果：' . $result);

            //如果添加成功，则记录log
            if ($result) {
                $this->admin_model->add_log($this->input->ip_address(), $_SESSION['admin_info'] . '  ' . $_SESSION['name'], '待审核局端添加：' . $_device_ip_addr); //记录登录日志
            }
            echo json_encode(array(
                "result" => $result
            ), JSON_UNESCAPED_UNICODE);
        }
    }
}