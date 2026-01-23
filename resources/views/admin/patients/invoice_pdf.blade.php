<!DOCTYPE html>
<html lang="bn">
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
            margin: 5px 0;
            text-align: center;
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

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 10px;
        }

        .totals-table {
            width: 60%;
            margin-left: auto;
        }

        .totals-table th,
        .totals-table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 10px;
        }

        .text-end {
            text-align: right;
        }

        .page-break {
            page-break-before: always;
        }

        /* ================= WATERMARK ================= */
        /* .watermark {
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            opacity: 0.08;
            width: 350px;
        } */

        .bangla-text {
            font-family: 'kalpurush', sans-serif !important;
            font-size: 14px;
        }

        .bangla-vertical {
            font-family: 'kalpurush', sans-serif !important;
            border: 1px solid #000;
            border-radius: 8px;
            padding: 3px;
            display: inline-block;
            margin: 4px auto;
        }
    </style>
</head>

<body>

{{-- ================= WATERMARK LOGIC ================= --}}
{{-- @php
    $center = \App\Models\CenterDetails::first();
    $logoBase64 = null;

    if ($center && $center->logo_image) {
        $logoPath = public_path('storage/' . $center->logo_image);
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }
@endphp --}}

{{-- @if($logoBase64)
    <img src="{{ $logoBase64 }}" class="watermark">
@endif --}}

{{-- ================= HEADER ================= --}}
<div class="header">
    @include('admin.reports.center_header')
</div>

<div class="invoice-title">
    <u>MONEY RECEIPT</u>
</div>

<div style="margin-top: 20px;">
    <span style="font-size: 14px;">
        Bill No: <strong>{{ $report->report_code }}</strong>
    </span>

    {!! \App\Services\BarcodeService::getHtmlImg(
        $report->report_code,
        ['style' => 'height:25px;width:200px;float:right']
    ) !!}
</div>

<br>

{{-- ================= FOOTER ================= --}}
<div class="footer">
    <p class="bangla-text">
        রিপোর্ট সংগ্রহের শেষ সময় রাত ১১.০০, রিপোর্ট নেয়ার সময় রিসিট অবশ্যই সাথে আনতে হবে
    </p>

    <p class="bangla-text bangla-vertical">
        ডেলিভারী তারিখ হতে ৩০ দিনের মধ্যে রিপোর্ট সংগ্রহ করা যাবে
    </p>

    <p class="bangla-text" style="border-bottom:1px solid #000">
        {{ $center->address }} মোবাইলঃ {{ $center->phone }}
    </p>

    <p>
        Computer Generated Invoice |
        Printed: {{ date('d M, Y h:i A') }}
    </p>
</div>

{{-- ================= PATIENT INFO ================= --}}
<table style="font-size: 12px; border:1px dashed #000;text-transform: uppercase;">
    <tr>
        <td style="white-space:nowrap;padding:2px 3px">ID No</td>
        <td style="white-space:nowrap;padding:2px 3px">:</td>
        <td style="white-space:nowrap;padding:2px 3px"><strong>{{ $report->daily_id ?? $report->id }}</strong></td>
        <td style="white-space:nowrap;padding:2px 3px">Mobile</td>
        <td style="white-space:nowrap;padding:2px 3px">:</td>
        <td style="white-space:nowrap;padding:2px 3px"><strong>{{ $report->patient->mobile }}</strong></td>
        <td style="white-space:nowrap;padding:2px 3px">Date & Time</td>
        <td style="white-space:nowrap;padding:2px 3px">:</td>
        <td style="white-space:nowrap;padding:2px 3px"><strong>{{ date('d M Y, h:i A', strtotime($report->created_at)) }}</strong></td>
    </tr>
    <tr>
        <td style="white-space:nowrap;padding:2px 3px">Patient Name </td>
        <td style="white-space:nowrap;padding:2px 3px">:</td>
        <td style="white-space:nowrap;padding:2px 3px"><strong>{{ $report->patient->name }}</strong></td>
        <td style="white-space:nowrap;padding:2px 3px">Age</td>
        <td style="white-space:nowrap;padding:2px 3px">:</td>
        <td style="white-space:nowrap;padding:2px 3px"><strong>{{ $report->patient->age }} {{ $report->patient->age_unit }}</strong></td>
        <td style="white-space:nowrap;padding:2px 3px">Gender</td>
        <td style="white-space:nowrap;padding:2px 3px">:</td>
        <td style="white-space:nowrap;padding:2px 3px"><strong>{{ $report->patient->gender }}</strong></td>
    </tr>
    <tr>
        <td style="white-space:nowrap;padding:2px 3px">Ref. Doctor</td>
        <td style="white-space:nowrap;padding:2px 3px">:</td>
        <td style="white-space:nowrap;padding:2px 3px"><strong>{{ $report->referenceDoctor->name ?? 'Self' }}</strong></td>
    </tr>
</table>

<br>

{{-- ================= TEST LIST ================= --}}
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

    {{-- ================= TOTALS (LAST PAGE) ================= --}}
    @if($loop->last)

        <div style="width: 100%; display: flex; justify-content: space-between; ">
            <div style="width: 40%;">
                <table style="width: 100%; border-collapse: collapse; line-height: 1.5;">
                    <tr>
                        <td style="font-size: 14px; font-weight: bold; padding: 3px; display: flex; align-items: center;">Note:</td>
                        <td style="padding-top: 50px !important;font-size: 14px; font-weight: bold; padding: 3px; display: flex; align-items: center;white-space:nowrap ">
                            @if($report->due_amount == 0)
                                <span style="display: inline-block;font-weight: bold; padding: 5px 15px; border: 1px solid #28a745; color: #28a745; font-size: 30px; font-weight: bold; border-radius: 5px; padding-left: 100px;float:center !important">PAID</span>
                            @else
                                <span style="display: inline-block;font-weight: bold; padding: 5px 15px; border: 1px solid #dc3545; color: #dc3545; font-size: 30px; font-weight: bold; border-radius: 5px; margin-left: 100px;float:center !important">DUE : {{ number_format($report->due_amount, 0) }} TK.</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width: 60%; float: right;">
                <table class="totals-table" style="font-size:14px; margin-left: auto;float: right;margin-top: -35px;">
                    <tr>
                        <th style="font-size:11px">Total</th>
                        <td style="font-size:11px" class="text-end">{{ number_format($report->total_amount, 0) }}</td>
                    </tr>
                    <tr>
                        <th style="font-size:11px">Discount</th>
                        <td style="font-size:11px" class="text-end">{{ number_format($report->discount, 0) }}</td>
                    </tr>
                    <tr>
                        <th style="font-size:11px">Net Payable</th>
                        <td style="font-size:11px" class="text-end">
                            <strong>{{ number_format($report->final_amount, 0) }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <th style="font-size:11px">Paid</th>
                        <td style="font-size:11px" class="text-end">{{ number_format($report->paid_amount, 0) }}</td>
                    </tr>
                    <tr>
                        <th style="font-size:11px">Due</th>
                        <td style="font-size:11px" class="text-end">{{ number_format($report->due_amount, 0) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

@endforeach

</body>
</html>
