<?php
function pembiayaan_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 20;
	$tahun = variable_get('apbdtahun', 0);

	$ntitle = 'Pembiayaan';
	
    if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(uraian) like lower('%%%s%%')";    
				break;
			case 'filter':
				$ntitle ='';
				$kodek = arg(3);
				
				break;

			case 'excel':
				break;

			default:
				drupal_access_denied();
				break;
		}
		
	} else {
		$kodek = $_SESSION['kodekpembiayaan'];
	}
		
	if ($kodek == '61') {
		$ntitle .= ' Penerimaan'; 
		$qlike = " and left(kodero,2)='61' ";
	} elseif ($kodek == '62') {
		$ntitle .= ' Pengeluaran';
		$qlike = " and left(kodero,2)='62' ";
	}
	
	//$output .= drupal_get_form('pembiayaan_transfer_form');
	$output .= drupal_get_form('pembiayaan_main_form');
	$header = array (
		array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'field'=> 'kegiatan', 'valign'=>'top'),
		array('data' => 'Uraian','width' => '500px', 'field'=> 'sasaran', 'valign'=>'top'),
		array('data' => 'Sebelumnya', 'field'=> 'jumlahsebelum', 'width'=>'90px', 'valign'=>'top'),
		array('data' => 'Anggaran', 'field'=> 'jumlah', 'width'=>'90px', 'valign'=>'top'),
		array('data' => '', 'width' => '40px', 'valign'=>'top'),
	);
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodero';
    }
	
	$customwhere = sprintf(' and tahun=%s ', $tahun);	
    $where = ' where true' . $customwhere . $qlike ;

	//drupal_set_message($where);			
	
	$sql = "select tahun,kodero,uraian,jumlah,jumlahsebelum,jumlahsesudah from {anggperda} " . $where;
	//$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
    
	//drupal_set_message( $fsql);
	
    $countsql = "select count(*) as cnt from {anggperda} " . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	$fcountsql = $countsql;
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);

	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//$batas = mktime(20, 0, 0, 6, 16, 2015) ;
	//$sekarang = time () ;
	//$selisih =($batas-$sekarang) ;
	$allowedit = true;		//(($selisih>0) || (isSuperuser()));
	
	//CEK TAHUN
	$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
    
    $no=0;
	$totalmasuk = 0;
	$totalkeluar = 0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';
			
			if (user_access('kegiatanrkpd edit')) {
				//$uraian = l($data->uraian, 'apbd/pembiayaan/edit/' . $data->kodero , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				//$uraian = l($data->uraian, 'apbdpembiayaan/' . $data->kodero, array('html'=>TRUE));
				$uraian = $data->uraian;
			} else {
				$uraian = $data->uraian;
			}

			if ($allowedit) {
				if (user_access('kegiatanrkpd penghapusan')) {
					//$editlink =l('Rekening', 'apbd/pembiayaan/subkegiatan/' . $data->kodekeg, array('html'=>TRUE));
					//$editlink =l('Detil', 'apbdpembiayaan/' . $data->kodero, array('html'=>TRUE))  . '&nbsp;';
					//$editlink .=l('Hapus', 'apbd/pembiayaan/delete/' . $data->kodero, array('html'=>TRUE));
				}
			}
            $no++;
			
			$rows[] = array (
				array('data' => $no, 'align' => 'right', 'valign'=>'top'),
				array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data->jumlahsebelum), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
				array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
			);
			
			if (substr($data->kodero,0,2)=='61')
				$totalmasuk += $data->jumlah;
			else
				$totalkeluar += $data->jumlah;
		}
    } else {
        $rows[] = array (
            array('data' => 'Akses/data error, hubungi administrator', 'colspan'=>'6')
        );
    }
	if ($no==0) {
		//$linknew = l('Rekening Baru', 'apbdpembiayaan/', array('html' =>TRUE));	
		$rows[] = array (
			array('data' => 'Rekening belum diisikan, klik ' . $linknew . ' untuk menambahkan.', 'colspan'=>'8')
		);
	}
	
	if ($kodek == '61') 
		$ntitle .= ', ' . apbd_fn($totalmasuk); 
	elseif ($kodek == '62')
		$ntitle .= ', ' . apbd_fn($totalkeluar); 
	else 
		$ntitle = 'Pembiayaan, ' . apbd_fn($totalmasuk) . ' (' . apbd_fn($totalkeluar) .  ')'; 
	
	drupal_set_title($ntitle);

	$btn = "";
	if ($allowedit)
		if (user_access('kegiatanrkpd tambah')) {
			//$btn .= l('Rekening Baru', 'apbdpembiayaan/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
		}

	$status = 0;
	$record = 0;
	//$btn .= l('Cetak', 'apbd/pembiayaan/print/' . $kodek, array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

	//$btn .= "&nbsp;" . l('Simpan Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	

    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanrkpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/pembiayaan/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanrkpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/pembiayaan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function pembiayaan_main_form() {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilihan Data',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	
	$filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$kodek = arg(3);		
	} else {
		$kodek = $_SESSION['kodekpembiayaan'];
	}
	//drupal_set_message($filter);

	//if (isset($kodeuk)) {
	//    $form['formdata']['#collapsed'] = TRUE;
	//    //if (isUserKecamatan())
	//    //    if ($kodeuk != apbd_getuseruk())
	//    //        $form['formdata']['#collapsed'] = FALSE;
	//}

	$form['formdata']['kodek']= array(
		'#type' => 'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kodek,
		'#options' => array(	
			 '' => t('Semua'), 	
			 '61' => t('Penerimaan'), 	
			 '62' => t('Pengeluaran'),	
		   ),
	);		
	$form['formdata']['ss'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan'
	);
	
	return $form;
}
function pembiayaan_main_form_submit($form, &$form_state) {
	$kodek= $form_state['values']['kodek'];
	$_SESSION['kodekpembiayaan'] = $kodek;

	$uri = 'apbd/pembiayaanr/filter/' . $kodek ;
	drupal_goto($uri);
	
}


function pembiayaan_exportexcel($tahun) {

exit;
}
?>