        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="robots" content="noindex" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if (isset($setting))
            <!-- App Title -->
            <title>@yield('title') | {{ $setting->meta_title ?? '' }}</title>

            <meta name="description" content="{!! str_limit(strip_tags($setting->meta_description), 160, ' ...') !!}">
            <meta name="keywords" content="{!! strip_tags($setting->meta_keywords) !!}">

            @if (is_file('uploads/setting/' . $setting->favicon_path))
                <!-- App favicon -->
                <link rel="shortcut icon" href="{{ asset('uploads/setting/' . $setting->favicon_path) }}"
                    type="image/x-icon">
            @endif
        @endif

        @if (empty($setting))
            <!-- App Title -->
            <title>@yield('title')</title>
        @endif

        <!-- fontawesome icon -->
        <link rel="stylesheet" href="{{ asset('dashboard/fonts/fontawesome/css/fontawesome-all.min.css') }}">
        <!-- data tables css -->
        <link rel="stylesheet" href="{{ asset('dashboard/plugins/data-tables/css/datatables.min.css') }}">
        <!-- material datetimepicker css -->
        <link rel="stylesheet"
            href="{{ asset('dashboard/plugins/material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
        <!-- toastr css -->
        <link rel="stylesheet" href="{{ asset('dashboard/plugins/toastr/css/toastr.min.css') }}">


        <!-- page css -->
        @yield('page_css')


        <!-- vendor css -->
        <link rel="stylesheet" href="{{ asset('dashboard/css/style.css') }}?v=2.0">

        @php
            $version = App\Models\Language::version();
        @endphp
        @if ($version->direction == 1)
            <!-- RTL css -->
            <link rel="stylesheet" class="rtl-css" href="{{ asset('dashboard/css/layouts/rtl.css') }}">
        @endif


        <style>
            /* START Custom Css By Ibraheem */

            #preloader {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-image: linear-gradient(45deg, #ffffff, #ffffff);
                z-index: 9999999;
            }

            #preloader #status {
                position: absolute;
                left: 0;
                right: 0;
                top: 50%;
                -webkit-transform: translateY(-50%);
                transform: translateY(-50%);
            }

            #preloader #status .spinner {
                width: 40px;
                height: 40px;
                position: relative;
                margin: 100px auto;
            }

            #preloader #status .spinner .double-bounce1,
            #preloader #status .spinner .double-bounce2 {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                background-color: #2f55d4;
                opacity: 0.6;
                position: absolute;
                top: 0;
                left: 0;
                -webkit-animation: sk-bounce 2s infinite ease-in-out;
                animation: sk-bounce 2s infinite ease-in-out;
            }

            #preloader #status .spinner .double-bounce2 {
                -webkit-animation-delay: -1s;
                animation-delay: -1s;
            }

            @-webkit-keyframes sk-bounce {

                0%,
                100% {
                    -webkit-transform: scale(0);
                    transform: scale(0);
                }

                50% {
                    -webkit-transform: scale(1);
                    transform: scale(1);
                }
            }

            @keyframes sk-bounce {

                0%,
                100% {
                    -webkit-transform: scale(0);
                    transform: scale(0);
                }

                50% {
                    -webkit-transform: scale(1);
                    transform: scale(1);
                }
            }

            /* END Custom Css By Ibraheem */
        </style>
