@extends('web.layouts.master')
@section('title', __('navbar_feature'))

@section('social_meta_tags')
    @if(isset($setting))
    <meta property="og:type" content="website">
    <meta property='og:site_name' content="{{ $setting->title }}"/>
    <meta property='og:title' content="{{ $feature->title }}"/>
    <meta property='og:description' content="{!! str_limit(strip_tags($feature->description), 160, ' ...') !!}"/>
    <meta property='og:url' content="{{ route('feature.single', ['slug' => $feature->slug]) }}"/>
    <meta property='og:image' content="{{ asset('uploads/feature/'.$feature->attach) }}"/>


    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{!! '@'.str_replace(' ', '', $setting->title) !!}" />
    <meta name="twitter:creator" content="@HiTechParks" />
    <meta name="twitter:url" content="{{ route('feature.single', ['slug' => $feature->slug]) }}" />
    <meta name="twitter:title" content="{{ $feature->title }}" />
    <meta name="twitter:description" content="{!! str_limit(strip_tags($feature->description), 160, ' ...') !!}" />
    <meta name="twitter:image" content="{{ asset('uploads/feature/'.$feature->attach) }}" />
    @endif
@endsection

@section('content')

    <!-- main-area -->
    <main>

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area d-flex  p-relative align-items-center">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-12 col-lg-12">
                        <div class="breadcrumb-wrap text-left">
                            <div class="breadcrumb-title">
                                <h2>{{ $feature->title }}</h2>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="breadcrumb-wrap2">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('navbar_home') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('navbar_feature') }}</li>
                            </ol>
                        </nav>
                    </div> --}}
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- feature Detail -->
        <section class="project-detail">
            <div class="container">
                <!-- Lower Content -->
                <div class="lower-content">
                    <div class="row">
                        <div class="text-column col-lg-9 col-md-9 col-sm-12">
                            {{-- <h2>{{ $feature->title }}</h2> --}}

                            <div class="upper-box">
                                <div class="single-item-carousel owl-carousel owl-theme">
                                    <figure class="image"><img src="{{ asset('uploads/feature/'.$feature->attach) }}" alt="feature"></figure>
                                </div>
                            </div>
                            <div class="inner-column">
                                <p>{!! $feature->description !!}</p>
                            </div>
                        </div>

                        {{-- <div class="col-lg-3">
                            <aside class="sidebar-widget info-column">
                                <div class="inner-column3">
                                    <h3>{{ __('sidebar_feature') }}</h3>

                                    <ul class="project-info clearfix">
                                        @if(!empty($feature->faculty))
                                        <li>
                                            <strong>{{ __('field_faculty') }}: </strong>
                                            <span>{{ $feature->faculty }}</span>
                                        </li>
                                        @endif
                                        @if(!empty($feature->semesters))
                                        <li>
                                            <strong>{{ __('field_total') }} {{ __('field_semester') }}: </strong>
                                            <span>{{ $feature->semesters }}</span>
                                        </li>
                                        @endif
                                        @if(!empty($feature->credits))
                                        <li>
                                            <strong>{{ __('field_total_credit_hour') }}: </strong>
                                            <span>{{ $feature->credits }}</span>
                                        </li>
                                        @endif
                                        @if(!empty($feature->features))
                                        <li>
                                            <strong>{{ __('field_total') }} {{ __('field_subject') }}: </strong>
                                            <span>{{ $feature->features }}</span>
                                        </li>
                                        @endif
                                        @if(!empty($feature->duration))
                                        <li>
                                            <strong>{{ __('field_duration') }}: </strong>
                                            <span>{{ $feature->duration }}</span>
                                        </li>
                                        @endif
                                        @if(!empty($feature->fee))
                                        <li>
                                            <strong>{{ __('field_total') }} {{ __('field_fee') }}: </strong>
                                            <span>{{ round($feature->fee, $setting->decimal_place ?? 2) }} {!! $setting->currency_symbol !!}</span>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </aside>
                        </div> --}}
                    </div>
                </div>
            </div>
        </section>
        <!--End feature Detail -->

    </main>
    <!-- main-area-end -->

@endsection
