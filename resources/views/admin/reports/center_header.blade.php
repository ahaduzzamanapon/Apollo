<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    @font-face {
        font-family: 'kalpurush';
        src: url('{{ public_path("fonts/kalpurush.ttf") }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    body {
        font-family: 'sans-serif';
        margin: 0;
        padding: 0;
        /* font-size: 1px; */
    }

    .report-header {
        width: 100%;
        margin-bottom: 10px;
        /* border-bottom: 1px solid #1e4981; */
        text-align : center;
    }

    .bn {
        font-family: 'kalpurush', sans-serif;
        align-items: center;

    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Fix column widths */
    }

    .logo-cell {
        /* width: 25%; */
        /* text-align: center; */
        /* vertical-align: middle; */
    }

    .logo-cell img {
        /* max-height: 70px;
        width: auto;
        display: block; */
        /* margin: 0 auto; */
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
        /* margin-bottom: 20px !important; */
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
        padding-left:1px
    }

    .address-text {
        font-size: 9px;
        color: #000;
        padding-left: 1px;
    }
</style>

<div class="report-header">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @php
                    $logoPath = public_path('storage/' . $center->logo_image);
                    $logoBase64 = '';
                    if (file_exists($logoPath)) {
                        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($logoPath);
                        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                @endphp

                @if($logoBase64)

                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 80px; width: auto;margin-top:15px;padding-left: 118px">
                @endif
            </td>
            <td class="text-cell">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="govt-text bn" style="padding-left:85px">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার অনুমোদিত</td>
                    </tr>
                    <tr>
                        <td class="name-bn bn">{{ $center->name_bn }}</td>
                    </tr>
                    <tr>
                        <td class="name-en">{{ $center->name_en }}</td>
                    </tr>
                    <tr>
                        <td class="about-text bn">{{ $center->about }}</td>
                    </tr>
                    <tr>
                        <td class="address-text bn">
                        {{ $center->address }} মোবাইলঃ {{ "0".$center->phone }}
                    </td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>
