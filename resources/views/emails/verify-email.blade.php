<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <h2>Hello, {{ $user->name }}</h2>
    <p>Thank you for registering. Please verify your email by clicking the button below:</p>

    <a href="{{ $verificationUrl }}"
       style="display:inline-block;padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;">
       Verify Email
    </a>

    <p>If you did not create an account, no further action is required.</p>
</body>
</html>
