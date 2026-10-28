<?php 
    //Conexion a la DB
    require 'includes/config/database.php';
    $db = conectarDB();

    //Errores

    $errores = [];

    //Autentificar el usuario
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";

        $email = mysqli_real_escape_string($db, filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL));
        $password = mysqli_real_escape_string($db, $_POST['password']);

        //var_dump($email);

        if(!$email) {
            $errores[] ="El email es obliagtorio o no es válido";
        }

        if(!$password) {
            $errores[] ="El Password es obligatiro o incorrecto"; 
        }

        // echo "<pre>";
        // var_dump($errores);
        // echo "</pre>";

        if(empty($errores)) {
            
            //Revisar si el usuario existe

            $query = "SELECT * FROM usuarios WHERE email = '{$email}' ";
            $resultado = mysqli_query($db, $query);

            //var_dump($query);
            //var_dump($resultado);

            if( $resultado->num_rows) {

                //Revisar si el password es correcto

                $usuario = mysqli_fetch_assoc($resultado);
                
                //var_dump($usuario['password']);
                //Verificar si el password es correcto o no

                $auth = password_verify($password, $usuario['password']);

                //var_dump($auth);

                if($auth) {
                    //El usuario esta autentificado
                    session_start();

                    //Llenar el arreglo de la sesión
                    $_SESSION['usuario'] = $usuario;['email'];
                    $_SESSION['login'] = true;

                    // echo "<pre>";
                    // var_dump($_SESSION);
                    // echo "</pre>";

                    header('Location: /admin');


                    
                } else {
                    $errores[] = "El password es incorrecto";
                }
            } else {
                $errores[] = "El usuario no existe";

            }
        }
    }


    //Incluye el header
    require 'includes/funciones.php';
    incluirTemplate('header');
?>

    <main class="contenedor seccion contenido-centrado">
        <h1>Iniciar Sesión</h1>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>

        <?php endforeach; ?> 


        <form method="POST" class="formulario" novalidate>
            <fieldset>
                <legend>Email y Password</legend>

                <label for="email">E-mail</label>
                <input type="email" name="email" placeholder="Tu Email" id="email">

                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Tu Password" id="password">

            </fieldset>

            <input type="submit" value="Iniciar Sesión" class="boton boton-verde">

        </form>
    </main>

<?php 
    incluirTemplate('footer');
 ?>