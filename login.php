<?php
session_start();
 
// Token CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
 
$errores = [];
$usuario = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $token    = $_POST['csrf'] ?? '';
 
    if (!hash_equals($_SESSION['csrf'], $token)) {
        $errores['general'] = 'La sesión del formulario ha caducado. Inténtalo de nuevo.';
    }
 
    if ($usuario === '') {
        $errores['usuario'] = 'Escribe tu nombre de usuario.';
    } elseif (mb_strlen($usuario) < 3 || mb_strlen($usuario) > 30) {
        $errores['usuario'] = 'El usuario debe tener entre 3 y 30 caracteres.';
    }
 
    if ($password === '') {
        $errores['password'] = 'Escribe tu contraseña.';
    }
 
    if (!$errores) {
        // TODO: buscar el usuario en la base de datos con una consulta preparada
        // $stmt = $pdo->prepare('SELECT id, password FROM usuarios WHERE usuario = ?');
        // $stmt->execute([$usuario]);
        // $fila = $stmt->fetch();
        // if ($fila && password_verify($password, $fila['password'])) {
        //     session_regenerate_id(true);
        //     $_SESSION['usuario_id'] = $fila['id'];
        //     header('Location: inicio.php');
        //     exit;
        // }
        $errores['general'] = 'Usuario o contraseña incorrectos.';
    }
}
 
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inicio de sesión - InstaMessage</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header class="cabecera">
    <img src="img/Logo_completo_InstaMessage.png" alt="InstaMessage">
  </header>
 
  <main class="contenedor">
    <section class="tarjeta">
      <h1>Inicio de Sesion</h1>
 
      <?php if (isset($_GET['registro']) && $_GET['registro'] === 'ok'): ?>
        <p class="aviso aviso--ok">Cuenta creada. Ya puedes iniciar sesión.</p>
      <?php endif; ?>
      <?php if (isset($errores['general'])): ?>
        <p class="aviso aviso--error"><?= e($errores['general']) ?></p>
      <?php endif; ?>
 
      <form method="post" action="login.php" novalidate>
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
 
        <div class="campo <?= isset($errores['usuario']) ? 'campo--error' : '' ?>">
          <label for="usuario">Nombre de usuario:</label>
          <input type="text" id="usuario" name="usuario" value="<?= e($usuario) ?>" autocomplete="username">
          <?php if (isset($errores['usuario'])): ?><p class="error"><?= e($errores['usuario']) ?></p><?php endif; ?>
        </div>
 
        <div class="campo <?= isset($errores['password']) ? 'campo--error' : '' ?>">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" autocomplete="current-password">
          <?php if (isset($errores['password'])): ?><p class="error"><?= e($errores['password']) ?></p><?php endif; ?>
        </div>
 
        <button type="submit" class="boton">Iniciar sesion</button>
      </form>
 
      <p class="pie">Si no tienes cuenta <a href="registro.php">registrate</a></p>
    </section>
  </main>
</body>
</html>