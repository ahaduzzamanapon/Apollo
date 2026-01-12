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

    /* Common Utilities */
    .text-end { text-align: right; }
    .invoice-title { font-size: 11px; font-weight: bold; margin: 6px 0; }
    table { width: 100%; border-collapse: collapse; }
    
    .info-table td { font-size: 9px; padding: 2px 3px; }
    
    .items-table th, .items-table td {
        border: 1px solid #000;
        padding: 3px;
        font-size: 8.5px;
    }
    .items-table th { background: #f2f2f2; }

    .totals-table { width: 50%; margin-left: auto; }
    .totals-table th, .totals-table td {
        border: 1px solid #000;
        padding: 3px;
        font-size: 8.5px;
    }

    .bangla-text { font-family: 'kalpurush'; font-size: 8.5px; }
    .bangla-vertical {
        border: 1px solid #000;
        border-radius: 6px;
        padding: 2px 6px;
        display: inline-block;
        margin: 3px auto;
    }

    /* ================= PRINT STYLES ================= */
    @media print {
        @page {
            size: A5 portrait;
            margin: 12mm 8mm 18mm 8mm;
        }
        body { margin: 0; padding: 0; }
        .main-page { width: 100%; }
        
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            line-height: 1.2;
        }
        
        .page-break { page-break-before: always; }
    }

    /* ================= SCREEN STYLES (A5 PREVIEW) ================= */
    @media screen {
        body {
            background: #525659; /* Acrobat reader grey */
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        .main-page {
            background: white;
            width: 148mm;
            min-height: 210mm;
            /* Simulate Print Margins using Padding */
            padding: 12mm 8mm 18mm 8mm; 
            margin: 0 auto;
            position: relative;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            box-sizing: border-box;
        }

        .header {
            position: absolute;
            top: 12mm; /* Align with top padding start */
            left: 0;
            right: 0;
            text-align: center;
        }

        /* .footer {
            /* position: absolute; */
            /* bottom: 0mm; Match approximate print position (18mm margin - 10mm offset = 8mm from edge) */
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            line-height: 1.2;
        } */
        
        .page-break {
            border-bottom: 2px dashed #999;
            margin: 20px 0;
            position: relative;
        }
        .page-break::after {
            content: "Page Break";
            position: absolute;
            right: 0;
            top: -20px;
            color: #999;
            font-size: 10px;
        }
    }
    </style>
</head>

<body>

<div class="main-page">
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

        <table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 5px;">
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
                    <div style="font-size: 9px; margin-top: 2px;">{{ $center->address }} | Mobile: {{ $center->phone }}</div>
                </td>
            </tr>
        </table>
        
        <div class="invoice-title" style="text-align: center; font-size: 12px; font-weight: bold; margin-top: 2px; text-decoration: underline;">MONEY RECEIPT</div>
    </div> 

    <!-- Space for Header -->
    <div style="height: 25mm;"></div>

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
    <table class="info-table" style="border:1px solid #000; margin-bottom:5px;margin-top:15px">
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
</div>

</body>
</html>
