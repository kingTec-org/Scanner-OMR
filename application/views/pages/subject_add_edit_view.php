<div class="row border-bottom white-bg page-heading">
  <div class="col-lg-10">
      <h2><?php echo @$breadcrumb['heading']; ?></h2>
      <ol class="breadcrumb">
          <?php
          if(isset($breadcrumb['route']))
          { 
              foreach ($breadcrumb['route'] as $route)
              {
                  if(is_array($route))
                  {
                      echo "<li><a href=".base_url($route['path']).">".$route['title']."</a></li>";
                  }
                  else
                  {
                      echo "<li class='active'><strong>".$route."</strong></li>";
                  }
              }
          }
          ?>
          </li>
      </ol>
  </div>
  <div class="col-lg-2">
  </div>
</div>
<div class="row wrapper wrapper-content animated fadeInRight">
<div class="ibox-content">
<div class="page-content">
            <div class="wrap">
              <h4 id="success" style="text-align:center;"></h4>
                      
                    <form class="form-horizontal" role="form" action="<?php echo base_url('Subject/add'); ?>" method="post" id="subject_add">

                        <input type="hidden" name="ID" value="<?php echo @$View[0]['ID'];?>">
                         <div class="form-group">
                              <label  class="col-sm-2 control-label">Name : </label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="Name" name="title" value="<?php echo @$View[0]['title']; ?>" required>
                              </div>
                                <span id="name"></span>
                          </div>

                          <div class="form-group">
                              <label for="Business_name" class="col-sm-2 control-label">Description : </label>
                              <div class="col-sm-9">
                                <textarea type="text" class="form-control" name="description" placeholder="Description" rows="5"><?php echo @$View[0]['description']; ?></textarea>
                              </div>
                                <span id="Description"></span>
                          </div>

                  </div>
                           
                          <div class="form_footer">
                          <div class="row">
                              <div class="col-md-6 text-center col-md-offset-3 ">
                                        <button type="submit" class="btn btn-primary"><?php echo isset($What) ? 'Update' : 'Add'; ?></button>
                                    </div>
                              </div>

                            </form> 
                        

            </div>
        </div>
    </div>
  </div>

<!-- Custom and plugin javascript -->
<script src="<?php echo base_url("js/formSerialize.js"); ?>"></script>
<!-- Jquery Validate -->
<script src="<?php echo base_url("js/plugins/validate/jquery.validate.min.js"); ?>"></script>

<script type="text/javascript">
  $.validator.setDefaults({ ignore: ":hidden:not(select)" });
  $(".chosen-select").chosen();
  $(document).ready(function() {
    $("#subject_add").postAjaxData(function(result){
      if(result === 1)
      {
        var type = "<?php echo isset($What) ? 'Updated' : 'Added'; ?>";
        bootbox.alert({
            message: '<div class="text-success">Successfully '+type+'.</div>',
            size: 'small',
            backdrop:true,
            closeButton:false,
            callback:function(){
             location.reload();
            }
        });
      }
      else
      {
        bootbox.alert({
            message: '<div class="text-danger">'+result+'</div>',
            size: 'small',
            backdrop: true,
            closeButton:false
        });
      }
    });
    $("#subject_add").validate();
  });    
</script>