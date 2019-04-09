<?php
function unitkerja_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
 	drupal_add_js('files/js/kegiatanlt.js');
	switch($arg) {
		case 'show':
			$qlike = " and lower(namauk) like lower('%%%s%%')";
			$_SESSION['kodeu'] = '';
			break;
		case 'filter':
			$kodeu = arg(3);
			$_SESSION['kodeu'] = '';
			if (strlen($kodeu)>0) {
				$_SESSION['kodeu'] = $kodeu;
				$qlike .= sprintf(" and kodeu='%s' ", db_escape_string($kodeu));
			}
			break;
		default:
			$_SESSION['kodeu'] = '';
			break;
		
	}

	//dispensasirenja
	$header = array (
		array('data' => 'No', 'width' => '10px'),
		array('data' => 'Kode', 'field'=> 'kodedinas', 'width' => '50px'),
		array('data' => 'Nama', 'field'=> 'namauk'),
		array('data' => 'Pimpinan', 'field'=> 'pimpinannama'),
		array('data' => 'Disp. Pendapatan', 'width' => '120px'),
		array('data' => 'Disp. Belanja', 'width' => '110px'),
		array('data' => 'Disp. Revisi', 'width' => '90px'),
		array('data' => '', 'width' => '90px'),
	);
		
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodedinas';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = '';
	if (!isSuperuser()) {
		$customwhere = " and kodeuk='" . apbd_getuseruk() . "' ";
	}
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select kodeuk, namauk, namasingkat, dispensasibelanja, dispensasipendapatan, dispensasirevisi, pimpinannama, pimpinanjabatan, pimpinanpangkat, pimpinannip, iskecamatan, kodedinas from {unitkerja} ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
	
	//drupal_set_message($sql);
	
    $limit = 30;
    $countsql = "select count(*) as cnt from {unitkerja} " . $where;
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
			$editlink="";

			//if (user_access('unitkerja edit'))
			//	$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>&nbsp;", 'apbd/unitkerja/edit/' . $data->kodeuk, array('html'=>TRUE));
			//if (user_access('unitkerja penghapusan'))
            //    $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/unitkerja/delete/' . $data->kodeuk, array('html'=>TRUE));
            $buka = "<img src='/files/buka.png' >";
			$tutup = "<img src='/files/kunci.png'>";
			$kunci= array($tutup,$buka);
			
			if (user_access('unitkerja penghapusan'))
                $editlink = l('Bidang ', 'subunitkerja/' . $data->kodeuk, array('html'=>TRUE));
				$editlink .= l('Hapus', 'apbd/unitkerja/delete/' . $data->kodeuk, array('html'=>TRUE));

			$skpd = l($data->namauk, 'apbd/unitkerja/edit/' . $data->kodeuk, array('html'=>TRUE));	
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right'),
                //array('data' => $data->kodeuk, 'align' => 'left'),
                array('data' => $data->kodedinas, 'align' => 'left'),
                //array('data' => $data->namauk, 'align' => 'left'),
                array('data' => $skpd, 'align' => 'left'),
                //array('data' => $data->namasingkat, 'align' => 'left'),
                array('data' => $data->pimpinannama, 'align' => 'left'),
                array('data' => $kunci[$data->dispensasipendapatan], 'align' => 'center'),
                array('data' => $kunci[$data->dispensasibelanja], 'align' => 'center'),
                array('data' => $kunci[$data->dispensasirevisi], 'align' => 'center'),
                array('data' => $editlink, 'align' => 'right'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	if (user_access('unitkerja tambah')) {
		$btn .= l('Baru', 'apbd/unitkerja/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	if (user_access('unitkerja pencarian'))	{
		$btn .= l('Cari', 'apbd/unitkerja/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	}
	
    //$output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	$output .= $btn . "<div id='fl_filter'>" . drupal_get_form ('unitkerja_filter_form') . "</div>" . theme_box('', theme_table($header, $rows)) . $btn;

	
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

function unitkerja_filter_form() {
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Filter Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	
	$type='select';
	if (isSuperuser()) {
	
	
		$u = arg(3);
		$pquery = "select kodeu, urusansingkat from urusan order by kodeu";
		$pres = db_query($pquery);
		$urusan = array();
		$urusan[''] = '---SEMUA URUSAN---';
		
		while ($prow = db_fetch_object($pres)) {
			$urusan[$prow->kodeu] = $prow->urusansingkat ;
		}
		
		$form['formdata']['flurusan'] = array (
			'#type' => 'select',
			'#title' => 'Urusan',
			'#options' => $urusan,
			'#default_value' => $u,
		);
		
		//$form['formdata']['submit'] = array (
		//   '#type' => 'submit',
		//    '#value' => 'Filter',
		//);
		
		//return $form;
	}
}

function unitkerja_filter_form_submit($form, &$form_state) {
	$urusan = $form_state['values']['flurusan'];
	
	drupal_goto("apbd/unitkerja/filter/" . $urusan );
}
?>