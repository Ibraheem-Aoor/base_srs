@extends('admin.layouts.master')
@section('title', $title)
@section('content')

    <!-- Start Content-->
    <div class="main-body">
        <div class="page-wrapper">
            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- [ Card ] start -->
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('modal_edit') }} {{ $title }}</h5>
                        </div>
                        <div class="card-block">
                            <a href="{{ route($route . '.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i>
                                {{ __('btn_back') }}</a>
                            <a href="{{ route($route . '.edit', $row->id) }}" class="btn btn-info"><i
                                    class="fas fa-sync-alt"></i> {{ __('btn_refresh') }}</a>
                        </div>

                        <form class="needs-validation" novalidate action="{{ route($route . '.update_plan', [$row->id]) }}"
                            method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-block">
                                <div class="row">
                                    {{-- Start Subject Programs --}}
                                    <div class="col-md-12">
                                        <div class="w-100 d-flex">
                                            <label
                                                class="text-left w-50">{{ __('program_courses', ['program' => $row->title]) }}</label>
                                            <label class="text-right w-50">{{ __('field_program_total_hours') }} :
                                                {{ $row->default_total_hours }}</label>
                                        </div>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ trans_choice('module_subject', 1) }}</th>
                                                    <th>{{ __('field_type') }}</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($row->subjects as $program_subject)
                                                    <tr>
                                                        <td>{{ $loop->index + 1 }}</td>
                                                        <td>
                                                            <label for="">{{ $program_subject->code }} -
                                                                {{ $program_subject->title }}</label>
                                                            <input required type="hidden" class="form-control"
                                                                name="enroll_subjects[{{ $program_subject->id }}][subject_id]"
                                                                value="{{ $program_subject->id }}">
                                                        </td>
                                                        <td>
                                                            <select required class="form-control subject-type"
                                                                name="enroll_subjects[{{ $program_subject->id }}][subject_type_id]"
                                                                data-subject-id="{{ $program_subject->id }}">
                                                                <option value="">{{ __('select') }}</option>
                                                                @foreach ($subject_types as $key => $name)
                                                                    <option value="{{ $key }}"
                                                                        @selected(@$program_subject->pivot['subject_type_id'] == $key)>{{ $name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-12">
                                        <label>{{ __('program_note', ['program' => $row->title]) }}</label>
                                        <textarea name="notes" class="form-control text-left" cols="30" rows="10">
                                @isset($row)
{{ $row->notes }}
@endisset
                                </textarea>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <label>{{ __('Selected Types') }}</label>
                                        <table class="table table-bordered" id="selected-types-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Type') }}</th>
                                                    <th>{{ __('Number of Required Courses') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @isset($row->required_courses)
                                                    @foreach ($row->required_courses as $subject_type_id => $count)
                                                        <tr>

                                                            <td>{{ $subject_types[$subject_type_id] }}</td>
                                                            <td>
                                                                <input type="number" class="form-control"
                                                                    name="required_courses[{{ $subject_type_id }}]"
                                                                    value="{{ $count }}" min="0" required>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endisset

                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- End Subject Programs --}}
                                    <div class="form-group col-md-4"></div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i>
                                    {{ __('btn_update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- [ Card ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- End Content-->



@endsection
@section('page_js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subjectTypeSelects = document.querySelectorAll('.subject-type');
            const selectedTypesTableBody = document.querySelector('#selected-types-table tbody');

            subjectTypeSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const selectedType = this.options[this.selectedIndex].text;
                    const selectedTypeId = this.value;

                    if (!selectedTypeId) return;

                    // Check if type is already added
                    if ([...selectedTypesTableBody.children].some(row => row.dataset.typeId ===
                            selectedTypeId)) return;

                    // Add a new row for the selected type
                    const newRow = document.createElement('tr');
                    newRow.dataset.typeId = selectedTypeId;
                    newRow.innerHTML = `
                <td>${selectedType}</td>
                <td>
                    <input type="number" class="form-control" name="required_courses[${selectedTypeId}]" min="0" required>
                </td>
            `;

                    selectedTypesTableBody.appendChild(newRow);
                });
            });
        });
    </script>
@endsection
