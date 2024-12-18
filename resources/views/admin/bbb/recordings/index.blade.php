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
                                            <th>{{ __('field_preview') }}</th>
                                            <th>{{ __('field_id_no') }}</th>
                                            <th>{{ __('field_title') }}</th>
                                            <th>{{ __('field_date') }}</th>
                                            <th>{{ __('field_status') }}</th>
                                            {{-- <th>{{ __('field_action') }}</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows as  $row)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td><a target="_blank" href="{{ $row->playback_url}}"><img loading="lazy"
                                                            src="{{ $row->preview_img }}" width="200"
                                                            alt="{{ $row->name }}"></a></td>
                                                <td><a target="_blank"
                                                        href="{{ $row->playback_url}}">{{ $row->meetingId }}</a>
                                                </td>
                                                <td>{{ $row->name }}</td>
                                                <td>{{ $row->start_time }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-pill badge-{{ $row->state == 'published' ? 'success' : 'danger' }}">{{ $row->state }}</span>
                                                </td>
                                                {{-- <td>
                                                    @can($access . '-edit')
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
                                                    @endcan

                                                    @can($access . '-delete')
                                                        <button type="button" class="btn btn-icon btn-danger btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal-{{ $row->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        <!-- Include Delete modal -->
                                                        @include('admin.layouts.inc.delete')
                                                    @endcan
                                                </td> --}}
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
