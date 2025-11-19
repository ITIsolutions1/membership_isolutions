<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Acara</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">

        <!-- Header -->
        <div style="background-color: #1d4ed8; color: white; padding: 20px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
            <h2 style="margin: 0;">📢 Info Acara Terbaru</h2>
        </div>

        <!-- Body -->
        <div style="padding: 20px;">
            <p><strong>Hai, {{$nama}}</strong></p>
            <p>Kami ingin menginformasikan bahwa akan diadakan acara berikut:</p>

            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <tr>
                    <td style="padding: 8px; font-weight: bold; width: 35%;">Nama Acara</td>
                    <td style="padding: 8px;">: {{ $event->nama }}</td>
                </tr>

                @if(!empty($event->lokasi))
                <tr style="background-color: #f3f4f6;">
                    <td style="padding: 8px; font-weight: bold;">Lokasi</td>
                    <td style="padding: 8px;">: {{ $event->lokasi }}</td>
                </tr>
                @endif

                @if(!empty($event->tanggal))
                <tr>
                    <td style="padding: 8px; font-weight: bold;">Tanggal</td>
                    <td style="padding: 8px;">: {{ \Carbon\Carbon::parse($event->tanggal)->format('d M Y') }}</td>
                </tr>
                @endif

                @if(!empty($event->waktu))
                <tr style="background-color: #f3f4f6;">
                    <td style="padding: 8px; font-weight: bold;">Waktu</td>
                    <td style="padding: 8px;">: {{ $event->waktu }}</td>
                </tr>
                @endif

                @if(!empty($event->jenis_peminatan))
                <tr>
                    <td style="padding: 8px; font-weight: bold;">Jenis Acara</td>
                    <td style="padding: 8px;">: {{ ucfirst($event->jenis_peminatan) }}</td>
                </tr>
                @endif
                @if($event->jenis_peminatan == 'seminar')
                <tr style="background-color: #f3f4f6;">
                    <td class="align-top" style="padding: 8px; font-weight: bold;">Narasumber</td>
                    <td class="align-top" style="padding: 8px;">: {{ $event->narasumber }}</td>
                </tr>
                @endif
                @if($event->link)
                <tr>
                    <td class="align-top" style="padding: 8px; font-weight: bold;">Link</td>
                    <td class="align-top" style="padding: 8px;">: {{ $event->link }}</td>
                </tr>
                @endif
            </table>

            <p style="margin-top: 20px;">📲 Untuk informasi lebih lanjut dan pembaruan terkini, silakan pantau platform kami www.member.isolutions.co.id </p>

            <p>Hormat kami,</p>
            <p><strong>Membership Isolutions</strong></p>

            @if(!empty($event->gambar))
            <div style="margin-top: 15px;">
                <img src="{{ asset('storage/' . $event->gambar) }}" alt="Banner Acara" style="max-width: 100%; border-radius: 6px;">
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div style="background-color: #f1f5f9; color: #6b7280; text-align: center; padding: 12px; font-size: 12px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
            Email ini dikirim otomatis. Jangan membalas email ini.
        </div>
    </div>
</body>
</html>
