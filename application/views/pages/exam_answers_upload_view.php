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
    <form id="sheet_upload" action="#" class="table-responsive" method="POST">
      <div class="text-center"><i class="fa fa-spinner fa-pulse fa-2x pull-center"></i> <span class="h4">Loading Data ...</span></div>
    </form>
  </div>
</div>
<!-- Jquery Validate -->
<script src="<?php echo base_url("js/plugins/validate/jquery.validate.min.js"); ?>"></script>
<!-- Moment -->
<script src="<?php echo base_url('js/moment.min.js'); ?>"></script>

<script type="text/javascript">
  $(document).ready(function(){
    var id = '<?php echo $this->uri->segment(3); ?>';
    exam_type = {'1':'IIT JEE','2':'AIEEE','3':'PMT','4':'OLYMPIAD','5':'TALENT SEARCH'};
    $.ajax({
      url:base_url+'exam/get_exam_data/'+id+'/ONLY',
      error:function(err){
        $('#sheet_upload').html('<div class="alert alert-danger h4">Something Went Wrong.</div>');
      },
      success:function(res){
        var res = JSON.parse(res);
        if(typeof res == 'object')
        {
          console.log(res);
          var split = (res.subjects).split(',');
          var data = '';
          data += '<table class="table table-bordered" width="100%" cellspacing="0" cellpadding="0" >';
          data += '<thead><tr><th colspan="2" class="h3">'+exam_type[res.exam_type]+'</th></tr></thead><tr><th>Exam  </th><td>'+res.title+'</td></tr><tr><th>Date  </th><td>'+moment(res.date,'YYYY-MM-DD').format('DD/MM/YYYY')+'</td></tr><tr><th>Batch  </th><td>'+res.batch+'</td></tr><tr><th>Subjects  </th><td>'+res.subjects+'</td></tr>';
          var i = 1;
          data += '<tr>';
          data += '<th>Upload PDF </th><td><input type="hidden" name="id" value="'+id+'"> <input type="file" class="form-control" name="userfile" size="1" required></td>';
          data += '</tr><tr><td colspan="2"><div class="progress" id="steps"></div></td></tr>';
          data += '</table>';
          data += '<br><button id="upload_btn" class="btn btn-block btn-success" type="button" onclick="sheet_upload_step_1()"> <i class="fa fa-upload"></i> Upload OMR Sheet</button>'
          $('#sheet_upload').html(data);//+'<pre>'+JSON.stringify(res, null, "\t")+'</pre>');
          $('.chosen-select').chosen({width:'100%'});
        }
        else{
          $('#sheet_upload').html('<div class="alert alert-danger h4">No Data Found.</div>');
        }
      }
    });

    $("#sheet_upload").on('submit',function(e){
      e.preventDefault();
      var f = $("#sheet_upload").valid();
      console.log(f);
      if(f)
      {
        $('#upload_btn').removeAttr('onclick').addClass('disabled');
        $('#steps').html('<div id="step1" class="progress-bar progress-bar-striped active" role="progressbar" style="width:20%">Uploading PDF</div>');
        $.ajax({
          url:base_url+'exam/sheet_upload_step/1', // Url to which the request is send
          type: "POST",             // Type of request to be send, called as method
          data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
          contentType: false,       // The content type used when sending data to the server.
          cache: false,             // To unable request pages to be cached
          processData:false,        // To send DOMDocument or non processed data file it is set to false
          error: function(jqXHR, exception) {
            $('#upload_btn').attr('onclick','sheet_upload_step_1()').removeClass('disabled');
            $('#steps').html('<div id="step1" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width:20%">Not Uploaded - Error Occured</div>');
          },
          success: function(step1_data)   // A function to be called if request succeeds
          {
            var step1_data = JSON.parse(step1_data);
            console.log(step1_data.error);
            if(step1_data.error != undefined)
            {
              $('#upload_btn').attr('onclick','sheet_upload_step_1()').removeClass('disabled');
              $('#steps').html('<div id="step1" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width:100%">'+step1_data.error+'</div>');
            }
            else{
              $('#steps').html('<div id="step1" class="progress-bar" role="progressbar" style="width:20%">PDF Uploaded</div><div id="step2" class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" style="width:20%">Converting PDF to Images</div>');
              sheet_upload_step_2(step1_data);
            }
          }
        });
      }
    });

    $("#sheet_upload").validate();
  });

  function sheet_upload_step_1()
  {
    $('#sheet_upload').submit();
  }

  function sheet_upload_step_2(step1_data)
  {
    console.log(step1_data);
    $.ajax({
      url:base_url+'exam/sheet_upload_step/2',
      type: "POST",
      data:step1_data,
      error: function(jqXHR, exception) {
        $('#upload_btn').attr('onclick','sheet_upload_step_1()').removeClass('disabled');
        $('#steps').html('<div id="step1" class="progress-bar" role="progressbar" style="width:20%">PDF Uploaded</div><div id="step2" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width:20%">Converting PDF to Images - Error</div>');
      },
      success: function(step2_data)
      {
        var step2_data = JSON.parse(step2_data);
        console.log(step2_data.error);
        if(step2_data.error != undefined)
        {
          $('#upload_btn').attr('onclick','sheet_upload_step_1()').removeClass('disabled');
          $('#steps').html('<div id="step1" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width:100%">'+step2_data.error+'</div>');
        }
        else{
          $('#steps').html('<div id="step1" class="progress-bar" role="progressbar" style="width:20%">PDF Uploaded</div><div id="step2" class="progress-bar progress-bar-warning" role="progressbar" style="width:20%">PDF Converted to Images</div><div id="step3" class="progress-bar progress-bar-info progress-bar-striped active " role="progressbar" style="width:20%">OMR Starts</div>');
          sheet_upload_step_3(step2_data);
        }
      }
    });
  }

  function sheet_upload_step_3(step2_data)
  {
    console.log(step2_data);
    $.ajax({
      url:base_url+'exam/sheet_upload_step/3',
      type: "POST",
      data:step2_data,
      error: function(jqXHR, exception) {
        $('#upload_btn').attr('onclick','sheet_upload_step_1()').removeClass('disabled');
        $('#steps').html('<div id="step1" class="progress-bar" role="progressbar" style="width:20%">PDF Uploaded</div><div id="step2" class="progress-bar progress-bar-warning" role="progressbar" style="width:20%">PDF Converted to Images</div><div id="step2" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width:20%">OMR Starts - Error Occured</div>');
      },
      success: function(step3_data)
      {
        var step3_data = JSON.parse(step3_data);
        console.log(step3_data.error);
        if(step3_data.error != undefined)
        {
          $('#upload_btn').attr('onclick','sheet_upload_step_1()').removeClass('disabled');
          $('#steps').html('<div id="step1" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width:100%">'+step3_data.error+'</div>');
        }
        else{
          $('#steps').html('<div id="step1" class="progress-bar" role="progressbar" style="width:20%">PDF Uploaded</div><div id="step2" class="progress-bar progress-bar-warning" role="progressbar" style="width:20%">PDF Converted to Images</div><div id="step3" class="progress-bar progress-bar-info" role="progressbar" style="width:20%">OMR Completed</div><div id="step4" class="progress-bar progress-bar-success progress-bar-striped active " role="progressbar" style="width:20%">Recognizing Answers</div>');
          sheet_upload_step_4(step3_data);
        }
      }
    });
  }

</script>