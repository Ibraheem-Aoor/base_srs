<!DOCTYPE html>
<html lang="en-US" id="boxes">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,maximum-scale=1.0">
    <title>{{ $title }}</title>

    <style type="text/css" media="print">
        @media print {
            @page {
                size: A4 portrait;
                margin: 10px auto;
            }

            @page :footer {
                display: none;
            }

            @page :header {
                display: none;
            }

            body {
                margin: 15mm 15mm 15mm 15mm;
            }

            table,
            tbody {
                page-break-before: auto;
            }
        }

        table,
        img,
        svg {
            break-inside: avoid;
        }

        .template-container {
            -webkit-transform: scale(1.0);
            -moz-transform: scale(1.0);
            -ms-transform: scale(1.0);
            -o-transform: scale(1.0);
            transform: scale(1.0);
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/css/prints/receipt.css') }}" media="screen, print">

    @php
        $version = App\Models\Language::version();
    @endphp
    @if ($version->direction == 1)
        <!-- RTL css -->
        <style type="text/css" media="screen, print">
            .template-container {
                direction: rtl;
            }

            .template-container .top-meta-table tr td,
            .template-container .top-meta-table tr th {
                float: right;
                text-align: right;
            }

            .table-no-border.receipt thead th:nth-child(1),
            .table-no-border.receipt td:nth-child(1),
            .table-no-border.receipt .tfoot th:nth-child(1) {
                text-align: right;
            }

            .template-container .table-no-border tr td.temp-logo {
                float: none;
            }

            .table-no-border.receipt .exam-title {
                text-align: right !important;
            }
        </style>
    @endif
</head>

<body>

    <div class="template-container printable" style="width: {{ $print->width }}; height: {{ $print->height }};">
        <div class="template-inner">
            <!-- Header Section -->
            <table class="table-no-border">
                <tbody>
                    <tr>
                        <td class="temp-logo">
                            <div class="inner">
                                @if (is_file('uploads/' . $path . '/' . $print->logo_left))
                                    <img src="{{ asset('uploads/' . $path . '/' . $print->logo_left) }}" alt="Logo">
                                @endif
                            </div>
                        </td>
                        <td class="temp-title">
                            <div class="inner">
                                <h2>{{ __('financial_agreement') }}</h2>
                            </div>
                        </td>
                        <td class="temp-logo last">
                            <div class="inner">
                                @if (is_file('uploads/' . $path . '/' . $print->logo_right))
                                    <img src="{{ asset('uploads/' . $path . '/' . $print->logo_right) }}"
                                        alt="Logo">
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Header Section -->

            @php
                $enroll = \App\Models\Student::enroll($student->id);
            @endphp
            <!-- Student Info Section -->
            <table class="table-no-border top-meta-table">
                <tbody>
                    <tr>
                        <td class="meta-data">{{ __('field_student_id') }}:</td>
                        <td class="meta-data value width2"> {{ $student->student_id ?? '' }}</td>
                        <td class="meta-data">{{ __('field_name') }}:</td>
                        <td class="meta-data value"> {{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-data">{{ __('field_program') }}:</td>
                        <td class="meta-data value width2">
                            {{ $student->program->title . '(' . $student->program->shortcode . ')' ?? '' }}</td>
                        <td class="meta-data">{{ __('field_batch') }}:</td>
                        <td class="meta-data value"> {{ $student->batch->title ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
            <!-- Student Info Section -->

            <!-- Fees Section -->
            @php
                $program_total_fees = 0;
                $program_total_paid = 0;
                $program_total_pending = 0;
            @endphp
            @foreach ($rows as $session_start_date => $fees)
                @php
                    $session = \App\Models\Session::whereStartDate($session_start_date)->first();
                    if (!$session) {
                        $session = \App\Models\Session::whereDate('start_date', '<=', $session_start_date)
                            ->orWhere('end_date', '>=', $session_start_date)
                            ->orderBy('start_date')
                            ->first();
                    }
                    $sessionTotalPaid = 0;
                    $sessionTotalPending = 0;
                @endphp
                <h2 class="text-center">{{ __('field_session') }}:
                    {{ $session?->title }} ({{ $session->start_date }} - {{ $session?->end_date }})</h2>
                <table class="table-no-border receipt">
                    <thead>
                        <tr>
                            <th style="width:20% !important;">{{ __('field_fees_type') }}</th>
                            <th style="width:15% !important;">{{ __('field_fee') }}</th>
                            <th style="width:15% !important;">{{ __('field_discount') }}</th>
                            <th style="width:15% !important;">{{ __('field_fine_amount') }}</th>
                            <th style="width:15% !important;">{{ __('field_paid_amount') }}</th>
                            <th colspan="6">{{ __('field_pending_amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fees->where('status', '!=', 2)->groupBy('category_id') as $fees_category => $rows)
                            @php
                                $categoryTitle = $rows->first()->category->title ?? '';
                                $totalFee = $rows->sum('fee_amount');
                                $totalDiscount = $rows->sum('discount_amount');
                                $totalFine = $rows->sum('fine_amount');
                                $totalPaid = $rows->where('status', 1)->sum('paid_amount');
                                $totalPending = $totalFee - $totalPaid - $totalDiscount + $totalFine;
                                $sessionTotalPaid += $totalPaid;
                                $sessionTotalPending += $totalPending;
                                $program_total_fees += $totalFee;
                                $program_total_paid += $totalPaid;
                                $program_total_pending += $totalPending;
                            @endphp
                            @if (isset($totalFee) && $totalFee > 0)
                                <tr class="border-bottom fs-10">
                                    <td>{{ $categoryTitle }}</td>
                                    <td>
                                        {{ number_format((float) $totalFee, $setting->decimal_place ?? 2, '.', '') }}
                                        {!! $setting->currency_symbol !!}
                                    </td>
                                    <td>-
                                        {{ number_format((float) $totalDiscount, $setting->decimal_place ?? 2, '.', '') }}
                                        {!! $setting->currency_symbol !!}
                                    </td>
                                    <td>+
                                        {{ number_format((float) $totalFine, $setting->decimal_place ?? 2, '.', '') }}
                                        {!! $setting->currency_symbol !!}
                                    </td>
                                    <td>
                                        {{ number_format((float) $totalPaid, $setting->decimal_place ?? 2, '.', '') }}
                                        {!! $setting->currency_symbol !!}
                                    </td>
                                    <td colspan="6">
                                        {{ number_format((float) $totalPending, $setting->decimal_place ?? 2, '.', '') }}
                                        {!! $setting->currency_symbol !!}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>{{ __('field_session_total') }}:</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th class="text-right">
                                {{ number_format((float) $sessionTotalPaid, $setting->decimal_place ?? 2, '.', '') }}
                                {!! $setting->currency_symbol !!}
                            </th>
                            <th colspan="6" class="text-left">
                                {{ number_format((float) $sessionTotalPending, $setting->decimal_place ?? 2, '.', '') }}
                                {!! $setting->currency_symbol !!}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            @endforeach
            <!-- Fees Section -->

            {{-- START Program Total Fees --}}
            <table class="table-no-border receipt">
                <thead>
                    <tr>
                        <th style="width:20% !important;">&nbsp;</th>
                        <th style="width:15% !important;">&nbsp;</th>
                        <th style="width:15% !important;">&nbsp;</th>
                        {{-- <th style="width:15% !important;">{{ __('field_fine_amount') }}</th> --}}
                        <th colspan="6">&nbsp;</th>
                    </tr>
                </thead>
                <tfoot class="text-success">
                    <tr class="text-success">
                        <th class="text-success">{{ __('field_program_total') }}:</th>
                        <th class="text-success">&nbsp;</th>
                        <th class="text-success">&nbsp;</th>
                        <th class="text-success" class="text-right">
                            {{ number_format((float) $program_total_paid, $setting->decimal_place ?? 2, '.', '') }}
                            {!! $setting->currency_symbol !!}
                        </th>
                        <th class="text-success" colspan="6" class="text-right">
                            {{ number_format((float) $program_total_pending, $setting->decimal_place ?? 2, '.', '') }}
                            {!! $setting->currency_symbol !!}
                        </th>
                    </tr>
                </tfoot>
            </table>
            {{-- END Program Total Fees --}}


            <!-- Footer Section -->
            <table class="table-no-border">
                <tbody>
                    <tr>
                        <td class="temp-footer">
                            <div class="inner">
                                <p>{!! $print->footer_left !!}</p>
                            </div>
                        </td>
                        <td class="temp-footer">
                            <div class="inner">
                                <p>{!! $print->footer_center !!}</p>
                            </div>
                        </td>
                        <td class="temp-footer last">
                            <div class="inner">
                                <p>{!! $print->footer_right !!}</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Footer Section -->
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        function closeScript() {
            setTimeout(function() {
                window.open(window.location, '_self').close();
            }, 1000);
        }

        $(window).on('load', function() {
            var element = document.getElementById('boxes');
            var opt = {
                filename: '{{ $student->first_name . '' . $student->last_name . '_' . $student->student_id }}.pdf',
                image: {
                    type: 'jpeg', // Changed to jpeg
                    quality: 0.75 // Reduced quality
                },
                html2canvas: {
                    scale: 2, // Reduced scale
                    dpi: 96, // Reduced dpi
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A4'
                }
            };
            html2pdf().set(opt).from(element).save().then(closeScript);
        });
    </script>
</body>

</html>
