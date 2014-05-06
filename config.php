<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <link href="View/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <script src="http://malsup.github.com/jquery.form.js"></script> 
        <script>
            $(function() {
                var options = {
                    target: '#rta',
                };

                // pass options to ajaxForm 
                $('#myForm').ajaxForm(options);
            })
        </script>
        <style>
        .boxlogin{
            margin: 50px 50px;
            width: 320px;
/*            box-shadow: 0px 2px 10px #d6d6d6;
            border-radius: 4px;*/
            -webkit-border-radius: 4px;
            -moz-border-radius: 10px;
        }
        .myTop{
            border-top-left-radius: 0px !important;
            border-top-right-radius: 0px !important;
        }
        .myBottom{
            border-bottom-left-radius: 0px !important;
            border-bottom-right-radius: 0px !important;
        }
        .medios{
            border-radius: 0px !important
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
        <title>Login</title>
    </head>
    <body>
        <div class="">
            <form name="myForm" id="myForm" method="POST" action="Controller/Admin/creaXML" class="boxlogin">
                <input type="text" name="Host" value="localhost" placeholder="HOST" class="form-control myBottom">
                <input type="text" name="user" placeholder="USUARIO" class="form-control medios">
                <input type="text" name="password" placeholder="CONTRASEÑA" class="form-control medios">
                <input type="text" name="data" placeholder="BASE DE DATOS" class="form-control myTop">
                <input type="submit" class="btn btn-success btn-primary btn-block">
            </form>
        </div>
        <div id="rta"></div>
    </body>
</html>

<html>
    <head>

    </head>
    
</html>