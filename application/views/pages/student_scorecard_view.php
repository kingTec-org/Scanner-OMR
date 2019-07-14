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
  <div class="ibox-content" id="update_answer">
    <div class="text-center"><i class="fa fa-spinner fa-pulse fa-2x pull-center"></i> <span class="h4">Loading Data ...</span></div>
  </div>
</div>

<!-- Modal -->
<div id="change_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form id="update_new_answer" action="<?php echo base_url('student/update_answer'); ?>" class="table-responsive" method="POST">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Change Answer</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="student_ID" id="student_ID" value="<?php echo $this->uri->segment(3); ?>">
          <input type="hidden" name="exam_ID" id="exam_ID" value="<?php echo $this->uri->segment(4); ?>">
          <input type="hidden" name="question_ID" id="question_ID" value="">
          <input type="hidden" name="answer" id="answer" value="">
          <div class="row">
            <div class="col-sm-12" id="new_answer_div"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>

  </div>
</div>
<!-- Custom and plugin javascript -->
<script src="<?php echo base_url("js/formSerialize.js"); ?>"></script>
<!-- Jquery Validate -->
<script src="<?php echo base_url("js/plugins/validate/jquery.validate.min.js"); ?>"></script>
<!-- Chosen -->
<script src="<?php echo base_url("js/plugins/chosen/chosen.jquery.js"); ?>"></script>
<!-- Bootbox -->
<script src="<?php echo base_url('js/bootbox.min.js'); ?>"></script>
<!-- Moment -->
<script src="<?php echo base_url('js/moment.min.js'); ?>"></script>

<script type="text/javascript">
  $(document).ready(function(){
    var exam_id = '<?php echo $this->uri->segment(4); ?>';
    var student_id = '<?php echo $this->uri->segment(3); ?>';
    exam_type = {'1':'IIT JEE','2':'AIEEE','3':'PMT','4':'OLYMPIAD','5':'TALENT SEARCH'};
    r_char = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O'];
    c_char = ['P','Q','R','S','T','U','V','W','X','Y','Z'];
    get_exam_data(student_id,exam_id);

    $("#update_new_answer").postAjaxData(function(result){
      if(result === true)
      {
        bootbox.alert({
            message: '<div class="text-success">Successfully Updated.</div>',
            size: 'small',
            backdrop:true,
            closeButton:false,
            callback:function(){
             get_exam_data(student_id,exam_id);
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
    $("#update_new_answer").validate();
  });
  
  function get_exam_data(student_id,exam_id)
  {
    $.ajax({
      url:base_url+'student/scorecard/'+student_id+'/'+exam_id,
      error:function(err){
        $('#update_answer').html('<div class="alert alert-danger h4">Something Went Wrong.</div>');
      },
      success:function(res){
        var res = JSON.parse(res);
        var student = res.student;
        var subject = res.subject_chapter;
        var student_result = res.student_result;
        var res = res.exam;
        if(typeof res == 'object')
        {
          console.log(res);
          var split = (res.subjects).split(',');
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

          var data = '';
          data += '<table class="table table-bordered" width="100%" cellspacing="0" cellpadding="0" ><thead><tr><th colspan="4" class="h3">'+exam_type[res.exam_type]+'</th></tr></thead><tr><tr><th>Name  </th><td>'+student.name+'</td><th>Roll No.  </th><td>'+student.roll_no+'</td></tr><th>Exam  </th><td>'+res.title+'</td><th>Date  </th><td>'+moment(res.date,'YYYY-MM-DD').format('DD/MM/YYYY')+'</td></tr><tr><th>Batch  </th><td>'+res.batch+'</td><th>Subjects  </th><td>'+res.subjects+'</td></tr><tr><th>Total No. of Questions  </th><td>'+res.questions.total+' Qns <b>(Easy : '+easy+', Medium : '+medium+', Hard : '+hard+')<b></td><th>Maximum Marks </th><td>'+res.questions.marks+'</td></tr><tr><td colspan="2"></td><th>Marks Obtained</th><td>'+student_result.obtained+'</td></tr></table>';
          data += '<table class="" width="100%" cellspacing="0" cellpadding="0" >';
          var i = 1;
          // data += '<tr>';
          $.each(res.z_sections,function(k_sub,v_sub){

            //Whole Other Data
            data += '<tr><td><table width="100%" class="" cellspacing="0" cellpadding="0" ><thead><tr><td align="center"><b></b></td></tr></thead><tr>';
            $.each(v_sub,function(k,s){
              data += '<td width="100%"><table class="table table-bordered" width="100%" cellspacing="0" cellpadding="0" ><thead><tr><th class="h3" colspan="8">'+k_sub+' - '+s.section+'</th></tr><tr><th width="5%">No.</th><th width="10%">Question Type</th><th width="18%">Your Answer</th><th width="18%">Correct Answer</th><th width="14%">Chapters</th><th width="10%">Level</th><th width="15%">Marks Obtained</th><th width="10%"></th></tr></thead>';
              $.each(s.z_questions,function(k1,q){
                data += '<tr><input type="hidden" class="form-control" id="'+q.ID+'-ID" name="'+q.ID+'-ID" value="'+q.ID+'">';
               
                //Chapter Options 
                var chapter_options = '';//<option value="">Select Chapter</option>';
                if(subject[k_sub] != undefined)
                {
                  var chaptersID = (q.chapters == null) ? new Array() : (q.chapters).split(',');
                  var chapter_options = '';
                  $.each(subject[k_sub],function(chk,chv){
                    chapter_options += ($.inArray(chv.ID,chaptersID) != -1) ? chv.title+', ' : '';
                  });
                }7

                //Level Options
                var level_options = '';
                level_options += (q.level == 'E') ? 'Easy' : '';
                level_options += (q.level == 'M') ? 'Medium' : '';
                level_options += (q.level == 'H') ? 'Hard' : '';

                switch(q.qn_type)
                {
                  case 'single' :
                          data += '<td>'+(k1+1)+'. </td><td>'+(q.qn_type).toUpperCase()+' </td><td colspan="1">';

                          var ans = (student_result.ans == undefined) ? '' :student_result.ans[q.ID];
                          var marks = (student_result.marks == undefined) ? 0 : student_result.marks[q.ID];
                          if(ans != '')
                          {
                            var icon = (ans == q.key_answer) ? '<i class="fa fa-check text-success fa-2x"></i>' : '<i class="fa fa-remove text-danger fa-2x"></i>';
                          }
                          else{
                            var icon = '';
                          }

                          for (var i = 1; i <= q.length; i++) {
                            
                            if(i==ans)
                            {
                              var cls = (q.key_answer == ans) ? 'btn-primary' : 'btn-danger';
                            }
                            else{
                              var cls = 'btn-default';
                            }
                            
                            data += '<span type="button" class="btn btn-circle '+cls+'">'+r_char[i-1]+'</span> ';
                          }

                          data += '</td><td>';
                          
                          for (var i = 1; i <= q.length; i++) {
                            
                            var cls = (i == q.key_answer) ? 'btn-primary' : 'btn-default';
                            data += '<span type="button" class="btn btn-circle '+cls+'">'+r_char[i-1]+'</span> ';
                          }

                          data += '</td><td>'+chapter_options+'</td><td>'+level_options+'</td><td hidden width="25%">'+(q.qn_type).toUpperCase()+' ANSWER(S) : <span class="button-group-addon font-bold" >'+q.key_answer+'</span></td><td>'+marks+'</td><td><button type="button" id="change_btn-'+q.ID+'-key_answer_'+i+'" class="btn btn-default" onclick="change_modal(\''+q.ID+'\',\''+q.qn_type+'\',\''+q.length+'\',\''+k_sub+' - '+s.section+'\',\''+(k1+1)+'\')">Change</button></div></td>';
                          break;
                  
                  case 'multiple':
                          data += '<td>'+(k1+1)+'. </td><td>'+(q.qn_type).toUpperCase()+' </td><td colspan="1">';
                          
                          var ans = (student_result.ans == undefined) ? '' :student_result.ans[q.ID];
                          var marks = (student_result.marks == undefined) ? 0 : student_result.marks[q.ID];

                          for (var i = 1; i <= q.length; i++) {
                            
                            if(ans == q.key_answer && q.key_answer != null && ans != null)
                            {
                              var n = (ans).split(",");
                              var cls = ($.inArray(''+i+'',n) != '-1') ? 'btn-primary' : 'btn-default';
                            }
                            else if(ans != null){
                              var n = (ans).split(",");
                              var cls = ($.inArray(''+i+'',n) != '-1') ? 'btn-danger' : 'btn-default';
                            }
                            else{
                              var cls = 'btn-default';
                            }

                            
                            data += '<span type="button" class="btn btn-circle '+cls+'">'+r_char[i-1]+'</span> ';
                          }

                          data += '</td><td>';

                          for (var i = 1; i <= q.length; i++) {
                            
                            if(q.key_answer != null)
                            {
                              var n = (q.key_answer).split(",");
                              var cls = ($.inArray(''+i+'',n) != '-1') ? 'btn-primary' : 'btn-default';
                            }
                            else{
                              var cls = 'btn-default';
                            }
                            data += '<button  type="button" class="btn btn-circle '+cls+'">'+r_char[i-1]+'</button>';
                          }
                          
                          data += '</td><td>'+chapter_options+'</td><td>'+level_options+'</td><td hidden width="25%">'+(q.qn_type).toUpperCase()+' ANSWER(S) : <span class="button-group-addon font-bold">'+q.key_answer+'</span></td><td>'+marks+'</td><td><button type="button" id="change_btn-'+q.ID+'-key_answer_'+i+'" class="btn btn-default" onclick="change_modal(\''+q.ID+'\',\''+q.qn_type+'\',\''+q.length+'\',\''+k_sub+' - '+s.section+'\',\''+(k1+1)+'\')">Change</button></td>';
                          break;

                  case 'numeric':
                          var select = (q.key_answer < 0) ? 'selected' : '';
                          var vl = (q.key_answer < 0) ? Math.abs(q.key_answer) : q.key_answer;
                          var ans = (student_result.ans[q.ID] == null) ? '' : student_result.ans[q.ID];
                          var marks = (student_result.marks == undefined) ? 0 : student_result.marks[q.ID];
                          if(ans != '')
                          {
                            var icon = (ans == q.key_answer) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-remove text-danger"></i>';
                          }
                          else{
                            var icon = '';
                          }
                          data += '<td>'+(k1+1)+'. </td><td>'+(q.qn_type).toUpperCase()+' </td><td>'+icon+''+ans+' <td>'+q.key_answer;
                          data += '</td><td>'+chapter_options+'</td><td>'+level_options+'</td><td hidden width="25%">'+(q.qn_type).toUpperCase()+' ANSWER(S) :- <span class="button-group-addon font-bold">'+q.key_answer+'</span></td><td>'+marks+'</td><td><button type="button" id="change_btn-'+q.ID+'-key_answer_'+i+'" class="btn btn-default" onclick="change_modal(\''+q.ID+'\',\''+q.qn_type+'\',\''+q.length+'\',\''+k_sub+' - '+s.section+'\',\''+(k1+1)+'\')">Change</button></td>';
                          break;

                  case 'matrix':
                          q_len = (q.length).split(",");
                          data += '<td>'+(k1+1)+'. </td><td>'+(q.qn_type).toUpperCase()+' </td><td colspan="1"><table class="table table-bordered" cellspacing="0" cellpadding="0" width="100%">';
                          cls = 'btn-default';

                          var ans = (student_result.ans == undefined) ? '' :student_result.ans[q.ID];
                          var marks = (student_result.marks == undefined) ? 0 : student_result.marks[q.ID];

                          for (var i = 1; i <= q_len[0]; i++) {
                            
                            data += (i==0) ? '<tr><td align="center"></td>' : '<tr><td align="center"> '+r_char[i-1]+' </td>';
                            
                            for (var j = 1; j <= q_len[1]; j++) {
                              
                              var obj = JSON.parse(ans);
                              
                              if(i == 0)
                              {
                                data += '<td align="center"> '+j+' </td>';
                              }
                              else{
                                if(obj != null)
                                {
                                  $.each(obj,function(k,v){
                                    if(v.Row == i && v.Column == j)
                                    {
                                      console.log(v.Row+','+v.Column);
                                      console.log(JSON.parse(q.key_answer));
                                      cls = 'btn-danger';
                                      $.each(JSON.parse(q.key_answer),function(ak,av){
                                          if(av.Row == i && av.Column == j)
                                          {
                                            cls = 'btn-primary';
                                          }                                          
                                      });
                                      return false; 
                                    }
                                    else{
                                      cls = 'btn-default';
                                    }
                                  });
                                }
                                else{
                                  cls = 'btn-default';
                                }
                                data += '<td><button  type="button" class="btn btn-circle '+cls+'">'+c_char[j-1]+'</button></td>';
                              }
                            }
                            data += '</tr>';
                          }

                          data += '</table></td><td><table class="table table-bordered" cellspacing="0" cellpadding="0" width="100%">';
                          
                          for (var i = 1; i <= q_len[0]; i++) {
                            
                            data += (i==0) ? '<tr><td align="center"></td>' : '<tr><td align="center"> '+r_char[i-1]+' </td>';
                            
                            for (var j = 1; j <= q_len[1]; j++) {
                              
                              var obj = JSON.parse(q.key_answer);
                              
                              if(i == 0)
                              {
                                data += '<td align="center"> '+j+' </td>';
                              }
                              else{
                                if(obj != null)
                                {
                                  $.each(obj,function(k,v){
                                    if(v.Row == i && v.Column == j)
                                    {
                                      cls = 'btn-primary';
                                      return false; 
                                    }
                                    else{
                                      cls = 'btn-default';
                                    }
                                  });
                                }
                                else{
                                  cls = 'btn-default';
                                }
                                data += '<td><button  type="button" class="btn btn-circle '+cls+'">'+c_char[j-1]+'</button></td>';
                              }
                            }
                            data += '</tr>';
                          }

                          data += "</table></td><td>"+chapter_options+"</td><td>"+level_options+"</td><td hidden>"+(q.qn_type).toUpperCase()+" ANSWER(S) :- <span class='button-group-addon font-bold'><br>"+q.key_answer+"</span></td><td>"+marks+"</td><td><button type='button' id='change_btn-"+q.ID+"-key_answer_"+i+"' class='btn btn-default' onclick='change_modal(\""+q.ID+"\",\""+q.qn_type+"\",\""+q.length+"\",\""+k_sub+" - "+s.section+"\",\""+(k1+1)+"\")'>Change</button></td>";
                          break;

                  default :
                          data += '<td>'+'</td>';
                          break;
                }
                
                data += '</tr>';
              });
              data += '</table></td>';
              data += '</tr><tr>';             
              i++;
            });
            data += '</tr></table></td></tr>';
          });
          // data += '</tr>';
          data += '</table>';
          $('#update_answer').html(data);//+'<pre>'+JSON.stringify(res, null, "\t")+'</pre>');
          $('.chosen-select').chosen({width:'100%'});
        }
        else{
          $('#update_answer').html('<div class="alert alert-danger h4">No Data Found.</div>');
        }
      }
    });
  }

  function submit_data(){

    var data = $('#update_answer').serializeArray();
    var flag = true;
    $.each(data,function(k,v){
      if(v.value == '' || v.value == '[]' || v.value == '0')
      {
        flag = false;
      }
    });
    if(flag)
    {
      $('#update_answer').submit();
    }
    else{
      bootbox.confirm('You have not updated all the answers. Do you stil want to continue ?', function(result) {
        if(result == true)
        {
          $('#update_answer').submit();
        }
      });
    }
  }

  function set_val(selector,val,type,count,row)
  {
    switch(type){
      case 'single' :
        
                  if(!$('#btn-'+selector+'_'+val).hasClass('btn-primary'))
                  {
                    for (var i = 1; i <= count; i++) {
                      $('#btn-'+selector+'_'+i).removeClass('btn-primary').addClass('btn-default');
                    }
                    $('#btn-'+selector+'_'+val).addClass('btn-primary').removeClass('btn-default');
                  }

                  $('#'+selector).val(val);

                  break;

      case 'multiple':
                  
                  var arr = [];
                  var x = $('#'+selector).val();
                  if(x != '' && x != 0) { 
                    arr.push(x);
                  }
                  if($.inArray(val,x) == -1) { 
                    arr.push(val);
                  };

                  if(!$('#btn-'+selector+'_'+val).hasClass('btn-primary'))
                  {
                    $('#btn-'+selector+'_'+val).addClass('btn-primary').removeClass('btn-default');
                  }
                  else{
                    $('#btn-'+selector+'_'+val).removeClass('btn-primary').addClass('btn-default');
                    var new_arr = arr[0].split(',');
                    var n_arr = [];
                    $.each(new_arr,function(k,v){
                      if(v != val)
                      {
                        n_arr.push(v);
                      }
                    });
                    arr = n_arr;
                  }
                  $('#'+selector).val(arr);
                  break;

      case 'numeric':
                  console.log('#btn-'+row+'-'+selector+'_'+val);
                  if(!$('#btn-'+row+'-'+selector+'_'+val).hasClass('btn-primary'))
                  {
                    for (var k = 0; k <= 10; k++) {
                      $('#btn-'+k+'-'+selector+'_'+val).removeClass('btn-primary').addClass('btn-default');
                    }
                    console.log('#btn-'+row+'-'+selector+'_'+val);
                    $('#btn-'+row+'-'+selector+'_'+val).addClass('btn-primary').removeClass('btn-default');
                  }

                  var n_val = 0;
                  for (var j = 0; j <= 10; j++) {
                    for (var i = 1; i <= count; i++) {
                      if($('#btn-'+j+'-'+selector+'_'+i).hasClass('btn-primary'))
                      {
                        if(j == 10)
                        {
                          // n_val = (""+n_val).substr(0, i) + '.' + (""+n_val).substr(i + 1);
                        }
                        else{
                          n_val = n_val + j*Math.pow(10,count-i);
                        }
                      }
                    }
                  }

                  $('#'+selector).val(n_val);
                  break;

      case 'matrix':
                  cnt = count.split(",");

                  if(!$('#btn-'+row+'-'+selector+'_'+val).hasClass('btn-primary'))
                  {
                    $('#btn-'+row+'-'+selector+'_'+val).addClass('btn-primary').removeClass('btn-default');
                  }
                  else{
                    $('#btn-'+row+'-'+selector+'_'+val).removeClass('btn-primary').addClass('btn-default');
                  }

                  var n_arr = [];
                  for (var i = 1; i <= cnt[0]; i++) {
                    for (var j = 1; j <= cnt[1]; j++) {
                      if($('#btn-'+j+'-'+selector+'_'+i).hasClass('btn-primary'))
                      {
                        n_arr.push({'Row':i,'Column':''+j+''});
                      }
                    }
                  }
                  $('#'+selector).val(JSON.stringify(n_arr));
                  break;

      default :
                  break;
    }
  }

  function set_number(qID)
  {
    setTimeout(function(){
      $('#'+qID+'-key_answer').text($('#'+qID+'-select').val()+$('#'+qID).val());
    },100);
  }

  function change_modal(question_ID,type,qlength,title,question_no)
  {
    // console.log(question_ID);
    // console.log(type);
    // console.log(qlength);
    // console.log(title);
    // console.log(question_no);
    var data = '<table class="table table-bordered"><tr><td colspan="2" class="h3 font-bold">'+title+'</td></tr><tr><th>Question No. </th><th>'+question_no+'</th></tr><tr><th>Answer</th><td>';

    switch(type)
    {
      case 'single':
          for (var i = 1; i <= qlength; i++) {
            data += '<button type="button" id="btn-answer_'+i+'" class="btn btn-circle btn-default" onclick="set_val(\'answer\',\''+i+'\',\''+type+'\',\''+qlength+'\')">'+r_char[i-1]+'</button> ';
          }
          break;
      case 'multiple':
          for (var i = 1; i <= qlength; i++) {
            data += '<button type="button" id="btn-answer_'+i+'" class="btn btn-circle btn-default" onclick="set_val(\'answer\',\''+i+'\',\''+type+'\',\''+qlength+'\')">'+r_char[i-1]+'</button> ';
          }
          break;
      case 'numeric':
          data += '<div class="col-sm-3"><select class="form-control" id="select" name="sign" onchange="set_number()"><option value="">+</option><option value="-">-</option></select></div><div class="col-sm-9"><input type="number" name="number" class="form-control" id="number" maxlength="'+qlength+'" onkeyup="set_number()"></div>';
          break;
      case 'matrix':
          data += '<table class="table table-bordered" cellspacing="0" cellpadding="0" width="100%">';
          for (var i = 1; i <= q_len[0]; i++) {
            data += (i==0) ? '<tr><td align="center"></td>' : '<tr><td align="center"> '+r_char[i-1]+' </td>';
            for (var j = 1; j <= q_len[1]; j++) {
              
              if(i == 0)
              {
                data += '<td align="center"> '+j+' </td>';
              }
              else{
                data += '<td><button type="button" id="btn-'+j+'-answer_'+i+'" class="btn btn-circle btn-default" onclick="set_val(\'answer\',\''+i+'\',\''+type+'\',\''+qlength+'\',\''+j+'\')">'+c_char[j-1]+'</button></td>';
              }
            }
            data += '</tr>';
          }
          
          data += "</table>";
          break;
      default :
        bootbox.alert('Something Went Wrong');
    }

    data += '</td></tr></table>';
    $('#question_ID').val(question_ID);
    $('#answer').val('');
    $('#new_answer_div').html(data);
    $('#change_modal').modal('show');
  }
</script>