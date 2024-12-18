@extends('student.layouts.master')
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
                            <h5>{{ $title }}</h5>
                        </div>
                        <div class="card-block">
                            <form class="needs-validation" novalidate method="get" action="{{ route($route . '.index') }}">
                                <div class="row gx-2">
                                    <div class="form-group col-md-3">
                                        <label for="session">{{ __('field_session') }}</label>
                                        <select class="form-control" name="session" id="session">
                                            <option value="0">{{ __('all') }}</option>
                                            @foreach ($sessions as $session)
                                                <option value="{{ $session->session_id }}"
                                                    @if ($selected_session == $session->session_id) selected @endif>
                                                    {{ $session->session->title }}</option>
                                            @endforeach
                                        </select>

                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('field_session') }}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="category">{{ __('field_fees_type') }}</label>
                                        <select class="form-control" name="category" id="category" required>
                                            <option value="0">{{ __('all') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    @if ($selected_category == $category->id) selected @endif>
                                                    {{ $category->title }}</option>
                                            @endforeach
                                        </select>

                                        <div class="invalid-feedback">
                                            {{ __('required_field') }} {{ __('field_fees_type') }}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <button type="submit" class="btn btn-info btn-filter"><i class="fas fa-search"></i>
                                            {{ __('btn_filter') }}</button>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <a href="{{ route($route.'.print-financial-agreement',$auth_student->id) }}"
                                            target="__blank" class="btn btn-info btn-sm"><i class="fas fa-print"></i>
                                            {{ __('financial_agreement') }}</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-block">
                            <!-- [ Data table ] start -->
                            @isset($rows)
                                <div class="table-responsive">
                                    <table id="basic-table" class="display table nowrap table-striped table-hover"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('field_session') }}</th>
                                                <th>{{ __('field_semester') }}</th>
                                                <th>{{ __('field_fees_type') }}</th>
                                                <th>{{ __('field_fee') }}</th>
                                                <th>{{ __('field_discount') }}</th>
                                                <th>{{ __('field_fine_amount') }}</th>
                                                <th>{{ __('field_net_amount') }}</th>
                                                <th>{{ __('field_due_date') }}</th>
                                                <th>{{ __('field_status') }}</th>
                                                <th>{{ __('field_pay_date') }}</th>
                                                <th>{{ __('field_action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rows as $key => $row)
                                                @if ($row->status == 0)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $row->studentEnroll->session->title ?? '' }}</td>
                                                        <td>{{ $row->studentEnroll->semester->title ?? '' }}</td>
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



                                                            @if (isset($setting->decimal_place))
                                                                {{ number_format((float) $row->discount_amount, $setting->decimal_place, '.', '') }}
                                                            @else
                                                                {{ number_format((float) $row->discount_amount, 2, '.', '') }}
                                                            @endif
                                                            {!! $setting->currency_symbol !!}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $fine_amount = 0;
                                                            @endphp
                                                            @if (empty($row->pay_date) || $row->due_date < $row->pay_date)
                                                                @php
                                                                    $due_date = strtotime($row->due_date);
                                                                    $today = strtotime(date('Y-m-d'));
                                                                    $days = (int) (($today - $due_date) / 86400);
                                                                @endphp

                                                                @if ($row->due_date < date('Y-m-d'))
                                                                    @isset($row->category)
                                                                        @foreach ($row->category->fines->where('status', '1') as $fine)
                                                                            @if ($fine->start_day <= $days && $fine->end_day >= $days)
                                                                                @if ($fine->type == '1')
                                                                                    @php
                                                                                        $fine_amount =
                                                                                            $fine_amount +
                                                                                            $fine->amount;
                                                                                    @endphp
                                                                                @else
                                                                                    @php
                                                                                        $fine_amount =
                                                                                            $fine_amount +
                                                                                            ($row->fee_amount / 100) *
                                                                                                $fine->amount;
                                                                                    @endphp
                                                                                @endif
                                                                            @endif
                                                                        @endforeach
                                                                    @endisset
                                                                @endif
                                                            @endif


                                                            @if (isset($setting->decimal_place))
                                                                {{ number_format((float) $fine_amount, $setting->decimal_place, '.', '') }}
                                                            @else
                                                                {{ number_format((float) $fine_amount, 2, '.', '') }}
                                                            @endif
                                                            {!! $setting->currency_symbol !!}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $net_amount =
                                                                    $row->fee_amount - $row->discount_amount + $fine_amount;
                                                            @endphp

                                                            @if (isset($setting->decimal_place))
                                                                {{ number_format((float) $net_amount, $setting->decimal_place, '.', '') }}
                                                            @else
                                                                {{ number_format((float) $net_amount, 2, '.', '') }}
                                                            @endif
                                                            {!! $setting->currency_symbol !!}
                                                        </td>
                                                        <td>
                                                            @if (isset($setting->date_format))
                                                                {{ date($setting->date_format, strtotime($row->due_date)) }}
                                                            @else
                                                                {{ date('Y-m-d', strtotime($row->due_date)) }}
                                                            @endif
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
                                                        <td></td>
                                                        <td>
                                                            @if ($row->status != 1 && $row->status != 2 && !isset($row->prove_file_path))
                                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                                    data-fee_id="{{ encrypt($row->id) }}"
                                                                    data-bs-target="#payModal"><i class="fas fa-card"></i>
                                                                    {{ __('btn_pay') }}</button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @elseif($row->status == 1)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $row->studentEnroll->session->title ?? '' }}</td>
                                                        <td>{{ $row->studentEnroll->semester->title ?? '' }}</td>
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
                                                            @if (isset($setting->decimal_place))
                                                                {{ number_format((float) $row->discount_amount, $setting->decimal_place, '.', '') }}
                                                            @else
                                                                {{ number_format((float) $row->discount_amount, 2, '.', '') }}
                                                            @endif
                                                            {!! $setting->currency_symbol !!}
                                                        </td>
                                                        <td>
                                                            @if (isset($setting->decimal_place))
                                                                {{ number_format((float) $row->fine_amount, $setting->decimal_place, '.', '') }}
                                                            @else
                                                                {{ number_format((float) $row->fine_amount, 2, '.', '') }}
                                                            @endif
                                                            {!! $setting->currency_symbol !!}
                                                        </td>
                                                        <td>
                                                            @if (isset($setting->decimal_place))
                                                                {{ number_format((float) $row->paid_amount, $setting->decimal_place, '.', '') }}
                                                            @else
                                                                {{ number_format((float) $row->paid_amount, 2, '.', '') }}
                                                            @endif
                                                            {!! $setting->currency_symbol !!}
                                                        </td>
                                                        <td>
                                                            @if (isset($setting->date_format))
                                                                {{ date($setting->date_format, strtotime($row->due_date)) }}
                                                            @else
                                                                {{ date('Y-m-d', strtotime($row->due_date)) }}
                                                            @endif
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
                                                        <td>
                                                            @if (isset($setting->date_format))
                                                                {{ date($setting->date_format, strtotime($row->pay_date)) }}
                                                            @else
                                                                {{ date('Y-m-d', strtotime($row->pay_date)) }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endisset
                            <!-- [ Data table ] end -->
                        </div>

                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- End Content-->
    {{-- Start Pay Modal --}}
    <!-- Edit modal content -->
    <div id="payModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form class="needs-validation" novalidate action="{{ route($route . '.store') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">{{ __('modal_add') }}{{ __('upload_payment_receipt') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form Start -->
                        <input type="hidden" name="fee_id" value="">
                        <input type="file" class="form-control" name="receipt" required>
                        <!-- Form End -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="fas fa-times"></i> {{ __('btn_close') }}</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i>
                            {{ __('btn_save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Pay Modal --}}
@endsection
@section('page_js')
    <script>
        $(document).on('show.bs.modal', '#payModal', function(e) {
            var src = e.relatedTarget;
            $(this).find('input[name="fee_id"]').val(src.dataset.fee_id);
        });
    </script>
@endsection
