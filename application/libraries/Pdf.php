<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pdf {
	function pdf()
	{
		$CI = & get_instance();
		log_message('Debug', 'mPDF class is loaded.');
	}

	function load($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P')
	{
		include_once APPPATH.'/third_party/mpdf/mpdf.php';
		return new mPDF($mode,$format,$default_font_size,$default_font,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$orientation);
	}
}