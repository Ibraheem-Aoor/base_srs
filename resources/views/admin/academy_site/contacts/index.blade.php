@extends('admin.layouts.master')
@section('title', $title)
@section('content')

    <!-- Start Content-->
    <div class="main-body">
        <div class="page-wrapper">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-md-12">
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
                                            <th>{{ __('field_name') }}</th>
                                            <th>{{ __('field_email') }}</th>
                                            <th>{{ __('field_subject') }}</th>
                                            <th>{{ __('field_date') }}</th>
                                            <th>{{ __('field_action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows as $row)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td>
                                                    {{ $row->email }}
                                                </td>
                                                <td>{{ $row->subject }}</td>
                                                <td>{{ date('Y-m-d', strtotime($row->created_at)) }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-icon btn-primary btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#contact-modal" data-message="{{ $row->message }}">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    {{-- @can($access . '-edit')
                                                    <button type="button" class="btn btn-icon btn-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal-{{ $row->id }}">
                                                            <i class="far fa-edit"></i>
                                                        </button>
                                                        <a href="{{ route($route . '.edit', $row->id) }}"
                                                            class="btn bt-icon btn-success btn-sm"
                                                            title="{{ __('study_plan') }}">
                                                            <i class="far fa-file"></i>
                                                        </a>
                                                        <!-- Include Edit modal -->
                                                        @include($view . '.edit')
                                                    @endcan --}}
                                                </td>
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
    <div class="modal fade" id="contact-modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('message') }}</h5>
                    <p class="text-success mt-2" id="message">{{ __('modal_delete_warning') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i>
                        {{ __('btn_close') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('page_js')
    <script>
        $(document).on('shown.bs.modal', '#contact-modal', function(e) {
            var src = e.relatedTarget;
            $('#message').html(src.dataset.message);
        });
    </script>
@endsection
