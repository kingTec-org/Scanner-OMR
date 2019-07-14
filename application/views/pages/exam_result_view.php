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
  <div class="ibox-content" id="result">
    <div class="text-center"><i class="fa fa-spinner fa-pulse fa-2x pull-center"></i> <span class="h4">Loading Data ...</span></div>
  </div>
</div>
<!-- Moment -->
<script src="<?php echo base_url('js/moment.min.js'); ?>"></script>

<script type="text/javascript">
  $(document).ready(function(){
    var id = '<?php echo $this->uri->segment(3); ?>';
    exam_type = {'1':'IIT JEE','2':'AIEEE','3':'PMT','4':'OLYMPIAD','5':'TALENT SEARCH'};
    $.ajax({
      url:base_url+'exam/get_exam_data/'+id+'/RESULT',
      error:function(err){
        $('#result').html('<div class="alert alert-danger h4">Something Went Wrong.</div>');
      },
      success:function(res){
        var res = JSON.parse(res);
        if(typeof res == 'object')
        {
          console.log(res);
          var split = (res.subjects).split(',');
          var data = '';
          var easy = medium = hard = 0;

          $.each(res.questions,function(qk,qv){
            if(typeof (qv) == 'object')
            {
              if(qv.level == 'E')
              {
                ++easy;
              }
              else if(qv.level == 'M'){
                ++medium;
              }
              else{
                ++hard;
              }
            }
          });

          data += '<table class="table table-bordered" width="100%" cellspacing="0" cellpadding="0" ><thead><tr><th colspan="4" class="h3">'+exam_type[res.exam_type]+'</th></tr></thead><tr><th>Exam  </th><td colspan="3">'+res.title+'</td></tr><tr><th>Date  </th><td colspan="3">'+moment(res.date,'YYYY-MM-DD').format('DD/MM/YYYY')+'</td></tr><tr><th>Batch  </th><td>'+res.batch+'</td><th>Subjects  </th><td>'+res.subjects+'</td></tr><tr><th>Total No. of Questions  </th><td>'+res.questions.total+' Qns <b>(Easy : '+easy+', Medium : '+medium+', Hard : '+hard+')<b></td><th>Maximum Marks  </th><td>'+res.questions.marks+'</td></tr></table>';
          
          data += '<table class="table table-bordered" width="100%" cellspacing="0" cellpadding="0" ><thead><tr><th>Roll No.</th><th>Name</th><th>Question Attempted</th><th>Correct Answers</th><th>Incorrect Answers</th><th>Easy</th><th>Medium</th><th>Hard</th><th>Marks Obtained</th></tr></thead><tbody>';
          $.each(res.student_record,function(k,student){
            data += '<tr><td>'+student.roll_no+'</td><td><a onclick="window.open(\''+base_url+'student/scorecard/'+student.ID+'/'+res.ID+'\',\'_blank\',\'\')">'+student.name+'</a></td><td>'+student.qn_attempted+'</td><td>'+student.qn_correct+'</td><td>'+student.qn_incorrect+'</td><td>'+student.qn_easy+'</td><td>'+student.qn_medium+'</td><td>'+student.qn_hard+'</td><td>'+student.marks_obtained+'</td></tr>';            
          });
          data += '</tbody><tfoot><tr><th colspan="2">Overall Average</th><th>'+res.avg_attempted+'/'+res.questions.total+'</th><th>'+res.avg_correct+'/'+res.questions.total+'</th><th>'+res.avg_incorrect+'/'+res.questions.total+'</th><th>'+res.avg_easy+'/'+easy+'</th><th>'+res.avg_medium+'/'+medium+'</th><th>'+res.avg_hard+'/'+hard+'</th><th>'+res.avg_marks_obtained+'/'+res.questions.marks+'</th></tr></tfoot></table>';
          $('#result').html(data);//+'<pre>'+JSON.stringify(res, null, "\t")+'</pre>');
        }
        else{
          $('#result').html('<div class="alert alert-danger h4">No Data Found.</div>');
        }
      }
    });
  });

</script>