<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="en-us">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Paathshala OMR</title>
        <link href="<?php echo base_url('css/bootstrap.min.css'); ?>" rel="stylesheet" />
        <link href="<?php echo base_url("css/animate.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url('css/button.css'); ?>" rel="stylesheet" />
        <link href="<?php echo base_url('css/locked_screen.css'); ?>" rel="stylesheet" />
        <link href="<?php echo base_url("css/style.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("font-awesome/css/font-awesome.css"); ?>" rel="stylesheet">
    </head>
    <body class="skin-1">
        <div class="container">
            <div class="row login-screen">
                <div class="col-md-4 col-md-offset-4 text-center lock_box">
                	<img src="<?php echo base_url('img/logo_white.png'); ?>" class="margin-bottom30" />
                    <h1><i class="fa fa-lock"></i></h1>
                    <form id="lockForm" action="<?php echo base_url('login/process/Lock'); ?>" class="form-horizontal" method="post">
                        <div class="form-group">
                            <div class="form-control-static">
                               <h2><?php echo $name; ?></h2>
                               <p><?php echo $email; ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-key"></i></div>
                                <input type="password" class="form-control" id="p" name="password" placeholder="<?php echo $this->lang_library->translate('Password'); ?>">
                            </div>
                        </div>
                        <div id="login_error"></div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-md btn-block skYellowNormal"><i class="fa fa fa-unlock-alt padding-right5"></i> Unlock</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="<?php echo base_url('js/bootstrap.min.js'); ?>"></script>

        <!-- Jquery Validate -->

        <script src="<?php echo base_url("js/plugins/validate/jquery.validate.min.js"); ?>"></script>

        <script src="<?php echo base_url('js/formSerialize.js'); ?>"></script>

        <script type="text/javascript">

        base_url = '<?php echo base_url(); ?>';

		$(document).ready(function() {

            var auth_fail = '<?php echo $this->lang_library->translate("Authentication Failed!!"); ?>';

            //alert(auth_fail);

			$("#lockForm").postAjaxData(function(result){

                if(result === true)

                {

                    window.location.href = '<?php echo base_url($this->config->item("skyq")["default_home_page"]); ?>';

                }

                else {

                    $("#login_error").text(auth_fail);

                }

            });

		});            

        </script>

    </body>

</html>