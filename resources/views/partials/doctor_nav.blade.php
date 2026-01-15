<a href="{{ route('doctor.dashboard') }}" class="{{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
    <i data-feather="home"></i><span>Dashboard</span>
</a>
<a href="{{ route('doctor.slots') }}" class="{{ request()->routeIs('doctor.slots*') ? 'active' : '' }}">
    <i data-feather="clock"></i><span>Time Slots</span>
</a>
<a href="{{ route('doctor.all-appointments') }}" class="{{ request()->routeIs('doctor.all-appointments*') ? 'active' : '' }}">
    <i data-feather="calendar"></i><span>All Bookings</span>
</a>
<a href="{{ route('doctor.my-services-schedules') }}" class="{{ request()->routeIs('doctor.my-services-schedules') && !request()->routeIs('doctor.my-services-schedules.confirmed') ? 'active' : '' }}">
    <i data-feather="calendar"></i><span>Pending Services</span>
</a>
<a href="{{ route('doctor.my-services-schedules.confirmed') }}" class="{{ request()->routeIs('doctor.my-services-schedules.confirmed') ? 'active' : '' }}">
    <i data-feather="check-circle"></i><span>Service Schedules</span>
</a>
<a href="{{ route('doctor.categories') }}" class="{{ request()->routeIs('doctor.categories*') ? 'active' : '' }}">
    <i data-feather="folder"></i><span>Categories</span>
</a>
<a href="{{ route('doctor.services') }}" class="{{ request()->routeIs('doctor.services*') ? 'active' : '' }}">
    <i data-feather="activity"></i><span>Services</span>
</a>
<a href="{{ route('doctor.branches') }}" class="{{ request()->routeIs('doctor.branches*') ? 'active' : '' }}">
    <i data-feather="map-pin"></i><span>Branches</span>
</a>
<a href="{{ route('doctor.promos.index') }}" class="{{ request()->routeIs('doctor.promos*') ? 'active' : '' }}">
    <i data-feather="star"></i><span>Promotions</span>
</a>
<a href="{{ route('doctor.notifications.index') }}" class="{{ request()->routeIs('doctor.notifications.*') ? 'active' : '' }}">
    <i data-feather="list"></i><span>Notifications</span>
</a>
<a href="{{ route('doctor.history') }}" class="{{ request()->routeIs('doctor.history*') ? 'active' : '' }}">
    <i data-feather="file-text"></i><span>History</span>
</a>

