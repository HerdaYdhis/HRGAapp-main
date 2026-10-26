@component('mail::message')
# Reset Password

Assalamualaikum,

Klik Tombol di bawah ini untuk melakukan Reset Password

@component('mail::button', ['url' => 'https://donasi-frontend-xi.vercel.app/reset-password/'. urlencode($token) . '?email=' . urlencode($email), 'color' => 'primary'])
Reset Password
@endcomponent

atau jika anda tidak dapat menekan tombol, dapat menggunakan link tersebut

<a href="https://donasi-frontend-xi.vercel.app/reset-password/{{urlencode($token)}}?email={{urlencode($email)}}">http://localhost:5173/reset-password/{{urlencode($token)}}?email={{urlencode($email)}}</a>


Biarkan link ini, jika password anda tidak ingin diganti.


Jazakumullah.

@endcomponent
