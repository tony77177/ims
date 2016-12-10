<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 登录页控制器
 * Created by PhpStorm.
 * User: TONY
 * Date: 13-12-26
 * Time: 下午8:15
 */
class Login extends CI_Controller{

    function __construct(){
        parent::__construct();
    }

    //登录首页
    public function index(){
        $this->load->view('login');
    }

    //登录
    public function check_login(){
        
        $user_name = $this->input->post('user', TRUE);
        $pwd = $this->input->post('pwd', TRUE);
        $result = $this->admin_model->check_login($user_name, md5($pwd));
        if ($result > 0) {
            $this->session->set_userdata('admin_info', $user_name); //记录用户名，用于判断是否登录
            $this->admin_model->add_log($this->input->ip_address(), $user_name,'用户登录'); //记录登录日志
//            redirect(site_url('manager'));
            die("<script>window.location.href='" . site_url('manager') . "';</script>");
        } else {
            echo "fail";
        }
    }

    //退出
    public function logout(){
        $this->session->sess_destroy();
        redirect(site_url('login'));
    }
}

/* End of file login.php */
/* Location: ./app/controllers/login.php */