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
                        <!--                        <div class="form-group">-->
                        <!--                        </div>-->

                        <div class="col-sm-2">
                            <br>
                            <input id="file" type="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
<!--                            <input class="input-text" type="hidden" id="file_flag" value="0"/>-->
<!--                            <button id="upload" class="btn btn-primary" type="button"><i-->
<!--                                    class="glyphicon glyphicon-upload"></i>上传设备文件-->
<!--                            </button>-->
                            <!--                            <input id="lefile" type="file" style="display:none">-->
                            <!--                            <input type="text" class="form-control" id="dev_multi_data" disabled>-->
                            <!--<!--                            <input id="photoCover" class="input-large" type="text" style="height:30px;">-->

                            <!--                            <button class="btn" onclick="$('input[id=lefile]').click();" id="choose_file_btn">选择上传文件</button>-->
                        </div>
                    </div>


                    <div class="panel-body">
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" id="btn-multi-add"><i class="fa fa-plus"></i>
                                批量添加局端
                            </button>
                        </div>
                        <div style="color: red;">
                            <br>
                            <b>注：批量添加设备过程较慢，添加途中请勿关闭或者刷新浏览器，请等待系统自动运行结束后再操作。</b>
                        </div>
                    </div>
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-6 -->

        </div>
        <!-- /.row -->
    </div>


    <script>

        $("#upload").click(function () {

        });

        //        $("#choose_file_btn").click(function () {
        //            var btnUpload = $("#choose_file_btn");
        //            var file_name = $("#lefile").val();
        //            $("#video_status").attr('value', file_name);
        //            new AjaxUpload(btnUpload, {
        //                action: '#',
        //                name: 'lefile',
        ////                        data: {
        ////                            file_type: 0, //上传文件类型
        ////                            up_type: 'emoji',
        ////                            order_id: 0, //地址序号
        ////                            up_act: 'add'
        ////                        },
        //                onSubmit: function(file, ext) {//ext file suffix
        //
        //                    if (!(ext && /^(mp4)$/.test(ext))) {
        //                        alert('请上传MP4格式视频文件！');
        //                        return false;
        //                    }
        //
        ////                        warn.html('正在上传...');
        //                    //loading事件
        //                    dialog({
        //                        id: 'file_upload',
        //                        title: '添加中，请稍后...',
        //                        width: 150,
        //                        quickClose: true
        //                    }).show();
        //                },
        //                onComplete: function(file, response) {
        //                    dialog.get('file_upload').close();
        //                    if (response != "error" && response != "") {
        ////                        warn.html('上传成功！');
        //                        $("#video_status").html(response);
        //                        $("#video_status").attr('value', response);
        //                    } else {
        //                        alert('上传失败，请重新上传！');
        //                    }
        //
        //                }
        //            });
        //        });

        //        $('input[id=lefile]').change(function() {
        //            $('#dev_multi_data').val($(this).val());
        //        });

        $("#btn-multi-add").click(function () {
            var community_info = $("#community_info").val();
            var sr_info = $("#sr_info").val();
            if (community_info == 'all' || sr_info == 'all') {
                var d = dialog({
                    content: '请选择所属小区及分前端！'
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 1500);
                return;
            }
//            var file_flag = $("#file_flag").attr('value');
//            if (file_flag == 0) {
//                var d = dialog({
//                    content: '请先上传设备文件！'
//                });
//                d.show();
//                setTimeout(function () {
//                    d.close().remove();
//                }, 1500);
//                return;
//            }

            //uploading both data and file in one form using Ajax
            //resolution: http://stackoverflow.com/questions/21060247/send-formdata-and-string-data-together-through-jquery-ajax
            var formData = new FormData();
            formData.append('file', $('#file')[0].files[0]);
            formData.append('community_info', community_info);
            formData.append('sr_info', sr_info);
            if ($('#file').val() == '') {
                var info = dialog({
                    content: '请选择需要添加的文件'
                });
                info.show();
                setTimeout(function () {
                    info.close().remove();
                }, 1500);
                return false;
            }
            //loading事件
            dialog({
                id: 'result_info',
                title: '设备批量添加中，请稍后...',
                width: 'auto',
                quickClose: true
            }).show();
            $.ajax({
                url: '<?php echo site_url('device_info/add_multi_dev') ?>',
                type: 'POST',
                cache: false,
                data: formData,
                processData: false,
                contentType: false
            }).done(function (res) {
                var res = jQuery.parseJSON(res);//解析JSON
//                alert(res.result);
                if (res.result == 'file_error') {
                    var info = dialog({
                        content: '上传文件类型错误，只能上传excel文件'
                    });
                    info.show();
                    setTimeout(function () {
                        dialog.get('result_info').close();
                        info.close().remove();
                    }, 1500);
                    return false;
                } else if (res.result == 'fail') {
                    var info = dialog({
                        content: '添加失败，请稍后再试'
                    });
                    info.show();
                    setTimeout(function () {
                        dialog.get('result_info').close();
                        info.close().remove();
                    }, 1500);
                    return false;
                } else {
                    $("#btn-multi-add").html("添加成功");
                    $("#btn-multi-add").attr('disabled', true);
                    var info = dialog({
                        content: '添加成功<br>添加成功局端数：'+res.success_num+'<br>添加失败局端数：'+res.fail_num
                    });
                    info.show();
                    setTimeout(function () {
                        dialog.get('result_info').close();
//                        info.close().remove();
                    }, 1000);
                    return false;
                }
            }).fail(function (res) {
                var info = dialog({
                    content: '上传失败，请稍后再试'
                });
                info.show();
                setTimeout(function () {
                    dialog.get('result_info').close();
                    info.close().remove();
                }, 1500);
                return false;
            });

        });

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


    </script>

<?php require_once('common/footer.php'); ?>