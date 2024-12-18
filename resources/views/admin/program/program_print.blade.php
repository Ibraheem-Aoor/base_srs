<html>

<head>
    <title>Program Completion Plan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
        }

        .table-header {
            background-color: #e5e7eb;
        }

        .table-row {
            border-bottom: 1px solid #d1d5db;
        }

        .avoid-page-break {
            page-break-inside: avoid;
            break-inside: avoid;
            /* For modern browsers */
        }
    </style>
</head>

<body class="bg-white p-8">
    <div class="max-w-4xl mx-auto" id="boxes">
        <div class="text-center mb-2">
            <h1 class="text-xl font-bold">
                Omaintec | PROGRAM COMPLETION PLAN (PCP)
            </h1>
            <h2 class="text-lg font-bold">
                {{ $program->title }} ({{ $program->shortcode }})
            </h2>
        </div>
        @php
            if (!isset($student)) {
                $student = getAuthUser('student');
            }
        @endphp
        @isset($student)
            <div class="mb-4 avoid-page-break">
                <h3 class="font-bold mb-2">
                    APPLICANT
                </h3>
                <p>
                    <strong>
                        First Name:
                    </strong>
                    {{ $student->first_name }}
                </p>
                <p>
                    <strong>
                        Last Name:
                    </strong>
                    {{ $student->last_name }}
                </p>
            </div>
        @endisset

        @foreach ($subjects as $subject_type_id => $subjects)
            <div class="page-break">
                <h3 class="font-bold">
                    {{ \App\Models\SubjectType::find($subject_type_id)?->title }} COMPONENT | {{ count($subjects) }}
                    Courses @if ($program->required_courses && @$program->required_courses[$subject_type_id] != 0)
                        ({{ @$program->required_courses[$subject_type_id] . ' ' . __('Required') }})
                    @endif
                    / {{ $subjects->sum('credit_hour') }} Semester Hours
                </h3>
                <table class="w-full text-sm">
                    <thead class="table-header">
                        <tr>
                            <th class="p-2 text-left">
                                Course Code and Course Title
                            </th>
                            <th class="text-center">
                                Units
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $subject)
                            <tr class="table-row avoid-page-break">
                                <td class="p-2 text-left">
                                    ({{ $subject->code }})
                                    {{ $subject->title }}
                                </td>
                                <td class="text-center">
                                    {{ $subject->credit_hour }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        @isset($program->notes)
            <div class="mb-4 avoid-page-break">
                <h3 class="font-bold mb-2">
                    NOTES
                </h3>
                <p>
                    {{ $program->notes }}
                </p>
            </div>
        @endisset
    </div>
    {{-- JS --}}
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
                filename: '{{ $program->title }}',
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
