<?php
function bidang_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatanlt.js');
	drupal_set_title('RPJM');
	//drupal_set_html_head($h);
	switch($arg) {
		case 'show':
			$qlike = " and lower(program) like lower('%%%s%%')";
			$_SESSION['kodeu'] = '';
			break;
		case 'filter':
			$tahun = arg(3);
			$kodeu = arg(4);
			$program = arg(5);
			$_SESSION['kodeu'] = '';
			if (strlen($kodeu)>0) {
				$_SESSION['kodeu'] = $kodeu;
				$qlike .= sprintf(" and kodeu='%s' ", db_escape_string($kodeu));
			}
			if (strlen($program)>0) {
				$qlike .= sprintf(" and lower(program) like lower('%%%s%%') ", db_escape_string($program));
			}			
			break;
		default:
			$_SESSION['kodeu'] = '';
			break;
		
	}

    $header = array (
        array('data' => 'No', 'width' => '10px'),
		array('data' => ucwords(strtolower('kode')), 'field'=> 'kodepro', 'valign'=>'top'),
		array('data' => ucwords(strtolower('program')), 'field'=> 'program', 'valign'=>'top'),
		array('data' => ucwords(strtolower('sasaran')), 'field'=> 'sasaran', 'valign'=>'top'),
		array('data' => ucwords(strtolower('target')), 'field'=> 'target', 'valign'=>'top'),

		//array('data' => '', 'width' => '110px'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodepro';
    }
	
	if (strlen($tahun)<=0) $tahun = '2014'; 
	//$customwhere = sprintf(' and tahun=%s ', $tahun);
    
	$where = ' where true' . $qlike ;

    $sql = 'select kodepro, tahun, program, s' . $tahun . '  sasaran, t' . $tahun . ' target from {program}' . $where;
    //$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
    $limit = 25;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {program}" . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	$fcountsql = $countsql;
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
    if ($result) {
        while ($data = db_fetch_object($result)) {
			//$editlink = '';
			
			if (user_access('bidang edit'))
				$program = l($data->program, 'apbd/bidang/edit/' . $data->kodepro, array('html'=>TRUE));
			else 
				$program = $data->program;
				
			//if (user_access('bidang penghapusan'))
            //    $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/bidang/delete/' . $data->kodepro, array('html'=>TRUE));
			
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $data->kodepro, 'align' => 'left', 'valign'=>'top'),
				array('data' => $program, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->sasaran, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->target, 'align' => 'left', 'valign'=>'top'),
                //array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    } 
	$btn = "";
	//if (user_access('bidang tambah')) {
	//	$btn .= l('Tambah Data Baru', 'apbd/program/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	//}
	//if (user_access('program pencarian'))	{
	//	$btn .= l('Cari Data', 'apbd/program/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	//}
    $output .= $btn . "<div id='fl_filter'>" . drupal_get_form ('bidang_filter_form') . "</div>" . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

function bidang_filter_form() {
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Filter Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	
	$tahun = arg(3);
	$kodeu = arg(4);

	$opttahun = array();     
	$opttahun['2014'] = '2014';
	$opttahun['2015'] = '2015';
	$opttahun['2016'] = '2016';
	$opttahun['2017'] = '2017';
	$opttahun['2018'] = '2018';

	$form['formdata']['tahun']= array(
		'#type'         => 'select', 
		'#title'        => 'Tahun', 
		'#options'	=> $opttahun,
		//'#description'  => 'tahun', 
		//'#maxlength'    => 4, 
		//'#size'         => 6, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#width'         => 20, 
		'#default_value'=> $tahun, 
		'#weight' => 1,
	);
	
	$pquery = "select kodeu, urusansingkat from urusan order by urusansingkat";
	$pres = db_query($pquery);
	$urusan = array();
	$urusan[''] = '---SEMUA URUSAN---';
	
	while ($prow = db_fetch_object($pres)) {
		$urusan[$prow->kodeu] = $prow->urusansingkat ;
	}
	
    $form['formdata']['urusan'] = array (
        '#type' => 'select',
        '#title' => 'Urusan',
		'#options' => $urusan,
		'#default_value' => $kodeu,
		'#weight' => 2,
    );
	
	$form['formdata']['program']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Program', 
		//'#description'  => 'kegiatan', 
		//'#maxlength'    => 60, 
		'#size'         => 42, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $program, 
		'#weight' => 3,
	);
	
	$form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Tampilkan',
		'#weight' => 4,
    );
	
	return $form;
}

function bidang_filter_form_submit($form, &$form_state) {
	$tahun = $form_state['values']['tahun'];
	$kodeu = $form_state['values']['urusan'];
	$program = $form_state['values']['program'];
	
	drupal_goto("apbd/bidang/filter/" . $tahun . '/' . $kodeu . '/' . $program);
}


?>