<?php require_once('common/header.php');?>
<?php require_once('common/menu.php');?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">局端审核</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="<?php echo site_url('device_info/dev_unchecked_view')?>"><b>未审核局端列表</b></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo site_url('device_info/dev_checked_view')?>">已审核局端列表</a>
                </div>

                <div class="panel-body">
                    <button type="button" class="btn btn-primary" id="btn-checked"> 通过审核
                    </button>&nbsp;&nbsp;
<!--                    <button type="button" class="btn btn-danger" id="btn-unchecked"> 不通过审核-->
<!--                    </button>-->
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
                        <table class="table table-striped table-bordered" id="device_unchecked_list" width="100%">
                            <thead>
                            <tr>
                                <th><label><input type="checkbox" id="checkAll" >全选</label></th>
                                <th>序号</th>
                                <th>局端安装地址</th>
                                <th>局端IP地址</th>
                                <th>所属小区</th>
                                <th>添加者</th>
                                <th>添加时间</th>
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

        var table = $("#device_unchecked_list").DataTable({
//            "paging":true,
            "pagingType": "full_numbers",
            "responsive": true,
            //"lengthMenu":[5,10,25,50],
            "processing": true,
            "searching": true, //是否开启搜索
            "serverSide": true,//开启服务器获取数据
//            "order": [[4, "desc"]], //默认排序
            "ajax": { // 获取数据
                "url": "<?php echo site_url('device_info/get_dev_unchecked_list') ?>",
                "dataType": "json", //返回来的数据形式,
                data:function (d) {
                    d.sr_info = $('#sr_info').val(),
                        d.community_info = $('#community_info').val();
                }
            },

            "columns": [ //定义列数据来源
                {
                    'data': "id",
//                    "sClass": "text-center",
                    "orderable": false,
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox"  class="checkchild"  value="' + data + '" />';
                    },
                    "bSortable": false
                },
                {'data':"id"},
                {'data': "positional_info"},
                {'data': "ip_addr"},
                {'data': "community_id"},
                {'data': "add_user"},
                {'data': "add_time"},

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


        //全选操作
        $("#checkAll").change(function () {
            var check = $(this).prop("checked");
            $(".checkchild").prop("checked", check);
        });


        //通过审核逻辑操作
        $("#btn-checked").click(function () {

            if ($(".checkchild:checked").length == 0) {
                var d = dialog({
                    content: '请选择需要审核的数据！'
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 1500);
                return;
            }


            var confirm_modal = dialog({
                title: '提示',
                content: '确定要执行审核操作吗？',
                okValue: '确定',
                ok: function () {
                    var _dev_id = [];//目标ID数组
                    $(".checkchild:checked").each(function () {
                        _dev_id.push($(this).val());//数组入栈操作
                    });

                    //loading事件
                    dialog({
                        id: 'result_info',
                        title: '操作中，请稍后...',
                        width: 150,
                        quickClose: true
                    }).show();
                    $.ajax({
                        url: "<?php echo site_url('device_info/dev_checked_operation') ?>",
                        type: "POST",
                        data: {
                            _dev_id: _dev_id
                        },
                        dataType: "json",
                        success: function (msg) {
                            if (msg.result) {
                                var success_info = dialog({
                                    content: '操作成功！'
                                });
                                success_info.show();
                                setTimeout(function () {
                                    dialog.get('result_info').close();
                                    success_info.close().remove();
                                    $("#checkAll").attr("checked", false);//取消全选checkbox
                                    table.ajax.reload();
                                }, 1000);
                            } else {
                                dialog.get('result_info').close();
                                var err_msg = dialog({
                                    content: '操作失败，请稍后再试！'
                                });
                                err_msg.show();
                                setTimeout(function () {
                                    dialog.get('result_info').close();
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

//            $.each(_target_id,function(n,value){
//                alert(value);
//            });
                },
                cancelValue: '取消',
                cancel: function () {
                }
            });
            confirm_modal.showModal();

        });



    });
</script>

<?php require_once('common/footer.php');?>