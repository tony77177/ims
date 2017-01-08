<!DOCTYPE html>
<html lang="zh-CN">
<head>

    <title>欢迎使用：<?php echo $this->config->config['sys_name'];?></title>
    <base href="<?php echo base_url() ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="resource/images/favicon.ico" type="image/x-icon" />
    <base href="<?php echo base_url() ?>">

    <!-- Bootstrap Core CSS -->
    <link href="resource/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="resource/css/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="resource/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="resource/css/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="resource/css/font-awesome.min.css" rel="stylesheet" type="text/css">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
<!--    <script src="resource/js/jquery-1.10.2.min.js"></script>-->
    <script type="text/javascript" src="resource/js/jquery-1.10.2.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="resource/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="resource/js/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="resource/js/raphael.min.js"></script>
    <!--<script src="resource/js/morris.min.js"></script>-->
    <!--<script src="resource/js/morris-data.js"></script>-->

    <!-- Custom Theme JavaScript -->
    <script src="resource/js/sb-admin-2.min.js"></script>


    <!-- artDialog -->
    <link href="resource/artDialog/css/ui-dialog.css" rel="stylesheet" type="text/css">
    <script src="resource/artDialog/dist/dialog-min.js"></script>


</head>

<body>

<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <img src="resource/images/logo.gif" class="navbar-brand" width="auto" height="auto"><a class="navbar-brand" href="javascript:"><?php echo $this->config->config['sys_name'].'&nbsp;'.$this->config->config['sys_version'];?></a>
        </div>

