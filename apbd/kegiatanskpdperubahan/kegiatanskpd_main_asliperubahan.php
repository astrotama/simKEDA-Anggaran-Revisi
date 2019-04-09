<?php
function kegiatanskpd_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 15;
	
	$kodesuk = '';
	$tahun = variable_get('apbdtahun', 0);
	$ntitle = 'Belanja';
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
				$statusisi = arg(5);
				$kodesuk = arg(6);
				$statustw = arg(7);
				$statusinaktif = arg(8);
				$jenis = arg(9);

				break;

			case 'excel':
				$kodeuk = arg(3);
				kegiatanskpd_exportexcel($kodeuk);
				break;
				
			default:
				drupal_access_denied();
				break;
		}
	} else {
		$tahun = variable_get('apbdtahun', 0);
		$sumberdana = $_SESSION['sumberdana'];
		$statusisi = $_SESSION['statusisi'];
		$statustw = $_SESSION['statustw'];	
		$statusinaktif = $_SESSION['statusinaktif'];
		$jenis = $_SESSION['jenis'];
		
		/*
		if (isSuperuser() || isUserview()) {
			$kodeuk = $_SESSION['kodeuk'];
			if ($kodeuk == '') 	$kodeuk = '00';
			
			
		} else {
			$kodeuk = apbd_getuseruk();
			if (isUserKecamatan())
				$kodesuk = apbd_getusersuk();
			else
				$kodesuk = $_SESSION['kodesuk'];
		}
		*/
	}
	
	
	if(isUserview()){
		$url='apbd/belanjadppa';
		drupal_goto($url);
	}
	if (isSuperuser() || isUserview()) {
		$kodeuk = $_SESSION['kodeuk'];
		if ($kodeuk == '') 	$kodeuk = '00';
		
		
	} else {
		$kodeuk = apbd_getuseruk();
		if (isUserKecamatan())
			$kodesuk = apbd_getusersuk();
		else
			$kodesuk = $_SESSION['kodesuk'];
	}	
	if (isSuperuser() || isUserview()) {
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
		$qlike .= sprintf(' and k.inaktif=0 and k.plafon>0 and k.kodeuk=\'%s\' ', $kodeuk);
		if ($kodesuk != '') {
			$qlike .= sprintf(' and (k.kodesuk=\'%s\' ', $kodesuk);
			$qlike .= " or k.kodesuk='')";
		}
		
		$adminok = false;
	}

	//keg cari
	//if (strlen($kegcari)>0) {
	//	$qlike .= sprintf(" and lower(k.kegiatan) like lower('%%%s%%') ", db_escape_string($kegcari));
	//}
	
	//STATUS PENGISIAN
	if ($statusisi=='sudah') {
		$qlike .= sprintf(' and (k.total=k.plafon) and (k.plafon>0)');
	} elseif ($statusisi=='sebagian') {
		$qlike .= sprintf(' and (k.total>0) and (k.total<k.plafon) and (k.plafon>0) ');
	} elseif ($statusisi=='belum') {
		$qlike .= sprintf(' and (k.total=0 or k.total is null) and (k.plafon>0) ');
	} elseif ($statusisi=='lebih') {
		$qlike .= sprintf(' and (k.total>k.plafon) and (k.plafon>0) ');
	}

	//STATUS TW
	if ($statustw=='sudah') {
		$qlike .= sprintf(' and k.total>0 and (k.total=(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	} elseif ($statustw=='belum') {
		$qlike .= sprintf(' and k.total>0 and (k.total>(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	}
	

	//STATUS INAKTIF
	if ($statusinaktif=='0') {
		$qlike .= sprintf(' and k.inaktif=0 ');
	} elseif ($statusinaktif=='1') {
		$qlike .= sprintf(' and (k.inaktif=1 or k.plafon=0) ');
	} elseif ($statusinaktif=='2') {
		$qlike .= sprintf(' and k.dispensasi=1 ');
	}

	//STATUS INAKTIF
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
			
	
	//$output .= drupal_get_form('kegiatanskpd_transfer_form');
	$output .= drupal_get_form('kegiatanskpd_main_form');
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Target', 'field'=> 'target', 'valign'=>'top'),
			array('data' => 'Sumberdana', 'field'=> 'sumberdana', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Penetapan', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Perubahan', 'field'=> 'totalp','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	}
	
	else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Target', 'field'=> 'target', 'valign'=>'top'),
			array('data' => 'Sumberdana', 'field'=> 'sumberdana', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Penetapan', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Perubahan', 'field'=> 'totalp', 'width' => '90px','valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
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
    $where = ' where true' . $customwhere . $qlike ;

	//drupal_set_message($where);
	$pquery = 'select sum(total) jumlahx from {kegiatanperubahan} k ' . $where;
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ntitle .= ', Jumlah Anggaran : ' . apbd_fn($data->jumlahx);
	
	drupal_set_title($ntitle);	
	
	$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis, k.lokasi,k.programtarget,k.total,k.totalp,
			k.plafon,u.namasingkat, k.isppkd,  k.adminok, k.sumberdana1 sumberdana, k.inaktif,k.dispensasi, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanperubahan} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) " . $where;
	//$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanperubahan} k" . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	$fcountsql = $countsql;
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);

	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//$batas = mktime(20, 0, 0, 6, 16, 2015) ;
	//$sekarang = time () ;
	//$selisih =($batas-$sekarang) ;
	$allowedit = true;		//(($selisih>0) || (isSuperuser() || isUserview()));
	
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

			//PLAFON Penetapan
			$plafon_lama = 0;
			$sql_pl = "select plafon from {kegiatanskpd} where kodekeg='" . $data->kodekeg . "'";
			$res_pl = db_query($sql_pl);
			if ($data_pl=db_fetch_object($res_pl)) {
				$plafon_lama = $data_pl->plafon;
			}
			
			$editlink = '';
			
			if (isSuperuser()) {
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpdperubahan/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				$kegname = l($data->kegiatan, 'apbd/kegiatanskpdperubahan/edit/' . $data->kodekeg , array('html' =>TRUE));
			} else {
				$kegname = $data->kegiatan;
			}
			$indexdpa=$data->periode;
			//$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/program/edit/' . $data->kodepro, array('html'=>TRUE));
			//$progname = l($data->program, 'apbd/program/edit/' . $data->kodepro , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
			

			if ($data->total==0) {
				//$editlink =l('Rekening', 'apbd/kegiatanskpdperubahan/rekening/edit/' . $data->kodekeg, array('html'=>TRUE));
				$editlink = l('Rekening', 'apbdkegrekeningrevisi/' . $data->kodekeg , array('html'=>TRUE));

			} else {
				//$editlink =l('Rekening', 'apbd/kegiatanskpdperubahan/rekening/' . $data->kodekeg, array('html'=>TRUE));
				$editlink = l('Rekening', 'apbd/kegiatanrevisi/rekening/' . $data->kodekeg  . '/0', array('html'=>TRUE));
				
			}

			
			if (isSuperuser()) {
				$editlink .= "&nbsp;" . l('Edit', 'apbd/kegiatanskpdperubahan/editadmin/' . $data->kodekeg, array('html'=>TRUE));
				$editlink .= "&nbsp;" . l('Hapus', 'apbd/kegiatanskpdperubahan/delete/' . $data->kodekeg, array('html'=>TRUE));
				//$editlink .= "&nbsp;" . l('Cetak', 'apbd/kegiatanskpd/printperubahan/' . $data->kodekeg . '/10/dpa' , array('html'=>TRUE)) ;
				
			} 
			if($indexdpa==1)
			{
				$editlink .= "&nbsp;" . l('Cetak', 'apbd/kegiatanskpd/print/' . $data->kodekeg . '/10/dpa' , array('html'=>TRUE)) ;
			}
			else
			{
				$editlink .= "&nbsp;" . l('Cetak', 'apbd/kegiatanskpd/printperubahan/' . $data->kodekeg . '/10/dpa' , array('html'=>TRUE)) ;
			}
			
            $no++;
			
			if ($data->total > $data->plafon)
				$limit = "<img src='/files/limit.png'>";
			else
				$limit = '';

			if ($data->inaktif) 
				//$inaktif = 'x';
				$inaktif = "<img src='/files/inaktif.png'>";
			
			else {
				if ($data->dispensasi) 
					$inaktif = "<img src='/files/revisi16.jpg'>";
				else
					$inaktif ='';
			}
			
			//group1.png
			if ($plafon_lama==$data->plafon)
				$str_plafon = "<img src='/files/icon-still.png'>";
			else if ($plafon_lama>$data->plafon)
				$str_plafon = "<img src='/files/icon-down.png'>";
			else
				$str_plafon = "<img src='/files/icon-up.png'>";
			
			if (isSuperuser()) { 
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $inaktif, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $limit, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $str_plafon, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->totalp), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $inaktif, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $limit, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $str_plafon, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->totalp), 'align' => 'right', 'valign'=>'top'),
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
	if ($allowedit)
		//
		if (isSuperuser()) {
			//$btn .= l('Baru', 'apbd/kegiatanskpdperubahan/editadmin/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
		}
 
	$status = 0;
	$record = 0;
	if (isSuperuser())
		//$btn .= l('Baru', 'apbd/kegiatanrevisi/editnew/81/3/2/1', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;" ;
		$btn .= l('Baru', 'apbd/kegiatanskpdperubahan/editadmin', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;" ;
		
	$btn .= l('Cetak', 'apbd/laporan/rka/rekapaggbelanja/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

    if(isSuperuser()){$btn .= "&nbsp;" . l("Cari", 'apbd/kegiatanskpdperubahan/find/' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;}
	
	if (isSuperuser() || isUserview()) {
		$btn .= "&nbsp;" . l('Simpan Excel', 'apbd/kegiatanskpdperubahan/excel/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	
	}
	
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanskpdperubahan/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpdperubahan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function kegiatanskpd_main_form() {
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
		$statusisi = arg(5);
		$kodesuk = arg(6);
		$statustw = arg(7);
		$statusinaktif = arg(8);
		$jenis = arg(9);
		
	} else {
		$sumberdana = $_SESSION['sumberdana'];
		$statusisi = $_SESSION['statusisi'];
		$statustw = $_SESSION['statustw'];	
		$statusinaktif = $_SESSION['statusinaktif'];	
		$jenis = $_SESSION['jenis'];	

		if (isSuperuser() || isUserview()) 
			$kodeuk = $_SESSION['kodeuk'];
		else
			$kodesuk = $_SESSION['kodesuk'];
	}
	//drupal_set_message($filter);

	//if (isset($kodeuk)) {
	//    $form['formdata']['#collapsed'] = TRUE;
	//    //if (isUserKecamatan())
	//    //    if ($kodeuk != apbd_getuseruk())
	//    //        $form['formdata']['#collapsed'] = FALSE;
	//}
		   
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
 	
	$form['formdata']['statusisi']= array(
		'#type' => 'radios', 
		'#title' => t('Pengisian'), 
		'#default_value' => $statusisi,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'sudah' => t('Selesai'), 	
			 'sebagian' => t('Sebagian'),
			 'belum' => t('Belum'),	
			 'lebih' => t('Lebih Plafon'),	
		   ),
		'#weight' => 6,		
	);	

	$form['formdata']['ss1'] = array (
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
	
	if (isSuperuser() || isUserview()) {
		$statusinaktiftype = 'radios';
	} else {
		$statusinaktiftype = 'hidden';
		$statusinaktif = '0';
	}
	
	$form['formdata']['statusinaktif']= array(
		'#type' => $statusinaktiftype, 
		'#title' => t('Status'), 
		'#default_value' => $statusinaktif,
		'#options' => array(	
			 '' => t('Semua'), 	
			 '0' => t('Aktif'),	
			 '1' => t('Inaktif'), 	
			 '2' => t('Perpanjang'),
		   ),
		'#weight' => 10,		
	);		
	
	$form['formdata']['ss'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 11,
	);		
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 12
	);
	
	return $form;
}
function kegiatanskpd_main_form_submit($form, &$form_state) {
	$sumberdana = $form_state['values']['sumberdana'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$statusisi = $form_state['values']['statusisi'];
	$statustw = $form_state['values']['statustw'];
	$statusinaktif = $form_state['values']['statusinaktif'];
	$jenis = $form_state['values']['jenis'];
	
	$tahun= $form_state['values']['tahun'];

	$_SESSION['sumberdana'] = $sumberdana;
	$_SESSION['statusisi'] = $statusisi;
	$_SESSION['statustw'] = $statustw;
	$_SESSION['statusinaktif'] = $statusinaktif;
	$_SESSION['jenis'] = $jenis;
	
	if (isSuperuser() || isUserview()) 
		$_SESSION['kodeuk'] = $kodeuk;
	else
		$_SESSION['kodesuk'] = $kodesuk;
	
	$uri = 'apbd/kegiatanskpdperubahan/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis;
	drupal_goto($uri);
	
}

function kegiatanskpd_transfer_form() {
	$form['formtransfer'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Transfer Data Dari MUSRENBANGCAM',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	$pquery = "select kodeuk, namauk, namasingkat from {unitkerja} where aktif=1 and iskecamatan=1 order by namasingkat" ;
	$pres = db_query($pquery);
	$dinas = array();
	$kodeuk = apbd_getuseruk();
	$typekodeuk = 'select';
	if (!isSuperuser())
		$typekodeuk='hidden';
	//$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->namasingkat;
	}
	
	$form['formtransfer']['kodeuk']= array(
		'#type'         => 'select', 
		//'#title'        => 'Kecamatan',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk,
		'#attributes'	=> array('style' => 'margin-left: 20px;'),
	); 
	
	

	$musrenbang = l("<div class='boxp' >MUSRENBANGCAM</div>", 'apbd/kegiatancam', array('html'=> true));
	$renja= l("<div class='boxp'>RENJA SKPD</div>", 'apbd/kegiatanskpdperubahan', array('html'=>true));
	$proses = "<div class='boxproses' id='boxproses'><a href='#transfercamskpd' class='btn_blue' style='color: white;'>---Transfer---></a></div>";
	$document = "<div style='height: 50px; text-align:center;'>$musrenbang $proses $renja<div style='clear:both;'></div></div>";
	$form['formtransfer']['keterangan'] = array (
		'#type' => 'markup',
		'#value' => $document,
		'#weight' => 1,
	);
	return $form;
}

function kegiatanskpd_exportexcel($kodeuk) {
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '640M');

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'files/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("SIPKD Anggaran Online")
							 ->setLastModifiedBy("SIPKD Anggaran Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SIPKD Anggaran Online.")
							 ->setKeywords("office 2007 SIPKD Anggaran Online openxml php")
							 ->setCategory("SIPKD Anggaran Analisa");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'Fungsi')
			->setCellValue('B' . $row ,'Urusan')
            ->setCellValue('C' . $row ,'SKPD')
            ->setCellValue('D' . $row ,'Program')
			->setCellValue('E' . $row ,'Kegiatan')
			->setCellValue('F' . $row ,'Akun Utama')
			->setCellValue('G' . $row ,'Akun Kelompok')
			->setCellValue('H' . $row ,'Akun Jenis')
			->setCellValue('I' . $row ,'Akun Obyek')
			->setCellValue('J' . $row ,'Akun Rincian')
			->setCellValue('K' . $row ,'Jumlah');

//Open data							 
//$customwhere = sprintf(' and k.tahun=%s ', variable_get('apbdtahun', 0));
if ($kodeuk!='00') {
	$customwhere .= sprintf(' and kegiatanskpd.kodeuk=\'%s\' ', $kodeuk);	
}	
$where = ' where inaktif=0 ' . $customwhere;
	
$sql = "SELECT programurusanfungsi.namafungsi, programurusanfungsi.namaurusan, CONCAT_WS(' - ', unitkerja.kodedinas, unitkerja.namasingkat) skpd, programurusanfungsi.namaprogram, kegiatanskpd.kegiatan, rekeninglengkap.akunutama, rekeninglengkap.akunkelompok, rekeninglengkap.akunjenis, rekeninglengkap.akunobyek, rekeninglengkap.akunrincian,
anggperkeg.jumlah FROM unitkerja inner join kegiatanskpd ON unitkerja.kodeuk=kegiatanskpd.kodeuk INNER JOIN programurusanfungsi ON kegiatanskpd.kodepro=programurusanfungsi.kodepro INNER JOIN anggperkeg ON anggperkeg.kodekeg=kegiatanskpd.kodekeg INNER JOIN rekeninglengkap ON rekeninglengkap.kodero=anggperkeg.kodero " . $where;
$result = db_query($sql);
while ($data = db_fetch_object($result)) {
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, $data->namafungsi)
				->setCellValue('B' . $row, $data->namaurusan)
				->setCellValue('C' . $row, $data->skpd)
				->setCellValue('D' . $row, $data->namaprogram)
				->setCellValue('E' . $row, $data->kegiatan)
				->setCellValue('F' . $row, $data->akunutama)
				->setCellValue('G' . $row, $data->akunkelompok)
				->setCellValue('H' . $row, $data->akunjenis)
				->setCellValue('I' . $row, $data->akunobyek)
				->setCellValue('J' . $row, $data->akunrincian)
				->setCellValue('K' . $row, $data->jumlah);
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Analisis Belanja SKPD');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'analisis_belanja_skpd_' . $kodeuk . '.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

?>