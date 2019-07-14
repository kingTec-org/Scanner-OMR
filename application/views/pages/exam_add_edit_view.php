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
    <form id="exam_add" action="<?php echo base_url('exam/step1_add'); ?>" class="" method="POST">
      <fieldset>
          <div class="form-group">
              <div class="row">
                  <div class="col-lg-4">
                    <label>Exam Type </label>
                    <select id="exam_type" name="exam_type" class="chosen-select form-control required" onchange="on_exam_change()">
                      <option value="1">IIT - JEE</option>
                      <option value="2">AIEEE</option>
                      <option value="3">PMT</option>
                      <option value="4">Olympiad</option>
                      <option value="5">Talent Search</option>
                    </select>
                  </div>
                  <div class="col-lg-4">
                    <label>Exam Title</label>
                    <input type="text" id="title" name="title" class="form-control required" placeholder="Exam Title">
                  </div>
                  <div class="col-lg-2 hidden">
                    <label>Paper Size </label>
                    <select id="paper_size" name="paper_size" class="form-control required">
                      <option value="A4">A4</option>
                      <option value="Legal">Legal</option>
                    </select>
                  </div>
                  <div class="col-lg-4">
                    <label>Date Of Exam </label>
                    <input type="date" id="date" name="date" class="form-control required">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-8 form-group">
                    <label>Subject </label>
                    <div class="h5" id="subject_div">
                      <i class="fa fa-spin fa-spinner"></i> Loading..
                    </div>
                    <select id="subject" name="subjects[]" class="hidden form-control required" multiple>
                    </select>
                  </div>
                  <div class="col-lg-4">
                    <label>Batch </label>
                    <div class="h5" id="batch_div">
                      <i class="fa fa-spin fa-spinner"></i> Loading..
                    </div>
                    <select id="batch" name="batch" class="hidden form-control required">
                      <option value=""></option>
                    </select>
                  </div>
              </div>
          </div>
          <div class="form-group">
              <div class="row">
                  <div class="col-lg-12">
                    <label id="section_title">Sections </label>
                    <div class=" table-responsive">
                      <table class="table table-bordered" width="100%">
                        <tr>
                          <th class="question_td">Section</th>
                          <th>Total Questions</th>
                          <th class="question_td">Question Type</th>
                          <th>Marking Scheme</th>
                          <th class="question_td">Action</th>
                        </tr>
                        <tbody id="sections">
                        <tr id="tr-1"><td class="question_td"><input type="text" name="section-1" id="section-1" class="form-control" placeholder="Section Title" required></td><td><input type="number" name="no_of_qn-1" id="no_of_qn-1" class="form-control" placeholder="No. of Questions" required></td><td class="question_td"><select name="qn_type-1" id="qn_type-1" onchange="get_qn_type_value(1)" class="form-control" required><option value="single">Single</option><option value="multiple">Multiple</option><option value="numeric">Numeric</option><option value="matrix">Matrix</option></select><label>Length/Dimension/Digits</label><div class="" id="qn_type_value_div-1"><input type="number" name="length-1" id="length-1" class="form-control" value="4" min="1" required></div></td><td><label>Correct Marks</label><input type="number" name="max-1" id="max-1" class="form-control" value="4" required><label>Leave Marks</label><input type="number" name="leave-1" id="leave-1" class="form-control" value="0" required><label>Incorrect Marks</label><input type="number" name="min-1" id="min-1" class="form-control" value="-1" required></td><td class="question_td"></td></tr>
                        </tbody>
                        <tfoot class="question_td">
                          <tr>
                            <input type="hidden" id="count" value="1">
                            <td colspan="4"></td>
                            <td colspan="1"><button type="button" class="btn btn-block btn-info" onclick="add_section()"> <i class="fa fa-plus"></i> Add Sections </button></td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
              </div>
          </div>
          <div class="form-group">
              <div class="row">
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary btn-block"> Generate Exam <i class="fa fa-arrow-right"></i></button>
                  </div>
              </div>
          </div>

      </fieldset>
    </form>
  </div>
</div>
<!-- Custom and plugin javascript -->
<script src="<?php echo base_url("js/formSerialize.js"); ?>"></script>
<!-- Chosen -->
<script src="<?php echo base_url("js/plugins/chosen/chosen.jquery.js"); ?>"></script>
<!-- Jquery Validate -->
<script src="<?php echo base_url("js/plugins/validate/jquery.validate.min.js"); ?>"></script>
<!-- Bootbox -->
<script src="<?php echo base_url('js/bootbox.min.js'); ?>"></script>

<script type="text/javascript">
  $(document).ready(function(){
    
    $.ajax({
      type:'JSON',
      url:base_url+'batch/get_show_data/yes',
      error:function(err){
        bootbox.alert('Something went wrong : Batch List');
      },
      success:function(res){
        var res = JSON.parse(res);
        if(typeof res == 'object')
        {
          var data = '<option value="">Select Batch...</option>';
          $.each(res,function(k,v){
            data += '<option value="'+v.ID+'">'+v.title+'</option>';
          });
          $('#batch').html(data).removeClass('hidden').addClass('chosen-select').chosen();
          $('#batch_div').addClass('hidden');
        }
        else{
          bootbox.alert('Something went wrong : Batch List');
        }
      }
    });

    $.ajax({
      type:'JSON',
      url:base_url+'subject/get_show_data/yes',
      error:function(err){
        bootbox.alert('Something went wrong : Subject List');
      },
      success:function(res){
        var res = JSON.parse(res);
        if(typeof res == 'object')
        {
          var data = '';
          $.each(res,function(k,v){
            data += '<option value="'+v.ID+'">'+v.title+'</option>';
          });
          $('#subject').html(data).removeClass('hidden').addClass('chosen-select').chosen();
          $('#subject_div').addClass('hidden');
        }
        else{
          bootbox.alert('Something went wrong : Subject List');
        }
      }
    });

    $.validator.setDefaults({ ignore: ":hidden:not(select)" });
    $('.chosen-select').chosen();
    $("#exam_add").postAjaxData(function(result){
      if(result === true)
      {
        var type = "<?php echo isset($What) ? 'Updated' : 'Added'; ?>";
        bootbox.alert({
            message: '<div class="text-success">Successfully '+type+'.</div>',
            size: 'small',
            backdrop:true,
            closeButton:false,
            callback:function(){
             window.location.href = base_url+'exam/show';
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
    $("#exam_add").validate();
  });

  function add_section()
  {
    var c = $('#count').val();
    ++c;
    $('#sections').append('<tr id="tr-'+(c)+'"><td><input type="text" name="section-'+c+'" id="section-'+c+'" class="form-control" placeholder="Section Title" required></td><td><input type="number" name="no_of_qn-'+c+'" id="no_of_qn-'+c+'" class="form-control" placeholder="No. of Questions" required></td><td><select name="qn_type-'+c+'" id="qn_type-'+c+'" onchange="get_qn_type_value('+c+')" class="form-control" required><option value="single">Single</option><option value="multiple">Multiple</option><option value="numeric">Numeric</option><option value="matrix">Matrix</option></select><label>Length/Dimension/Digits</label><div class="" id="qn_type_value_div-'+c+'"><input type="number" name="length-'+c+'" id="length-'+c+'" class="form-control" value="4" min="1" required></div></td><td><label>Correct Marks</label><input type="number" name="max-'+c+'" id="max-'+c+'" class="form-control" value="4" required><label>Leave Marks</label><input type="number" name="leave-'+c+'" id="leave-'+c+'" class="form-control" value="0" required><label>Incorrect Marks</label><input type="number" name="min-'+c+'" id="min-'+c+'" class="form-control" value="-1" required></td><td><button type="button" class="btn btn-danger btn-block" onclick="remove_section('+c+')"><i class="fa fa-remove"></i> Remove</button></td></tr>');
    $('#count').val(c);
  }

  function get_qn_type_value(n)
  {
    if($('#qn_type-'+n).val() == 'matrix')
    {
      $('#qn_type_value_div-'+n).html('<input type="number" name="length-'+n+'[]" id="length-'+n+'-1" class="form-control" value="4" min="1" required><input type="number" name="length-'+n+'[]" id="length-'+n+'-2" class="form-control" value="4" min="1" required>');
    }
    else{
      $('#qn_type_value_div-'+n).html('<input type="number" name="length-'+n+'" id="length-'+n+'" class="form-control" value="4" min="1" required>');
    }
  }

  function remove_section(n)
  { 
    $('#tr-'+n).remove();
  }

  function on_exam_change()
  {
    var exam_type = $('#exam_type').val();
    if(exam_type != 1)
    {
      $('#section-1').val('MCQ');
      $('#section_title').text('MCQ');
      $('.question_td').addClass('hidden');
      var count = $('#count').val();
      for(var i = 2;i<=count;i++)
      {
        remove_section(i);
      }
    }
    else{
      $('#section-1').val('MCQ');
      $('#section_title').text('Sections');
      $('.question_td').removeClass('hidden');
    }
  }

</script>