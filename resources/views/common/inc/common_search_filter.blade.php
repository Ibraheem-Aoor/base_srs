<div class="form-group col-md-3">
    <label for="faculty">{{ __('field_faculty') }} <span>*</span></label>
    <select class="form-control faculty" name="faculty" id="faculty" required>
        <option value="">{{ __('select') }}</option>
        @if (isset($faculties))
            @foreach ($faculties->sortBy('title') as $faculty)
                <option value="{{ $faculty->id }}" @if ($selected_faculty == $faculty->id) selected @endif>
                    {{ $faculty->title }}</option>
            @endforeach
        @endif
    </select>

    <div class="invalid-feedback">
        {{ __('required_field') }} {{ __('field_faculty') }}
    </div>
</div>
<div class="form-group col-md-3">
    <label for="program">{{ __('field_program') }} <span>*</span></label>
    <select class="form-control program " name="program" id="program" required>
        <option value="">{{ __('select') }}</option>
        @if (isset($programs))
            @foreach ($programs->sortBy('title') as $program)
                <option value="{{ $program->id }}" @selected($program->id == $selected_program) >
                    {{ $program->title }}</option>
            @endforeach
        @endif
    </select>

    <div class="invalid-feedback">
        {{ __('required_field') }} {{ __('field_program') }}
    </div>
</div>
<div class="form-group col-md-3">
    <label for="session">{{ __('field_session') }} <span>*</span></label>
    <select class="form-control session" name="session" id="session" required>
        <option value="">{{ __('select') }}</option>
        @if (isset($sessions))
            @foreach ($sessions->sortByDesc('id') as $session)
                <option value="{{ $session->id }}" @if ($selected_session == $session->id) selected @endif>
                    {{ $session->title }}</option>
            @endforeach
        @endif
    </select>

    <div class="invalid-feedback">
        {{ __('required_field') }} {{ __('field_session') }}
    </div>
</div>
<div class="form-group col-md-3 d-none">
    <label for="semester">{{ __('field_semester') }} <span>*</span></label>
    <select class="form-control semester" name="semester" id="semester">
        <option value="">{{ __('select') }}</option>
        @if (isset($semesters))
            @foreach ($semesters->sortBy('id') as $semester)
                <option value="{{ $semester->id }}" @if ($selected_semester == $semester->id) selected @endif>
                    {{ $semester->title }}</option>
            @endforeach
        @endif
    </select>

    <div class="invalid-feedback">
        {{ __('required_field') }} {{ __('field_semester') }}
    </div>
</div>
<div class="form-group col-md-3 d-none">
    <label for="section">{{ __('field_section') }} <span>*</span></label>
    <select class="form-control section" name="section" id="section">
        <option value="">{{ __('select') }}</option>
        @if (isset($sections))
            @foreach ($sections->sortBy('title') as $section)
                <option value="{{ $section->id }}" @if ($selected_section == $section->id) selected @endif>
                    {{ $section?->title }}</option>
            @endforeach
        @endif
    </select>

    <div class="invalid-feedback">
        {{ __('required_field') }} {{ __('field_section') }}
    </div>
</div>


<script src="{{ asset('dashboard/plugins/jquery/js/jquery.min.js') }}"></script>

<script type="text/javascript">
    "use strict";
    $(".faculty").on('change', function(e) {
        e.preventDefault(e);
        var program = $(".program");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ route('filter-program') }}",
            data: {
                _token: $('input[name=_token]').val(),
                faculty: $(this).val()
            },
            success: function(response) {
                // var jsonData=JSON.parse(response);
                $('option', program).remove();
                $('.program').append('<option value="">{{ __('select') }}</option>');
                $.each(response, function() {
                    $('<option/>', {
                        'value': this.id,
                        'text': this.title
                    }).appendTo('.program');
                });
            }

        });
    });

    $("#program").on('change', function(e) {
        console.log('SS');
        e.preventDefault(e);
        var session = $(".session");
        var semester = $(".semester");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ route('filter-session') }}",
            data: {
                _token: $('input[name=_token]').val(),
                program: $(this).val()
            },
            success: function(response) {
                // var jsonData=JSON.parse(response);
                $('option', session).remove();
                $('.session').append('<option value="">{{ __('select') }}</option>');
                $.each(response, function() {
                    $('<option/>', {
                        'value': this.id,
                        'text': this.title
                    }).appendTo('.session');
                });
            }

        });

    });


    @if (isset($is_mulit_programs))
        $(document).ready(function() {
            $("select.program").each(function() {
                $(this).select2().on('select2:select select2:unselect', function(e) {
                    var session = $(".session");
                    var semester = $(".semester");
                    var section = $(this).closest('.form-group').find('.section');

                    // Ajax calls or any other logic you have
                    // For example:
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    // Sample Ajax call for session filtering
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('filter-session') }}",
                        data: {
                            _token: $('input[name=_token]').val(),
                            program: $(this).val()
                        },
                        success: function(response) {
                            // var jsonData=JSON.parse(response);
                            $('option', session).remove();
                            $('.session').append(
                                '<option value="">{{ __('select') }}</option>'
                            );
                            $.each(response, function() {
                                $('<option/>', {
                                    'value': this.id,
                                    'text': this.title
                                }).appendTo('.session');
                            });
                        }
                    });



                });
            });
        });
    @endif
</script>
