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

                            @can($access . '-import')
                                <a href="{{ route('admin.exam-attendance.import') }}" class="btn btn-dark btn-sm float-right">
                                    <i class="fas fa-upload"></i> {{ __('btn_import') }}
                                </a>
                            @endcan
                        </div>
                        <div class="card-block">
                            <form class="needs-validation" novalidate method="get"
                                action="{{ route($route . '.index') }}">
                                <div class="row gx-2">
                                    @include('common.inc.subject_search_filter')

                                    <div class="form-group col-md-3">
                                        <button type="submit" class="btn btn-info btn-filter">
                                            <i class="fas fa-search"></i> {{ __('btn_search') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="card">
                        <form class="needs-validation" novalidate action="{{ route($route . '.store') }}" method="post"
                            enctype="multipart/form-data" id="update-form">
                            @csrf

                            @if (isset($rows, $rows->first()->exams))
                                <div class="card-block">
                                    <!-- [ Data table ] start -->
                                    <div class="table-responsive">
                                        <table class="display table nowrap table-striped table-hover printable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('field_student_id') }}</th>
                                                    <th>{{ __('field_name') }}</th>
                                                    @foreach ($rows->first()->exams->groupBy('exam_type_id') as $exam_type_id => $exams)
                                                        <th>{{ $exams->first()->type->title }}
                                                            ({{ $exams->first()->type->marks }})
                                                        </th>
                                                    @endforeach
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
                                                        @foreach ($row->exams->where('subject_id' , request('subject'))->groupBy('exam_type_id') as $exam_type_id => $exams)
                                                            @foreach ($exams as $exam)
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                        name="marks[{{ $exam->id }}]" id="marks"
                                                                        value="{{ $exam->achieve_marks ? round($exam->achieve_marks, 2) : '' }}"
                                                                        style="width: 100px;"
                                                                        data-v-max="{{ $exam->marks }}" data-v-min="0">
                                                                </td>
                                                            @endforeach
                                                        @endforeach
                                                        <td>{{ $row->subject->code ?? '' }}</td>
                                                        <td>{{ $row->type->title ?? '' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- [ Data table ] end -->
                                </div>

                                @if (count($rows) > 0)
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-success update">
                                            <i class="fas fa-check"></i> {{ __('btn_update') }}
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="card-block text-center">
                                    <h5>{{ __('no_result_found') }}</h5>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- End Content-->

@endsection

@section('page_js')
    <script>
        function checkMaxMarkAndSubmit() {
            // Get all marks input fields
            var marksInputs = document.querySelectorAll('input[name^="marks"]');

            // Flag to track if any mark is greater than its data-v-max value
            var hasInvalidMark = false;

            // Iterate over marks input fields
            marksInputs.forEach(function(input) {
                var mark = parseFloat(input.value);
                var maxMark = parseFloat(input.getAttribute('data-v-max'));
                if (mark > maxMark) {
                    hasInvalidMark = true;
                    // Display error message using toastr
                    toastr.error('MAX MARK IS ' + maxMark);

                    // Stop iteration
                    return false;
                }
            });

            // If any mark is greater than its data-v-max value, prevent form submission
            if (hasInvalidMark) {
                return false;
            }

            // If all marks are valid, allow form submission
            return true;
        }

        // Attach event listener to form submission event
        document.getElementById('update-form').addEventListener('submit', function(event) {
            // Call checkMaxMarkAndSubmit function before form submission
            if (!checkMaxMarkAndSubmit()) {
                // If checkMaxMarkAndSubmit returns false, prevent form submission
                event.preventDefault();
            }
        });
    </script>
@endsection
