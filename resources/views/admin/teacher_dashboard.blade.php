@extends('admin.layouts.master')
@section('title', $title)
@section('page_css')
    <style>
        #pieChart {
            max-width: 100% !important;
            max-height: 500px !important;
        }

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
            <!-- IAU LOGIN -->
            <div class="row">
                {{-- <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('University Library') }}</h5> --}}
                            {{-- <p class="card-text">With supporting text below as a natural lead-in to additional content.</p> --}}
                            {{-- <a href="<?= url('') ?>/auto-login/university-library" class="btn btn-primary">Signin</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('IAU Study Room') }}</h5> --}}
                            {{-- <p class="card-text">With supporting text below as a natural lead-in to additional content.</p> --}}
                            {{-- <a href="https://iauonline.net/" target="__blank" class="btn btn-primary">Signin</a>
                        </div>
                    </div>
                </div> --}}

                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Omaintec Academy Room') }}</h5>
                            {{-- <p class="card-text">With supporting text below as a natural lead-in to additional content.</p> --}}
                            <a href="{{ route('admin.moodle.login') }}" class="btn btn-primary">Signin</a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <!-- [ bitcoin-wallet section ] start-->
                <div class="col-sm-4 col-md-4 col-xl-4 button-divs d-none"
                    onclick='window.location.href="{{ route('admin.exam-marking.index') }}"'>
                    <div class="card bg-c-blue bitcoin-wallet">
                        <div class="card-block">
                            <h5 class="text-white mb-2">
                                {{ trans_choice('module_grade', 1) }}</h5>
                            <i class="fa-solid fa-scroll f-70 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-xl-4 button-divs d-none"
                    onclick='window.location.href="{{ route('admin.exam-type.index') }}"'>
                    <div class="card bg-c-blue bitcoin-wallet">
                        <div class="card-block">
                            <h5 class="text-white mb-2"> {{ __('module_exam_type') }}
                            </h5>
                            <i class="fas fa-user-graduate f-70 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-xl-4 button-divs d-none"
                    onclick='window.location.href="{{ route('admin.student.index') }}"'>
                    <div class="card bg-c-blue bitcoin-wallet">
                        <div class="card-block">
                            <h5 class="text-white mb-2">{{ trans_choice('module_student', 2) }}
                            </h5>
                            <i class="fas fa-user-tag f-70 text-white"></i>
                        </div>
                    </div>
                </div>
                <!-- [ bitcoin-wallet section ] end-->
            </div>
            {{-- Class Schedule --}}
            <div class="row">
                @if (isset($class_routines))
                    <div class="card-block bg-light">
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
                                                @foreach ($class_routines->where('day', $weekday)->where('teacher_id',Auth::user()->id) as $row)
                                                    <a href="javascript:void(0);"
                                                        onclick="MeetingDialog.show(<?= $row->id ?>);">
                                                        <div class="class-time">
                                                            {{ $row->subject->code ?? '' }}<br>
                                                            @if (isset($setting->time_format))
                                                                {{ date($setting->time_format, strtotime($row->start_time)) }}
                                                            @else
                                                                {{ date('h:i A', strtotime($row->start_time)) }}
                                                            @endif <br>
                                                            @if (isset($setting->time_format))
                                                                {{ date($setting->time_format, strtotime($row->end_time)) }}
                                                            @else
                                                                {{ date('h:i A', strtotime($row->end_time)) }}
                                                            @endif
                                                            <br>
                                                            {{ __('field_room') }}:
                                                            {{ $row->room->title ?? '' }}<br>
                                                            {{ $row->teacher->staff_id }} -
                                                            {{ $row->teacher->first_name ?? '' }}
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
            <!-- Event Calendar start -->
            <div class="row">
                <div class="col-xl-8 col-md-8 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ $title }}</h5>
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
            <!-- Event Calendar end -->
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
                        foreach ($event_rows as $key => $row) {
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

