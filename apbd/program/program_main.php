<?php
function program_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatanlt.js');
	switch($arg) {
		case 'show':
			$qlike = " and lower(program) like lower('%%%s%%')";
			$_SESSION['kodeu'] = '';
			break;
		case 'filter':
			$tahun = arg(3);
			$kodeu = arg(4);
			$_SESSION['kodeu'] = '';
			if (strlen($kodeu)>0) {
				$_SESSION['kodeu'] = $kodeu;
				$qlike .= sprintf(" and kodeu='%s' ", db_escape_string($kodeu));
			}
			break;
		default:
			$_SESSION['kodeu'] = '';
			$tahun = variable_get('apbdtahun', 0);
			break;
		
	}  

    $header = array (
        array('data' => 'No', 'width' => '10px'),
		array('data' => 'Kode', 'field'=> 'kodepro', 'valign'=>'top'),
		array('data' => 'Program', 'field'=> 'program', 'valign'=>'top'),
		array('data' => 'Indikator Kinerja', 'valign'=>'top'),
		array('data' => '', 'width' => '110px'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodeu,kodepro';
    }

    $customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select kodepro, kodeu, tahun, program, sifat, st' . $tahun . ' sasaran, np from {program}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 50;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {program}" . $where;
    $fcountsql = sprintf($countsql, addslashes($nama));
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
			$editlink = '';
			//if (user_access('program edit'))
			//	$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/program/edit/' . $data->kodepro, array('html'=>TRUE));
			//if (user_access('program penghapusan'))
         //       $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/program/delete/' . $data->kodepro, array('html'=>TRUE));
				
				if (user_access('program edit'))
					$pname = l($data->program, 'apbd/program/edit/' . $data->kodepro, array('html'=>TRUE)). "&nbsp;";
				else 
					$pname = $data->program;

				//RPJMD
				$editlink = l('2015|', 'apbd/programsasaran/' . $data->kodepro . '2015'  , array('html'=>TRUE));
				$editlink .= l('2016|', 'apbd/programsasaran/' . $data->kodepro . '2016'  , array('html'=>TRUE));
				$editlink .= l('2017|', 'apbd/programsasaran/' . $data->kodepro . '2017'  , array('html'=>TRUE));
				$editlink .= l('2018|', 'apbd/programsasaran/' . $data->kodepro . '2018'  , array('html'=>TRUE));
				$editlink .= l('2019|', 'apbd/programsasaran/' . $data->kodepro . '2019'  , array('html'=>TRUE));
				
					
				if (user_access('program penghapusan'))
					$editlink .=l('Hapus ', 'apbd/program/delete/' . $data->kodepro, array('html'=>TRUE));
				
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $data->kodeu . $data->kodepro, 'align' => 'left', 'valign'=>'top'),
				array('data' => $pname, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->sasaran, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	if (user_access('program tambah')) {
		$btn .= l('Baru', 'apbd/program/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	if (user_access('program pencarian'))	{
		$btn .= l('Cari', 'apbd/program/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	}
    $output .= "<div id='fl_filter'>" . drupal_get_form ('program_filter_form') . "</div>" .  $btn . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

function program_filter_form() {
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pilihan Data',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,        
    );
    $filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$tahun = arg(3);		
		$u = arg(4);
		
	} else {
		$tahun = variable_get('apbdtahun', 0);
	}	

	$tahunopt = array();
	$tahunopt['2015'] = '2015';
	$tahunopt['2016'] = '2016';
	$tahunopt['2017'] = '2017';
	$tahunopt['2018'] = '2018';
	$tahunopt['2019'] = '2019';
	$form['formdata']['tahun']= array(
		'#type'         => 'select', 
		'#title'        => 'Tahun',
		'#options'	=> $tahunopt,
		'#width'         => 20, 
		'#default_value'=> $tahun, 
	);	
	
	$pquery = "select kodeu, urusansingkat from urusan order by kodeu";
	$pres = db_query($pquery);
	$urusan = array();
	$urusan[''] = '---SEMUA URUSAN---';
	
	while ($prow = db_fetch_object($pres)) {
		$urusan[$prow->kodeu] = $prow->kodeu . ' - ' . $prow->urusansingkat ;
	}
	
    $form['formdata']['flurusan'] = array (
        '#type' => 'select',
        '#title' => 'Urusan',
		'#options' => $urusan,
		'#default_value' => $u,
    );
	
	$form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Tampilkan',
    );
	
	return $form;
}
function program_filter_form_submit($form, &$form_state) {
	$tahun = $form_state['values']['tahun'];
	$urusan = $form_state['values']['flurusan'];
	
	drupal_goto("apbd/program/filter/" . $tahun . "/" . $urusan );
}


?>