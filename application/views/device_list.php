<?php require_once('common/header.php');?>
<?php require_once('common/menu.php');?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">局端列表</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    局端信息列表
                </div>

                <div class="panel-body">
                    <div class="col-sm-2">
                        小区选择
                        <select class="form-control input-sm" id="community_info">
                            <option value="all">全部</option>
                            <?php
                            foreach ($community_info as $item) {
                                echo "<option value=" . $item['id'] . ">" . $item['community_name'] . "</option>";
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-sm-2">
                        分前端选择
                        <select class="form-control input-sm" id="sr_info">
                            <option value="all">全部</option>
                            <?php
                            foreach ($sr_info as $item) {
                                echo "<option value=" . $item['id'] . ">" . $item['sr_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                </div>

                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="device_list_info" width="100%">
                            <thead>
                            <tr>
                                <!--                                <th>序号</th>-->
                                <!--                                <th>操作人</th>-->
                                <!--                                <th>登录IP</th>-->
                                <!--                                <th>内容</th>-->
                                <!--                                <th>时间</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-6 -->

    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->


<link href="resource/DataTables/media/css/dataTables.bootstrap.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" type="text/css" href="resource/DataTables/media/css/dataTables.responsive.css">

<script type="text/javascript" charset="utf8" src="resource/DataTables/media/js/jquery.dataTables.min.js"></script>

<script type="text/javascript" src="resource/DataTables/media/js/dataTables.bootstrap.js"></script>

<script type="text/javascript" src="resource/DataTables/media/js/dataTables.responsive.min.js"></script>


<script>

//    $.fn.dataTable.ext.search.push(
//        function( settings, data, dataIndex ) {
//            var sr_info = $('#sr_info').val();
//            var community_info = $('#community_info').val();
//            var age = parseFloat( data[0] ) || 0; // use data for the age column
//            alert(sr_info);
////            return true;
//        }
//    );



    $(document).ready(function () {
        var table = $("#device_list_info").DataTable({
//            "paging":true,
            "pagingType": "full_numbers",
            "responsive": true,
            //"lengthMenu":[5,10,25,50],
            "processing": true,
            "searching": true, //是否开启搜索
            "serverSide": true,//开启服务器获取数据
//            "order": [[4, "desc"]], //默认排序
            "ajax": { // 获取数据
                "url": "<?php echo site_url('manager/get_device_info') ?>",
                "dataType": "json", //返回来的数据形式,
                data:function (d) {
                    d.sr_info = $('#sr_info').val(),
                    d.community_info = $('#community_info').val();
                }
            },
            "columns": [ //定义列数据来源
//                {'title': "序号", 'data': "id"},
                {'title': "局端安装地址", 'data': "positional_info"},
                {'title': "局端IP地址", 'data': "ip_addr"},
                {'title': "更新时间", 'data': "update_time"},
            ],
            "language": { // 定义语言
                "sProcessing": "<img src='resource/images/loading.gif'/> &nbsp;&nbsp;数据加载中，请稍后...&nbsp;&nbsp;",
                "sLengthMenu": "每页显示 _MENU_ 条记录",
                "sZeroRecords": "没有匹配的结果",
                "sInfo": "当前显示第 _START_ 至 _END_ 条，共 _TOTAL_ 条。",
                "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
                "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
                "sInfoPostFix": "",
                "sSearch": "搜索:",
                "sUrl": "",
                "sEmptyTable": "表中数据为空",
                "sLoadingRecords": "<img src='resource/images/loading.gif'/>载入中...",
                "sInfoThousands": ",",
                "oPaginate": {
                    "sFirst": "首页",
                    "sPrevious": "上一页",
                    "sNext": "下一页",
                    "sLast": "末页"
                },
                "stripeClasses": ["odd", "even"],//为奇偶行加上样式，兼容不支持CSS伪类的场合
            }

        });//table end

        /*
        * 下拉列表搜索查询
        * 具体解决参见：
        * https://www.datatables.net/forums/discussion/30286/ajax-reload-is-not-sending-updated-params#Comment_81214
        * */
        $('#sr_info, #community_info').change(function () {
            table.ajax.reload();
        });

    });
</script>

<?php require_once('common/footer.php');?>


