<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="logo-div">
            <img src="{{ asset('logo.png') }}" class="logo-img"/>
        </div>
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('admin.dashboard') }}" class="site_title">
                <span>{{ config('app.name') }}</span>
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
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_0') }}</h3>
                <ul class="nav side-menu">
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            {{ __('views.backend.section.navigation.menu_0_1') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_1') }}</h3>
                <ul class="nav side-menu">
                    <li>
                        <a href="{{ route('admin.departments.index') }}">
                            <i class="fa fa-institution" aria-hidden="true"></i>
                            Departments
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users') }}">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}">
                            <i class="fa fa-institution" aria-hidden="true"></i>
                            Categories
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.leaves.index') }}">
                            <i class="fa fa-institution" aria-hidden="true"></i>
                            Leave
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shift') }}">
                            <i class="fa fa-institution" aria-hidden="true"></i>
                            Shift
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.statuses.index') }}">
                            <i class="fa fa-institution" aria-hidden="true"></i>
                            Status
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.employee') }}">
                            <i class="fa fa-institution" aria-hidden="true"></i>
                            Work Type
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.employee') }}">
                            <i class="fa fa-institution" aria-hidden="true"></i>
                            Employee
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}">
                            <i class="fa fa-sign-out" aria-hidden="true"></i>
                            {{ __('views.backend.section.header.menu_0') }}
                        </a>
                    </li>
                </ul>
                </div>
        </div>
        <!-- /sidebar menu -->
    </div>
</div>
