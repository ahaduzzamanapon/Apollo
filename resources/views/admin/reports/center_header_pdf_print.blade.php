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
            <td class="text-cell" style="text-align: center;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="govt-text bn" style="text-align: center;">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার অনুমোদিত</td>
                    </tr>
                    <tr>
                        <td class="name-bn bn" style="text-align: center;">{{ $center->name_bn }}</td>
                    </tr>
                    <tr>
                        <td class="name-en" style="text-align: center;">{{ $center->name_en }}</td>
                    </tr>
                    <tr>
                        <td class="about-text bn" style="text-align: center;">{{ $center->about }}</td>
                    </tr>
                    <tr>
                        <td class="address-text bn" style="text-align: center;">
                        {{ $center->address }} মোবাইলঃ {{ "0".$center->phone }}
                    </td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>
