
            <div class="page-container">
                <!-- BEGIN SIDEBAR -->
                <div class="page-sidebar-wrapper">
                    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                    <div class="page-sidebar navbar-collapse collapse">
                        <!-- BEGIN SIDEBAR MENU -->
                        <ul class="page-sidebar-menu {{!isset($menu_items) || empty($menu_items) ? "hide" : ""}}" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                            <li class="sidebar-toggler-wrapper hide">
                                <div class="sidebar-toggler">
                                    <span></span>
                                </div>
                            </li>
                            <!-- END SIDEBAR TOGGLER BUTTON -->
                            @foreach($menu_items as $menuItem)
                                @php $itemAdded = 0;@endphp
                                @if( count(array_intersect($userPermissions, $menuItem['role'])) > 0 && $itemAdded === 0)
                                    @if(isset($menuItem['subItems']))
                                        <li class="nav-item{{isset($menuItem['class']) ? $menuItem['class'] : ""}}">
                                            <a href="javascript:;" class="nav-link nav-toggle custom-hover-li">
                                                <i class="icon-{{$menuItem['icon']}}"></i>
                                                <span class="title">@lang($menuItem['name'])</span>
                                                <span class="arrow"></span>
                                            </a>
                                            <ul class="sub-menu">
                                                @foreach($menuItem['subItems'] as $subItem)
                                                    @if( count(array_intersect($userPermissions, $subItem['role'])) > 0)
                                                        @if(isset($subItem['subItems']))
                                                            <li class="nav-item{{isset($subItem['class']) ? $subItem['class'] : ""}}">
                                                                <a href="javascript:;" class="nav-link nav-toggle custom-hover-li">
                                                                    <span class="title">@lang($subItem['name'])</span>
                                                                    <span class="arrow"></span>
                                                                </a>
                                                                <ul class="sub-menu">
                                                                    @foreach($subItem['subItems'] as $subSubItem)
                                                                        @if( count(array_intersect($userPermissions, $subSubItem['role'])) > 0)
                                                                            <li class="nav-item{{isset($subSubItem['class']) ? $subSubItem['class'] : ""}}">
                                                                                <a href="{{route($subSubItem['url'])}}" class="nav-link ">
                                                                                    <span class="title">@lang($subSubItem['name'])</span>
                                                                                </a>
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        @else
                                                            <li class="nav-item{{isset($subItem['class']) ? $subItem['class'] : ""}}">
                                                                <a href="{{route($subItem['url'])}}" class="nav-link ">
                                                                    <span class="title">@lang($subItem['name'])</span>
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                                <span class="sep"></span>
                                            </ul>
                                        </li>
                                    @else
                                        <li class="nav-item {{isset($menuItem['class']) ? $menuItem['class'] : ""}}">
                                            <a href="{{route($menuItem['url'])}}" class="nav-link nav-toggle">
                                                <i class="icon-{{$menuItem['icon']}}"></i>
                                                <span class="title">@lang($menuItem['name'])</span>
                                            </a>
                                        </li>
                                    @endif
                                    @php $itemAdded = 1;@endphp
                                @endif
                            @endforeach
                        </ul>
                        <!-- END SIDEBAR MENU -->
                    </div>
                </div>
                <!-- END SIDEBAR -->