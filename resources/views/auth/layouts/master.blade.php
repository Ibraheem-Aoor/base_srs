<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

         @include('admin.layouts.common.header_script')

         <style type="text/css" media="screen">
             h3 {
                font-size: 18px;
             }
             .auth-logo {
                position: absolute;
                left: 40px;
                top: 10px;
                overflow: hidden;
             }
             .auth-logo img {
                max-height: 100px;
                max-width: 100px;
             }

             @media screen and (max-width: 767px) {
                .auth-logo img {
                    max-height: 70px;
                }
             }
         </style>

    </head>
    <body>
<div class="image-container"><img alt="Background Image" class="background-image" src="{{ asset('uploads/setting/HEADER.jpg') }}"></div>

        <div class="auth-wrapper">

            @if(isset($setting))
            @if(is_file('uploads/setting/'.$setting->logo_path))
            <a href="#" class="auth-logo">
                <img src="{{ asset('uploads/setting/'.$setting->logo_path) }}" alt="logo">
            </a>
            @endif
            @endif

            <div class="auth-content">

                <!-- Start Content-->
                @yield('content')
                <!-- End Content-->

            </div>
        </div>

        @include('admin.layouts.common.footer_script')
    </body>
</html>
