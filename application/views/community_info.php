<?php require_once('common/header.php'); ?>
<?php require_once('common/menu.php'); ?>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">小区信息</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        小区信息列表
                    </div>
                    <div class="panel-body">
                        <button type="button" class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> 添加小区
                        </button>
                    </div>

                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="sr_list_info" width="100%">
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


    <!-- DataTables -->
    <link href="resource/DataTables/media/css/dataTables.bootstrap.css" rel="stylesheet" type="text/css">

    <link rel="stylesheet" type="text/css" href="resource/DataTables/media/css/dataTables.responsive.css">

    <script type="text/javascript" charset="utf8" src="resource/DataTables/media/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript" src="resource/DataTables/media/js/dataTables.bootstrap.js"></script>

    <script type="text/javascript" src="resource/DataTables/media/js/dataTables.responsive.min.js"></script>


    <script type="text/javascript">

        $(document).ready(function () {
            var _table = $("#sr_list_info").DataTable({
                "pagingType": "full_numbers",
                "responsive": true,
                //"lengthMenu":[5,10,25,50],
                "processing": true,
                "searching": true, //是否开启搜索
                "serverSide": true,//开启服务器获取数据
//            "order": [[4, "desc"]], //默认排序
                "ajax": { // 获取数据
                    "url": "<?php echo site_url('community_info/get_community_info') ?>",
                    "dataType": "json" //返回来的数据形式
                },
                "columns": [ //定义列数据来源
                    {'title': "序号", 'data': "id"},
                    {'title': "小区名称", 'data': "community_name"},
                    {'title': "更新时间", 'data': "update_time"}
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
        });

        //添加小区信息
        $("#btn-add").click(function () {
            var d = dialog({
                title: '添加小区',
//            quickClose: true,
                content: '小区名称：<input type="text" class="form-control" id="community_name" placeholder="请输入小区名称" autofocus>'+
                '分前端选择<select class="form-control input-sm" id="sr_id"><option value="all">请选择所属分前端</option>' +
                '<?php
                    foreach ($sr_info as $item) {
                        echo "<option value=" . $item['id'] . ">" . $item['sr_name'] . "</option>";
                    }
                    ?>' +
                '</select>',
                okValue: '添加',
                ok: function () {
                    var _community_name = $.trim($('#community_name').val());
                    var _sr_id = $('#sr_id').val();
                    if (_community_name.length == 0 || _sr_id == 'all') {
                        var info = dialog({
                            content: '请完成小区名称及所属分前端信息！'
                        });
                        info.show();
                        setTimeout(function () {
                            info.close().remove();
                        }, 1500);
                        return false;
                    } else {
                        //loading事件
                        dialog({
                            id: 'result_info',
                            title: '添加中，请稍后...',
                            width: 150,
                            quickClose: true
                        }).show();
                        $.ajax({
                            url: "<?php echo site_url('community_info/add_community_info') ?>",
                            type: "POST",
                            data: {_community_name: _community_name, _sr_id: _sr_id},
                            dataType: "json",
                            success: function (msg) {
                                if (msg.result) {
                                    var success_info = dialog({
                                        content: '添加成功！'
                                    });
                                    success_info.show();
                                    dialog.get('result_info').close();
                                    setTimeout(function () {
                                        $("#sr_list_info").dataTable().fnDraw(false);
                                        success_info.close().remove();
                                    }, 1000);
                                } else {
                                    var err_msg = dialog({
                                        content: '添加失败，请检查小区是否已经存在！'
                                    });
                                    err_msg.show();
                                    dialog.get('result_info').close();
                                    setTimeout(function () {
                                        err_msg.close().remove();
                                    }, 1500);
                                    return false;
                                }
                            },
                            error: function () {
                                var d = dialog({
                                    content: '连接数据库错误，请稍后再试！'
                                });
                                d.show();
                                setTimeout(function () {
                                    dialog.get('result_info').close();
                                    d.close().remove();
                                }, 3000);
                            }
                        });
                    }
                },
                cancelValue: '取消',
                cancel: function () {
                }
            });
            d.showModal();
        });
    </script>

<?php require_once('common/footer.php'); ?>