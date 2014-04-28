<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script src="http://malsup.github.com/jquery.form.js"></script> 
        <style>
            .campo label {
                display: inline-block;
                width: 10%;
            }
            body {
                margin: 2% 15%;
            }
        </style>
        <script>
            $(function(){
                options = {
                    success: function(a){;
                        if (a == 'TRUE'){
                            window.location.replace("View/home");
                        }
                        else{
                            alert(a);
                        }
                    }
                }
                $('#login').ajaxForm(options);
            })
        </script>
        <title>Login</title>
    </head>
    <body>
        <form id="login" method="POST" action="Controller/usuario" style="text-align: center">
        
            <div class="campo">
                <label for="nuip">Usuario</label>
                <input type="text" name="nuip" id="nuip" required placeholder="Identificación">
            </div>
            <div class="campo">
                <label for="nuip">Contraseña</label>
                <input type="password" name="psw" id="psw" required placeholder="Clave Secreta">
            </div>
            <div class="campo">
                <input type="submit" value="Ingresar">
            </div>
        </form>
    </body>
</html>
