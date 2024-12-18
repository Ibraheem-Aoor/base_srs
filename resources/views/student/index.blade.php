@extends('student.layouts.master')
@section('title', $title)

@section('page_css')
    <style>
        .button-divs {
            cursor: pointer !important;
        }
    </style>
    <!-- Full calendar css -->
    <link rel="stylesheet" href="{{ asset('dashboard/plugins/fullcalendar/css/fullcalendar.min.css') }}">
@endsection

@section('content')

    <!-- Start Content-->
    <div class="main-body">
        <div class="page-wrapper">
            <!-- START IAU SignIn -->
            <div class="row">
                {{-- <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('E Library') }}</h5> --}}
                            {{-- <p class="card-text">With supporting text below as a natural lead-in to additional content.</p> --}}
                            {{-- <a href="<?= url('') ?>/auto-login/university-library" class="btn btn-primary">Signin</a>
                        </div>
                    </div>
                </div> --}}

                <div class="col-sm-4">
                    {{-- <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('IAU Study Room') }}</h5> --}}
                            {{-- <p class="card-text">With supporting text below as a natural lead-in to additional content.</p> --}}
                            {{-- <a href="https://iauonline.net/" target="__blank" class="btn btn-primary">Signin</a>
                        </div>
                    </div> --}}
                </div>

                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Omaintec Academy Room') }}</h5>
                            {{-- <p class="card-text">With supporting text below as a natural lead-in to additional content.</p> --}}
                            <a href="{{ route('student.moodle.login') }}" class="btn btn-primary">Signin</a>
                        </div>
                    </div>
                </div>

            </div>
            <!-- END IAU Sign  -->

            <div class="row">
                <!-- [ bitcoin-wallet section ] start-->
                {{-- <div class="col-sm-6 col-md-6 col-xl-6 button-divs"
                    onclick='window.location.href="{{ route('student.transcript.index') }}"'>
                    <div class="card bg-c-blue bitcoin-wallet">
                        <div class="card-block">
                            <h5 class="text-white mb-2">
                                {{ trans_choice('field_grades', 1) }}</h5>
                            <i class="fa-solid fa-scroll f-70 text-white"></i>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="col-sm-6 col-md-6 col-xl-6 button-divs"
                    onclick='window.location.href="{{ route('student.fees.index') }}"'>
                    <div class="card bg-c-blue bitcoin-wallet">
                        <div class="card-block">
                            <h5 class="text-white mb-2"> {{ trans_choice('module_fees_report', 1) }}
                            </h5>
                            <i class="fas fa-money-bill-wave f-70 text-white"></i>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="col-sm-4 col-md-4 col-xl-4 button-divs"
                    onclick='window.location.href="{{ route('student.exam-routine.index') }}"'>
                    <div class="card bg-c-blue bitcoin-wallet">
                        <div class="card-block">
                            <h5 class="text-white mb-2">{{ __('field_exam') }}
                            </h5>
                            <i class="fas fa-file-alt f-70 text-white"></i>
                        </div>
                    </div>
                </div> --}}
                <!-- [ bitcoin-wallet section ] end-->
            </div>

            <div class="row">

                {{-- @php
                    function field($slug)
                    {
                        return \App\Models\Field::field($slug);
                    }
                @endphp --}}

                {{-- @if (field('panel_assignment')->status == 1)
                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            @if (isset($assignments))
                                <div class="card-header">
                                    <h5>{{ trans_choice('module_assignment', 2) }}</h5>
                                </div>
                                <div class="card-block">
                                    <!-- [ Data table ] start -->
                                    <div class="table-responsive">
                                        <table class="display table nowrap table-striped table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('field_title') }}</th>
                                                    <th>{{ __('field_subject') }}</th>
                                                    <th>{{ __('field_status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($assignments as $key => $row)
                                                    @if ($row->assignment->status == 1)
                                                        <tr>
                                                            <td>
                                                                <a
                                                                    href="{{ route('student.assignment.show', $row->id) }}">{!! str_limit($row->assignment->title ?? '', 50, ' ...') !!}</a>
                                                            </td>
                                                            <td>{{ $row->assignment->subject->code ?? '' }}</td>
                                                            <td>
                                                                @if ($row->attendance == 1)
                                                                    <span
                                                                        class="badge badge-pill badge-success">{{ __('status_submitted') }}</span>
                                                                @else
                                                                    <span
                                                                        class="badge badge-pill badge-primary">{{ __('status_pending') }}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- [ Data table ] end -->
                                </div>
                            @endif
                        </div>
                    </div>
                @endif --}}

                {{-- @if (field('panel_fees_report')->status == 1)
                    <div class="col-sm-12 col-lg-6">
                        <div class="card">
                            @if (isset($fees))
                                <div class="card-header">
                                    <h5>{{ trans_choice('module_student_fees', 2) }}</h5>
                                </div>
                                <div class="card-block">
                                    <!-- [ Data table ] start -->
                                    <div class="table-responsive">
                                        <table class="display table nowrap table-striped table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('field_fees_type') }}</th>
                                                    <th>{{ __('field_fee') }}</th>
                                                    <th>{{ __('field_status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($fees as $key => $row)
                                                    <tr>
                                                        <td>{{ $row->category->title ?? '' }}</td>
                                                        <td>
                                                            @if (isset($setting->decimal_place))
                                                                {{ number_format((float) $row->fee_amount, $setting->decimal_place, '.', '') }}
                                                            @else
                                                                {{ number_format((float) $row->fee_amount, 2, '.', '') }}
                                                            @endif
                                                            {!! $setting->currency_symbol !!}
                                                        </td>
                                                        <td>
                                                            @if ($row->status == 1)
                                                                <span
                                                                    class="badge badge-pill badge-success">{{ __('status_paid') }}</span>
                                                            @elseif($row->status == 2)
                                                                <span
                                                                    class="badge badge-pill badge-danger">{{ __('status_canceled') }}</span>
                                                            @else
                                                                <span
                                                                    class="badge badge-pill badge-primary">{{ __('status_pending') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- [ Data table ] end -->
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ trans_choice('module_notification', 2) }}</h5>
                            </div>
                            <div class="card-block">
                                <!-- [ Data table ] start -->
                                <div class="table-responsive">
                                    <table class="display table nowrap table-striped table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>{{ __('field_title') }}</th>
                                                <th>{{ __('field_type') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (Auth::guard('student')->user()->unreadNotifications as $key => $notification)
                                                @if ($key < 10)
                                                    @php
                                                        $notification_link = 'student.dashboard.index';
                                                        $notification_type = '';
                                                        if ($notification->data['type'] == 'content') {
                                                            $notification_link = 'student.download.show';
                                                            $notification_type = trans_choice('module_content', 1);
                                                        } elseif ($notification->data['type'] == 'notice') {
                                                            $notification_link = 'student.notice.show';
                                                            $notification_type = trans_choice('module_notice', 1);
                                                        } elseif ($notification->data['type'] == 'assignment') {
                                                            $notification_link = 'student.assignment.index';
                                                            $notification_type = trans_choice('module_assignment', 1);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            @if ($notification->data['type'] == 'assignment')
                                                                <a class="media"
                                                                    href="{{ route($notification_link) }}">{{ str_limit($notification->data['title'], 30, ' ...') }}</a>
                                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                            @else
                                                                <a class="media"
                                                                    href="{{ route($notification_link, $notification->data['id']) }}">{{ str_limit($notification->data['title'], 30, ' ...') }}</a>
                                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $notification_type }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- [ Data table ] end -->
                            </div>
                        </div>
                    </div>
                @endif --}}

            </div>
            {{-- Start Class Schedule --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ trans_choice('module_class_routine' , 1) }}</h5>
                        </div>
                        @if (isset($class_routine_rows))
                            <div class="card-block">
                                <!-- [ Data table ] start -->
                                <div class="table-responsive">
                                    <table class="table class-routine-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('day_saturday') }}</th>
                                                <th>{{ __('day_sunday') }}</th>
                                                <th>{{ __('day_monday') }}</th>
                                                <th>{{ __('day_tuesday') }}</th>
                                                <th>{{ __('day_wednesday') }}</th>
                                                <th>{{ __('day_thursday') }}</th>
                                                <th>{{ __('day_friday') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $weekdays = ['1', '2', '3', '4', '5', '6', '7'];
                                            @endphp
                                            <tr>
                                                @foreach ($weekdays as $weekday)
                                                    <td>
                                                        @foreach ($class_routine_rows->where('day', $weekday) as $row)
                                                            <a href="javascript:void(0);"
                                                                onclick="MeetingDialog.show(<?= $row->id ?>);">
                                                                <div class="class-time">
                                                                    {{ $row->subject->code ?? '' }}<br>
                                                                    @if (isset($setting->time_format))
                                                                        {{ date($setting->time_format, strtotime($row->start_time)) }}
                                                                    @else
                                                                        {{ date('h:i A', strtotime($row->start_time)) }}
                                                                    @endif <br />
                                                                    @if (isset($setting->time_format))
                                                                        {{ date($setting->time_format, strtotime($row->end_time)) }}
                                                                    @else
                                                                        {{ date('h:i A', strtotime($row->end_time)) }}
                                                                    @endif
                                                                    <br>
                                                                    {{ __('field_room') }}:
                                                                    {{ $row->room->title ?? '' }}<br>
                                                                    {{ $row->teacher->first_name ?? '' }}
                                                                    {{ $row->teacher->last_name ?? '' }}
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- [ Data table ] end -->
                            </div>
                        @endif

                    </div>
                </div>
            </div>
            {{-- End Class Schedule --}}
            {{-- Start Calendar --}}
            <div class="row">
                <div class="col-xl-8 col-md-8 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ trans_choice('module_calendar', 2) }}</h5>
                        </div>
                        <div class="card-block">

                            <!-- [ Calendar ] start -->
                            <div id='calendar' class='calendar'></div>
                            <!-- [ Calendar ] end -->

                        </div>
                    </div>
                </div>
                <!-- [Event List] start -->
                <div class="col-xl-4 col-md-4 col-sm-12">
                    <div class="card statistial-visit">
                        <div class="card-header">
                            <h5>{{ __('upcoming') }} {{ trans_choice('module_event', 1) }}</h5>
                        </div>
                        <div class="card-block">
                            @foreach ($latest_events as $key => $latest_event)
                                @if ($key <= 9)
                                    <p>
                                        <mark style="color: {{ $latest_event->color }}">
                                            <i class="fas fa-calendar-check"></i> {{ $latest_event->title }}
                                        </mark>
                                        <br>
                                        <small>
                                            @if (isset($setting->date_format))
                                                {{ date($setting->date_format, strtotime($latest_event->start_date)) }}
                                            @else
                                                {{ date('Y-m-d', strtotime($latest_event->start_date)) }}
                                            @endif

                                            @if ($latest_event->start_date != $latest_event->end_date)
                                                <i class="fas fa-exchange-alt"></i>
                                                @if (isset($setting->date_format))
                                                    {{ date($setting->date_format, strtotime($latest_event->end_date)) }}
                                                @else
                                                    {{ date('Y-m-d', strtotime($latest_event->end_date)) }}
                                                @endif
                                            @endif
                                        </small>
                                    </p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- [Event List] end -->
            </div>
            {{-- End Calendar --}}
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- End Content-->


    @include('dialog.meeting_dialog')
@endsection

@section('page_js')
    <!-- Full calendar js -->
    <script src="{{ asset('dashboard/plugins/fullcalendar/js/lib/moment.min.js') }}"></script>
    <script src="{{ asset('dashboard/plugins/fullcalendar/js/lib/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('dashboard/plugins/fullcalendar/js/fullcalendar.min.js') }}"></script>


    <script type="text/javascript">
        // Full calendar
        $(window).on('load', function() {
            "use strict";
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'today'
                },
                defaultDate: '@php echo date("Y-m-d"); @endphp',
                editable: false,
                droppable: false,
                events: [

                    @php
                        foreach ($events as $key => $row) {
                            echo "{
                                title: '" .
                                $row->title .
                                "',
                                start: '" .
                                $row->start_date .
                                "',
                                end: '" .
                                $row->end_date .
                                "',
                                borderColor: '" .
                                $row->color .
                                "',
                                backgroundColor: '" .
                                $row->color .
                                "',
                                textColor: '#fff'
                            }, ";
                        }
                    @endphp

                ],
            });
        });
    </script>
@endsection
