<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Paathshala OMR</title>
        <link href="<?php echo base_url("css/bootstrap.min.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("font-awesome/css/font-awesome.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/animate.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/plugins/codemirror/codemirror.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/plugins/codemirror/ambiance.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/plugins/dataTables/jquery.dataTables.min.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/plugins/dataTables/responsive.dataTables.min.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/plugins/datapicker/datepicker3.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/plugins/daterangepicker/daterangepicker-bs3.css"); ?>" rel="stylesheet">

        <!-- Sweet Alert -->
        <link href="<?php echo base_url('css/plugins/sweetalert/sweetalert.css'); ?>" rel="stylesheet">
        <!-- croper -->
        <link href="<?php echo base_url('css/plugins/cropper/cropper.min.css'); ?>" rel="stylesheet">
        <!-- summer note -->
        <link href="<?php echo base_url('css/plugins/summernote/summernote.css');?>" rel="stylesheet">
        <link href="<?php echo base_url('css/plugins/summernote/summernote-bs3.css');?>" rel="stylesheet"> 
        <link href="<?php echo base_url("css/plugins/clockpicker/clockpicker.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/jquery.qtip.css"); ?>" rel="stylesheet">
        <!-- Mainly scripts -->
        <script src="<?php echo base_url("js/jquery-2.1.1.js"); ?>"></script>
        <!-- Jquery Validate 
        <script src="<?php //echo base_url("js/plugins/validate/jquery.validate.min.js"); ?>"></script>-->
        <script src="<?php echo base_url("js/plugins/fullcalendar/moment.min.js"); ?>"></script>
        <link href="<?php echo base_url("css/plugins/fullcalendar/fullcalendar.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/plugins/fullcalendar/fullcalendar.print.css"); ?>" rel='stylesheet' media='print'>
        <script src="<?php echo base_url("js/plugins/fullcalendar/fullcalendar.min.js"); ?>"></script>
         <script src="<?php echo base_url("js/jquery.qtip.js"); ?>"></script>
        <!-- Chosen -->
        <script src="<?php echo base_url("js/plugins/chosen/chosen.jquery.js"); ?>"></script>
       
        <link href="<?php echo base_url("js/plugins/gritter/jquery.gritter.css"); ?>" rel="stylesheet">
        <!-- Chosen -->
        <link href="<?php echo base_url("css/plugins/chosen/chosen.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/style.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("css/d.css"); ?>" rel="stylesheet">
        <script type="text/javascript">var base_url = '<?php echo base_url(); ?>';</script>
    </head>
    <body class="fixed-sidebar skin-1" >
    <div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span>
                            <img alt="image" class="img-circle" src="<?php echo base_url('img/logo.jpg'); ?>" onerror="if (this.src != 'error.jpg') this.src = '<?php echo base_url('impImg/main.jpg'); ?>';"/ >
                         </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <strong class="font-bold">
                                        <?php echo $Login['Name']; ?>
                                    </strong>
                                </span>
                                <span class="text-muted text-xs block">
                                    <?php echo $this->session->userdata('Login_as'); ?>
                                </span>
                            </span> 
                        </a>
                    </div>
                    <div class="logo-element">
                        <i class="fa fa-user padding-right5"></i>
                    </div>
                </li>
                <li class="<?php echo (stripos(current_url(),base_url('Dashboard')) !== FALSE) ? 'active fade in' : ''; ?>">
                    <a href="<?php echo base_url('Dashboard'); ?>"><i class="fa fa-th-large"></i> Dashboard</a>
                </li>
                <li class="<?php echo (stripos(current_url(),base_url('Subject')) !== FALSE) ? 'active fade in' : ''; ?>">
                    <a href="<?php echo base_url('Subject'); ?>"><i class="fa fa-book"></i> Subjects</a>
                </li>
                <li class="<?php echo (stripos(current_url(),base_url('Chapter')) !== FALSE) ? 'active fade in' : ''; ?>">
                    <a href="<?php echo base_url('Chapter'); ?>"><i class="fa fa-book"></i> Chapters</a>
                </li>
                <li class="<?php echo (stripos(current_url(),base_url('Batch')) !== FALSE) ? 'active fade in' : ''; ?>">
                    <a href="<?php echo base_url('Batch'); ?>"><i class="fa fa-users"></i> Batch</a>
                </li>
                <li class="<?php echo (stripos(current_url(),base_url('Student')) !== FALSE) ? 'active fade in' : ''; ?>">
                    <a href="<?php echo base_url('Student'); ?>"><i class="fa fa-graduation-cap"></i> Student</a>
                </li>
                <li class="<?php echo (stripos(current_url(),base_url('Exam')) !== FALSE) ? 'active fade in' : ''; ?>">
                    <a href="#"><i class="fa fa-question-circle"></i> Examination
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li class="<?php echo (stripos(current_url(),base_url('Exam/step1_add')) !== FALSE) ? 'active fade in' : ''; ?>">
                            <a href="<?php echo base_url('Exam/step1_add'); ?>"><i class="fa fa-plus-square"></i> Create Exam </a>
                        </li>
                        <li class="<?php echo (stripos(current_url(),base_url('Exam/show')) !== FALSE) ? 'active fade in' : ''; ?>">
                            <a href="<?php echo base_url('Exam/show'); ?>"><i class="fa fa-eye"></i> View All Exams</a>
                        </li>
                    </ul>
                </li>
                <li class="<?php echo (stripos(current_url(),base_url('Report')) !== FALSE) ? 'active fade in' : ''; ?>">
                    <a href="#"><i class="fa fa-line-chart"></i> Reports
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li class="<?php echo (stripos(current_url(),base_url('Report/subjectwise')) !== FALSE) ? 'active fade in' : ''; ?>">
                            <a href="<?php echo base_url('Report/subjectwise'); ?>"><i class="fa fa-plus-square"></i> Subject Wise </a>
                        </li>
                        <li class="<?php echo (stripos(current_url(),base_url('Report/chapterwise')) !== FALSE) ? 'active fade in' : ''; ?>">
                            <a href="<?php echo base_url('Report/chapterwise'); ?>"><i class="fa fa-plus-square"></i> Chapter Wise </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i>
                    </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message">Welcome to Paathshala OMR</span>
                    </li>
                    
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                           <span class="text-xs block"><i class="fa fa-gear"></i>Options<b class="caret"></b></span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li>
                                <a href="<?php echo base_url('Settings/change_password'); ?>"><i class="fa fa-key padding-right5"></i> Change Password</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo base_url('Login/logout'); ?>"><i class="fa fa-sign-out padding-right5"></i> Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>

    <script type="text/javascript">
        $(document).ready(function() {
            Login_as='<?php echo $this->session->userdata('Login_as');?>';
            Login_Employee='<?php echo $this->session->userdata('employeeID');?>';
            $('#actModal').on('hidden.bs.modal', function (e) {
              $(this)
                .find("input,textarea,select")
                   .val('')
                   .end();
                   $('#modalDate').datepicker('remove');
            });

            $('select').on('change', function() {
              $('#meet_type').text('');
              $('#lead_ID').text('');
              $('#DateTime').text('');
              $('#time').text('');
            });
            $('#A<?php echo md5(current_url()); ?>').addClass('active');
            $("#A<?php echo md5(current_url()); ?>").parent().parent().addClass("active");
            $("#A<?php echo md5(current_url()); ?>").parent().addClass("in");
        });

        function showBtn() {
            $('#actBtn').removeAttr('onclick').attr('onclick','removeBtn()');
            $('#actall').prepend('<br><div id="clientAct" ><button class="btn btn-lg btn-warning btn-circle" type="button" data-toggle="tooltip" title="Add Activity" id="dCheck" onclick="clientModal()"><i class="fa fa-user-plus"></i></button></div><br>');
            $('[data-toggle="tooltip"]').tooltip();
            if(window.location.href.indexOf("Customer/view/") > -1 || window.location.href.indexOf("lead/view/") > -1) {
                $('#dCheck').removeAttr('onclick').attr('onclick','showActivity()');
            }
        }
        function removeBtn() {
            $('#actBtn').removeAttr('onclick').attr('onclick','showBtn()');
             $('#simpleAct').fadeOut(500,function() {
                 $('#simpleAct').remove();
             });
             $('#clientAct').fadeOut(1000,function() {
                  $('#clientAct').remove();
                  $('#actall').find('br').remove();
             })
        }

        function simpleModal() {
            $('#ttlModal').html('');
            $('#ttlModal').append('<div class="col-sm-12"><label>Title</label> <input type="text" name="title" class="form-control" placeholder="Title"><input type="hidden" value="simple" name="type"><span id="title" class="text-danger"></span></div>');
            $('#persn').attr('hidden',true);
            $('#mt').attr('hidden',true);
            $('#actModal').modal('show');
            var dateToday = new Date();
            $('#modalDate').datepicker({ startDate: dateToday
            });
            $('.modalTime').datetimepicker({
                    format: 'LT'
                });
            $('#isCont1').val('No');
            $('#cntResult1').val('NeedToContact');
        }

        function clientModal() {
            $('#ttlModal').html('<input type="hidden" value="withClient" name="type">');
            $('#persn').removeAttr('hidden');
            $('#mt').removeAttr('hidden');
            $('#actModal').modal('show');
            showPersons();
            showMeet();
            $('#modalDate').datepicker();
            $('.modalTime').datetimepicker({
                    format: 'LT'
                });
            $('#isCont1').val('No');
            $('#cntResult1').val('NeedToContact');
        }

        function showPersons() {
            $.ajax({
              type: 'POST',
              url: '<?php echo base_url(); ?>'+'bank/getPersons/',
              dataType: 'json',
              success:function(data)
              {
                $.each(data,function(k,v)
                {
                    $('#persns').append('<option value="'+v.ID+'">'+v.mainName+'</option>');
                    $('.persnsOntask').append('<option value="'+v.ID+'">'+v.mainName+'</option>');
                });
              $(".chosen-select").trigger('chosen:updated');
              $(".chosen-select").chosen();
               $("#persns_chosen").css('width','100%');
              }
            });
        }

        function showMeet() {
             $('.meetTypeModalonTask').html('');
            $.ajax({
              type: 'POST',
              url: '<?php echo base_url(); ?>'+'bank/getMeet/',
              dataType: 'json',
              success:function(data)
              {
                $.each(data,function(k,v)
                {
                    $('#meetTypeModal').append('<option value="'+v.ID+'">'+v.name+'</option>');
                    $('.meetTypeModalonTask').append('<option value="'+v.ID+'">'+v.name+'</option>');
                });
              $(".chosen-select").trigger('chosen:updated');
              $(".chosen-select").chosen();
              $("#meetTypeModal_chosen").css('width','100%');
              }
            });
        }

    </script>

        <?php echo $this->session->flashdata('itemUpdate');?>
        <div id="errorShow"></div>