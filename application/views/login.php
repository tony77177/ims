<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>局端信息查询</title>
    <base href="<?php echo base_url() ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Jquery -->
    <script src="resource/js/jquery-1.10.2.min.js"></script>

    <!-- Bootstrap -->
    <link href="resource/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="resource/css/sb-admin-2.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- artDialog -->
    <link href="resource/artDialog/css/ui-dialog.css" rel="stylesheet" type="text/css">
    <script src="resource/artDialog/dist/dialog-min.js"></script>

    <style>
        body{
            background: url("resource/images/login-bg.jpg") center;
        }
    </style>

</head>

<body>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">请登录</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <input type="text" class="form-control" id="user_name" name="user_name" placeholder="工&nbsp;号" autofocus>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="密&nbsp;码">
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" id="login_btn" type="button">登录</button>
                    <div id="info"></div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

<script>
    $(document).ready(function () {

        $("#login_btn").click(function () {
            var user = $("#user_name").val();
            var pwd = $("#password").val();

            if (user == '' || pwd == '') {
                var d = dialog({
                    content: '请输入工号及密码！'
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 1500);
            } else {
                $("#login_btn").html("登录中，请稍后...");
                $("#login_btn").attr('disabled', true);
                $.ajax({
                    url: "<?php echo site_url('login/check_login') ?>",
                    type: "POST",
                    data: {user: user, pwd: pwd},
                    success: function (msg) {
                        if (msg == 'fail') {
                            var d = dialog({
                                content: '工号或密码错误，请重新输入！'
                            });
                            d.show();
                            setTimeout(function () {
                                d.close().remove();
                            }, 1500);
                            $("#login_btn").html("登录");
                            $("#login_btn").attr('disabled', false);
                            return false;
                        } else {
                            $("#info").html(msg);
                        }
                    },
                    error: function () {
                        var d = dialog({
                            content: '连接数据库错误，请稍后再试！'
                        });
                        d.show();
                        setTimeout(function () {
                            d.close().remove();
                        }, 3000);
                        $("#login_btn").html("登录失败");
                    }
                });
            }
        });

        $(document).keyup(function (event) {
            if (event.keyCode == 13) {
                $("#login_btn").click();
            }
        });
    });
</script>
</html>