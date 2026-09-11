@php
    $current_route=request()->route()->getName();

    $shopItemRequestActive = in_array($current_route, ['shopitem-request.index']) ? 'active' : '';
    $usersAllActive = in_array($current_route, ['user.index']) ? 'active' : '';
    $rolesAllActive = in_array($current_route, ['roles.index']) ? 'active' : '';
    $auditAllActive = in_array($current_route, ['audit-trail.index']) ? 'active' : '';
@endphp
@php
    $manageOpen = request()->routeIs('category.index', 'unit.index', 'item.index', 'office.index', 'year.index');
@endphp
<ul class="nav flex-column">
    <li class="px-4 py-2">
        <small class="nav-text text-muted">Main Navigation</small>
    </li>
    <li>
        <a class="nav-link {{$current_route=='dashboard.index'?'active':''}}" href="{{ route('dashboard.index') }}">
            <i class="ti ti-layout-grid"></i><span class="nav-text">Dashboard</span>
        </a>
    </li>
    @if(Auth::user()->role == 'Administrator' || Auth::user()->role == 'Checker')
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center justify-content-between {{ $manageOpen ? '' : '' }}" data-bs-toggle="collapse" href="#manageMenu" role="button" aria-expanded="false" aria-controls="manageMenu">
                <div class="d-flex align-items-center">
                    <i class="ti ti-server me-2"></i>&nbsp;
                    <span class="nav-text">Manage</span>
                </div>
                <!-- <i class="ti ti-chevron-down"></i> -->
            </a>

            <div class="collapse {{ $manageOpen ? 'show' : '' }}" id="manageMenu">
                <ul class="nav flex-column ms-3 mt-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('manage/categorylist*') ? 'active' : '' }}" href="{{ route('category.index') }}">
                            <i class="ti ti-box"></i> <span class="nav-text">Category</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('manage/unit/list*') ? 'active' : '' }}" href="{{ route('unit.index') }}">
                            <i class="ti ti-file-like"></i> <span class="nav-text">Units</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('manage/item/list*') ? 'active' : '' }}" href="{{ route('item.index') }}">
                            <i class="ti ti-shopping-cart"></i> <span class="nav-text">Items</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('manage/office/list*') ? 'active' : '' }}" href="{{ route('office.index') }}">
                            <i class="ti ti-building"></i> <span class="nav-text">Offices</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('manage/year/list*') ? 'active' : '' }}" href="{{ route('year.index') }}">
                            <i class="ti ti-calendar"></i> <span class="nav-text">Years</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    @endif 

    @if(Auth::user()->role == 'Administrator' && Auth::user()->role !='Procurement Officer' && Auth::user()->role !='Checker' && Auth::user()->role !='MIS Checker')
        <li>
            <a class="nav-link {{ $shopItemRequestActive }}" href="{{ route('shopitem-request.index') }}">
                <i class="ti ti-shopping-cart"></i><span class="nav-text">Shop Item</span>
            </a>
        </li>
    @endif

    @if(Auth::guard('web')->user()->role == 'Administrator' || Auth::guard('web')->user()->role == 'Checker')
        <li class="px-4 py-2">
            <small class="nav-text" style="color: #919191 !important">User & Log Management</small>
        </li>
        <li>
            <a class="nav-link {{$usersAllActive}}" href="{{ route('user.index') }}">
                <i class="ti ti-users"></i><span class="nav-text">Users</span>
            </a>
        </li> 
    @endif
    @if(Auth::guard('web')->user()->role == 'Administrator')
        <li>
            <a class="nav-link {{ $rolesAllActive }}" href="{{ route('roles.index') }}">
                <i class="ti ti-id-badge"></i><span class="nav-text">User Roles</span>
            </a>
        </li> 
        <li>
            <a class="nav-link {{ $auditAllActive }}" href="{{ route('audit-trail.index') }}">
                <i class="ti ti-record-mail"></i><span class="nav-text">Audit Logs</span>
            </a>
        </li> 
    @endif
</ul>