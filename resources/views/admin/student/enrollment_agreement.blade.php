<html>

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        *{
            break-inside: avoid;
        }
    </style>
</head>

<body class="bg-gray-100 p-8" id="boxes">
    <div class="max-w-4xl mx-auto bg-white p-8 shadow-lg">
        <div class="flex justify-between items-center mb-2">
            <div>
                <img alt="Academy UK logo" class="h-20 w-20"
                    src="{{ asset('dashboard/images/eacademy.jpg') }}">
            </div>
            <div class="text-center">
                <h1 class="text-xl font-bold">Academy of E learning, Britain</h1>
                <p class="text-sm">الاكاديمية البريطانية للتعليم الالكتروني</p>
            </div>
            <div>
                <img alt="ASC logo" class="h-16 w-16"
                    src="{{ asset('dashboard/images/omaintec.jpg') }}" />
            </div>
        </div>
        <div class="text-center mb-2">
            <p>Address: {{ $topbarSetting->address }}</p>
            <p>Phone: {{ $topbarSetting->phone }}</p>
            <p>E-mail: {{ $topbarSetting->email }}</p>
        </div>
        <div class="max-w-4xl mx-auto">
            <h1 class="text-center text-2xl font-bold mb-2">
                ENROLLMENT AGREEMENT
            </h1>
            <p class="mb-2">
                The Enrollment Agreement is a written contract signed between a student and the Academy of E-Learning
                Britain concerning an educational
                program in agreement with Omaintec Aacademy The agreement specifies all costs the
                student named below must pay to enroll
                in the specific educational program for the specified study program. Costs for the program may change if
                the specifics of the educational
                program changes (i.e. student takes more units/courses than what was originally outlined in the original
                Enrollment Agreement). A copy of
                the completed enrollment agreement shall be given to the student upon enrollment.
            </p>
            <div class="bg-black text-white p-2 mb-2">
                <h3 class="font-bold">
                    CLASS LOCATION
                </h3>
            </div>
            <p class="mb-2">
                For hybrid instruction, location of instruction shall take place at the designated main, branch or
                satellite campus.
                For online students, instruction shall take place in
                <span class="text-red-500">
                    portal-omaintec.fame-uk.net
                </span>
                . Academy shall transmit all the lessons
                and other materials to the student if the student: (A) has fully paid for the educational program; and
                after having received the first lesson
                and initial materials, requests in writing that all the material be sent. If Academy transmits the balance
                of the material as the student requests, Academy
                shall remain obligated to provide the other educational services it agreed to provide, such as responses
                to student inquiries, student and
                faculty interaction, and evaluation and comment on lessons submitted by the student but shall not be
                obliged to pay any refund after all
                the lessons and material are transmitted.
            </p>
            <div class="bg-black text-white p-2 mb-2">
                <h3 class="font-bold">
                    STUDENT INFORMATION
                </h3>
            </div>
            <p class="mb-2">
                ({{ $student->first_name . ' ' . $student->father_name . ' ' . $student->last_name }} ,
                {{ $student->dob }}),
                <br />
                Address: {{ $student->permanent_address }}, {{ $student->permanent_village }},
                {{ $student->permanent_district }}
                <br />
                Email: {{ $student->email }}
            </p>
            <br><br><br><br><br><br><br><br><br>
            <div class="bg-black text-white p-2 mb-2">
                <h3 class="font-bold">
                    TUITION FEES
                </h3>
            </div>
            <p class="mb-2">
                ({{ $student->program->title }})
                <br />
                ({{ $student->program->faculty->title }}, International American University)
            </p>
            <table class="w-full border-collapse border border-black mb-2">
                <thead>
                    <tr>
                        <th class="border border-black p-2">
                            Fees Type
                        </th>
                        <th class="border border-black p-2">
                            Amount ($)
                        </th>
                        <th class="border border-black p-2">
                            Payment Due
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black p-2">
                            Admission Fees
                        </td>
                        <td class="border border-black p-2">
                            125
                        </td>
                        <td class="border border-black p-2">
                            Once at the beginning of first study session
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2">
                            Program basic tuition Fees
                        </td>
                        <td class="border border-black p-2">
                            6478
                        </td>
                        <td class="border border-black p-2">
                            In installments each study session
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2">
                            Credit Transfer Fees
                        </td>
                        <td class="border border-black p-2">
                            300
                        </td>
                        <td class="border border-black p-2">
                            If applicable
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2">
                            English Proficiency
                        </td>
                        <td class="border border-black p-2">
                            300
                        </td>
                        <td class="border border-black p-2">
                            If applicable
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2">
                            Graduation Fees
                        </td>
                        <td class="border border-black p-2">
                            900
                        </td>
                        <td class="border border-black p-2">
                            At the end of study completion
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="bg-black text-white p-2 mb-2">
                <h3 class="font-bold">
                    REGISTRATION
                </h3>
            </div>
            <p class="mb-2">
                IAU operates on a trimester academic calendar, which is comprised of three (3) academic terms (Spring,
                Summer,
                &amp; Fall). For each academic term, a student must register through the university's registration
                process. At the time
                of registration, the student will work with an academic staff to select the course(s) in which the
                student wishes to
                enroll for the academic term. The student will register for classes for the entire academic term.
                Depending on the
                student's Enrollment Status of full-time or part-time, the student will register for 1 to 4 courses per
                academic term.
                See explanation of Enrollment Status below.
            </p>
            <p class="mb-2">
                Spring (Jan-Apr)
                <br />
                • Session 1 (Jan-Feb)
                <br />
                • Session 2 (Mar-Apr)
            </p>
            <p class="mb-2">
                Summer (May-Aug)
                <br />
                • Session 1 (May-Jun)
                <br />
                • Session 2 (Jul-Aug)
            </p>
            <p class="mb-2">
                Fall (Sep-Dec)
                <br />
                • Session 1 (Sep-Oct)
                <br />
                • Session 2 (Nov-Dec)
            </p>
            <div class="bg-black text-white p-2 mb-2">
                <h3 class="font-bold">
                    FULL-TIME ENROLLMENT
                </h3>
            </div>
            <p>
                Full-time matriculated students are required to enroll full-time for each mandatory Spring and Fall
                trimester. F-1
                students are required to enroll full-time in the summer trimester if it is the initial or final
                enrollment term of their
                program. Full-time enrollment is defined as:
            </p>
        </div>
        <div class="mb-2">
            <ul class="list-disc pl-5">
                <li>
                    Undergraduate: 12 units per mandatory trimester.
                </li>
                <li>
                    Graduate: 9 units per mandatory trimester.
                </li>
            </ul>
        </div>
        <div class="bg-black text-white font-bold p-2 mb-2">
            PART-TIME ENROLLMENT
        </div>
        <div class="mb-2">
            <p>
                Part-time matriculated and non-matriculated students are required to enroll 6 units per trimester,
                including summer trimester.
            </p>
        </div>
        <br><br>
        <div class="bg-black text-white font-bold p-2 mb-2">
            POLICIES AND PROCEDURE FOR WITHDRAWAL AND REFUND
        </div>
        <div class="mb-2">
            <p>
                Application and admission fees are not refundable. Semester fees can be refundable completely or
                partially according to the conditions below. To withdraw or cancel the contract, fill in the form and
                submit it from your email to ({{ $topbarSetting->email }}).
            </p>
            <ul class="list-disc pl-5">
                <li>
                    Within the first week of session 100%
                </li>
                <li>
                    Within the 2nd week of the session 87.5%
                </li>
                <li>
                    within the 3rd week of the session 75%
                </li>
                <li>
                    within the 4th week of the session 62%
                </li>
                <li>
                    within the 5th week of the session 50%
                </li>
                <li>
                    later than the 5th week of the session 0%
                </li>
            </ul>
        </div>
        <div class="bg-black text-white font-bold p-2 mb-2">
            PROGRAM TUITION FEES INSTALLMENTS
        </div>
        <div class="mb-2">
            <p>
                Program basic tuition fees should be paid in instalments according to the table below.
            </p>
            <table class="w-full border-collapse border border-black">
                <thead>
                    <tr>
                        <th class="border border-black p-2">
                            Fees Type
                        </th>
                        <th class="border border-black p-2">
                            Paid Amount ($)
                        </th>
                        <th class="border border-black p-2">
                            Payment Date
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($student->installments as $installment)
                        <tr>
                            <td class="border border-black p-2">
                                {{ $installment->title }}
                            </td>
                            <td class="border border-black p-2">
                                {{ $installment->amount }}
                            </td>
                            <td class="border border-black p-2">
                                {{ $installment->payment_date }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <img src="{{ asset('dashboard/images/bank_info.png') }}" alt="bank info" width="130%">
        </div>
        <div class="bg-black text-white font-bold p-2 mb-2">
            CERTIFICATION
        </div>
        <div class="mb-2">
            <p>
                ________ (Initial) I understand that this enrollment agreement must be signed no later than (date).
                Otherwise, the enrollment agreement shall not be effective. ________ (Initial)
            </p>
            <p>
                ________ (Initial) I understand that this is a legally binding contract.
            </p>
            <p>
                My signature below certifies that I have read, understood, and agreed to my rights and responsibilities,
                and that the institution's cancellation and refund policies have been clearly explained to me.
            </p>
            <p class="font-bold">
                AEB Representative
            </p>
            <div class="flex justify-between mt-4">
                <div>
                    <p>
                        Signature
                    </p>
                    <p class="border-t border-black w-48">
                    </p>
                </div>
                <div>
                    <p>
                        Date
                    </p>
                    <p class="border-t border-black w-48">
                    </p>
                </div>
            </div>
            <div class="flex justify-between mt-4">
                <div>
                    <p>
                        Signature
                    </p>
                    <p class="border-t border-black w-48">
                    </p>
                </div>
                <div>
                    <p>
                        Date
                    </p>
                    <p class="border-t border-black w-48">
                    </p>
                </div>
            </div>
        </div>
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
                filename: 'Enrollment-Agreement.pdf',
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
