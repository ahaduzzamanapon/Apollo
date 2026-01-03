<!DOCTYPE html>
<html>
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $report->report_code }}</title>
    <style>
        @page { margin: 15px; }
        @font-face {
            font-family: 'Kalpurush';
            src: url('{{ public_path("fonts/Kalpurush.ttf") }}') format('truetype');
        }
        body { font-family: "Kalpurush",sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; font-weight: bold; }
        .header p { margin: 2px 0; }
        .invoice-title { font-size: 12px; font-weight: bold; margin-bottom: 10px; text-align: center; padding-bottom: 5px;width:10px }
        .bangla-text {
            font-family: 'Kalpurush', 'DejaVu Sans', sans-serif;
            font-size: 10px;
        }
        .bangla-vertical {
            border: 1px solid red;
            border-radius: 10px;
            padding: 6px 4px;
            font-family: 'Kalpurush';
            font-size: 10px;
            text-align: center;
            width: 300px;
            writing-mode: rl !important;
            /* position:absolute; */
            margin: 0 auto;

        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 2px;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000000;
            padding: 4px;
        }
        .items-table th {
            /* background-color: #d6c9c9; */
            text-align: left;
            border:1px solid #000000;
        }
        .text-end {
            text-align: right;
        }

        .totals-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table th, .totals-table td {
            border: 1px solid #000000;
            padding: 4px;
        }

        .footer {
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 20px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    @include('admin.reports.center_header')

    <div class="invoice-title">MONEY RECEIPT</div>

    <table class="info-table" style="border: 1px solid black;">
        <tr>
            <td width="55%">
                <strong>Bill No:</strong> {{ $report->report_code }}<br>
                <strong>Date:</strong> {{ date('d M, Y', strtotime($report->report_date)) }}<br>
                <strong>Ref:</strong> {{ $report->referenceDoctor->name ?? 'Self' }}<br>
                <strong>Patient Name:</strong> {{ $report->patient->name }}<br>
            </td>
            <td width="45%" class="text-end">
                <strong>Gender:</strong> {{ $report->patient->gender }}<br>
                <strong>Age:</strong> {{ $report->patient->age }}Y<br>
                <strong>Mobile:</strong> {{ $report->patient->mobile }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Test Name</th>
                <th class="text-end" width="20%">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->tests as $test)
            <tr>
                <td>{{ $test->category->test_name }}</td>
                <td class="text-end">{{ number_format($test->price, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

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
            <td class="text-end fw-bold">{{ number_format($report->final_amount, 0) }}</td>
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
    <br>
    <p class="bangla-text" style="text-align: center;">রিপোর্ট সংগ্রহের শেষ সময় রাত ১১.০০,"রিপোর্ট নেয়ার সময় রিসিটে থাকা মোবাইল নাম্বারের মোবাইলটি এবং রিসিট অবশ্যই সাথে আনতে হবে"</p>
    <p class="bangla-text bangla-vertical" > ডেলিভারী তারিখ হতে ৩০ দিনের মধ্যে রিপোর্ট সংগ্রহ করা যাবে</p>
    <div class="footer">
        <p style="border-top: 1px solid #1e4981;">Computer Generated Invoice. Printed: {{ date('d M, Y h:i A') }}</p>
    </div>
</body>
</html>
