<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido al Mundial 2026</title>
</head>
<body>
    <h2>¡Hola, {{ $user->name }}!</h2>
    <p>Gracias por registrarte en el sistema de predicciones del Mundial 2026.</p>
    <p>Tus credenciales de acceso son:</p>
    <ul>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Contraseña:</strong> {{ $password }}</li>
    </ul>
    <p>¡Mucha suerte en tus predicciones!</p>
</body>
</html>