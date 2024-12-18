@extends('admin.layouts.master')
@section('title', $title)
@section('content')

    <!-- Start Content-->
    <div class="main-body">
        <div class="page-wrapper">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ $title }}</h5>
                        </div>
                        <div class="card-block">
                            <form class="needs-validation" novalidate method="get"
                                action="{{ route('admin.subject-result') }}">
                                <div class="row gx-2">
                                    @include('common.inc.subject_search_filter')

                                    <div class="form-group col-md-3">
                                        <button type="submit" class="btn btn-info btn-filter"><i class="fas fa-search"></i>
                                            {{ __('btn_search') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="card">
                        @if (isset($rows))
                            <div class="card-header">
                                <a href="{{ route('admin.subject-result') }}" class="btn btn-info"><i
                                        class="fas fa-sync-alt"></i> {{ __('btn_refresh') }}</a>

                                <button type="button" class="btn btn-dark btn-print">
                                    <i class="fas fa-print"></i> {{ __('btn_print') }}
                                </button>
                            </div>
                        @endif

                        @php
                            $contribution = 0;
                            $exam_contribution = 0;
                        @endphp
                        @foreach ($examTypes as $examType)
                            @php
                                $contribution = $contribution + $examType->contribution;
                                $exam_contribution = $exam_contribution + $examType->contribution;
                            @endphp
                        @endforeach
                        @isset($resultContributions)
                            @php
                                $con_attendances = $resultContributions->attendances;
                                $con_assignments = $resultContributions->assignments;
                                $con_activities = $resultContributions->activities;

                                $contribution = $contribution + $con_attendances + $con_assignments + $con_activities;
                            @endphp
                        @endisset

                        @if (isset($rows))
                            <div class="card-block">
                                <!-- [ Data table ] start -->
                                <div class="table-responsive">
                                    <table class="display table nowrap table-striped table-hover printable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('field_student_id') }}</th>
                                                <th>{{ __('field_name') }}</th>

                                                @foreach ($exam_type_category->examsTypes->sortByDesc('id') as $examType)
                                                    <th>{{ $examType->title }}</th>
                                                @endforeach
                                                <th>{{ __('field_grade') }}</th>
                                                <th>{{ __('field_point') }}</th>
                                                <th>{{ __('field_total_marks') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rows as $key => $row)
                                                <tr>
                                                    <td>
                                                        @isset($row->student->student_id)
                                                            <a href="{{ route('admin.student.show', $row->student->id) }}">
                                                                #{{ $row->student->student_id ?? '' }}
                                                            </a>
                                                        @endisset
                                                    </td>
                                                    <td>{{ $row->student->first_name ?? '' }}
                                                        {{ $row->student->last_name ?? '' }}</td>
                                                    @php
                                                        $row_total_marks = 0;
                                                    @endphp
                                                    @foreach ($exam_type_category->examsTypes->sortByDesc('id') as $examType)
                                                        @foreach ($row->exams()->where('exam_type_id', $examType->id)->get() as $exam)
                                                            @php
                                                                $row_total_marks += $exam->achieve_marks;
                                                            @endphp
                                                            @if ($exam->achieve_marks == 0)
                                                                <td>0</td>
                                                            @else
                                                                <td>{{ $exam->achieve_marks }}</td>
                                                            @endif
                                                        @endforeach
                                                    @endforeach

                                                    @foreach ($grades as $grade)
                                                        @if ($grade->min_mark <= $row_total_marks && $grade->max_mark >= $row_total_marks)
                                                            <td>{{ $grade->title }}</td>
                                                            <td>{{ number_format((float) $grade->point, 2, '.', '') }}</td>
                                                        @endif
                                                    @endforeach



                                                    <td>{{ $row_total_marks }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- [ Data table ] end -->
                            </div>

                            @if (count($rows) < 1)
                                <div class="card-block">
                                    <h5>{{ __('no_result_found') }}</h5>
                                </div>
                            @endif
                        @endif

                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- End Content-->

@endsection
