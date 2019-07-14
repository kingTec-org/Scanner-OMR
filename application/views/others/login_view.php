<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="en-us">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Paathshala OMR</title>
        <link href="<?php echo base_url("css/animate.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url('css/bootstrap.min.css'); ?>" rel="stylesheet" />
        <link href="<?php echo base_url("css/style.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("font-awesome/css/font-awesome.css"); ?>" rel="stylesheet">
    </head>
    <body class="skBlueLight">
        <div class="container">
            <div class="row login-screen">
                <div class="col-md-4 col-md-offset-4 text-center">
                    <img src="<?php echo base_url('img/logo.jpg'); ?>" class="margin-bottom50" / height="200">
                    <br>
                    <br>
                    <form id="loginForm" action="<?php echo base_url('login/process'); ?>" class="form-horizontal" method="post" id="loginForm">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                <input type="text" class="form-control" id="u" name="email" placeholder="Username">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-key"></i></div>
                                <input type="password" class="form-control" id="p" name="password" placeholder="Password">
                            </div>
                        </div>
                        <div id="login_error"></div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-md btn-block skYellowNormal"><i class="fa fa-sign-in padding-right5"></i> Sign In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url('js/jquery-2.1.1.js'); ?>"></script>
        <script src="<?php echo base_url('js/bootstrap.min.js'); ?>"></script>
        <!-- Jquery Validate -->
        <script src="<?php echo base_url("js/plugins/validate/jquery.validate.min.js"); ?>"></script>
        <script src="<?php echo base_url('js/formSerialize.js'); ?>"></script>
        <script type="text/javascript">
        base_url = '<?php echo base_url(); ?>';
        $(document).ready(function() {
            $("#loginForm").postAjaxData(function(result){
                if(result === true)
                {
                    window.location.href = '<?php echo base_url("Dashboard"); ?>';
                }
                else
                {
                    $("#login_error").text("Authentication Failed!!");
                }
            });
        });            
        </script>
    </body>
</html>