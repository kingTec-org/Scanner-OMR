<section id="content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><?php echo @$breadcrumb['heading']; ?> </h2>
            </div>

            <div class="card-body card-padding">
                <h4 id="success" style="text-align:center;"></h4>
                <form class="form-horizontal" role="form" action="<?php echo base_url('Settings/cp'); ?>" method="post" id="change_pass">
                    <div class="row">
                        <!-- <div class="col-sm-12"> -->
                            <div class="input-group fg-float">
                                <span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                                <div class="fg-line">
                                    <input type="password" class="form-control" id="u" name="old_pw">
                                    <label class="fg-label">Old Password</label>
                                </div>
                            </div>
                            <br><br>
                            <div class="input-group fg-float">
                                <span class="input-group-addon last"><i class="zmdi zmdi-key"></i></span>
                                <div class="fg-line">
                                    <input type="password" class="form-control" id="p" name="new_pw">
                                    <label class="fg-label">New Password</label>
                                </div>
                            </div>
                            <br><br>
                            <div class="input-group fg-float">
                                <span class="input-group-addon"><i class="zmdi zmdi-lock-open"></i></span>
                                <div class="fg-line">
                                    <input type="password" class="form-control" id="cp" name="confirm_pw">
                                    <label class="fg-label">Confirm Password</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm m-t-10 waves-effect">Change Password</button>
                        <!-- </div> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Jquery Validate -->
    <script src="<?php echo base_url("js/jquery.validate.min.js"); ?>"></script>  
<!-- Custom and plugin javascript -->
<script src="<?php echo base_url("js/formSerialize.js"); ?>"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $("#change_pass").postAjaxData(function(result){
      if(result === true)
      {
        $("#success").text('Password has been changed successfully.');
      }
      else {
        if(typeof result === 'object')
        {
          $.each(result,function(dom,err){
            $("#success").text(err);
          });
        }
        else
        {
          alert('something went wrong.');
        }
      }
    });
  });    
</script>
