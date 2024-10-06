 <!-- sidebar menu area start -->
 @php
     $usr = Auth::guard('admin')->user();
 @endphp
 <div class="sidebar-menu">
     <div class="sidebar-header">
         <div class="logo">
             <a href="{{ route('admin.dashboard') }}">
                 {{--                <h2 class="text-white">Admin</h2> --}}
                 <img src="{{ asset('images/logo.png') }}" />
             </a>
         </div>
     </div>
     <div class="main-menu">
         <div class="menu-inner">
             <nav>
                 <ul class="metismenu" id="menu">

                     @if ($usr->can('dashboard.view'))
                         <li class="active">
                             <a href="javascript:void(0)" aria-expanded="true"><i
                                     class="ti-dashboard"></i><span>dashboard</span></a>
                             <ul class="collapse">
                                 <li class="{{ Route::is('admin.dashboard') ? 'active' : '' }}"><a
                                         href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                             </ul>
                         </li>
                     @endif

                     @if ($usr->can('role.create') || $usr->can('role.view') || $usr->can('role.edit') || $usr->can('role.delete'))
                         <li>
                             <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                                     Roles & Permissions
                                 </span></a>
                             <ul
                                 class="collapse {{ Route::is('admin.roles.create') || Route::is('admin.roles.index') || Route::is('admin.roles.edit') || Route::is('admin.roles.show') ? 'in' : '' }}">
                                 @if ($usr->can('role.view'))
                                     <li
                                         class="{{ Route::is('admin.roles.index') || Route::is('admin.roles.edit') ? 'active' : '' }}">
                                         <a href="{{ route('admin.roles.index') }}">All Roles</a>
                                     </li>
                                 @endif
                                 @if ($usr->can('role.create'))
                                     <li class="{{ Route::is('admin.roles.create') ? 'active' : '' }}"><a
                                             href="{{ route('admin.roles.create') }}">Create Role</a></li>
                                 @endif
                             </ul>
                         </li>
                     @endif


                     @if ($usr->can('admin.create') || $usr->can('admin.view') || $usr->can('admin.edit') || $usr->can('admin.delete'))
                         <li>
                             <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                                     Admins
                                 </span></a>
                             <ul
                                 class="collapse {{ Route::is('admin.admins.create') || Route::is('admin.admins.index') || Route::is('admin.admins.edit') || Route::is('admin.admins.show') ? 'in' : '' }}">

                                 @if ($usr->can('admin.view'))
                                     <li
                                         class="{{ Route::is('admin.admins.index') || Route::is('admin.admins.edit') ? 'active' : '' }}">
                                         <a href="{{ route('admin.admins.index') }}">All Admins</a>
                                     </li>
                                 @endif

                                 @if ($usr->can('admin.create'))
                                     <li class="{{ Route::is('admin.admins.create') ? 'active' : '' }}"><a
                                             href="{{ route('admin.admins.create') }}">Create Admin</a></li>
                                 @endif
                             </ul>
                         </li>
                     @endif

                     {{-- @if ($usr->can('admin.create') || $usr->can('admin.view') || $usr->can('admin.edit') || $usr->can('admin.delete')) --}}
                        <li>
                            <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-users"></i><span>
                                    Users
                                </span></a>
                            <ul class="collapse {{ Route::is('admin.users.create') || Route::is('admin.users.index') || Route::is('admin.users.edit') || Route::is('admin.users.show') ? 'in' : '' }}">

                                {{-- @if ($usr->can('admin.view')) --}}
                                    <li class="{{ Route::is('admin.users.index') || Route::is('admin.users.edit') ? 'active' : '' }}">
                                        <a href="{{ route('admin.users.index') }}">All Users</a>
                                    </li>
                                {{-- @endif --}}

                                {{-- @if ($usr->can('admin.create')) --}}
                                    <li class="{{ Route::is('admin.users.create') ? 'active' : '' }}">
                                        <a href="{{ route('admin.users.create') }}">Create User</a>
                                    </li>
                                {{-- @endif --}}


                            </ul>
                        </li>
                    {{-- @endif --}}



                     <li>
                         <a href="javascript:void(0)" aria-expanded="true">
                             <i class="fa fa-file"></i>
                             <span>Content Management</span>
                         </a>
                         <ul class="collapse {{ Route::is('content.index') ? 'in' : '' }}">
                             <li class="{{ Route::is('content.index') ? 'active' : '' }}">
                                 <a href="{{ route('content.index') }}">Add Content</a>
                             </li>
                         </ul>
                     </li>

                     <li>
                         <a href="javascript:void(0)" aria-expanded="true">
                             <i class="fa fa-file"></i>
                             <span>Server Monitor</span>
                         </a>
                         <ul class="collapse {{ Route::is('server-monitor.*') ? 'in' : '' }}">
                             <li class="{{ Route::is('server-monitor.index') ? 'active' : '' }}">
                                 <a href="{{ route('server-monitor.index') }}">Server List</a>
                             </li>
                             <li class="{{ Route::is('server-monitor.create') ? 'active' : '' }}">
                                 <a href="{{ route('server-monitor.create') }}">Add Server</a>
                             </li>
                         </ul>
                     </li>
                 </ul>
             </nav>
         </div>
     </div>
 </div>
 <!-- sidebar menu area end -->
