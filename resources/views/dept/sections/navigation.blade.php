<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="logo-div">
        <img src="{{ asset('logo.png') }}" class="logo-img"/>
        </div>
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('dept.dashboard') }}" class="site_title">
            </a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="{{ auth()->user()->avatar }}" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <h2>{{ auth()->user()->name }}</h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
           
                <ul class="nav side-menu">
                    <li>
                        <a href="{{ route('dept.dashboard') }}">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            {{ __('views.backend.section.navigation.menu_0_1') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dept.shiftBatch') }}">
                            <i class="fa fa-flag" aria-hidden="true"></i>
                            Shift Status
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dept.shift') }}">
                            <i class="fa fa-external-link" aria-hidden="true"></i>
                            Assign Shift
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dept.employeeReassignList') }}">
                            <i class="fa fa-exchange" aria-hidden="true"></i>
                            Re-Assign Shift
                        </a>
                    </li>
                    <!-- <li>
                        <a href="{{ route('dept.otherDept') }}">
                            <i class="fa fa-exchange" aria-hidden="true"></i>
                            Other Shift Assign
                        </a>
                    </li> -->
                    <li>
                        <a href="{{ route('logout') }}">
                            <i class="fa fa-sign-out" aria-hidden="true"></i>
                            {{ __('views.backend.section.header.menu_0') }}
                        </a>
                    </li>
                </ul>
        </div>
        <!-- /sidebar menu -->
    </div>
</div>
