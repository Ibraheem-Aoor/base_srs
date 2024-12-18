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

                        <form class="needs-validation" novalidate action="{{ route($route . '.update', [$row->id]) }}"
                            method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-block">
                                <div class="row">
                                    <!-- Form Start -->
                                    <div class="form-group col-md-4">
                                        <label for="title" class="form-label">{{ __('field_title') }}
                                            <span>*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            value="{{ $row->title }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('field_title') }}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="code">{{ __('field_code') }} <span>*</span></label>
                                        <input type="text" class="form-control" name="code" id="code"
                                            value="{{ $row->code }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('field_code') }}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="credit_hour">{{ __('field_credit_hour') }} <span>*</span></label>
                                        <input type="text" class="form-control" name="credit_hour" id="credit_hour"
                                            value="{{ $row->credit_hour }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('field_credit_hour') }}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="status" class="form-label">{{ __('module_exam_type') }}</label>
                                        <select class="form-control" name="exam_type" required>
                                            <option value="">{{ __('select') }}</option>
                                            @foreach ($mark_distribution_systems as $mark_distribution_system)
                                                <option value="{{ $mark_distribution_system->id }}"
                                                    @selected(@$row->programs->first()->pivot['exam_type_category_id'] == $mark_distribution_system->id || (old('exam_type') == $mark_distribution_system->id))>
                                                    {{ $mark_distribution_system->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>



                                    <div class="form-group col-md-4">
                                        <label for="status" class="form-label">{{ __('select_status') }}</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="1" @if ($row->status == 1) selected @endif>
                                                {{ __('status_active') }}</option>
                                            <option value="0" @if ($row->status == 0) selected @endif>
                                                {{ __('status_inactive') }}</option>
                                        </select>
                                    </div>

                                    {{-- Start Subject Programs --}}
                                    @foreach ($faculties as $index => $faculty)
                                        <div class="col-md-12">
                                            <label>{{ __('course_per_program', ['faculty' => $faculty->title]) }}</label>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('field_program') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($faculty->programs->where('status', 1)->sortBy('title') as $program)
                                                        @php
                                                            $program_object = $row
                                                                ->programs()
                                                                ->select(['id', 'title'])
                                                                ->find($program);
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="checkbox d-inline">
                                                                    <input type="checkbox"
                                                                        name="programs[{{ $program->id }}][is_checked]"
                                                                        id="program-{{ $program->id }}"
                                                                        value="{{ $program->id }}"
                                                                        @if (isset($program_object)) checked @endif>
                                                                    <label for="program-{{ $program->id }}"
                                                                        class="cr">{{ $program->title }}</label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                    {{-- End Subject Programs --}}
                                    <div class="form-group col-md-4"></div>
                                    {{-- Start Subject Prerequisites --}}
                                    <div class="col-md-12">
                                        <label>{{ __('field_prerequisites') }}</label>
                                        <table class="table table-bordered" id="prerequisitesTable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('field_subject') }}</th>
                                                    <th>{{ __('field_type') }}</th>
                                                    <th>{{ __('btn_remove') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($row->prerequisites as $prerequisit)
                                                    <tr>
                                                        <td>
                                                            <select
                                                                name="prerequisites[{{ $loop->index }}][prerequisit_id]"
                                                                class="form-control prerequisite-course" required>
                                                                @foreach ($courses as $course)
                                                                    <option value="{{ $course->id }}"
                                                                        @selected($course->id == $prerequisit->prerequisit_id)>{{ $course->title }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="prerequisites[{{ $loop->index }}][type]"
                                                                class="form-control">
                                                                <option value="parallel" @selected($prerequisit->type == 'parallel')>
                                                                    {{ __('parallel') }}</option>
                                                                <option value="prior" @selected($prerequisit->type == 'prior')>
                                                                    {{ __('prior') }}</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger removeRow"><i
                                                                    class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div class="col-sm-12 text-center">

                                            <button type="button" id="addRow"
                                                class="btn btn-primary text-center ">{{ __('btn_add_new') }}</button>
                                        </div>
                                    </div>
                                    {{-- End Subject Prerequisites --}}
                                    <!-- Form End -->
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
        $(document).ready(function() {
            $("#addRow").click(function() {
                var existingOptions = [];
                $(".prerequisite-course").each(function() {
                    existingOptions.push($(this).val());
                });

                var html = '<tr>';
                html += '<td><select required name="prerequisites[' + $('#prerequisitesTable tbody tr')
                    .length +
                    '][prerequisit_id]" class="form-control prerequisite-course" required>';
                @foreach ($courses as $course)
                    if (!existingOptions.includes('{{ $course->id }}')) {
                        html += '<option value="{{ $course->id }}">{{ $course->title }}</option>';
                    }
                @endforeach
                html += '</select></td>';
                html += '<td><select required name="prerequisites[' + $('#prerequisitesTable tbody tr')
                    .length +
                    '][type]" class="form-control"><option value="parallel">{{ __('parallel') }}</option><option value="prior">{{ __('prior') }}</option></select></td>';
                html +=
                    '<td><button type="button" class="btn btn-danger removeRow"><i class="fa fa-trash"></i></button></td>';
                html += '</tr>';

                $('#prerequisitesTable tbody').append(html);
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
    <script>
        $(document).on('change', '.checkbox.d-inline', function() {
            var checkbox = $(this).find('input[type="checkbox"]');
            var id = checkbox.val();
            if (checkbox.is(':checked')) {
                $(`select[id="category-${id}"]`).prop('required', true);
            } else {
                $(`select[id="category-${id}"]`).prop('required', false);
            }
        });
    </script>

@endsection
