<?php require_once('common/header.php'); ?>
<?php require_once('common/menu.php'); ?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">基本信息概况</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-th-large fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $dev_total_num; ?></div>
                            <div>局端总数</div>
                        </div>
                    </div>
                </div>
                <a href="<?php echo site_url('manager/device_list') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">查看详细信息...</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa  fa-archive  fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $community_total_num; ?></div>
                            <div>小区个数</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left">查看详细信息...</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-desktop fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $sr_total_num; ?></div>
                            <div>分前端个数</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left">查看详细信息...</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-institution fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $branches_total_num; ?></div>
                            <div>分公司个数</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left">查看详细信息...</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> 分前端设备占比统计
                </div>
                <div class="panel-body">
                    <div id="dev_statistic_orderby_sr" style="width: auto;height:300px;">
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-6 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> 小区设备占比统计
                </div>
                <div class="panel-body">
                    <div id="dev_statistic_orderby_com" style="width: auto;height:300px;">
                    </div>
                </div>
            </div>
        </div>


    </div>

    <hr/>

</div>

<script src="resource/ECharts/echarts.min.js"></script>

<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var dev_statistic_orderby_sr = echarts.init(document.getElementById('dev_statistic_orderby_sr'));

    // 指定图表的配置项和数据
    option = {
        title: {
            text: '分前端设备统计',
//            subtext: '',
            x: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: "{a}： <br/>{b} : {c}台 ({d}%)"
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data:
                [<?php foreach ($dev_statistic_sr_info as $item){
                    echo "'".$item['name']."',";
                } ?>]
        },
        series: [
            {
                name: '设备来源',
                type: 'pie',
                radius: '55%',
                center: ['50%', '60%'],
                data: [
                    <?php
                    foreach ($dev_statistic_sr_info as $item){
                        echo "{value:".$item['value'].",name:'".$item['name']."'},";
                    }
                    ?>
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };

    // 使用刚指定的配置项和数据显示图表。
    dev_statistic_orderby_sr.setOption(option);


    var dev_statistic_orderby_com = echarts.init(document.getElementById('dev_statistic_orderby_com'));

    // 指定图表的配置项和数据
    option = {
        title: {
            text: '小区设备统计',
//            subtext: '',
            x: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: "{a}： <br/>{b} : {c}台 ({d}%)"
        },
//        legend: {
//            orient: 'vertical',
//            left: 'left',
//            data:
//                [<?php //foreach ($dev_statistic_com_info as $item){
//                    echo "'".$item['name']."',";
//                } ?>//]
//        },
        series: [
            {
                name: '设备来源',
                type: 'pie',
                radius: '55%',
                center: ['50%', '50%'],
                data: [
                    <?php
                    foreach ($dev_statistic_com_info as $item){
                        echo "{value:".$item['value'].",name:'".$item['name']."'},";
                    }
                    ?>
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };

    // 使用刚指定的配置项和数据显示图表。
    dev_statistic_orderby_com.setOption(option);


//    var dev_all_statistic = echarts.init(document.getElementById('dev_all_statistic'));
//
//    option = {
//        title: {
//            text: '某地区蒸发量和降水量',
//            subtext: '纯属虚构'
//        },
//        tooltip: {
//            trigger: 'axis'
//        },
//        legend: {
//            data: ['蒸发量', '降水量']
//        },
//        toolbox: {
//            show: true,
//            feature: {
//                dataView: {show: true, readOnly: false},
//                magicType: {show: true, type: ['line', 'bar']},
//                restore: {show: true},
//                saveAsImage: {show: true}
//            }
//        },
//        calculable: true,
//        xAxis: [
//            {
//                type: 'category',
//                data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
//            }
//        ],
//        yAxis: [
//            {
//                type: 'value'
//            }
//        ],
//        series: [
//            {
//                name: '蒸发量',
//                type: 'bar',
//                data: [2.0, 4.9, 7.0, 23.2, 25.6, 76.7, 135.6, 162.2, 32.6, 20.0, 6.4, 3.3],
//                markPoint: {
//                    data: [
//                        {type: 'max', name: '最大值'},
//                        {type: 'min', name: '最小值'}
//                    ]
//                },
//                markLine: {
//                    data: [
//                        {type: 'average', name: '平均值'}
//                    ]
//                }
//            },
//            {
//                name: '降水量',
//                type: 'bar',
//                data: [2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3],
//                markPoint: {
//                    data: [
//                        {name: '年最高', value: 182.2, xAxis: 7, yAxis: 183},
//                        {name: '年最低', value: 2.3, xAxis: 11, yAxis: 3}
//                    ]
//                },
//                markLine: {
//                    data: [
//                        {type: 'average', name: '平均值'}
//                    ]
//                }
//            }
//        ]
//    };
//
//    dev_all_statistic.setOption(option);


</script>

<!-- /#page-wrapper -->

<?php require_once('common/footer.php'); ?>

