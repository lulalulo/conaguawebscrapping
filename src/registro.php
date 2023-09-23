<?php 

/* Se incluye la configuracion*/
require_once "config.php" ;

/*Variables para el inicio de sesion*/
$usuario = $psswd = $confirmacion = "";
$usuario_err = $psswd_err = $confirmacion_err = "";

/* PROCESAR PARAMETROS DE LOGIN */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* VALIDAR NOMBRE DE USUARIO */
    if (empty(trim($_POST["usuario"]))) {
        $usuario_err = "Ingrese su nombre de usuario.";
    }
    elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["usuario"]))) {
        $usuario_err = "Su nombre de usuario solo puede contener letras, numeros y guiones.";
    }
    else {
        /* PREPARAR EL SELECT STATEMENT */
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            /*  */
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            /* ESTABLECER PARAMETROS */
            $param_username = trim($_POST["usuario"]);

            /* EJECUTAR EL STATEMENT DETERMINADO */
            if (mysqli_stmt_execute($stmt)) {
                /* GUARDAR RESULTADO */
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $usuario_err = "Este nombre de usuario ya esta en uso.";
                } else{
                    $usuario = trim($_POST["usuario"]);
                }
            }
            else {
                echo "Hubo un error. Por favor intente mas tarde.";
            }

            /* CERRAR STATEMENT */
            mysqli_stmt_close($stmt);
        }
    }

    if(empty(trim($_POST["contrasena"]))){
        $psswd_err = "Ingrese una contrasena";     
    } elseif(strlen(trim($_POST["contrasena"])) < 6){
        $psswd_err = "La contrasena debe contener minimo 6 caracteres.";
    } else{
        $psswd = trim($_POST["contrasena"]);
    }
    
    if(empty(trim($_POST["confirmacion"]))){
        $confirmacion_err = "Confirme la contrasena";     
    } else{
        $confirmacion = trim($_POST["confirmacion"]);
        if(empty($psswd_err) && ($psswd != $confirmacion)){
            $confirmacion_err = "Las contrasenas no coinciden.";
        }
    }
    
    if(empty($usuario_err) && empty($psswd_err) && empty($confirmacion_err)){
        
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $param_usuario, $param_psswd);
            
            $param_usuario = $usuario;
            $param_psswd = password_hash($psswd, PASSWORD_DEFAULT); 
            
            if(mysqli_stmt_execute($stmt)){
                header("location: login.php");
            } else{
                echo "Error. Intente mas tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);


}

?>

<html>
<head>
    
    <title>Sign Up</title>
    
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Registrar nueva cuenta</h2>
        <p>Ingrese informacion para crear una cuenta.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nombre de usuario</label>
                <input type="text" name="usuario" class="form-control <?php echo (!empty($usuario_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $usuario; ?>">
                <span class="invalid-feedback"><?php echo $usuario_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Contrasena</label>
                <input type="password" name="contrasena" class="form-control <?php echo (!empty($psswd_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $psswd; ?>">
                <span class="invalid-feedback"><?php echo $psswd_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirmar contrasena</label>
                <input type="password" name="confirmacion" class="form-control <?php echo (!empty($confirmacion_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirmacion; ?>">
                <span class="invalid-feedback"><?php echo $confirmacion_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Registrarse">
                <input type="reset" class="btn btn-secondary ml-2" value="Restaurar formulario">
            </div>
            <p>Ya tienes una cuenta? <a href="login.php">Inicia sesion aqui</a>.</p>
        </form>
    </div>    
</body>
</html>