@extends('admin.layouts.master')
@section('title', $title)
@section('content')

    <!-- Start Content-->
    <div class="main-body">
        <div class="page-wrapper">
            <!-- [ Main Content ] start -->
            <div class="row">
                @can($access . '-create')
                    <div class="col-md-4">
                        <form class="needs-validation" novalidate action="{{ route($route . '.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('btn_create') }} {{ $title }}</h5>
                                </div>
                                <div class="card-block">
                                    <!-- Form Start -->
                                    <div class="form-group">
                                        <label for="category" class="form-label">{{ __('module_exam_type_category') }} <span>*</span></label>
                                        <select name="category" id="category" class="form-control" required>
                                            <option value="">{{ __('select') }}</option>
                                            @foreach ($mark_distribution_categories as $category)
                                                <option value="{{ $category->id }}" @selected(old('category') == $category->id)>
                                                    {{ $category->title }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('module_exam_type_category') }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="title" class="form-label">{{ __('field_title') }} <span>*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            value="{{ old('title') }}" required>
                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('field_title') }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="marks" class="form-label">{{ __('field_marks') }} <span>*</span></label>
                                        <input type="text" class="form-control autonumber" name="marks" id="marks"
                                            value="{{ old('marks') }}" required>
                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('field_marks') }}
                                        </div>
                                    </div>
                                    <!-- Form End -->
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i>
                                        {{ __('btn_save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endcan
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ $title }} {{ __('list') }}</h5>
                        </div>
                        <div class="card-block">
                            <!-- [ Data table ] start -->
                            <div class="table-responsive">
                                <table id="basic-table" class="display table nowrap table-striped table-hover"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('field_title') }}</th>
                                            <th>{{ __('field_category') }}</th>
                                            <th>{{ __('field_marks') }}</th>
                                            <th>{{ __('field_result_contribution') }}</th>
                                            <th>{{ __('field_status') }}</th>
                                            <th>{{ __('field_action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows as $category_id => $exam_types_categories)
                                            @php
                                                $total_marks = 0;
                                                $total_contribution = 0;
                                            @endphp
                                            <tr class="group-header">
                                                <td colspan="7" class="bg-success text-white text-center">
                                                    <b>{{ $exam_types_categories->first()->category->title }}</b>
                                                </td>
                                            </tr>
                                            @foreach ($exam_types_categories as $key => $row)
                                                @php
                                                    $total_marks += $row->marks;
                                                    $current_contribution = ($row->marks / $exam_types_categories->sum('marks')) * 100;
                                                    $total_contribution += $current_contribution;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->parent->iteration }}.{{ $key + 1 }}</td>
                                                    <td>{{ $row->title }}</td>
                                                    <td>{{ $row->category->title }}</td>
                                                    <td>{{ round($row->marks, 2) }}</td>
                                                    <td>{{ round($current_contribution, 2) }} %</td>
                                                    <td>
                                                        @if ($row->status == 1)
                                                            <span class="badge badge-pill badge-success">{{ __('status_active') }}</span>
                                                        @else
                                                            <span class="badge badge-pill badge-danger">{{ __('status_inactive') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @can($access . '-edit')
                                                            <button type="button" class="btn btn-icon btn-primary btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editModal-{{ $row->id }}">
                                                                <i class="far fa-edit"></i>
                                                            </button>
                                                            @include($view . '.edit')
                                                        @endcan

                                                        @can($access . '-delete')
                                                            <button type="button" class="btn btn-icon btn-danger btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal-{{ $row->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @include('admin.layouts.inc.delete')
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="bg-info text-white">
                                                <th></th>
                                                <th>{{ __('field_grand_total') }}</th>
                                                <th></th>
                                                <th>{{ round($total_marks, 2) }}</th>
                                                <th>{{ round($total_contribution, 2) }} %</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- [ Data table ] end -->
                        </div>
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
        $(document).ready(function() {
            // Remove custom rows before DataTables initialization
            $('#basic-table tbody .group-header').each(function() {
                $(this).remove();
            });

            // Initialize DataTables
            var table = $('#basic-table').DataTable({
                "order": []
            });

            // Add the custom rows back
            $('#basic-table tbody').prepend($('.group-header'));
        });
    </script>
@endsection
