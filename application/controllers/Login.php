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
        $this->load->library('Common_class');
    }

    //登录首页
    public function index(){
        $this->load->view('login');
    }

    //登录
    public function check_login(){
        
        $user_name = $this->input->post('user', TRUE);
        $pwd = $this->input->post('pwd', TRUE);

        $data = array(
            'wcode' => $user_name,
            'passwd' => $pwd
        );

        //无网络时采用的连接方法
        $this->session->set_userdata('admin_info', 'gy1667'); //记录用户名，用于判断是否登录
        $this->session->set_userdata('name','赵昱');
        $this->admin_model->add_log($this->input->ip_address(), 'gy1667 赵昱', '用户登录'); //记录登录日志
//            redirect(site_url('manager'));
        die("<script>window.location.href='" . site_url('manager') . "';</script>");


        $result_info = json_decode($this->common_class->curl_request("http://58.16.134.2:8088/mobile/rest/businessservice/login",$data));

        if ($result_info->retCode === '0') {
            $this->session->set_userdata('admin_info', $result_info->wcode); //记录用户名，用于判断是否登录
            $this->session->set_userdata('name',$result_info->name);
            $this->admin_model->add_log($this->input->ip_address(), $result_info->wcode . '  ' . $result_info->name, '用户登录'); //记录登录日志
//            redirect(site_url('manager'));
            die("<script>window.location.href='" . site_url('manager') . "';</script>");
        }else{
            echo "fail";
        }

//        $result = $this->admin_model->check_login($user_name, md5($pwd));
//        if ($result > 0) {
//            $this->session->set_userdata('admin_info', $user_name); //记录用户名，用于判断是否登录
//            $this->admin_model->add_log($this->input->ip_address(), $user_name,'用户登录'); //记录登录日志
////            redirect(site_url('manager'));
//            die("<script>window.location.href='" . site_url('manager') . "';</script>");
//        } else {
//            echo "fail";
//        }
    }

    //退出
    public function logout(){
        $this->session->sess_destroy();
        redirect(site_url('login'));
    }
}

/* End of file login.php */
/* Location: ./app/controllers/login.php */