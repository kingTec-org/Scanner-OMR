<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
        'subject' => array(
                array(
                        'field' => 'title',
                        'label' => 'Subject Name',
                        'rules' => 'required'
                )
        ),
        'chapter' => array(
                array(
                        'field' => 'subject_ID',
                        'label' => 'Subject Selection',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'title',
                        'label' => 'Chapter Name',
                        'rules' => 'required'
                )
        ),
        'batch' => array(
                array(
                        'field' => 'title',
                        'label' => 'Batch Name',
                        'rules' => 'required'
                )
        ),
        'student' => array(
                array(
                        'field' => 'batch_ID',
                        'label' => 'Batch Selection',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'name',
                        'label' => 'Student Name',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'roll_no',
                        'label' => 'Student Roll Number',
                        'rules' => 'required'
                )    
        ),
        'exam' => array(
                array(
                        'field' => 'title',
                        'label' => 'Exam Title',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'paper_size',
                        'label' => 'OMR Paper Size',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'date',
                        'label' => 'Exam Date',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'subjects',
                        'label' => 'Subjects',
                        'rules' => 'required'
                ),
        )
);

?>