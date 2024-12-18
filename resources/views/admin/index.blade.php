@if ($auth_user->hasRole('Teacher'))
    @include('admin.teacher_dashboard')
@else
    @include('admin.admin_dashboard')
@endif
