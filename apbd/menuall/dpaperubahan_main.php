<?php
function dpaperubahan_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	$qlike='';
	$limit = 15;
	
	$kodesuk = '';
	$tahun = variable_get('apbdtahun', 0);
	$ntitle = 'DPPA-SKPD';
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
				$jenisrevisi = arg(8);

				break;

			default:
				drupal_access_denied();
				break;
		}
	} else {
		$tahun = variable_get('apbdtahun', 0);
		$sumberdana = $_SESSION['sumberdana'];
		$jenis = $_SESSION['jenis'];
		$jenisrevisi = $_SESSION['jenisrevisi'];

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
		$qlike .= sprintf(' and k.totalp>0 and (k.totalp=(k.tw1p+k.tw2p+k.tw3p+k.tw4p)) ');
	} elseif ($statustw=='belum') {
		$qlike .= sprintf(' and k.totalp>0 and (k.totalp>(k.tw1p+k.tw2p+k.tw3p+k.tw4p)) ');
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
	
	//JENIS REVISI
	switch ($jenisrevisi) {
		case '1':
			$qlike .= ' and k.r_geser=1 ';
			break;
			
		case '2':
			$qlike .= ' and k.r_admin=1 ';
			break;
			
		case '3':
			$qlike .= ' and k.r_transfer=1 ';
			break;
	}
	
	//$output .= drupal_get_form('dpaperubahan_transfer_form');
	$output .= drupal_get_form('dpaperubahan_main_form');
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Penetapan', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Perubahan', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #1', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #2', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #3', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #4', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Tgl DPPA', 'width' => '110px', 'valign'=>'top'),
			array('data' => 'Ket', 'valign'=>'top'),
			array('data' => '', 'valign'=>'top'),
		);
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Penetapan', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Perubahan', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #1', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #2', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #3', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #4', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Tgl DPPA', 'width' => '110px', 'valign'=>'top'),
			array('data' => 'Ket', 'valign'=>'top'),
			array('data' => '', 'valign'=>'top'),
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
    $where = ' where k.inaktif=0 and k.periode=2 ' . $customwhere . $qlike ;

	//drupal_set_message($where);
	$pquery = 'select sum(totalp) jumlahx from {kegiatanperubahan} k ' . $where;
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ntitle .= ', Jumlah Anggaran : ' . apbd_fn($data->jumlahx);
	
	drupal_set_title($ntitle);	
	
	$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis,
			k.total,k.totalp,u.namasingkat, k.adminok,  k.tw1p, k.tw2p, k.tw3p, k.tw4p, dn.btltgl, dn.bltgl, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi, k.r_admin, k.r_geser, k.r_transfer from {kegiatanperubahan} k inner join {unitkerja} u on ( k.kodeuk=u.kodeuk) inner join {program} p on (k.kodepro = p.kodepro) inner join {dpanomor1} dn on u.kodeuk=dn.kodeuk " . $where;
			
	//$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
    //echo $fsql;
    $countsql = "select count(k.kodekeg) cnt from {kegiatanperubahan} k " . $where;
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
			
			/* 
			if (user_access('kegiatanskpd edit')) {
				//$kegname = l($data->kegiatan, 'apbd/kegiatanrevisi/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				$kegname = l($data->kegiatan, 'apbd/kegiatanrevisi/edit/' . $data->kodekeg , array('html' =>TRUE));
			} else {
				$kegname = $data->kegiatan;
			}
			*/
			$kegname = $data->kegiatan;

			//$editlink .= "&nbsp;" .  l('Triwulan', 'apbd/kegiatanrevisi/triwulan/' . $data->kodekeg, array('html'=>TRUE));
			//Cetak

			//$uri = 'apbd/kegiatanrevisi/printusulan/' . $id . '/'. $topmargin . '/rka/pdf/sampuldppa';

			//if ($data->triwulan)
			//	$editlink .= l('TW', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg . '/1', array('html'=>TRUE)) . "&nbsp;";
			//else
			//	$editlink .= 'TW'  . "&nbsp;";
			
			
			$tanggal = '';
			
			$ket = '';
			if ($data->r_admin) $ket = 'A;';
			if ($data->r_geser) $ket .= 'G;';
			if ($data->r_transfer) $ket .= 'T;';

			if ($data->jenis==1) {
				$tanggal = $data->btltgl;
			} else {
				$tanggal = $data->bltgl;
			}
			
			if ($data->totalp == ($data->tw1p+$data->tw2p+$data->tw3p+$data->tw4p)) {
				/*
				$editlink .= "&nbsp;" . l('Sampul', 'apbd/kegiatanrevisi/printusulan/' . $data->kodekeg . '/10/rka/pdf/sampuldppa' , array('html'=>TRUE)) ;
				if ($data->jenis==1) {
					$editlink .= "&nbsp;" . l('Cetak(2.1)', 'apbd/kegiatanrevisi/printusulan/' . $data->kodekeg . '/10/dpa/pdf' , array('html'=>TRUE)) ;
					$tanggal = $data->btltgl;
				} else {
					$editlink .= "&nbsp;" . l('Cetak(2.2.1)', 'apbd/kegiatanrevisi/printusulan/' . $data->kodekeg . '/10/dpa/pdf' , array('html'=>TRUE)) ;
					$tanggal = $data->bltgl;
				}
				*/
				
				$seribu = 1000;
				if ((($data->tw1p % $seribu)>0) or (($data->tw2p % $seribu)>0) or (($data->tw3p % $seribu)>0) or (($data->tw4p % $seribu)>0)) {
					$editlink = l('TW', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg . '/1/dpa', array('html'=>TRUE)) . "&nbsp;";
					$editlink .= 'Cetak';
					
				} else {
					$editlink = 'TW' . "&nbsp;";
					 
					//printusulan 
					//printperubahan
					//$editlink .= l('Cetak', 'apbd/kegiatanskpd/printperubahan/' . $data->kodekeg . '/10/dpa/', array('html'=>TRUE)) ;
					//if (isSuperuser())
						$editlink .= l('Cetak', 'apbd/kegiatanskpd/printperubahan/' . $data->kodekeg . '/10/dpa/', array('html'=>TRUE)) ;
					//else
					//	$editlink .= 'Cetak';
				}
				
			} else {
				$editlink = l('TW', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg . '/1/dpa', array('html'=>TRUE)) . "&nbsp;";
				$editlink .= 'Cetak';
			}
			
			//apbd/kegiatanskpd/printusulan/201603006001/10/dpa/pdf/sampuldppa
			
            $no++;
			$editlink .= "&nbsp;" . l('Sampul', 'apbd/kegiatanskpd/printusulan/' . $data->kodekeg . '/10/dpa/pdf/sampuldppa', array('html'=>TRUE)) ;;
			if (isSuperuser()) { 
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->totalp), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw1p), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw2p), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw3p), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw4p), 'align' => 'right', 'valign'=>'top'),
					array('data' => $tanggal, 'align' => 'left', 'valign'=>'top'),
					array('data' => $ket, 'align' => 'left', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->totalp), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw1p), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw2p), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw3p), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw4p), 'align' => 'right', 'valign'=>'top'),
					array('data' => $tanggal, 'align' => 'left', 'valign'=>'top'),
					array('data' => $ket, 'align' => 'left', 'valign'=>'top'),
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
	$btn = l('Sampul Depan', 'apbd/kegiatanskpd/print/' . $kodeuk . '/10/dpa/pdf/sampuld/perubahan/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	$btn .= "&nbsp;" . l('Ringkasan APBD (DPPA-SKPD)', 'apbd/laporan/rka/ringkasananggaran/' . $kodeuk . '/3/10/dpa', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	$btn .= "&nbsp;" . l('Rekap Belanja Langsung (DPPA-SKPD 2.2)', 'apbd/laporan/rka/rekapaggblprogramtw/' . $kodeuk . '/10/dpaperubahan', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	
	}*/
	
	/*
	if ($kodeuk != '00') {
		$btn .= l('Sampul Depan', 'apbd/kegiatanrevisi/print/' . $kodeuk . '/10/dpa/pdf/sampuld', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

		$btn .= "&nbsp;" . l('Ringkasan APBD (DPA-SKPD)', 'apbd/laporan/rka/ringkasananggaran/' . $kodeuk . '/3/10/dpa', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
		
		$btn .= "&nbsp;" . l('Sampul DPA Pendapatan', 'apbd/kegiatanrevisi/print/' . $kodeuk . '/10/dpa/pdf/sampulp' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

		$btn .= "&nbsp;" . l('Pendapatan (DPA-SKPD 1)', 'apbd/pendapatan/print/' . $kodeuk . '/10/dpa' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

		$btn .= "&nbsp;" . l('Rekap Belanja Langsung (DPA-SKPD 2.2)', 'apbd/laporan/rka/rekapaggblprogramtw/' . $kodeuk . '/10/dpa' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	}
	*/
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;
	//$output = theme_box('', theme_table($header, $rows));

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanrevisi/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanrevisi/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function dpaperubahan_main_form() {
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
		$jenisrevisi = arg(8);
		
	} else {
		$sumberdana = $_SESSION['sumberdana'];
		$statustw = $_SESSION['statustw'];	
		$jenis = $_SESSION['jenis'];	
		$jenisrevisi = $_SESSION['jenisrevisi'];

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
		$pquery = "select uk.kodedinas, uk.kodeuk, uk.namasingkat, uk.namauk from {unitkerja} uk inner join {kegiatanperubahan} kp on (uk.kodeuk=kp.kodeuk) where uk.aktif=1 and kp.periode=2 and kp.inaktif=0 order by kodedinas" ;
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
 	
	$form['formdata']['jenisrevisi']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis Revisi'), 
		'#default_value' => $jenisrevisi,
		'#options' => array(	
			 '0' => t('Semua'), 	
			 '1' => t('[1] Pergeseran'), 	
			 '2' => t('[2] Administrasi'), 	
			 '3' => t('[3] Dana Transfer'),	
			 '4' => t('[4] Darurat'),	
		   ),
		'#weight' => 6,		
	);	
	$form['formdata']['ss27'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 7,
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
function dpaperubahan_main_form_submit($form, &$form_state) {
	
	$sumberdana = $form_state['values']['sumberdana'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$statustw = $form_state['values']['statustw'];
	$jenis = $form_state['values']['jenis'];
	$jenisrevisi = $form_state['values']['jenisrevisi'];
	
	
	$tahun= $form_state['values']['tahun'];

	$_SESSION['sumberdana'] = $sumberdana;
	$_SESSION['statustw'] = $statustw;
	$_SESSION['jenis'] = $jenis;
	$_SESSION['jenisrevisi'] = $jenisrevisi;
	
	if (isSuperuser()) 
		$_SESSION['kodeuk'] = $kodeuk;
	else
		$_SESSION['kodesuk'] = $kodesuk;
	
	$uri = 'apbd/belanjadppa/filter/' . $kodeuk . '/' . $sumberdana . '/' . $kodesuk . '/'. $jenis . '/' . $statustw . '/' . $jenisrevisi;
	drupal_goto($uri);
	
}

?>