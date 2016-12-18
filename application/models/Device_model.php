<?php
/**
 * 设备管理模型
 * Created by PhpStorm.
 * User: TONY
 * Date: 16-12-11
 * Time: 下午16:14
 */

class Device_model extends CI_Model{

    function __construct() {
        parent::__construct();
        $this->load->model('common_model');
    }

    /**
     * 添加新闻
     * @param $_title       标题
     * @param $_content     内容
     * @param $_author      作者
     * @return bool         TRUE OR FALSE
     */
    function add_news($_title,$_content,$_author,$_type){
        $insert_sql = "INSERT INTO t_news(id,title,content,author,type,create_dt) VALUES(UUID(),'" . $_title . "','" . $_content . "','" . $_author . "','" . $_type . "','" . date('Y-m-d H:i:s') . "')";
        $result = $this->common_model->execQuery($insert_sql, 'default', TRUE);
        return $result;
    }

    /**
     * 删除新闻
     * @param   $_id    新闻ID
     * @return bool         TRUE OR FALSE
     */
    function del_news($_id){
        $del_sql = "DELETE FROM t_news WHERE id='" . $_id . "'";
        $result = $this->common_model->execQuery($del_sql, 'default', TRUE);
        return $result;
    }

    /**
     * 编辑新闻
     * @param   $_id      新闻ID
     * @param   $_title     新闻标题
     * @param   $_content   新闻内容
     * @return bool         TRUE OR FALSE
     */
    function upd_news($_id, $_title, $_content){
        $upd_news = "UPDATE t_news SET title='" . $_title . "',content='" . $_content . "' WHERE id='" . $_id . "'";
        $result = $this->common_model->execQuery($upd_news, 'default', TRUE);
        return $result;
    }

    /**
     * 获取列表
     * @param int $offset       偏移量
     * @param int $page_size    显示条数
     * @param string $where     条件查询
     * @return mixed            结果集
     */
    function get_list($offset = NULL, $page_size = NULL, $where = NULL)
    {
        $list_sql = "SELECT * FROM t_deviceinfo $where ORDER BY id ASC LIMIT $offset,$page_size";
        $result = $this->common_model->getDataList($list_sql, 'default');
        return $result;
    }

    /**
     * 获取新闻数据总条数
     * @return int              条数
     */
    function get_list_total_num($where = NULL){
        $sql = "SELECT COUNT(*) AS num FROM t_deviceinfo $where";
        $count = $this->common_model->getTotalNum($sql, 'default');
        return $count;
    }


}

/* End of file news_model.php */
/* Location: ./app/models/news_model.php */