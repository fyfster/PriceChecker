            <!-- BEGIN HEADER -->
            <div class="page-header navbar navbar-fixed-top">
                <!-- BEGIN HEADER INNER -->
                <div class="page-header-inner">
                    <!-- BEGIN LOGO -->
                    <div class="page-logo">
                        <a href="{{route('/')}}">
                            <img src="{{$base_url}}/assets/company/logo_1.jpg" alt="logo" class="logo-default" style="margin: 4px;">
                        </a>
                        <div class="menu-toggler sidebar-toggler {{!isset($menu_items) || empty($menu_items) ? "hide" : ""}}">
                            <span></span>
                            <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
                        </div>
                    </div>
                    <!-- END LOGO -->
                    <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                    <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                        <span></span>
                    </a>
                    <!-- END RESPONSIVE MENU TOGGLER -->
                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            <!-- BEGIN USER LOGIN DROPDOWN -->
                            <li class="dropdown dropdown-user">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <span class="username">
                                {{$username}} </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{route('profile')}}">
                                            <i class="icon-user"></i> @lang('common.menu_my_profile')</a>
                                    </li>
                                    <li>
                                        <a href="{{route('forgot-password')}}">
                                            <i class="icon-lock"></i> @lang('common.menu_reset_password')</a>
                                    </li>
                                    <li class="divider">
                                    </li>
                                    <li>
                                        <a href="{{route('logout')}}">
                                            <i class="icon-key"></i> @lang('common.menu_logout')</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- END USER LOGIN DROPDOWN -->
                        </ul>
                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
                <!-- END HEADER INNER -->
            </div>
            <!-- END HEADER -->