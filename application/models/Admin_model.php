<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 管理员模型
 * Created by PhpStorm.
 * User: TONY
 * Date: 13-12-26
 * Time: 下午9:43
 */

class Admin_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('common_model');
    }

    /**
     * 登录验证
     * @param $_username    用户名
     * @return mixed        返回条数
     */
    function check_login($_username){
        $check_sql = "SELECT COUNT(*) AS num FROM t_admin WHERE user_name='" . $_username . "'";
        $num = $this->common_model->getTotalNum($check_sql, 'default');
        return $num;
    }

    /**
     *验证是否为非法登录
     */
    function auth_check(){
        if (!$this->session->userdata('admin_info')) {
            redirect(site_url() . "/login");
        }
    }

    /**
     * 验证是否为管理员
     */
    function check_is_manager(){
        if(!$_SESSION['is_manager']){
            redirect(site_url() . "/manager/device_list");
        }
    }

    /**
     * 修改密码
     * @param $_login_name      用户名
     * @param $_pwd             密码
     * @return bool             TRUE OR FALSE
     */
    function change_pwd($_login_name, $_pwd){
        $update_sql = "UPDATE t_admin SET pass_word='" . $_pwd . "' WHERE user_name='" . $_login_name . "'";
        $result = $this->common_model->execQuery($update_sql, 'default', TRUE);
        return $result;
    }

    /**
     * 记录操作日志
     * @param $_ip              操作IP地址
     * @param $_username        用户账号
     * @param $_log_content     操作内容
     * @return mixed
     */
    function add_log($_ip,$_username,$_log_content){
        $log_sql = "INSERT INTO t_log(log_username,ip_addr,log_content,log_datetime) VALUES('" . $_username . "','" . $_ip . "','" . $_log_content . "','" . date("Y-m-d H:i:s") . "')";
        $result = $this->common_model->execQuery($log_sql, 'default', TRUE);
        return $result;
    }
}

/* End of file admin_model.php */
/* Location: ./app/models/admin_model.php */