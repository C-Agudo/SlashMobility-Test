<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>Hello {{ $user['user_name'] }},thanks for signin</h2>
    <p>Please, confirm your email clicking on the next link: </p>

    <a href=" {{ url('/register/verify/' . $user['confirmation_code']) }} ">Click here to confirm</a>
</body>
</html>