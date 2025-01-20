<!doctype html>
<html>

<head>
    <meta http-equiv=3D"Content-Type" content=3D"text/html; charset=3DUTF-8">
</head>

<body style=3D"font-family: sans-serif;">
    <div style=3D"display: block; margin: auto; max-width: 600px;" class=3D"main">
        <h1 style=3D"font-size: 18px; font-weight: bold; margin-top: 20px">Selamat {{ $name }} Berhasil
            Generate OTP!</h1>
        <p>Silakan gunakan OTP Code di bawah.</p>
        <h1 style="background-color: #c4f7ff; text-align: center; font-size: 2.5em;">{{ $otp }}</h1>
        <p>waktu otp code aktfi 5 menit dari sekarang!</p>
    </div>
    <style>
        .main {
            background-color: white;
        }

        a:hover {
            border-left-width: 1em;
            min-height: 2em;
        }
    </style>
</body>

</html>
