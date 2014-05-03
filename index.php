<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script src="http://malsup.github.com/jquery.form.js"></script> 
        <link href="View/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <style>
        .boxlogin{
            margin: 50px auto;
            width: 320px;
            box-shadow: 0px 2px 10px #d6d6d6;
            border-radius: 4px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 10px;
        }
        #psw{
            border-top-left-radius: 0px !important;
            border-top-right-radius: 0px !important;
        }
        #nuip{
            border-bottom-left-radius: 0px !important;
            border-bottom-right-radius: 0px !important;
        }
        input[type="password"]{
            margin-top: -1px;
        }
        input[type="submit"]{
            margin-top: 15px;
        }
        input::-webkit-input-placeholder:before{
            font-weight: bold;
        }
        input::-webkit-input-placeholder{
            font-style: italic;
        }
        input:-moz-placeholder:before {
            font-weight: bold;
        }
        input:-moz-placeholder {
            font-style: italic;
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
        <div class="jumbotron boxlogin">
        <form id="login" method="POST" action="Controller/usuario" class="form-signin" autocomplete="off">
            <h2 class="form-signin-heading">Ingresa</h2>
            <div class="campo">
                <input type="text" name="nuip" id="nuip" class="form-control" required placeholder="Identificación">
            </div>
            <div class="campo">
                <input type="password" name="psw" id="psw" class="form-control" required placeholder="Contraseña">
            </div>
            <div class="campo">
                <input type="submit" value="Acceder" class="btn btn-lg btn-primary btn-block">
            </div>
        </form>
        </div>
    </body>
</html>
