<?php require_once('common/header.php'); ?>
<?php require_once('common/menu.php'); ?>

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
                        <button type="button" class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> 添加局端
                        </button>
                    </div>
                    <p style="color:red;"><b>注：添加之后需要管理员审核通过才能显示。</b></p>
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
                                <th>局端安装地址</th>
                                <th>局端IP地址</th>
                                <th>更新时间</th>
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

    $(document).ready(function () {
        var table = $("#device_list_info").DataTable({
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
                data: function (d) {
                    d.sr_info = $('#sr_info').val(),
                        d.community_info = $('#community_info').val();
                }
            },
            "columns": [ //定义列数据来源
//                {'title': "序号", 'data': "id"},
                {'data': "positional_info"},
                {'data': "ip_addr"},
                {'data': "update_time"},
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


        //添加局端信息
        $("#btn-add").click(function () {
            var d = dialog({
                title: '添加局端',
                width: 'auto',
                content: '局端IP：<input type="text" class="form-control" id="device_ip_addr" placeholder="请输入局端IP">' +
                '局端安装地址：<input type="text" class="form-control" id="device_positional_info" placeholder="请输入局端安装地址">' +
                '局端MAC：<input type="text" class="form-control" id="device_mac" placeholder="局端MAC（可不填）">' +
                '小区选择：' +
                '<select class="form-control input-sm" id="community_id"><option value="all">请选择所属小区</option>' +
                '<?php
                    foreach ($community_info as $item) {
                        echo "<option value=" . $item['id'] . "," . $item['sr_id'] . ">" . $item['community_name'] . "</option>";
                    }
                    ?>' +
                '</select>',
                okValue: '添加',
                ok: function () {
                    var _device_ip_addr = $.trim($('#device_ip_addr').val());
                    if (!isValidIP(_device_ip_addr)) {
                        var info = dialog({
                            content: '局端IP地址输入错误，请重新输入！'
                        });
                        info.show();
                        setTimeout(function () {
                            info.close().remove();
                        }, 1500);
                        return false;
                    }

                    if ($('#community_id').val() == 'all') {
                        var info = dialog({
                            content: '请选择所属小区！'
                        });
                        info.show();
                        setTimeout(function () {
                            info.close().remove();
                        }, 1500);
                        return false;
                    }

                    var _device_positional_info = $.trim($('#device_positional_info').val());
                    var data_array = ($.trim($('#community_id').val())).split(',');//利用,来分割小区ID和分前端ID
                    var _community_info = data_array[0];
                    var _sr_info = data_array[1];
                    var _device_mac = $.trim($('#device_mac').val());

                    if (_device_ip_addr.length == 0 || _device_positional_info.length == 0) {
                        var info = dialog({
                            content: '请完善局端信息必填内容！'
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
                            url: "<?php echo site_url('manager/add_uncheck_dev') ?>",
                            type: "POST",
                            data: {
                                _device_ip_addr: _device_ip_addr,
                                _device_positional_info: _device_positional_info,
                                _community_info: _community_info,
                                _sr_info: _sr_info,
                                _device_mac: _device_mac
                            },
                            dataType: "json",
                            success: function (msg) {
                                if (msg.result == true) {
                                    var success_info = dialog({
                                        content: '添加成功！'
                                    });
                                    success_info.show();
                                    setTimeout(function () {
                                        success_info.close().remove();
                                    }, 1000);
                                } else {
                                    dialog.get('result_info').close();
                                    var err_msg = dialog({
                                        content: '添加失败，请检查局端IP是否已经存在！'
                                    });
                                    err_msg.show();
                                    setTimeout(function () {
                                        err_msg.close().remove();
                                    }, 1500);
                                    return false;
                                }
                                dialog.get('result_info').close();
                            },
                            error: function () {
                                dialog.get('result_info').close();
                                var d = dialog({
                                    content: '连接数据库错误，请稍后再试！'
                                });
                                d.show();
                                setTimeout(function () {
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

        //IP地址正则验证
        function isValidIP(ip) {
            var reg = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/
            return reg.test(ip);
        }

    });
</script>

<?php require_once('common/footer.php'); ?>


