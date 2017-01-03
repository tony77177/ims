<?php require_once('common/header.php'); ?>
<?php require_once('common/menu.php'); ?>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">局端添加</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        单一添加
                    </div>
                    <div class="panel-body">
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> 单个添加局端
                            </button>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        批量添加
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
                    <div class="panel-body">
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" id="btn-multi-add"><i class="fa fa-plus"></i> 批量添加局端
                            </button>
                        </div>
                    </div>
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-6 -->

        </div>
        <!-- /.row -->
    </div>
    <!-- /#page-wrapper -->

    <script>

        //添加局端信息
        $("#btn-add").click(function () {
            var d = dialog({
                title: '添加局端',
                width: 'auto',
                content: '局端IP：<input type="text" class="form-control" id="device_ip_addr" placeholder="请输入局端IP" autofocus>' +
                '局端安装地址：<input type="text" class="form-control" id="device_positional_info" placeholder="请输入局端安装地址">' +
                '局端MAC：<input type="text" class="form-control" id="device_mac" placeholder="局端MAC（可不填）">' +
                '小区选择：' +
                '<select class="form-control input-sm" id="community_info"><option value="all">请选择所属小区</option>' +
                '<?php
                    foreach ($community_info as $item) {
                        echo "<option value=" . $item['id'] . ">" . $item['community_name'] . "</option>";
                    }
                    ?>' +
                '</select>' +
                '分前端选择<select class="form-control input-sm" id="sr_info"><option value="all">请选择所属分前端</option>' +
                '<?php
                    foreach ($sr_info as $item) {
                        echo "<option value=" . $item['id'] . ">" . $item['sr_name'] . "</option>";
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
                    var _device_positional_info = $.trim($('#device_positional_info').val());
                    var _community_info = $('#community_info').val();
                    var _sr_info = $('#sr_info').val();
                    var _device_mac = $.trim($('#device_mac').val());
                    if (_device_ip_addr.length == 0 || _device_positional_info.length == 0 || _community_info == 'all' || _sr_info == 'all') {
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
                            url: "<?php echo site_url('device_info/add_single_dev') ?>",
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
                                if (msg.result==true) {
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



    </script>

<?php require_once('common/footer.php'); ?>