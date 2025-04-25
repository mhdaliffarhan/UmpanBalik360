<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Penilaian</title>
</head>

<body style="font-family: 'Open Sans', sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 16px; box-shadow: 0px 2px 12px rgba(0, 0, 0, 0.1); border: 1px solid #e0e0e0;">
        
        <!-- Email Header -->
        <h1 style="color: #344767; font-weight: 700; font-size: 24px; margin-bottom: 16px; text-align: center;">Undangan Penilaian</h1>
        
        <!-- Email Body -->
        <p style="font-size: 14px; color: #6c757d; line-height: 1.6;">Halo,</p>
        <p style="font-size: 14px; color: #6c757d; line-height: 1.6;">Anda telah diundang untuk mengikuti penilaian <strong>{{ $penilaianDetails['nama_penilaian'] }}</strong> oleh tim kerja <strong>{{ $penilaianDetails['tim_kerja'] }}</strong>.</p>
        <p style="font-size: 14px; color: #6c757d; line-height: 1.6;">Penilaian ini akan berlangsung dari <strong>{{ $penilaianDetails['tanggal_mulai'] }}</strong> hingga <strong>{{ $penilaianDetails['tanggal_selesai'] }}</strong>.</p>
        <p style="font-size: 14px; color: #6c757d; line-height: 1.6;">Silakan klik tombol di bawah ini untuk melihat rincian lebih lanjut dan berpartisipasi dalam penilaian.</p>
        
        <!-- Button -->
        <div style="text-align: center;">
            <a href="{{ $penilaianDetails['link'] }}" style="display: inline-block; background-color: #f53939; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; margin-top: 16px; box-shadow: 0 4px 8px rgba(94, 114, 228, 0.2); font-weight: 600;">Lihat Penilaian</a>
        </div>

        <!-- Footer -->
        <div style="margin-top: 32px; text-align: center; font-size: 12px; color: #adb5bd;">
            <p style="margin: 0;">Jika Anda memiliki pertanyaan, silakan hubungi tim kerja {{ $penilaianDetails['tim_kerja'] }}.</p>
            <p><a href="#" style="color: #f53939; text-decoration: none;">Lihat Detail Lebih Lanjut</a></p>
        </div>
    </div>
</body>

</html>
