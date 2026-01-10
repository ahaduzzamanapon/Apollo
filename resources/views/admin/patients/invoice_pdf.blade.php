<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $report->report_code }}</title>

    <style>
        @page {
            margin: 90px 15px 110px 15px;
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
        }

        /* ================= HEADER ================= */
        .header {
            position: fixed;
            top: -75px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .invoice-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 3px;
        }

        /* ================= FOOTER ================= */
        .footer {
            position: fixed;
            bottom: -95px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
        }

        /* ================= TABLES ================= */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            font-size: 12px;
            padding: 2px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 9px;
        }

        .totals-table {
            width: 45%;
            margin-left: auto;
        }

        .totals-table th,
        .totals-table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 9px;
        }

        .text-end {
            text-align: right;
        }

        /* ================= PAGE BREAK ================= */
        .page-break {
            page-break-before: always;
        }

        .bangla-text {
            font-family: 'kalpurush';
            font-size: 11px;
        }

        .bangla-vertical {
            border: 1px solid #000;
            border-radius: 8px;
            padding: 3px;
            display: inline-block;
            margin: 4px auto;
        }
    </style>
</head>

<body>

<!-- ================= HEADER (ALL PAGES) ================= -->
<div class="header">
    @include('admin.reports.center_header')
</div>
<div class="invoice-title" style="padding-bottom: 5px;padding-top:5px;text-align: center">MONEY RECEIPT</div>

<!-- ================= FOOTER (ALL PAGES) ================= -->
<div class="footer">
    <p class="bangla-text">
        রিপোর্ট সংগ্রহের শেষ সময় রাত ১১.০০, রিপোর্ট নেয়ার সময় রিসিট অবশ্যই সাথে আনতে হবে
    </p>

    <p class="bangla-text bangla-vertical">
        ডেলিভারী তারিখ হতে ৩০ দিনের মধ্যে রিপোর্ট সংগ্রহ করা যাবে
    </p>

    <p class="bangla-text" style="border-bottom:1px solid #000">
        {{ $center->address }} মোবাইলঃ {{ '0'.$center->phone }}
    </p>

    <p>
        Computer Generated Invoice |
        Printed: {{ date('d M, Y h:i A') }}
    </p>
</div>

<!-- ================= PATIENT INFO (FIRST PAGE ONLY) ================= -->
<table class="info-table" style="border:1px solid #000; margin-bottom:10px;">
    <tr>
        <td width="55%">
            <strong>Bill No:</strong> {{ $report->report_code }}<br>
            <strong>Date:</strong> {{ date('d M, Y', strtotime($report->report_date)) }}<br>
            <strong>Patient:</strong> {{ $report->patient->name }}
        </td>
        <td width="45%" class="text-end">
            <strong>Gender:</strong> {{ $report->patient->gender }}<br>
            <strong>Age:</strong> {{ $report->patient->age }} {{ $report->patient->age_unit }}<br>
            <strong>Mobile:</strong> {{ $report->patient->mobile }}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <strong>Ref:</strong> DR. {{ $report->referenceDoctor->name ?? 'Self' }}
        </td>
    </tr>
</table>

<!-- ================= TEST LIST (22 PER PAGE) ================= -->
@php
    $chunks = $report->tests->chunk(22);
@endphp

@foreach($chunks as $index => $tests)

    @if($index > 0)
        <div class="page-break"></div>
    @endif

    <table class="items-table">
        <thead>
            <tr>
                <th>Test Name</th>
                <th width="20%" class="text-end">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tests as $test)
            <tr>
                <td>{{ $test->category->test_name }}</td>
                <td class="text-end">{{ number_format($test->price, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ================= TOTALS (LAST PAGE ONLY) ================= -->
    @if($loop->last)
        <br>
        <table class="totals-table">
            <tr>
                <th>Total</th>
                <td class="text-end">{{ number_format($report->total_amount, 0) }}</td>
            </tr>
            <tr>
                <th>Discount</th>
                <td class="text-end">{{ number_format($report->discount, 0) }}</td>
            </tr>
            <tr>
                <th>Net Payable</th>
                <td class="text-end">
                    <strong>{{ number_format($report->final_amount, 0) }}</strong>
                </td>
            </tr>
            <tr>
                <th>Paid</th>
                <td class="text-end">{{ number_format($report->paid_amount, 0) }}</td>
            </tr>
            <tr>
                <th>Due</th>
                <td class="text-end">{{ number_format($report->due_amount, 0) }}</td>
            </tr>
        </table>
    @endif

@endforeach

</body>
</html>
