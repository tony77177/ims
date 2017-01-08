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
        $this->admin_model->check_is_manager();
        $this->load->library('excel');
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

        //检测局端是否存在
        $check_dev_is_exist_sql = "SELECT COUNT(*) AS num FROM t_deviceinfo WHERE ip_addr='" . $_device_ip_addr . "'";
        $check_result = $this->common_model->getTotalNum($check_dev_is_exist_sql, 'default');
        if ($check_result > 0) {
            echo json_encode(array(
                "result" => 'false'
            ), JSON_UNESCAPED_UNICODE);
        } else {
            //注：branch_id为分公司ID，由于暂时只供观山湖使用，所以此处直接填入观山湖公司ID：1001
            $add_sql = "INSERT INTO t_deviceinfo(ip_addr,positional_info,branch_id,serverroom_id,community_id,dev_mac,add_time,update_time) VALUES ";
            $add_sql .= "('" . $_device_ip_addr . "','" . $_device_positional_info . "','1001','" . $_sr_info . "','" . $_community_info . "','" . $_device_mac . "','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')";
            $result = $this->common_model->execQuery($add_sql, 'default');

            //如果添加成功，则记录log
            if ($result) {
                $this->admin_model->add_log($this->input->ip_address(), $_SESSION['admin_info'] . '  ' . $_SESSION['name'], '添加局端：' . $_device_ip_addr); //记录登录日志
            }
            echo json_encode(array(
                "result" => $result
            ), JSON_UNESCAPED_UNICODE);
        }
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
        $dev_id_arrary = $this->input->post('_dev_id', TRUE);
        $dev_id = implode(',', $dev_id_arrary);//将数组转成字符串，利用 Mysql中WHERE IN 实现批量更新


        //批量更新局端为已审核状态，同时添加审核人名称及审核时间
        $checked_sql = "UPDATE t_unchecked_dev SET flag=1,checked_user='" . $_SESSION['admin_info'] . " " . $_SESSION['name'] . "',checked_date='" . date("Y-m-d H:i:s") . "' WHERE id IN (" . $dev_id . ")";
        $result = $this->common_model->execQuery($checked_sql, 'default');

        if ($result) {
            $this->admin_model->add_log($this->input->ip_address(), $_SESSION['admin_info'] . '  ' . $_SESSION['name'], '审核局端'); //记录登录日志
            //准备添加数据到t_device正式表
            log_message('info','开始执行批量审核机制');

            $insert_sql = "INSERT INTO t_deviceinfo(ip_addr,positional_info,branch_id,serverroom_id,community_id,dev_mac,add_time,update_time)";
            $insert_sql .= " VALUES ";

            //此处采用获取ID之后再读取数据库的方法，暂时不采用前台传数据方法，为了防止数据不被篡改
            for ($i = 0; $i < count($dev_id_arrary); $i++) {
                $get_dev_info_by_id = "SELECT * FROM t_unchecked_dev WHERE id='" . $dev_id_arrary[$i] . "'";
                $data = $this->common_model->getDataList($get_dev_info_by_id, 'default');
                $data = $data[0];
                if ($i + 1 == count($dev_id_arrary)) {
                    $insert_sql .= "('" . $data['ip_addr'] . "','" . $data['positional_info'] . "','" . $data['branch_id'] . "','" . $data['serverroom_id'] . "','" . $data['community_id'] . "','" . $data['dev_mac'] . "','" . date("Y-m-d H:i:s") . "'),'" . date("Y-m-d H:i:s") . "')";
                } else {
                    $insert_sql .= "('" . $data['ip_addr'] . "','" . $data['positional_info'] . "','" . $data['branch_id'] . "','" . $data['serverroom_id'] . "','" . $data['community_id'] . "','" . $data['dev_mac'] . "','" . date("Y-m-d H:i:s") . "'),'" . date("Y-m-d H:i:s") . "'),";
                }
            }

            log_message('info', '批量审核SQL：' . $insert_sql);
            $insert_result = $this->common_model->execQuery($insert_sql, 'default');
            log_message('info', '批量审核SQL执行结果：' . $insert_result);

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

    /**
     * 批量添加局端信息
     * 处理逻辑：
     * 1、前台同时上传文件信息和数据信息（both data and file）
     * 2、首先保存上传的excel文件信息
     * 3、其次利用PHPEXCEL开始读取局端信息
     *  3.1 中途每个步骤核实对应的信息，如IP地址合法性检查，如果非法的则不添加进DB
     * 4、添加成功后返回添加成功条数信息及失败条数信息
     */
    public function add_multi_dev()
    {
        //获取上传文件后缀类型
        $file_array = explode(".", $_FILES['file']['name']);
        $file_extension = strtolower(array_pop($file_array));

        if ($file_extension != 'xls' && $file_extension != 'xlsx') {
            echo(json_encode(
                array(
                    "result" => "file_error"
                ), JSON_UNESCAPED_UNICODE
            ));
        } else {
            //文件保存后的新名字及保存目录
            $file_new_Name = $this->config->config['upload_path'] . $_SESSION['admin_info'] . '-' . time() . '.' . $file_extension;
            //保存文件，保存成功返回 TRUE 否则返回FALSE
            $flag = move_uploaded_file($_FILES['file']['tmp_name'], $file_new_Name);
            log_message('info', 'excel文件新名字：' . $file_new_Name);
            log_message('info', 'excel文件是否保存成功标志：' . $flag);
            if ($flag) {
                //文件保存成功，开始读文件
                $objPHPExcel = PHPExcel_IOFactory::load($file_new_Name);
                $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                foreach ($cell_collection as $cell) {
                    $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                    $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                    //获取具体值此处使用：getFormattedValue，因为部分excel中包含格式，所以包含格式一起读取
                    $data_value = trim($objPHPExcel->getActiveSheet()->getCell($cell)->getFormattedValue());
                    //header will/should be in row 1 only. of course this can be modified to suit your need.
                    if ($row == 1) {
                        $header[$row][$column] = $data_value;
                    } else {
                        $arr_data[$row][$column] = $data_value;
                    }
                }
                //send the data in an array format
//                $data['header'] = $header;
//                $data['values'] = $arr_data;

                $_community_info = $this->input->post('community_info', TRUE);//小区ID
                $_sr_info = $this->input->post('sr_info', TRUE);//分前端ID

                $success_num = 0;//添加成功具体数量
                $fail_num = 0;//添加失败具体数量
                foreach ($arr_data as $item) {
                    //判断IP地址是否合法，PHP 5 >= 5.2.0, 支持该函数
                    if (filter_var($item['A'], FILTER_VALIDATE_IP)) {
                        //检测局端是否存在
                        $check_dev_is_exist_sql = "SELECT COUNT(*) AS num FROM t_deviceinfo WHERE ip_addr='" . $item['A'] . "'";
                        $check_result = $this->common_model->getTotalNum($check_dev_is_exist_sql, 'default');
                        if ($check_result == 0) {
                            //注：branch_id为分公司ID，由于暂时只供观山湖使用，所以此处直接填入观山湖公司ID：1001
                            $add_sql = "INSERT INTO t_deviceinfo(ip_addr,positional_info,branch_id,serverroom_id,community_id,dev_mac,add_time,update_time) VALUES ";
                            $add_sql .= "('" . $item['A'] . "','" . $item['B'] . "','1001','" . $_sr_info . "','" . $_community_info . "','" . $item['C'] . "','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')";
                            $result = $this->common_model->execQuery($add_sql, 'default');

                            //如果添加成功，则记录log
                            if ($result) {
                                $this->admin_model->add_log($this->input->ip_address(), $_SESSION['admin_info'] . '  ' . $_SESSION['name'], '添加局端：' . $item['A']); //记录登录日志
                            }
                            $success_num++;
                        } else {
                            log_message('info', '批量添加失败局端IP：' . $item['A']);
                            $fail_num++;
                        }
                    } else {
                        log_message('info', '批量添加失败局端IP：' . $item['A']);
                        $fail_num++;
                    }
                }

                if ($success_num == 0) {
                    echo(json_encode(
                        array(
                            "result" => "fail"
                        ), JSON_UNESCAPED_UNICODE
                    ));
                } else {
                    echo(json_encode(
                        array(
                            "result" => "success",
                            "success_num" => $success_num,
                            "fail_num" => $fail_num
                        ), JSON_UNESCAPED_UNICODE
                    ));
                }
            } else {
                log_message('info', '上传文件保存失败：' . $file_new_Name);
                echo(json_encode(
                    array(
                        "result" => "fail"
                    ), JSON_UNESCAPED_UNICODE
                ));
            }
        }
    }

    //加载待编辑局端信息列表
    public function dev_edit_view()
    {
        //小区信息获取
        $get_community_info_sql = "SELECT id,community_name FROM t_community";
        $data['community_info'] = $this->common_model->getDataList($get_community_info_sql, 'default');

        //分前端信息获取
        $get_sr_info_sql = "SELECT id,sr_name FROM t_serverroom";
        $data['sr_info'] = $this->common_model->getDataList($get_sr_info_sql, 'default');

        $this->load->view('device_edit', $data);
    }

    //局端信息修改
    public function dev_edit_operation()
    {
        //设备ID、安装地址及设备IP获取，暂时只支持修改设备安装地址
        $_dev_id = $this->input->post("_dev_id", TRUE);
        $_device_positional_info = $this->input->post("_device_positional_info", TRUE);
        $_ip_addr = $this->input->post("_ip_addr", TRUE);

        //局端信息修改DB操作
        $update_sql = "UPDATE t_deviceinfo SET positional_info='" . $_device_positional_info . "',update_time='" . date("Y-m-d H:i:s") . "'";
        $update_sql .= " WHERE id='" . $_dev_id . "' AND ip_addr='" . $_ip_addr . "'";
        log_message('info', '局端信息修改SQL：' . $update_sql);
        $result = $this->common_model->execQuery($update_sql, 'default');
        log_message('info', '局端信息修改结果：' . $result);

        //如果添加成功，则记录log
        if ($result) {
            $this->admin_model->add_log($this->input->ip_address(), $_SESSION['admin_info'] . '  ' . $_SESSION['name'], '更新局端：' . $_ip_addr); //记录登录日志
        }
        echo json_encode(array(
            "result" => $result
        ), JSON_UNESCAPED_UNICODE);
    }

}