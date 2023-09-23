
<?php


// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: frente.php");
    exit;
}
 
// Include config file
require_once "config.php";
 


/*Variables para el inicio de sesion*/
$usuario = $psswd = $confirmacion = "";
$usuario_err = $psswd_err = $confirmacion_err = "";

/* PROCESAR PARAMETROS DE LOGIN */
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    // VERIFICAR SI NOMBRE DE USUARIO ESTA VACIO
    if(empty(trim($_POST["usuario"]))){
        $usuario_err = "Ingrese su nombre de usuario.";
    } else{
        $usuario = trim($_POST["usuario"]);
    }

    /* VERIFICAR SI CONTRASENA ESTA VACIA */
    
    if(empty(trim($_POST["contrasena"]))){
        $psswd_err = "Ingrese su contrasena.";
    } else{
        $psswd = trim($_POST["contrasena"]);
    }

    // Validar credenciales
    if(empty($usuario_err) && empty($psswd_err)){
        
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            
            mysqli_stmt_bind_param($stmt, "s", $param_usuario);
            
            
            $param_usuario = $usuario;
            
            
            if(mysqli_stmt_execute($stmt)){
                
                mysqli_stmt_store_result($stmt);
                
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_psswd);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($psswd, $hashed_psswd)){
                            
                            session_start();
                            
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["usuario"] = $username;                            
                            
                            
                            header("location: frente.php");
                        } else{
                            
                            $login_err = "Nombre de usuario o contrasenia invalido.";
                        }
                    }
                } else{
                    
                    $login_err = "Nombre de usuario o contrasenia invalido.";
                }
            } else{
                echo "Ocurrio un error. Intente mas tarde";
            }

            
            mysqli_stmt_close($stmt);
        }
    }

    
    
    mysqli_close($link);

}
?>

 
<html>
<head>
    <title>Inicio de sesion</title>
    <link rel="stylesheet">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>


</head>
<body>
    <div class="wrapper">
        <h2>Iniciar sesion</h2>
        <p>Llene los campos de sesion.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nombre de usuario</label>
                <input type="text" name="usuario" class="form-control <?php echo (!empty($usuario_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $usuario; ?>">
                <span class="invalid-feedback"><?php echo $usuario_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Contrasena</label>
                <input type="password" name="contrasena" class="form-control <?php echo (!empty($psswd_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $psswd_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Iniciar sesion">
            </div>
            <p>No tienes una cuenta? <a href="registro.php">Registrarse</a>.</p>
        </form>
    </div>
</body>
</html>