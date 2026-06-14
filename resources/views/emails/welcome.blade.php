<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido al Sistema de Eventos</title>
</head>
<body>
    <h2>¡Hola, {{ $user->name }}!</h2>
    <p>Gracias por registrarte en el sistema de eventos.</p>
    <p>Tus credenciales de acceso son:</p>
    <ul>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Contraseña:</strong> {{ $password }}</li>
    </ul>
    <p>¡Mucha suerte en tus eventos!</p>
</body>
</html>