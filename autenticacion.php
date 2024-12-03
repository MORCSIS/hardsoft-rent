<?php
include 'connection.php';

$conexion = connection();

session_start();

// Verificar que se enviaron los datos requeridos
if (!isset($_POST['username'], $_POST['password'], $_POST['g-recaptcha-response'])) {
    header('Location: index.html');
    exit;
}

// Verificación de reCAPTCHA
$secretKey = '6LfAiSIqAAAAAFLP8XWq0qvTfELVOon6p3oykZf4';
$responseKey = $_POST['g-recaptcha-response'];
$userIP = $_SERVER['REMOTE_ADDR'];

$url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";
$response = file_get_contents($url);
$response = json_decode($response);

if (!$response->success) {
    echo "<script>alert('Por favor, verifica el CAPTCHA.'); window.location='index.html';</script>";
    exit;
}

// Prevenir inyección SQL
if ($stmt = $conexion->prepare('SELECT id, password, name, rol, email, activo, fecha_creacion, session_token FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $name, $rol, $email, $activo, $fecha_creacion, $session_token);
        $stmt->fetch();

        // Validar la contraseña con password_verify
        if (password_verify($_POST['password'], $password)) {
            // Verificar si ya existe una sesión activa
            if (!empty($session_token)) {
                echo "<script>
                    if (confirm('Ya existe una sesión activa para este usuario. ¿Desea cerrar la sesión anterior y continuar?')) {
                        window.location = 'cerrar-sesion-activa.php?username=" . urlencode($_POST['username']) . "';
                    } else {
                        window.location = 'Inicio.php';
                    }
                </script>";
                exit;
            }

            // Generar nuevo token de sesión
            $new_session_token = bin2hex(random_bytes(16));

            // Actualizar el token de sesión en la base de datos
            $update_stmt = $conexion->prepare('UPDATE accounts SET session_token = ? WHERE id = ?');
            $update_stmt->bind_param('si', $new_session_token, $id);
            $update_stmt->execute();
            $update_stmt->close();

            session_regenerate_id();
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = $name;
            $_SESSION['id'] = $id;
            $_SESSION['rol'] = $rol;
            $_SESSION['email'] = $email;
            $_SESSION['activo'] = $activo;
            $_SESSION['fecha_creacion'] = $fecha_creacion;
            $_SESSION['session_token'] = $new_session_token;

            header('Location: Inicio.php');
            exit;
        } else {
            echo "<script>alert('Error, usuario y/o contraseña incorrectos, verifique y reinténtelo'); window.location='index.html';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error, usuario y/o contraseña incorrectos, verifique y reinténtelo'); window.location='index.html';</script>";
        exit;
    }
    $stmt->close();
}
?>