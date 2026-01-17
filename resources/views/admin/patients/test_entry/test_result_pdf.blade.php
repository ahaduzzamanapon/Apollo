<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Test Result</title>
    <style>
    @page {
        /* size: A4 portrait; */
        margin: 40px 15px 50px 15px;
    }

    @font-face {
        font-family: 'kalpurush';
        src: url('{{ public_path("fonts/kalpurush.ttf") }}') format('truetype');
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
        border: 1px solid #ccc;
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
    
    /* ================= PAGE BREAK ================= */
    .page-break {
        /* CSS page break can be finicky in mPDF, we'll use <pagebreak /> tag */
    }
    
    /* ================= FOOTER ================= */
    .footer {
        width: 100%;
        text-align: center;
        font-size: 10px;
        border-top: 1px solid #000;
        padding-top: 5px;
        margin-top: 20px;
    }
    
    /* ================= CENTER HEADER STYLES ================= */
    .report-header {
        width: 100%;
        margin-bottom: 5px; /* Reduced margin */
        text-align: center;
    }

    .bn {
        font-family: 'kalpurush', sans-serif;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .text-cell {
        width: 100%;
        text-align: left;
        vertical-align: middle;
        padding-left: 5px;
    }

    .govt-text {
        font-size: 11px;
        color: #1e4981;
    }

    .name-bn {
        font-size: 23px;
        font-weight: bold;
        color: #1e4981;
        word-spacing: 1px;
    }

    .name-en {
        font-size: 22px;
        color: #c0392b;
        word-spacing: 0px;
    }

    .about-text {
        font-size: 16px;
        color: #0a7c3a;
        word-spacing: 1px;
        padding-left: 1px;
    }

    .address-text {
        font-size: 9px;
        color: #000;
        padding-left: 1px;
    }

    /* ================= CBC SPECIFIC STYLES ================= */
    .cbc-report-title {
        font-size: 14pt;
        margin: 10px auto;
        text-align: center;
        border: 1px solid #000;
        padding: 4px 15px;
        width: fit-content;
        font-weight: bold;
    }

    .cbc-info-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 10px;
    }

    .cbc-info-table td, .cbc-info-table th {
        border: none;
        padding: 2px 5px;
        font-size: 10pt;
        text-align: left;
    }

    .cbc-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cbc-table th {
        text-decoration: underline;
        font-size: 11pt;
        padding: 5px;
        text-align: left;
    }

    .cbc-table td {
        padding: 4px 5px;
        vertical-align: top;
    }

    .cbc-row-dotted {
        border-bottom: 1px dotted #000;
    }

    .cbc-group-header {
        font-size: 11pt;
        text-decoration: underline;
        font-weight: bold;
        padding-top: 15px !important;
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

<!-- RESULTS -->
@php
    $categories = $patients->groupBy('test_category_name');
    $categoryCount = $categories->count();
    $currentIndex = 0;
@endphp

@foreach($categories as $categoryName => $categoryRows)
    @php
        $currentIndex++;
    @endphp
    
    {{-- Add page break BEFORE each category except the first one --}}
    @if($currentIndex > 1)
        <pagebreak />
    @endif
    
    <div>
        <!-- CENTER HEADER ON EACH PAGE -->
        @include('admin.reports.center_header_pdf_print', ['center' => $center])
        
        @php
            $isHematology = (stripos($categoryName, 'hematology') !== false || stripos($categoryName, 'haematology') !== false);
        @endphp

        @if($isHematology)
            <div class="cbc-report-title">HAEMATOLOGY REPORT</div>
            
            <!-- CBC PATIENT INFO -->
            <table class="cbc-info-table">
                <tr>
                    <th width="120px">Patient ID</th>
                    <td width="10px">:</td>
                    <td width="200px">{{ $patient->id }}</td>
                    <th width="100px">Delivery Date</th>
                    <td width="10px">:</td>
                    <td>{{ date('d M, Y') }}</td>
                </tr>
                <tr>
                    <th>Patient's Name</th>
                    <td>:</td>
                    <td>{{ $patient->name }}</td>
                    <th>Age</th>
                    <td>:</td>
                    <td>{{ $patient->age }} {{ $patient->age_unit }}</td>
                    <th width="60px">Gender</th>
                    <td width="10px">:</td>
                    <td>{{ $patient->gender }}</td>
                </tr>
                <tr>
                    <th>Refd. By</th>
                    <td>:</td>
                    <td colspan="7">{{ $patient->referenceDoctor->name ?? 'Self' }}</td>
                </tr>
            </table>
        @else
            <h3 style="margin: 10px 0 5px 0; text-decoration: underline; text-align: center;">PATHOLOGY REPORT</h3>
            
            <!-- PATIENT INFO -->
            <table class="info-table">
                <tr>
                    <td width="50%">
                        <strong>Patient ID:</strong> {{ $patient->id }}<br>
                        <strong>Name:</strong> {{ $patient->name }}<br>
                        <strong>Age/Gender:</strong> {{ $patient->age }} {{ $patient->age_unit }} / {{ $patient->gender }}
                    </td>
                    <td width="50%" class="text-end">
                         <strong>Report Date:</strong> {{ date('d M, Y') }}<br>
                         <strong>Mobile:</strong> {{ $patient->mobile }}
                    </td>
                </tr>
            </table>
            
            <!-- Category Header -->
            <h4 style="margin: 10px 0; padding: 8px; background-color: #e9e9e9; text-align: center; border: 1px solid #ccc;">
                {{ $categoryName ?? 'Uncategorized' }}
            </h4>
        @endif
        
        @if($isHematology)
            <table class="cbc-table">
                <thead>
                    <tr>
                        <th width="40%">Test</th>
                        <th width="30%">Result</th>
                        <th width="30%" style="text-align: center;">Reference Value(for adult)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $tests = $categoryRows->groupBy('test_name');
                    @endphp

                    @foreach($tests as $testName => $rows)
                        <!-- If test name contains 'Count', show it as a group header -->
                        @if(stripos($testName, 'Count') !== false)
                            <tr>
                                <td colspan="3" class="cbc-group-header">{{ $testName }}</td>
                            </tr>
                        @else
                            <!-- Otherwise maybe just a normal row or hide group header if single test -->
                        @endif

                        @foreach($rows as $row)
                            @php
                                $currentTestResult = $savedResults[$row->test_id] ?? null;
                                $resultData = $currentTestResult ? json_decode($currentTestResult->resilt, true) : [];
                                $val = $resultData[$row->field_id] ?? '';
                            @endphp
                            <tr class="cbc-row-dotted">
                                <td>{{ $row->perameter }}</td>
                                <td style="font-weight: bold;">{{ $val }} {{ $row->unit }}</td>
                                <td style="text-align: center;">{{ $row->ref_val }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @else
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
                </tbody>
            </table>
        @endif
    </div>
@endforeach

<!-- FOOTER -->
<div class="footer">
    <table width="100%">
        <tr>
            <td width="33%" style="text-align: left;">
                Printed By: {{ Auth::user()->name ?? 'Admin' }}<br>
                Date: {{ date('d M, Y h:i A') }}
            </td>
            <td width="33%" style="text-align: center;">
                <br>
                _______________________<br>
                Technologist Signature
            </td>
             <td width="33%" style="text-align: right;">
                <br>
                 _______________________<br>
                Authorized Signature
            </td>
        </tr>
    </table>
</div>

</body>
</html>
