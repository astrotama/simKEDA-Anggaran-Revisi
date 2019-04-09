<?php
function kegiatanrevisi_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 15;
	$status = 0;
	$tahun = variable_get('apbdtahun', 0);
	$ntitle = 'Revisi Kegiatan';
    if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and k.status=0 and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
				$nntitle ='';
				$kodeuk = arg(3);
				$tipe = arg(4);
				$status = arg(5);
				

				break;

			case 'excel':
				kegiatanrevisi_exportexcel($tahun, $kodeuk);
				break;

			default:
				drupal_access_denied();
				break;
		}
	} else {
		$tahun = variable_get('apbdtahun', 0);

		//$qlike = ' and k.status=0 ';
		$tipe = $_SESSION['tiperevisi'];
		$status = $_SESSION['statusrevisi'];
		if ($status=='') $status=0;
		
		if (isSuperuser()) {
			$kodeuk = $_SESSION['kodeukrevisi'];	
			if ($kodeuk == '') 	$kodeuk = '00';
		} else
			$kodeuk = apbd_getuseruk();
	}

	if (isSuperuser()) {
		if ($kodeuk !='00') {
			$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
			$pquery = sprintf("select kodeuk, namasingkat from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk));
			$presult = db_query($pquery);
			if ($data=db_fetch_object($presult)) {
				$nntitle .= $data->namasingkat . ", ";
			}
		} 
							
	} 

	//TIPE REVISI
	if ($tipe != '') 
		$qlike .= sprintf(' and (k.tipe=\'%s\') ', $tipe);
	
	//Status
	$qlike .= sprintf(' and (k.status=\'%s\') ', $status);
	
	drupal_set_title($ntitle);	drupal_set_title($ntitle);
	
	//$output .= drupal_get_form('kegiatanrevisi_transfer_form');
	$output .= drupal_get_form('kegiatanrevisi_main_form');
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatanlama', 'valign'=>'top'),
			array('data' => 'Rev Nama', 'valign'=>'top', 'width' => '90px'),
			array('data' => 'Rev Program','valign'=>'top', 'width' => '100px'),
			array('data' => 'Rev Plafon', 'valign'=>'top', 'width' => '90px'),
			array('data' => 'Plafon Lama', 'field'=> 'plafonlama', 'width' => '100px', 'valign'=>'top'),
			array('data' => 'Plafon Baru','field'=> 'plafonbaru', 'width' => '90px',  'valign'=>'top'),
			array('data' => ' ', 'width' => '40px', 'valign'=>'top'),
		);
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatanlama', 'valign'=>'top'),
			array('data' => 'Rev Nama', 'valign'=>'top', 'width' => '90px'),
			array('data' => 'Rev Program', 'valign'=>'top', 'width' => '100px'),
			array('data' => 'Rev Plafon', 'valign'=>'top', 'width' => '90px'),
			array('data' => 'Plafon Lama', 'field'=> 'plafonlama', 'width' => '100px', 'valign'=>'top'),
			array('data' => 'Plafon Baru','field'=> 'plafonbaru', 'width' => '90px',  'valign'=>'top'),
			array('data' => ' ', 'width' => '40px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kegiatanlama';
    }
	
	$customwhere = sprintf(' and k.tahun=%s ', $tahun);
	if (!isSuperuser()) {
		//$kodeuk = apbd_getuseruk();
		$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);	
	}	
    $where = ' where true' . $customwhere . $qlike ;

	//drupal_set_message($where);			
	
	$sql = "select k.id, k.tipe, k.kodekeg,k.tipe,k.tahun,k.kodeuk,k.kegiatanlama,k.kegiatanbaru,
			k.plafonlama,k.plafonbaru,k.kodeprolama,k.kodeprobaru,u.namasingkat,k.status from {kegiatanrevisi} k inner join {unitkerja} u on ( k.kodeuk=u.kodeuk) " . $where;
			
	//$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
	
	//drupal_set_message($fsql);
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanrevisi} k" . $where;
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
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';

			if (user_access('kegiatanskpd edit')) {
				//$kegname = l($data->kegiatan, 'apbd/kegiatanrevisi/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				if ($data->tipe==0) {
					$kegname = l($data->kegiatanlama, 'apbd/kegiatanrevisi/edit/' . $data->id , array('html' =>TRUE));

					if ($data->kegiatanbaru == '') 
						//$revnama = 'Tidak';
						$revnama = "<img src='/files/inaktif.png'>";
					else	
						//$revnama = 'Ya';
						$revnama = "<img src='/files/aktif.png'>";
					
					if ($data->kodeprobaru == '') 
						//$revprogram = 'Tidak';
						$revprogram = "<img src='/files/inaktif.png'>";
					else
						//$revprogram = 'Ya';
						$revprogram = "<img src='/files/aktif.png'>";
					
					if ($data->plafonbaru == 0) {
						$plafonbaru = $data->plafonlama;
						//$revplafon = 'Tidak';
						$revplafon = "<img src='/files/inaktif.png'>";
						
					}
					else {
						$plafonbaru = $data->plafonbaru;
						//$revplafon = 'Ya';
						$revplafon = "<img src='/files/aktif.png'>";
					}
					
				} else {
					$kegname = l($data->kegiatanbaru, 'apbd/kegiatanrevisi/editnew/' . $data->id , array('html' =>TRUE));
					$plafonbaru = $data->plafonbaru;
					
					//$revnama = 'Baru';
					//$revprogram = 'Baru';
					//$revplafon = 'Baru';

					$revnama = "<img src='/files/newdoc.png'>";
					$revprogram = "<img src='/files/newdoc.png'>";
					$revplafon = "<img src='/files/newdoc.png'>";

				}
				
			} else {
				if ($data->tipe==0)
					$kegname = $data->kegiatanlama;
				else
					$kegname = $data->kegiatanbaru;
			}

			if (user_access('kegiatanskpd penghapusan')) {
				if ($data->status==0)
					$editlink =l('Hapus ', 'apbd/kegiatanrevisi/delete/' . $data->id, array('html'=>TRUE));			 
				else if ($data->status==1)
					$editlink = 'Disetujui';
				else
					$editlink = 'Ditolak';
			}
			
            $no++;
			
			if (isSuperuser()) { 
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					
					array('data' => $revnama, 'align' => 'center', 'valign'=>'top'),
					array('data' => $revprogram, 'align' => 'center', 'valign'=>'top'),
					array('data' => $revplafon, 'align' => 'center', 'valign'=>'top'),
					
					array('data' => apbd_fn($data->plafonlama), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($plafonbaru), 'align' => 'right', 'valign'=>'top'),
					
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),

					array('data' => $revnama, 'align' => 'center', 'valign'=>'top'),
					array('data' => $revprogram, 'align' => 'center', 'valign'=>'top'),
					array('data' => $revplafon, 'align' => 'center', 'valign'=>'top'),

					array('data' => apbd_fn($data->plafonlama), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($plafonbaru), 'align' => 'right', 'valign'=>'top'),
					array('data' => $revisi, 'align' => 'left', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			}
		}
    } else {
		$rows[] = array (
			array('data' => 'Akses/Data error, hubungi administrator', 'colspan'=>'9')
		);
    }

	if ($no==0) {
		$linknew = l('Revisi Baru', 'apbd/kegiatanrevisi/edit/', array('html' =>TRUE));	
		$rows[] = array (
			array('data' => 'Tidak ada data revisi, klik ' . $linknew . ' untuk menambahkan.', 'colspan'=>'9')
		);
	}
		
	$btn = "";

	$status = 0;
	$record = 0;

	$btn .= l('Revisi Baru', 'apbd/kegiatanrevisi/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";

	$btn .= l('Kegiatan Baru', 'apbd/kegiatanrevisi/editnew/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	
	//$btn .= l('Cetak', '', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

	//$btn .= "&nbsp;" . l('Simpan Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	

    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanrevisi/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanrevisi/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function kegiatanrevisi_main_form() {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilihan Data',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	$status = 0;
	$filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$kodeuk = arg(3);
		$tipe = arg(4);
		$status = arg(5);
		
	} else {
		$tipe = $_SESSION['tiperevisi'];
		$status = $_SESSION['statusrevisi'];
		if ($status=='') $status=0;
		
		if (isSuperuser()) {
			$kodeuk = $_SESSION['kodeukrevisi'];	
			if ($kodeuk == '') 	$kodeuk = '00';
		} else
			$kodeuk = apbd_getuseruk();
	}
	//drupal_set_message($filter);

	//if (isset($kodeuk)) {
	//    $form['formdata']['#collapsed'] = TRUE;
	//    //if (isUserKecamatan())
	//    //    if ($kodeuk != apbd_getuseruk())
	//    //        $form['formdata']['#collapsed'] = FALSE;
	//}
		   
	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		$type='select';
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
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
	

	$form['formdata']['tipe']= array(
		'#type' => 'radios', 
		'#title' => t('Tipe'), 
		'#default_value' => $tipe,
		'#options' => array(	
			 '' => t('Semua'), 	
			 '0' => t('Kegiatan Revisi'), 	
			 '1' => t('Kegiatan Baru'),	
		   ),
		'#weight' => 3,		
	);	

	$form['formdata']['ss'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 4,
	);		

	$form['formdata']['status']= array(
		'#type' => 'radios', 
		'#title' => t('Status'), 
		'#default_value' => $status,
		'#options' => array(	
			 '0' => t('Usulan'), 	
			 '1' => t('Disetujui'), 	
			 '9' => t('Ditolak'),	
		   ),
		'#weight' => 5,		
	);	

	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 6,
	);		

	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 7
	);
	
	return $form;
}
function kegiatanrevisi_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$tipe = $form_state['values']['tipe'];
	$status = $form_state['values']['status'];
	
	$tahun= $form_state['values']['tahun'];
	
	$_SESSION['tiperevisi'] = $tipe;
	$_SESSION['statusrevisi'] = $status;
	
	if (isSuperuser()) 
		$_SESSION['kodeukrevisi'] = $kodeuk;
	
	$uri = 'apbd/kegiatanrevisi/filter/' . $kodeuk . '/' . $tipe . '/' . $status;
	drupal_goto($uri);
	
}

function kegiatanrevisi_transfer_form() {

}


?>