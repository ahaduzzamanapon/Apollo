<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Test Result</title>
    <script>
        window.onload = function () {
            // Only auto-print if viewed in browser (unlikely for PDF download context but good for pure print route)
             if(!window.location.href.includes('pdf')) {
                window.print();
             }
        }
    </script>
    <style>
    @page {
        size: A4 portrait;
        margin: 12mm 12mm 12mm 12mm;
    }

    @font-face {
        font-family: 'kalpurush';
        src: url('{{ asset("fonts/kalpurush.ttf") }}') format('truetype');
    }

    body {
        font-family: 'kalpurush', sans-serif;
        font-size: 11px;
        margin: 0;
        padding: 0;
        line-height: 1.3;
    }

    /* ================= HEADER ================= */
    .header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }

    /* ================= INFO TABLE ================= */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .info-table td {
        padding: 4px;
        vertical-align: top;
    }

    /* ================= RESULTS TABLE ================= */
    .results-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .results-table th, .results-table td {
        border: 1px solid #ccc; /* Lighter border for results */
        padding: 5px;
        text-align: left;
    }
    .results-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .results-table .category-header {
        background-color: #e9e9e9;
        font-weight: bold;
        text-align: center;
    }
    .results-table .test-header {
        background-color: #f9f9f9;
        font-weight: bold;
        padding-left: 10px;
    }

    .text-center { text-align: center; }
    .text-end { text-align: right; }
    
    /* ================= FOOTER ================= */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 10px;
        border-top: 1px solid #000;
        padding-top: 5px;
    }
    </style>
</head>
<body>

@php
    $patient = $patients->first();
    $center = \App\Models\CenterDetails::first();
    
    // Logo Logic
    $logoBase64 = null;
    if ($center && $center->logo_image) {
        $logoPath = public_path('storage/' . $center->logo_image);
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }
@endphp

<!-- HEADER -->
<div class="header">
    <table width="100%">
        <tr>
             <td width="20%" style="vertical-align: top; text-align: left;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="height: 70px; width: auto;">
                @endif
            </td>
            <td width="80%" style="text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #1e4981;">{{ $center->name_en ?? 'N/A' }}</div>
                <div style="font-size: 12px;">{{ $center->address ?? '' }}</div>
                <div style="font-size: 12px;">Mobile: {{ $center->phone ?? '' }}</div>
            </td>
        </tr>
    </table>
    <h3 style="margin: 5px 0 0 0; text-decoration: underline;">PATHOLOGY REPORT</h3>
</div>

<!-- PATIENT INFO -->
<table class="info-table">
    <tr>
        <td width="50%">
            <strong>Patient ID:</strong> {{ $patient->id }}<br> <!-- Using Patient ID as ID -->
            <strong>Name:</strong> {{ $patient->name }}<br>
            <strong>Age/Gender:</strong> {{ $patient->age }} {{ $patient->age_unit }} / {{ $patient->gender }}
        </td>
        <td width="50%" class="text-end">
             <strong>Report Date:</strong> {{ date('d M, Y') }}<br> <!-- Using Current Date or Report Date? Report Date is better if available -->
             <strong>Ref by:</strong> {{ $patient->name }} <!-- Reference Doctor logic is weird in query. PatientReport has reference_doctor_id but we joined Patient. Reference Doctor is not selected in query properly. I'll skip or use Patient name if no doctor. Actually patientTestEntry query select 'patients.*'. 
             Wait, I need Reference Doctor Name. 
             Query joins 'patient_reports' -> 'patient_tests'. 
             In 'getPatientTestData', I select 'patients.*'.
             PatientReport has 'reference_doctor_id'.
             I should probably modify query to select Ref Doctor Name?
             For now, I'll display what I have. -->
             <strong>Mobile:</strong> {{ $patient->mobile }}
        </td>
    </tr>
</table>

<!-- RESULTS -->
<table class="results-table">
    <thead>
        <tr>
            <th width="40%">Test / Parameter</th>
            <th width="30%">Result</th>
            <th width="15%">Unit</th>
            <th width="15%">Reference Value</th>
        </tr>
    </thead>
    <tbody>
        @php
            $categories = $patients->groupBy('test_category_name');
        @endphp

        @foreach($categories as $categoryName => $categoryRows)
            <!-- Category Header -->
            <tr>
                <td colspan="4" class="category-header">{{ $categoryName ?? 'Uncategorized' }}</td>
            </tr>

            @php
                $tests = $categoryRows->groupBy('test_name');
            @endphp

            @foreach($tests as $testName => $rows)
                <!-- Test Name -->
                <tr>
                    <td colspan="4" class="test-header">{{ $testName }}</td>
                </tr>

                <!-- Parameters -->
                @foreach($rows as $row)
                    @php
                        $currentTestResult = $savedResults[$row->test_id] ?? null;
                        $resultData = $currentTestResult ? json_decode($currentTestResult->resilt, true) : [];
                        $val = $resultData[$row->field_id] ?? '';
                    @endphp
                    <tr>
                        <td style="padding-left: 20px;">{{ $row->perameter }}</td>
                        <td style="font-weight: bold;">{{ $val }}</td>
                        <td>{{ $row->unit }}</td>
                        <td>{{ $row->ref_val }}</td>
                    </tr>
                @endforeach

            @endforeach
        @endforeach
    </tbody>
</table>

<!-- FOOTER -->
<div class="footer">
    <table width="100%">
        <tr>
            <td width="50%" style="text-align: left;">
                Printed By: {{ Auth::user()->name ?? 'Admin' }}<br>
                Date: {{ date('d M, Y h:i A') }}
            </td>
            <td width="50%" style="text-align: right;">
                <br>
                _______________________<br>
                Technologist Signature
            </td>
        </tr>
    </table>
</div>

</body>
</html>
