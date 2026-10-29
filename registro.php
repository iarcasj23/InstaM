<?php
session_start();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$errores   = [];
$nombre    = '';
$apellidos = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $token     = $_POST['csrf'] ?? '';

    if (!hash_equals($_SESSION['csrf'], $token)) {
        $errores['general'] = 'La sesión del formulario ha caducado. Inténtalo de nuevo.';
    }

    $patronNombre = '/^[\p{L}][\p{L}\s\'-]{1,49}$/u';

    if ($nombre === '') {
        $errores['nombre'] = 'Escribe tu nombre.';
    } elseif (!preg_match($patronNombre, $nombre)) {
        $errores['nombre'] = 'El nombre solo puede tener letras (2 a 50 caracteres).';
    }

    if ($apellidos === '') {
        $errores['apellidos'] = 'Escribe tus apellidos.';
    } elseif (!preg_match($patronNombre, $apellidos)) {
        $errores['apellidos'] = 'Los apellidos solo pueden tener letras (2 a 50 caracteres).';
    }

    if ($password === '') {
        $errores['password'] = 'Escribe una contraseña.';
    } elseif (strlen($password) < 8) {
        $errores['password'] = 'Usa al menos 8 caracteres.';
    }

    if ($password2 === '') {
        $errores['password2'] = 'Repite la contraseña.';
    } elseif ($password !== $password2) {
        $errores['password2'] = 'Las contraseñas no coinciden.';
    }

    if (!$errores) {
        // TODO: guardar en la base de datos con una consulta preparada
        // $hash = password_hash($password, PASSWORD_DEFAULT);
        // $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, apellidos, password) VALUES (?, ?, ?)');
        // $stmt->execute([$nombre, $apellidos, $hash]);
        header('Location: login.php?registro=ok');
        exit;
    }
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function campo(string $id, string $etiqueta, string $tipo, string $valor, array $errores, string $auto): void {
    $clase = isset($errores[$id]) ? 'campo campo--error' : 'campo';
    echo '<div class="' . $clase . '">';
    echo '<label for="' . $id . '">' . $etiqueta . '</label>';
    echo '<input type="' . $tipo . '" id="' . $id . '" name="' . $id . '" value="' . e($valor) . '" autocomplete="' . $auto . '">';
    if (isset($errores[$id])) {
        echo '<p class="error">' . e($errores[$id]) . '</p>';
    }
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Regístrate - InstaMessage</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header class="cabecera">
    <img src="img/Logo_completo_InstaMessage.png" alt="InstaMessage">
  </header>

  <main class="contenedor">
    <section class="tarjeta">
      <h1>Registrate</h1>

      <?php if (isset($errores['general'])): ?>
        <p class="aviso aviso--error"><?= e($errores['general']) ?></p>
      <?php endif; ?>

      <form method="post" action="registro.php" novalidate>
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

        <?php
        campo('nombre', 'Nombre', 'text', $nombre, $errores, 'given-name');
        campo('apellidos', 'Apellidos', 'text', $apellidos, $errores, 'family-name');
        campo('password', 'Contraseña', 'password', '', $errores, 'new-password');
        campo('password2', 'Repetir Contraseña', 'password', '', $errores, 'new-password');
        ?>

        <button type="submit" class="boton">Crear Cuenta</button>
      </form>

      <p class="pie">Ya tienes cuenta <a href="login.php">inicia sesion</a></p>
    </section>
  </main>
</body>
</html>