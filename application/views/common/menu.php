<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li>
                <a href="<?php echo site_url('manager')?>"><i class="fa fa-dashboard fa-fw"></i> 主页概况</a>
            </li>
            <li>
                <a href="<?php echo site_url('manager/device_list')?>"><i class="fa fa-table fa-fw"></i> 局端列表</a>
            </li>
            <li>
                <a href="#"><i class="fa fa-wrench fa-fw"></i> 局端管理<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="panels-wells.html">局端添加</a>
                    </li>
                    <li>
                        <a href="buttons.html">局端修改</a>
                    </li>
                    <li>
                        <a href="buttons.html">局端审核</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#"><i class="fa fa-sitemap fa-fw"></i> 分类信息<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="<?php echo site_url('static_info/branch_info')?>">分公司信息</a>
                    </li>
                    <li>
                        <a href="<?php echo site_url('static_info/serverroom_info')?>">分前端信息</a>
                    </li>
                    <li>
                        <a href="#">小区信息</a>
                        <!-- /.nav-third-level -->
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="<?php echo site_url('log_info')?>"><i class="fa fa-dashboard fa-fw"></i> 操作日志</a>
            </li>
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->
</nav>