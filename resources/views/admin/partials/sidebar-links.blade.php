<a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i data-feather="home"></i><span>Dashboard</span>
</a>
<a href="{{ route('admin.slots') }}" class="{{ request()->routeIs('admin.slots*') ? 'active' : '' }}">
    <i data-feather="clock"></i><span>Time Slots</span>
</a>
<a href="{{ route('admin.appointments') }}" class="{{ request()->routeIs('admin.appointments') ? 'active' : '' }}">
    <i data-feather="calendar"></i><span>Consultations</span>
</a>
<a href="{{ route('admin.my-services-schedules') }}" class="{{ request()->routeIs('admin.my-services-schedules') ? 'active' : '' }}">
    <i data-feather="calendar"></i><span>Pending Services</span>
</a>
<a href="{{ route('admin.my-services-schedules.confirmed') }}" class="{{ request()->routeIs('admin.my-services-schedules.confirmed') ? 'active' : '' }}">
    <i data-feather="check-circle"></i><span>Service Schedules</span>
</a>
<a href="{{ route('admin.patients') }}" class="{{ request()->routeIs('admin.patients') || request()->routeIs('admin.branch.patients') ? 'active' : '' }}">
    <i data-feather="users"></i><span>Patient History</span>
</a>
<a href="{{ route('admin.categories') }}" class="{{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
    <i data-feather="layers"></i><span>Categories</span>
</a>
<a href="{{ route('admin.services') }}" class="{{ request()->routeIs('admin.services*') ? 'active' : '' }}">
    <i data-feather="briefcase"></i><span>Services</span>
</a>
<a href="{{ route('admin.promos') }}" class="{{ request()->routeIs('admin.promos*') ? 'active' : '' }}">
    <i data-feather="star"></i><span>Promotions</span>
</a>
<a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">
    <i data-feather="user"></i><span>Profile</span>
</a>
<a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
    <i data-feather="list"></i><span>Notifications</span>
</a>

