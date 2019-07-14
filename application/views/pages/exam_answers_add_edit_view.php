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
    <form id="exam_add" action="<?php echo base_url('exam/step2_add/'.$this->uri->segment(3)); ?>" class="table-responsive" method="POST">
      <div class="text-center"><i class="fa fa-spinner fa-pulse fa-2x pull-center"></i> <span class="h4">Loading Data ...</span></div>
    </form>
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
    var id = '<?php echo $this->uri->segment(3); ?>';
    r_char = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O'];
    c_char = ['P','Q','R','S','T','U','V','W','X','Y','Z'];
    $.ajax({
      url:base_url+'exam/get_exam_data/'+id+'/YES',
      error:function(err){
        $('#exam_add').html('<div class="alert alert-danger h4">Something Went Wrong.</div>');
      },
      success:function(res){
        var res = JSON.parse(res);
        var subject = res.subject_chapter;
        var res = res.exam;
        if(typeof res == 'object')
        {
          console.log(res);
          var split = (res.subjects).split(',');
          var data = '';
          data += '<table class="table table-bordered" width="100%" cellspacing="0" cellpadding="0" >';
          data += '<tr><th>Exam  </th><td>'+res.title+'</td><th>Date  </th><td>'+moment(res.date,'YYYY-MM-DD').format('DD/MM/YYYY')+'</td><th>Batch  </th><td>'+res.batch+'</td></tr></table><table class="" width="100%" cellspacing="0" cellpadding="0" >';
          var i = 1;
          // data += '<tr>';
          $.each(res.z_sections,function(k_sub,v_sub){

            //Whole Other Data
            data += '<tr><td><table width="100%" class="" cellspacing="0" cellpadding="0" ><thead><tr><td align="center"><b></b></td></tr></thead><tr>';
            $.each(v_sub,function(k,s){
              data += '<td width="50%"><table class="table table-bordered" width="100%" cellspacing="0" cellpadding="0" ><thead><tr><td align="center" colspan="6">'+k_sub+' - '+s.section+'</td></tr></thead>';
              $.each(s.z_questions,function(k1,q){
                data += '<tr><input type="hidden" class="form-control" id="'+q.ID+'-ID" name="'+q.ID+'-ID" value="'+q.ID+'">';
               
                //Chapter Options 
                var chapter_options = '';//<option value="">Select Chapter</option>';
                if(subject[k_sub] != undefined)
                {
                  var chaptersID = (q.chapters == null) ? new Array() : (q.chapters).split(',');
                  $.each(subject[k_sub],function(chk,chv){
                    var selected = ($.inArray(chv.ID,chaptersID) != -1) ? 'selected' : '';
                    chapter_options += '<option value="'+chv.ID+'" '+selected+'>'+chv.title+'</option>';
                  });
                }

                //Level Options
                var level_options = '';
                level_options += (q.level == 'E') ? '<option value="E" selected>Easy</option>' : '<option value="E">Easy</option>';
                level_options += (q.level == 'M') ? '<option value="E" selected>Medium</option>' : '<option value="M">Medium</option>';
                level_options += (q.level == 'H') ? '<option value="E" selected>Hard</option>' : '<option value="H">Hard</option>';

                switch(q.qn_type)
                {
                  case 'single' :
                          data += '<td width="5%">'+(k1+1)+'. </td><td width="5%">'+(q.qn_type).toUpperCase()+' </td><td width="25%">';
                          
                          for (var i = 1; i <= q.length; i++) {
                            
                            var cls = (i == q.key_answer) ? 'btn-primary' : 'btn-default';
                            
                            data += '<button type="button" id="btn-'+q.ID+'-key_answer_'+i+'" class="btn btn-circle '+cls+'" onclick="set_val(\''+q.ID+'-key_answer\',\''+i+'\',\''+q.qn_type+'\',\''+q.length+'\')">'+r_char[i-1]+'</button> ';
                          }

                          data += '</td><td width="20%"><select data-placeholder="Choose Chapers..." class="chosen-select form-control" name="'+q.ID+'-chapters[]" multiple>'+chapter_options+'</select></td><td width="20%"><select class="form-control" name="'+q.ID+'-level">'+level_options+'</select></td><td hidden width="25%">'+(q.qn_type).toUpperCase()+' ANSWER(S) : <span class="button-group-addon font-bold" id="'+q.ID+'-key_answer">'+q.key_answer+'</span><input type="hidden" class="form-control" id="'+q.ID+'" name="'+q.ID+'-key_answer" value="'+q.key_answer+'"></td>';
                          break;
                  
                  case 'multiple':
                          data += '<td width="5%">'+(k1+1)+'. </td><td width="5%">'+(q.qn_type).toUpperCase()+' </td><td width="25%">';
                          
                          for (var i = 1; i <= q.length; i++) {
                            
                            if(q.key_answer != null)
                            {
                              var n = (q.key_answer).split(",");
                              var cls = ($.inArray(''+i+'',n) != '-1') ? 'btn-primary' : 'btn-default';
                            }
                            else{
                              var cls = 'btn-default';
                            }
                            data += '<button type="button" id="btn-'+q.ID+'-key_answer_'+i+'" class="btn btn-circle '+cls+'" onclick="set_val(\''+q.ID+'-key_answer\',\''+i+'\',\''+q.qn_type+'\',\''+q.length+'\')">'+r_char[i-1]+'</button>';
                          }
                          
                          data += '</td><td width="20%"><select data-placeholder="Choose Chapers..." class="chosen-select form-control" name="'+q.ID+'-chapters[]" multiple>'+chapter_options+'</select></td><td width="20%"><select class="form-control" name="'+q.ID+'-level">'+level_options+'</select></td><td hidden width="25%">'+(q.qn_type).toUpperCase()+' ANSWER(S) :- <span class="button-group-addon font-bold" id="'+q.ID+'-key_answer">'+q.key_answer+'</span><input type="hidden" class="form-control" id="'+q.ID+'" name="'+q.ID+'-key_answer" value="'+q.key_answer+'"></td>';
                          break;

                  case 'numeric':
                          var select = (q.key_answer < 0) ? 'selected' : '';
                          var vl = (q.key_answer < 0) ? Math.abs(q.key_answer) : q.key_answer;
                          data += '<td width="5%">'+(k1+1)+'. </td><td width="5%">'+(q.qn_type).toUpperCase()+' </td><td width="10%"><select class="form-control" id="'+q.ID+'-select" name="'+q.ID+'-num_type" onchange="set_number(\''+q.ID+'\')"><option value="">+</option><option value="-" '+select+'>-</option></select><td width="15%"><input type="number" class="form-control" id="'+q.ID+'" name="'+q.ID+'-key_answer" value="'+vl+'" maxlength="'+q.length+'" onkeyup="set_number(\''+q.ID+'\')">';
                          
                          // for (var j = 0; j < 10; j++) {
                          //   for (var i = 1; i <= q.length; i++) {                            
                              
                          //     var str = String(q.key_answer);
                          //     var str1 = new Array(q.length-str.length+1).join('0') + str;
                          //     var num = str1.charAt(i-1);
                          //     var cls = (num == j) ? 'btn-primary' : 'btn-default';

                          //     data += ' <button type="button" id="btn-'+j+'-'+q.ID+'-key_answer_'+i+'" class="btn btn-circle '+cls+'" onclick="set_val(\''+q.ID+'-key_answer\',\''+i+'\',\''+q.qn_type+'\',\''+q.length+'\',\''+j+'\')">';
                          //     data += (j==10) ? '.' : j;
                          //     data += '</button> ';
                          //   }
                          //   data += '<br><br>';
                          // }
                          
                          data += '</td><td width="20%"><select data-placeholder="Choose Chapers..." class="chosen-select form-control" name="'+q.ID+'-chapters[]" multiple>'+chapter_options+'</select></td><td width="20%"><select class="form-control" name="'+q.ID+'-level">'+level_options+'</select></td><td hidden width="25%">'+(q.qn_type).toUpperCase()+' ANSWER(S) :- <span class="button-group-addon font-bold" id="'+q.ID+'-key_answer">'+q.key_answer+'</span></td>';
                          break;

                  case 'matrix':
                          q_len = (q.length).split(",");
                          data += '<td width="5%">'+(k1+1)+'. </td><td width="5%">'+(q.qn_type).toUpperCase()+' </td><td width="25%"><table class="table table-bordered" cellspacing="0" cellpadding="0" width="100%">';
                          cls = 'btn-default';
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
                                data += '<td><button type="button" id="btn-'+j+'-'+q.ID+'-key_answer_'+i+'" class="btn btn-circle '+cls+'" onclick="set_val(\''+q.ID+'-key_answer\',\''+i+'\',\''+q.qn_type+'\',\''+q.length+'\',\''+j+'\')">'+c_char[j-1]+'</button></td>';
                              }
                            }
                            data += '</tr>';
                          }
                          
                          data += "</table></td><td width='20%'><select data-placeholder='Choose Chapers...' class='chosen-select form-control' name='"+q.ID+"-chapters[]' multiple>"+chapter_options+"</select></td><td width='20%'><select class='form-control' name='"+q.ID+"-level'>"+level_options+"</select></td><td hidden width='25%'>"+(q.qn_type).toUpperCase()+" ANSWER(S) :- <span class='button-group-addon font-bold' id='"+q.ID+"-"+q.qn_type+"'><br>"+q.key_answer+"</span><input type='hidden' class='form-control' id='"+q.ID+"' name='"+q.ID+"-key_answer' value='"+q.key_answer+"'></td>";
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
          data += '<button class="btn btn-block btn-success" type="button" onclick="submit_data()"> Update Answer Sheet</button>'
          $('#exam_add').html(data);//+'<pre>'+JSON.stringify(res, null, "\t")+'</pre>');
          $('.chosen-select').chosen({width:'100%'});
        }
        else{
          $('#exam_add').html('<div class="alert alert-danger h4">No Data Found.</div>');
        }
      }
    });

    $("#exam_add").postAjaxData(function(result){
      if(result === true)
      {
        bootbox.alert({
            message: '<div class="text-success">Successfully Updated.</div>',
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
    $("#exam_add").validate();
  });
  
  function submit_data(){

    var data = $('#exam_add').serializeArray();
    var flag = true;
    $.each(data,function(k,v){
      if(v.value == '' || v.value == '[]' || v.value == '0')
      {
        flag = false;
      }
    });
    if(flag)
    {
      $('#exam_add').submit();
    }
    else{
      bootbox.confirm('You have not updated all the answers. Do you stil want to continue ?', function(result) {
        if(result == true)
        {
          $('#exam_add').submit();
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

                  $('input[name="'+selector+'"]').val(val);
                  $('#'+selector).text(r_char[val-1]);
                  
                  break;

      case 'multiple':
                  
                  var arr = [];
                  var x = $('input[name="'+selector+'"]').val();
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
                  $('input[name="'+selector+'"]').val(arr);
                  var c_val = ($('input[name="'+selector+'"]').val()).split(',');
                  var nnew = '';
                  $.each(c_val,function(k,v){
                    if(k<c_val.length-1)
                    {
                      nnew += r_char[v-1]+','; 
                    }
                    else{
                      nnew += r_char[v-1];
                    }
                  })
                  $('#'+selector).text(nnew);

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

                  $('input[name="'+selector+'"]').val(n_val);
                  $('#'+selector).text(n_val);

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
                  $('input[name="'+selector+'"]').val(JSON.stringify(n_arr));
                  $('#'+selector).html('<br>'+JSON.stringify(n_arr)+'');
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
</script>