<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $report->report_code }}</title>
    <script>
        window.onload = function () {
            window.print();
        }
    </script>
    <style>
    @page {
        size: A5 portrait;
        margin: 2mm 8mm 0mm 8mm;
    }

    @font-face {
        font-family: 'kalpurush';
        src: url('{{ asset("fonts/kalpurush.ttf") }}') format('truetype');
    }

    body {
        font-family: 'kalpurush', sans-serif;
        font-size: 9.5px;
        margin: 0;
        padding: 0;
        line-height: 1.25;
    }

    /* ================= HEADER ================= */
    .header {
        position: fixed;
        /* top: -8mm; */
        left: 0;
        right: 0;
        text-align: center;
    }

    .invoice-title {
        font-size: 11px;
        font-weight: bold;
        margin: 6px 0;
    }

    /* ================= FOOTER ================= */
    .footer {
        position: fixed;
        bottom: 0mm;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8px;
        line-height: 1.2;
    }

    /* ================= TABLES ================= */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        font-size: 9px;
        padding: 2px 3px;
    }

    .items-table th,
    .items-table td {
        border: 1px solid #000;
        padding: 3px;
        font-size: 8.5px;
    }

    .items-table th {
        background: #f2f2f2;
    }

    .totals-table {
        width: 50%;
        margin-left: auto;
    }

    .totals-table th,
    .totals-table td {
        border: 1px solid #000;
        padding: 3px;
        font-size: 8.5px;
    }

    .text-end {
        text-align: right;
    }

    /* ================= PAGE BREAK ================= */
    .page-break {
        page-break-before: always;
    }

    /* ================= BANGLA ================= */
    .bangla-text {
        font-family: 'kalpurush', sans-serif !important;
        font-size: 8.5px;
    }

    .bangla-vertical {
        font-family: 'kalpurush', sans-serif !important;
        border: 1px solid #000;
        border-radius: 6px;
        padding: 2px 6px;
        display: inline-block;
        margin: 3px auto;
    }

    /* ================= WATERMARK ================= */
    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.1;
        z-index: -1;
        width: 280px;
        height: 280px;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        pointer-events: none;
    }

    @media print {
        .watermark {
            opacity: 0.2;
        }
    }
    </style>


</head>

<body >


<!-- ================= HEADER (ALL PAGES) ================= -->
<div class="header">
    @php
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
    @endphp

    <table width="100%" style="border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 5px;">
        <tr>
            <!-- Logo Column -->
            <td width="20%" style="vertical-align: top; text-align: left;margin-top:10px">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="height: 60px; width: auto;">
                @endif
            </td>

            <!-- Info Column -->
            <td width="80%" style="text-align: center; vertical-align: middle;">
                <div style="font-size: 10px; color: #1e4981;">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার অনুমোদিত</div>
                <div style="font-size: 18px; font-weight: bold; color: #1e4981; font-family: 'kalpurush', sans-serif;">{{ $center->name_bn }}</div>
                <div style="font-size: 18px; font-weight: bold; color: #c0392b; text-transform: uppercase;">{{ $center->name_en }}</div>
                <div style="font-size: 10px; color: #0a7c3a;">{{ $center->about }}</div>
                <div style="font-size: 9px; margin-top: 2px;">{{ $center->address }}  Mobile: {{ $center->phone }}</div>
            </td>
        </tr>
    </table>

    <div class="invoice-title" style="text-align: center; font-size: 17px; font-weight: bold; margin-top: 2px; text-decoration: underline;padding-bottom: 5px;">MONEY RECEIPT</div>
</div>
<!-- Space for Header -->
<div style="height: 25mm;"></div>

<!-- ================= FOOTER (ALL PAGES) ================= -->
<div style="margin-top: 20px;margin-bottom: 15px;" >
    <span style="font-size: 14px;">Bill No: <strong>{{ $report->report_code }}</strong></span>
    {!! \App\Services\BarcodeService::getHtmlImg($report->report_code, ['style' => 'height: 25px;width: 200px;float: right']) !!}
</div>

<!-- ================= PATIENT INFO (FIRST PAGE ONLY) ================= -->

<div class="row mb-4" style="text-transform: uppercase;font-size:15px;border: 1px dashed #000;line-height: 20px;padding-top: 8px;height:auto">
    <div class="col-md-12">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 14%; padding: 2px 5px;white-space:nowrap"><span>ID No</span></td>
                <td style="width: 2%; padding: 2px 2px; text-align: center; font-weight: bold;">:</td>
                <td style="width: 18%; padding: 2px 5px;white-space:nowrap"><strong>{{ $report->daily_id ?? $report->id }}</strong></td>
                <td style="width: 14%; padding: 2px 5px;white-space:nowrap"><span>Mobile</span></td>
                <td style="width: 2%; padding: 2px 2px; text-align: center; font-weight: bold;">:</td>
                <td style="width: 18%; padding: 2px 5px;white-space:nowrap"><strong>{{ $report->patient->mobile }}</strong></td>
                <td style="width: 14%; padding: 2px 5px;white-space:nowrap"><span>Date & Time</span></td>
                <td style="width: 2%; padding: 2px 2px; text-align: center; font-weight: bold;">:</td>
                <td style="width: 16%; padding: 2px 5px;white-space:nowrap"><strong>{{ date('d M Y', strtotime($report->report_date)).date(' h:i A',strtotime($report->created_at)) }}</strong></td>
            </tr>
            <tr>
                <td style="width: 14%; padding: 2px 5px;white-space:nowrap"><span>Patient Name</span></td>
                <td style="width: 2%; padding: 2px 2px; text-align: center; font-weight: bold;">:</td>
                <td style="width: 18%; padding: 2px 5px;white-space:nowrap"><strong style="word-wrap: break-word; overflow-wrap: break-word;">{{ $report->patient->name }}</strong></td>
                <td style="width: 14%; padding: 2px 5px;white-space:nowrap"><span>Age</span></td>
                <td style="width: 2%; padding: 2px 2px; text-align: center; font-weight: bold;">:</td>
                <td style="width: 18%; padding: 2px 5px;white-space:nowrap"><strong>{{ $report->patient->long_formatted_age }}</strong></td>
                <td style="width: 14%; padding: 2px 5px;white-space:nowrap"><span>Gender</span></td>
                <td style="width: 2%; padding: 2px 2px; text-align: center; font-weight: bold;">:</td>
                <td style="width: 16%; padding: 2px 5px;white-space:nowrap"><strong>{{ $report->patient->gender }}</strong></td>
            </tr>
            <tr>
                <td style="padding:2px 5px;white-space:nowrap">Ref. Doctor</td>
                <td style="padding:2px 2px; text-align: center; font-weight: bold;">:</td>
                <td colspan="7" style="padding:2px 5px;white-space:nowrap">
                    <strong>{{ $report->referenceDoctor->name ?? 'Self' }}</strong>
                </td>

        </table>

    </div>
</div>
    <!-- ================= TEST LIST (22 PER PAGE) ================= -->
    @php
        $chunks = $report->tests->chunk(22);
    @endphp

    @foreach($chunks as $index => $tests)

        @if($index > 0)
            <div class="page-break"></div>
        @endif

        <table class="items-table mt-2" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th style="font-size:15px   ">Test Name</th>
                    <th style="font-size:15px   " width="20%" class="text-end">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tests as $test)
                <tr>
                    <td style="font-size:15px;">{{ $test->category->test_name }}</td>
                    <td style="font-size:15px;" class="text-end">{{ number_format($test->price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ================= TOTALS (LAST PAGE ONLY) ================= -->
        @if($loop->last)

            <div style="width: 100%; display: flex; justify-content: space-between; margin-top: 20px;">
                <div style="width: 40%;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-size: 20px; font-weight: bold; padding: 3px;">Note:</td>
                            <td style="font-size: 30px; padding: 3px;font-weight: bold; text-align: center;">
                                @if($report->due_amount == 0)
                                    <span style="display: inline-block;font-weight: bold ;padding: 5px 15px; border: 1px solid #28a745; color: #28a745; font-size: 30px; font-weight: bold; border-radius: 5px;">PAID</span>
                                @else
                                    <span style="display: inline-block;font-weight: bold; padding: 5px 15px; border: 1px solid #dc3545; color: #dc3545; font-size: 30px; font-weight: bold; border-radius: 5px;white-space:nowrap">DUE : {{ number_format($report->due_amount, 0) }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="width: 60%;">
                    <table class="totals-table" style="font-size:15px; margin-left: auto;">
                        <tr>
                            <th style="font-size:15px">Total</th>
                            <td style="font-size:15px" class="text-end">{{ number_format($report->total_amount, 0) }}</td>
                        </tr>
                        <tr>
                            <th style="font-size:15px">Discount</th>
                            <td style="font-size:15px" class="text-end">{{ number_format($report->discount, 0) }}</td>
                        </tr>
                        <tr>
                            <th style="font-size:15px">Net Payable</th>
                            <td style="font-size:15px" class="text-end">
                                <strong>{{ number_format($report->final_amount, 0) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th style="font-size:15px">Paid</th>
                            <td style="font-size:15px" class="text-end">{{ number_format($report->paid_amount, 0) }}</td>
                        </tr>
                        <tr>
                            <th style="font-size:15px">Due</th>
                            <td style="font-size:15px" class="text-end">{{ number_format($report->due_amount, 0) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        <div class="footer" >
            <p class="bangla-text" style="font-size: 14px;">
                রিপোর্ট সংগ্রহের শেষ সময় রাত ১১.০০, রিপোর্ট নেয়ার সময় রিসিট অবশ্যই সাথে আনতে হবে
            </p>

            <p class="bangla-text bangla-vertical" style="font-size: 14px;">
                ডেলিভারী তারিখ হতে ৩০ দিনের মধ্যে রিপোর্ট সংগ্রহ করা যাবে
            </p>

            <p class="bangla-text" style="border-bottom:1px solid #000;font-size: 12px;">
                {{ $center->address }} মোবাইলঃ {{ $center->phone }}
            </p>

            <p style="font-size: 14px;">
                Prepared By: {{ auth()->user()->name }} |
                Printed: {{ date('d M, Y h:i A') }}
            </p>
        </div>

    @endforeach

</body>
</html>
