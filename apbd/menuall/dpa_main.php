<?php
function dpa_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	$qlike='';
	$limit = 15;
	
	$kodesuk = '';
	$tahun = variable_get('apbdtahun', 0);
	$ntitle = 'DPA-SKPD';
    if ($arg) {
		switch($arg) {
			case 'show':
				//$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				$qlike = sprintf(" and lower(k.kegiatan) like lower('%%%s%%') ", db_escape_string(arg(3)));	
				//drupal_set_message(arg(4));
				break;
			case 'filter':
				$nntitle ='';
				$kodeuk = arg(3);
				$sumberdana = arg(4);
				$kodesuk = arg(5);
				$jenis = arg(6);
				$statustw = arg(7);

				break;

			default:
				drupal_access_denied();
				break;
		}
	} else {
		$tahun = variable_get('apbdtahun', 0);
		$sumberdana = $_SESSION['sumberdana'];
		$jenis = $_SESSION['jenis'];
		

	}

	if (isSuperuser()) {
		$kodeuk = $_SESSION['kodeuk'];
		if ($kodeuk == '') 	$kodeuk = '00';
		
		
	} else {
		$kodeuk = apbd_getuseruk();
		if (isUserKecamatan())
			$kodesuk = apbd_getusersuk();
		else
			$kodesuk = $_SESSION['kodesuk'];
	}	
	if (isSuperuser()) {
		if ($kodeuk !='00') {
			$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
			$pquery = sprintf("select kodeuk, namasingkat from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk));
			$presult = db_query($pquery);
			if ($data=db_fetch_object($presult)) {
				$ntitle .= ' ' . $data->namasingkat;
			}
		} 
		$adminok = true;
							
	} else {
		$qlike .= sprintf(' and k.plafon>0 and k.kodeuk=\'%s\' ', $kodeuk);
		if ($kodesuk != '') {
			$qlike .= sprintf(' and (k.kodesuk=\'%s\' ', $kodesuk);
			$qlike .= " or k.kodesuk='')";
		}
		
		$adminok = false;
	}



	//STATUS TW
	if ($statustw=='sudah') {
		$qlike .= sprintf(' and k.total>0 and (k.total=(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	} elseif ($statustw=='belum') {
		$qlike .= sprintf(' and k.total>0 and (k.total>(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	}
	

	//STATUS GAJI
	if ($jenis=='gaji') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=0 ');
	} elseif ($jenis=='langsung') {
		$qlike .= sprintf(' and k.jenis=2 ');
	} elseif ($jenis=='ppkd') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=1 ');
	}
	
	//SUMBER DANA
	if ($sumberdana != '') {
		$qlike .= sprintf(' and (k.sumberdana1=\'%s\'  or k.sumberdana2=\'%s\') ', $sumberdana, $sumberdana);
		$ntitle .= ' ' . $sumberdana;
	}
			
	
	//$output .= drupal_get_form('dpa_transfer_form');
	$output .= drupal_get_form('dpa_main_form');
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #1', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #2', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #3', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #4', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Tgl DPA', 'width' => '110px', 'valign'=>'top'),
			array('data' => '', 'width' => '220px', 'valign'=>'top'),
		);
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #1', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #2', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #3', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #4', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Tgl DPA', 'width' => '110px', 'valign'=>'top'),
			array('data' => '', 'width' => '120px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by koderesmi';
    }
	
	$customwhere = sprintf(' and k.tahun=%s ', $tahun);
	//if (!isSuperuser()) {
	//	$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);	
	//}	
    $where = ' where k.inaktif=0 ' . $customwhere . $qlike ;

	//drupal_set_message($where);
	$pquery = 'select sum(total) jumlahx from {kegiatanskpd} k ' . $where;
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ntitle .= ', Jumlah Anggaran : ' . apbd_fn($data->jumlahx);
	
	drupal_set_title($ntitle);	
	
	//$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis,
	//		k.total,u.namasingkat, k.adminok,  k.tw1, k.tw2, k.tw3, k.tw4, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanskpd} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) " . $where;

	$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis,
			k.total,u.namasingkat, k.adminok,  k.tw1, k.tw2, k.tw3, k.tw4, dn.btltgl, dn.bltgl, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanskpd} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) inner join {dpanomor} dn on u.kodeuk=dn.kodeuk " . $where;
			
	//$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanskpd} k" . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	$fcountsql = $countsql;
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);

	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//$batas = mktime(20, 0, 0, 6, 16, 2015) ;
	//$sekarang = time () ;
	//$selisih =($batas-$sekarang) ;
	$allowedit = true;		//(($selisih>0) || (isSuperuser()));
	
	//CEK TAHUN
	//$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
    
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
			
			//$strperpanjangan = '';
			//if ($data->dispensasi) $strperpanjangan = ' ***/Perpanjangan RKA\***';
			
			if (user_access('kegiatanskpd edit')) {
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('html' =>TRUE));
				$kegname = $data->kegiatan;
			} else {
				$kegname = $data->kegiatan;
			}


			//$editlink .= "&nbsp;" .  l('Triwulan', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg, array('html'=>TRUE));
			//Cetak
			
			$tanggal = '';
			if ($data->total==($data->tw1+$data->tw2+$data->tw3+$data->tw4)) {
				$editlink .= "&nbsp;" . l('Sampul', 'apbd/kegiatanskpd/print/' . $data->kodekeg . '/10/dpa/pdf/sampul' , array('html'=>TRUE)) ;
				if ($data->jenis==1) {
					$editlink .= "&nbsp;" . l('Cetak(2.1)', 'apbd/kegiatanskpd/print/' . $data->kodekeg . '/10/dpa' , array('html'=>TRUE)) ;
					$tanggal = $data->btltgl;
				} else {
					$editlink .= "&nbsp;" . l('Cetak(2.2.1)', 'apbd/kegiatanskpd/print/' . $data->kodekeg . '/10/dpa' , array('html'=>TRUE)) ;
					$tanggal = $data->bltgl;
				}
			} else
				$editlink .= "&nbsp;" . 'Sampul' . "&nbsp;" . 'Cetak';
				
            $no++;
			
			if (isSuperuser()) { 
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw1), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw2), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw3), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw4), 'align' => 'right', 'valign'=>'top'),
					array('data' => $tanggal, 'align' => 'left', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw1), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw2), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw3), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw4), 'align' => 'right', 'valign'=>'top'),
					array('data' => $tanggal, 'align' => 'left', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),

				);
			}
		}
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";

	/*if ($kodeuk != '00') {
		$btn .= l('Sampul Depan', 'apbd/kegiatanskpd/print/' . $kodeuk . '/10/dpa/pdf/sampuld', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

		$btn .= "&nbsp;" . l('Ringkasan APBD (DPA-SKPD)', 'apbd/laporan/rka/ringkasananggaran/' . $kodeuk . '/3/10/dpa', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
		
		$btn .= "&nbsp;" . l('Sampul DPA Pendapatan', 'apbd/kegiatanskpd/print/' . $kodeuk . '/10/dpa/pdf/sampulp' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

		$btn .= "&nbsp;" . l('Pendapatan (DPA-SKPD 1)', 'apbd/pendapatan/print/' . $kodeuk . '/10/dpa' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

		$btn .= "&nbsp;" . l('Rekap Belanja Langsung (DPA-SKPD 2.2)', 'apbd/laporan/rka/rekapaggblprogramtw/' . $kodeuk . '/10/dpa' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	}*/
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanskpd/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function dpa_main_form() {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilihan Data',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	$filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$kodeuk = arg(3);
		$sumberdana = arg(4);
		$kodesuk = arg(5);
		$jenis = arg(6);
		$statustw = arg(7);
		
	} else {
		$sumberdana = $_SESSION['sumberdana'];
		$statustw = $_SESSION['statustw'];	
		$jenis = $_SESSION['jenis'];	

		if (isSuperuser()) 
			$kodeuk = $_SESSION['kodeuk'];
		else
			$kodesuk = $_SESSION['kodesuk'];
	}
		   
	if (!isSuperuser()) {
		$typeuk = 'hidden';
		$kodeuk = apbd_getuseruk();
		
		$typesuk ='select';

		$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf('select kodesuk, namasuk from {subunitkerja} where kodeuk=\'%s\' order by kodesuk', $kodeuk);
		
		//drupal_set_message($pquery);
		
		$pres = db_query($pquery);
		$subskpd = array();
		$subskpd[''] = '- Pilih Bidang -';
		while ($data = db_fetch_object($pres)) {
			$subskpd[$data->kodesuk] = $data->namasuk;
		}

		if (isUserKecamatan()) {
			$typesuk='hidden';
			$kodesuk = apbd_getusersuk();
		} else
			$typesuk='select';
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		
		$typeuk='select';
		$typesuk='hidden';
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => $typeuk, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
		'#weight' => 2,
	);

	$form['formdata']['kodesuk']= array(
		'#type'         => $typesuk, 
		'#title'        => 'Bidang/Bagian',
		'#options'		=> $subskpd,
		//'#description'  => 'kodesuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodesuk, 
		'#weight' => 3,
	); 
	
	$pquery = "select sumberdana from {sumberdanalt} order by nomor" ;
	$pres = db_query($pquery);
	$sumberdanaotp = array();
	$sumberdanaotp[''] = '- SEMUA -';
	while ($data = db_fetch_object($pres)) {
		$sumberdanaotp[$data->sumberdana] = $data->sumberdana;
	}
	$form['formdata']['sumberdana']= array(
		'#type'         => 'select', 
		'#title'        => 'Sumber Dana', 
		'#options'		=> $sumberdanaotp,
		'#width'         => 30, 
		'#default_value'=> $sumberdana, 
		'#weight' => 4,
	);

	$form['formdata']['jenis']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis'), 
		'#default_value' => $jenis,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'gaji' => t('Gaji'), 	
			 'langsung' => t('Langsung'),
			 'ppkd' => t('PPKD'),	
		   ),
		'#weight' => 5,		
	);	
	
	$form['formdata']['ssj'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 5,
	);		
 	
	
	$form['formdata']['statustw']= array(
		'#type' => 'radios', 
		'#title' => t('Tri Wulan'), 
		'#default_value' => $statustw,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'sudah' => t('Sudah'), 	
			 'belum' => t('Belum'),	
		   ),
		'#weight' => 8,		
	);		
	$form['formdata']['ss2'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 9,
	);		
	

	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 12
	);
	
	return $form;
}
function dpa_main_form_submit($form, &$form_state) {
	
	$sumberdana = $form_state['values']['sumberdana'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$statustw = $form_state['values']['statustw'];
	$jenis = $form_state['values']['jenis'];
	
	$tahun= $form_state['values']['tahun'];

	$_SESSION['sumberdana'] = $sumberdana;
	$_SESSION['statustw'] = $statustw;
	$_SESSION['jenis'] = $jenis;
	
	if (isSuperuser()) 
		$_SESSION['kodeuk'] = $kodeuk;
	else
		$_SESSION['kodesuk'] = $kodesuk;
	
	$uri = 'apbd/belanjadpa/filter/' . $kodeuk . '/' . $sumberdana . '/' . $kodesuk . '/'. $jenis . '/' . $statustw;
	drupal_goto($uri);
	
}

?>