@extends('admin.layouts.master')
@section('title', $title)
@section('content')

    <!-- Start Content-->
    <div class="main-body">
        <div class="page-wrapper">
            <!-- [ Main Content ] start -->
            <div class="row">
                @can($access . '-edit')
                    <div class="col-md-4">
                        <form class="needs-validation" novalidate action="{{ route($route . '.update', $row->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('btn_update') }} {{ $title }}</h5>
                                </div>
                                <div class="card-block">
                                    <a href="{{ route($route . '.index') }}" class="btn btn-primary"><i
                                            class="fas fa-arrow-left"></i> {{ __('btn_back') }}</a>

                                    <a href="{{ route($route . '.edit', $row->id) }}" class="btn btn-info"><i
                                            class="fas fa-sync-alt"></i> {{ __('btn_refresh') }}</a>
                                </div>
                                <div class="card-block">
                                    <!-- Form Start -->
                                    <div class="form-group">
                                        <label for="title" class="form-label">{{ __('field_title') }} <span>*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            value="{{ $row->title }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('field_title') }}
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="amount" class="form-label">{{ __('field_amount') }}
                                            ({!! $setting->currency_symbol !!} / %) <span>*</span></label>
                                        <input type="text" class="form-control autonumber" name="amount" id="amount"
                                            value="{{ round($row->amount, 2) }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('field_amount') }}
                                        </div>
                                    </div>

                                    <div class="form-group d-inline">
                                        <div class="radio d-inline">
                                            <input type="radio" name="type" id="type_fixed-{{ $row->id }}"
                                                value="1" @if ($row->type == 1) checked @endif>
                                            <label for="type_fixed-{{ $row->id }}"
                                                class="cr">{{ __('amount_type_fixed') }}</label>
                                        </div>
                                    </div>
                                    <div class="form-group d-inline">
                                        <div class="radio d-inline">
                                            <input type="radio" name="type" id="type_percentage-{{ $row->id }}"
                                                value="2" @if ($row->type == 2) checked @endif>
                                            <label for="type_percentage-{{ $row->id }}"
                                                class="cr">{{ __('amount_type_percentage') }}</label>
                                        </div>
                                    </div>
                                    <br>
                                    {{-- START  Acutal Discount Type For Adminstration Purposes "GRANT|DISCOUNT" --}}
                                    <div class="form-group d-inline">
                                        <div class="radio d-inline">
                                            <input type="radio" name="actual_type" id="actual_type_discount" value="discount"
                                                @if ($row->discount_type == 'discount') checked @endif>
                                            <label for="actual_type_discount" class="cr">{{ __('field_discount') }}</label>
                                        </div>
                                    </div>
                                    <div class="form-group d-inline">
                                        <div class="radio d-inline">
                                            <input type="radio" name="actual_type" id="acutal_type_grant" value="grant"
                                                @if ($row->discount_type == 'grant') checked @endif>
                                            <label for="acutal_type_grant"
                                                class="cr">{{ __('field_discount_grant') }}</label>
                                        </div>
                                    </div>
                                    {{-- END Acutal Discount Type For Adminstration Purposes "GRANT|DISCOUNT" --}}

                                    <!-- Form End -->
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i>
                                        {{ __('btn_update') }}</button>
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
                                            <th>{{ __('field_amount') }}</th>
                                            <th>{{ __('field_action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows as $key => $row)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $row->title }}</td>
                                                <td>
                                                    @if (isset($setting->decimal_place))
                                                        {{ number_format((float) $row->amount, $setting->decimal_place, '.', '') }}
                                                    @else
                                                        {{ number_format((float) $row->amount, 2, '.', '') }}
                                                    @endif
                                                    @if ($row->type == 1)
                                                        {!! $setting->currency_symbol !!}
                                                    @elseif($row->type == 2)
                                                        %
                                                    @endif
                                                </td>
                                                <td>
                                                    @can($access . '-edit')
                                                        <a href="{{ route($route . '.edit', $row->id) }}"
                                                            class="btn btn-icon btn-primary btn-sm">
                                                            <i class="far fa-edit"></i>
                                                        </a>
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

@endsection
