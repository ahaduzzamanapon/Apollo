<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CenterDetails List</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laravel</h1>
        <p>CenterDetails Report</p>
        <p>Generated on: 2026-01-02 18:03:48</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name_Bn</th>
                <th>Name_En</th>
                <th>About</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Logo_Image</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name_bn }}</td>
                <td>{{ $item->name_en }}</td>
                <td>{{ $item->about }}</td>
                <td>{{ $item->address }}</td>
                <td>{{ $item->phone }}</td>
                <td>{{ $item->logo_image }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Laravel. All rights reserved.</p>
    </div>
</body>
</html>
