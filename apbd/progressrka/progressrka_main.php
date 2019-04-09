<?php
function progressrka_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatanlt.js');
	switch($arg) {
		case 'filter':
			$kelompok = arg(3);
			if (strlen($kelompok)>0) {
				$qlike .= sprintf(" and u.kelompok='%s' ", db_escape_string($kelompok));
			}
			break;
		default:
			$kelompok = '';
			break;
		
	}  

	
    $header = array (
        array('data' => 'No', 'width' => '10px'),
		array('data' => 'SKPD', 'field'=> 'kodedinas', 'valign'=>'top'),
		array('data' => 'Plafon', 'field'=> 'plafonnom', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'plafonjml', 'valign'=>'top'),
		array('data' => 'Belum', 'field'=> 'belum', 'valign'=>'top'),
		array('data' => 'Sebagian', 'field'=> 'sebagian', 'valign'=>'top'),
		array('data' => 'Selesai', 'field'=> 'selesai', 'valign'=>'top'),
		array('data' => 'Persen', 'field'=> 'persen', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by p.persen desc, p.selesai desc, p.sebagian desc, p.plafonjml desc, p.belum, p.plafonnom desc';
		
    }

    $where = ' where true' . $qlike ;

    $sql = 'select u.kodedinas, u.namasingkat, p.plafonjml, p.plafonnom, p.belum, p.sebagian, p.selesai, p.persen from {unitkerja} u inner join {progressrka} p on u.kodeuk=p.kodeuk ' . $where;
	
	//drupal_set_message($sql);
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 20;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {unitkerja} u " . $where;
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
			
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                array('data' => $data->kodedinas . ' - ' . $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data->plafonnom), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->plafonjml), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->belum), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->sebagian), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->selesai), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn2($data->persen), 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong', 'colspan'=>'3')
        );
    }
	$btn = "";
	//if (user_access('program pencarian'))	{
	//	$btn .= l('Cari', 'apbd/program/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	//}
	 
	//$uri = 'apbd/progressrka/print/' . $kelompok . '/'. $order . '/10'/pdf' ;
	$btn .= l('Cetak', 'apbd/progressrka/print/' . $kelompok . '/0/10/pdf', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	
	
    $output .= "<div id='fl_filter'>" . drupal_get_form ('progressrka_filter_form') . "</div>" .  $btn . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

function progressrka_filter_form() {
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pilihan Data',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,        
    );
    $filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$kelompok = arg(3);		
		//$order = arg(4);
		
	} else {
		$tahun = variable_get('apbdtahun', 0);
	}	

	$kelompokopt = array();
	$kelompokopt[''] = '-Semua-';
	$kelompokopt['0'] = 'Dinas/Badan/Kantor';
	$kelompokopt['1'] = 'Kecamatan';
	$kelompokopt['2'] = 'Puskesmas';
	$kelompokopt['3'] = 'SMP/SMA/SMK';
	$kelompokopt['4'] = 'UPT Disdikpora';
	$form['formdata']['kelompok']= array(
		'#type'         => 'select', 
		'#title'        => 'Kelompok',
		'#options'	=> $kelompokopt,
		'#width'         => 20, 
		'#default_value'=> $kelompok, 
		//'#weight' => 1,
	);	
	
	/*
	$form['formdata']['order']= array(
		'#type' => 'radios', 
		'#title' => t('Pengurutan'), 
		'#default_value' => $order,
		'#options' => array(	
			 '0' => t('Besar ke Kecil'), 	
			 '1' => t('Kecil ke Besar'),	
		   ),
		'#weight' => 2,		
	);
	*/
	
	$form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Tampilkan',
    );
	
	return $form;
}
function progressrka_filter_form_submit($form, &$form_state) {
	$kelompok = $form_state['values']['kelompok'];
	
	drupal_goto("apbd/progressrka/filter/" . $kelompok );
}


?>