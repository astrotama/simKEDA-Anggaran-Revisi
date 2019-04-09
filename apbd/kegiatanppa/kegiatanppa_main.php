<?php
function kegiatanppa_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	$limit = 150;

	$revisi = $arg;    
		
	if($revisi=='9') $revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi;
	
	if ($revisi=='')
		drupal_set_title('Penomoran DPA');
	else
		drupal_set_title('Penomoran DPA Revisi/Perubahan #' . $periode);
	
	//$output .= drupal_get_form('kegiatanppa_transfer_form');
	$output = drupal_get_form('kegiatanppa_main_form');
	$header = array (
		array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD',  'valign'=>'top'),
		array('data' => 'PAD No', 'valign'=>'top'),
		array('data' => 'PAD Tgl', 'valign'=>'top'),
		array('data' => '', 'valign'=>'top'),
		array('data' => 'BTL No', 'valign'=>'top'),
		array('data' => 'BTL Tgl', 'valign'=>'top'),
		array('data' => '', 'valign'=>'top'),
		array('data' => 'BL No', 'valign'=>'top'),
		array('data' => 'BL Tgl', 'valign'=>'top'),
		array('data' => '', 'valign'=>'top'),
	);
	  
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by uk.kodedinas';
    }

	$pquery = "select uk.kodedinas, d.kodeuk, uk.namasingkat, d.penno, d.pentgl, d.penok, d.btlno, d.btltgl, d.btlok, d.blno, d.bltgl, d.blok from {unitkerja} uk left join {dpanomor".$revisi."} d on uk.kodeuk=d.kodeuk where uk.aktif=1 and uk.kodeuk in (select kodeuk from {kegiatanrevisiperubahan} where status=1 union select '00' kodeuk from {unitkerja} union select '81' kodeuk from {unitkerja})" ;
	
    //$fsql = sprintf($sql, addslashes($nama));
	$fsql = $pquery;
		//echo $fsql;
    $countsql = "select count(*) as cnt from {unitkerja} uk " ;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	//drupal_set_message($tablesort);
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
			$padlink = '';
			$padno = '';
			$padtgl = '';
			
			$btllink = '';
			$bllink = '';
			//drupal_set_message($data->namasingkat);
			
			if ($data->kodeuk=='00') {
				$padlink =l ('Nomor PEN', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/pen/' . $revisi , array('html'=>TRUE)) . '&nbsp;';
				
				$padno =  '<font color="red">' . $data->penno . '</font>';
				$padtgl =  '<font color="red">' . $data->pentgl . '</font>';

				$btlno =  '<font color="red">' . $data->btlno . '</font>';
				$btltgl =  '<font color="red">' . $data->btltgl . '</font>';
				
				$btllink =l ('Nomor BTL', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/btl/' . $revisi , array('html'=>TRUE)) . '&nbsp;';
				
				
			} else {
				if (($data->penok>0) or ($data->kodeuk=='81')) {
					$padlink =l ('Nomor PAD', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/pen/' . $revisi , array('html'=>TRUE)) . '&nbsp;';
					
					$padno =  $data->penno;
					$padtgl =  $data->pentgl;
				}
				

				$btlno =  $data->btlno;
				$btltgl =  $data->btltgl;
				
				//$padlink =l ('Nomor PAD', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/pen' , array('html'=>TRUE)) . '&nbsp;';
				if ($data->btlok>0)
					$btllink =l ('Nomor BTL', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/btl/' . $revisi , array('html'=>TRUE)) . '&nbsp;';
				if ($data->blok>0)
					$bllink =l ('Nomor BL', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/bl/' . $revisi , array('html'=>TRUE)) . '&nbsp;';
			}
			
            $no++;
			//drupal_set_message($data->namasingkat);
			$rows[] = array (
				array('data' => $no, 'align' => 'right', 'valign'=>'top'),
				
				array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
				array('data' => $padno, 'align' => 'left', 'valign'=>'top'),
				array('data' => $padtgl, 'align' => 'left', 'valign'=>'top'),
				array('data' => $padlink, 'align' => 'right', 'valign'=>'top'),
				array('data' => $btlno, 'align' => 'left', 'valign'=>'top'),
				array('data' => $btltgl, 'align' => 'left', 'valign'=>'top'),
				array('data' => $btllink, 'align' => 'right', 'valign'=>'top'),
				array('data' => $data->blno, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->bltgl, 'align' => 'left', 'valign'=>'top'),
				array('data' => $bllink, 'align' => 'right', 'valign'=>'top'),
				
			);
		}
    } else {

    }
	
	
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;
//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanppa tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanppa/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanppa pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanppa/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function kegiatanppa_main_form() {

	$form['reset'] = array (	
		'#type' => 'submit',
		'#value' => 'Reset'
	);
	$form['generate'] = array (
		'#type' => 'submit',
		'#value' => 'Generate'
	);
	return $form;
	
}
function kegiatanppa_main_form_submit($form, &$form_state) {
	if ($form_state['clicked_button']['#value'] == $form_state['values']['reset']) {
		//reset_no_dpa();
		reset_no_dpa_ulang();
	} else {
		//gen_no_dpa();
		gen_no_dpa_ulang();
	}		
}


function reset_no_dpa() {
	$revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi+1;

	$sql = 'delete from dpanomor' . $revisi;
	$res = db_query($sql);
	
	$fsql = "select kodeuk from {unitkerja} where kodeuk in (select kodeuk from {kegiatanrevisiperubahan} where status=1 union select '00' kodeuk from {unitkerja} union select '81' kodeuk from {unitkerja}) order by kodedinas" ;
	$res = db_query($fsql);
	if ($res) {
		while ($data = db_fetch_object($res)) {
			
			$btlok = 0; $blok = 0;	$penok = 0;
			$sql = sprintf('select distinct jenis from {kegiatanperubahan} where periode=2 and kodeuk=\'%s\'', $data->kodeuk);
			$res_keg = db_query($sql);
			while ($datakeg = db_fetch_object($res_keg)) {
				
				if ($datakeg->jenis=='1')
					$btlok = 1;
				else
					$blok = 1;
			}	
			
			$sql = sprintf('select distinct kodeuk from {anggperukperubahan} where jumlah<>jumlahp and kodeuk=\'%s\'', $data->kodeuk);
			$res_keg = db_query($sql);
			while ($datakeg = db_fetch_object($res_keg)) {
				$penok = 1;
			}	
			
			$sql = sprintf("insert into dpanomor" . $revisi . " (kodeuk, penok, btlok, blok) values ('%s', '%s', '%s', '%s')", $data->kodeuk, $penok, $btlok, $blok);
			$res_i = db_query($sql);
			
			
		}
	}	
}	

function reset_no_dpa_ulang() {
	$revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi+1;

	//$sql = 'delete from dpanomor' . $revisi;
	//$res = db_query($sql);
	
	$fsql = "select distinct kodeuk from {kegiatanrevisiperubahan} where kodeuk not in (select kodeuk from dpanomor" . $revisi . ")" ;
	$res = db_query($fsql);
	if ($res) {
		while ($data = db_fetch_object($res)) {
			
			$btlok = 0; $blok = 0;	$penok = 0;
			$sql = sprintf('select distinct jenis from {kegiatanperubahan} where periode=2 and kodeuk=\'%s\'', $data->kodeuk);
			$res_keg = db_query($sql);
			while ($datakeg = db_fetch_object($res_keg)) {
				
				if ($datakeg->jenis=='1')
					$btlok = 1;
				else
					$blok = 1;
			}	
			
			$sql = sprintf('select distinct kodeuk from {anggperukperubahan} where jumlah<>jumlahp and kodeuk=\'%s\'', $data->kodeuk);
			$res_keg = db_query($sql);
			while ($datakeg = db_fetch_object($res_keg)) {
				$penok = 1;
			}	
			
			//drupal_set_message($data->kodeuk);
			
			$sql = sprintf("insert into dpanomor" . $revisi . " (kodeuk, penok, btlok, blok) values ('%s', '%s', '%s', '%s')", $data->kodeuk, $penok, $btlok, $blok);
			$res_i = db_query($sql);
			
			
		}
	}	
}

function gen_no_dpa() {
	
	//drupal_set_message('Hai...');
	
	$revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi+1;
	
	$tanggal = strftime('%e %B %Y', time());
	
	$sql = 'select dpatgl' . $revisi . ' tanggal, dpabtlformat' . $revisi . ' dpabtlformat, dpablformat' . $revisi . ' dpablformat, dpapenformat' . $revisi . ' dpapenformat from {setupapp}';
	$res = db_query($sql);
	if ($res) {	
		while ($data = db_fetch_object($res)) {
			$tanggal = $data->tanggal;
			
			$dpabtlformat = $data->dpabtlformat;
			$dpablformat = $data->dpablformat;
			$dpapenformat = $data->dpapenformat;
		}
	}
	
	
	//hapus
	$sql = 'delete from kegiatandpa';
	$res = db_query($sql);
	
	
	$penno = 0; $btlno = 0; $blno = 0;
	
	$sql = 'select u.kodeuk, u.kodedinas, d.btlok, d.blok, d.penok from {dpanomor' . $revisi . '} d inner join {unitkerja} u on d.kodeuk=u.kodeuk';
	$res = db_query($sql);
	if ($res) {	
		while ($data = db_fetch_object($res)) {
			if ($data->penok == '1') {
				$penno++;
				
				$nomor = sprintf("%03d", $penno);
				$sql = sprintf("update {dpanomor" . $revisi . "} set penno='%s', pentgl='%s' where kodeuk='%s'", $nomor, $tanggal, $data->kodeuk);
				$resx = db_query($sql);
				
			}	
			
			//BTL
			if ($data->btlok == '1') {
				$btlno++;

				$nomor = sprintf("%03d", $btlno);
				$sql = sprintf("update {dpanomor" . $revisi . "} set btlno='%s', btltgl='%s' where kodeuk='%s'", $nomor, $tanggal, $data->kodeuk);
				$resx = db_query($sql);
				
				
				$sql = sprintf('select kodekeg,kodepro from {kegiatanperubahan} where jenis=1 and periode=\'%s\' and kodeuk=\'%s\'', $periode, $data->kodeuk);
				$res_keg = db_query($sql);
				while ($datakeg = db_fetch_object($res_keg)) {
					//drupal_set_message($datakeg->kodekeg);

					$kodekeg = $data->kodedinas . '.' . $datakeg->kodepro . '.' . substr($datakeg->kodekeg, -3);

					$dpanolengkap = str_replace('NNN', $nomor, $dpabtlformat);
					$dpanolengkap = str_replace('NOKEG', $kodekeg, $dpanolengkap);
					$dpanolengkap = str_replace('TAHUN', apbd_tahun(), $dpanolengkap);
					
					//drupal_set_message($dpanolengkap);
					
					$sql = sprintf("insert into kegiatandpa (kodekeg, dpano, dpatgl) values ('%s', '%s', '%s')", $datakeg->kodekeg, $dpanolengkap, $tanggal);
					$res_i = db_query($sql);
							
				}	
				
			}	
			
			if ($data->blok == '1') {
				$blno++;

				$nomor = sprintf("%03d", $blno);
				$sql = sprintf("update {dpanomor" . $revisi . "} set blno='%s', bltgl='%s' where kodeuk='%s'", $nomor, $tanggal, $data->kodeuk);
				$resx = db_query($sql);
				
						
				$sql = sprintf('select kodekeg,kodepro from {kegiatanperubahan} where jenis=2 and periode=\'%s\' and kodeuk=\'%s\'', $periode, $data->kodeuk);
				$res_keg = db_query($sql);
				while ($datakeg = db_fetch_object($res_keg)) {


					$kodekeg = $data->kodedinas . '.' . $datakeg->kodepro . '.' . substr($datakeg->kodekeg, -3);

					$dpanolengkap = str_replace('NNN', $nomor, $dpablformat);
					$dpanolengkap = str_replace('NOKEG', $kodekeg, $dpanolengkap);
					$dpanolengkap = str_replace('TAHUN', apbd_tahun(), $dpanolengkap);
					
					$sql = sprintf("insert into kegiatandpa (kodekeg, dpano, dpatgl) values ('%s', '%s', '%s')", $datakeg->kodekeg, $dpanolengkap, $tanggal);
					$res_i = db_query($sql);
				}	
				
			}	
		}		
	}
	
}	

function gen_no_dpa_ulang() {
	
	//drupal_set_message('Hai...');
	
	$revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi+1;
	
	$tanggal = strftime('%e %B %Y', time());
	
	$sql = 'select dpatgl' . $revisi . ' tanggal, dpabtlformat' . $revisi . ' dpabtlformat, dpablformat' . $revisi . ' dpablformat, dpapenformat' . $revisi . ' dpapenformat from {setupapp}';
	$res = db_query($sql);
	if ($res) {	
		while ($data = db_fetch_object($res)) {
			$tanggal = $data->tanggal;
			
			$dpabtlformat = $data->dpabtlformat;
			$dpablformat = $data->dpablformat;
			$dpapenformat = $data->dpapenformat;
		}
	}
	
	
	
	$penno = 0; $btlno = 0; $blno = 0;
	
	$sql = "select u.kodeuk, u.kodedinas, d.btlok, d.blok, d.penok from {dpanomor" . $revisi . "} d inner join {unitkerja} u on d.kodeuk=u.kodeuk where blno=''";
	$res = db_query($sql);
	if ($res) {	
		while ($data = db_fetch_object($res)) {
			
			drupal_set_message('kodeuk : ' . $data->kodeuk);

			if ($data->btlok == '1') {
				
				$btlno = 0;
				$sql = 'select btlno from {dpanomor' . $revisi . '} order by btlno desc limit 1';
				$resnum = db_query($sql);
				if ($resnum) {
					while ($datanum = db_fetch_object($resnum)) {
						$btlno = $datanum->btlno;
					}
				}	
				$btlno++;
				
				$nomor = sprintf("%03d", $btlno);
				$sql = sprintf("update {dpanomor" . $revisi . "} set btlno='%s', btltgl='%s' where kodeuk='%s'", $nomor, $tanggal, $data->kodeuk);
				$resx = db_query($sql);
				drupal_set_message('no : ' . $btlno);
				
						
				$sql = sprintf('select kodekeg,kodepro from {kegiatanperubahan} where jenis=1 and periode=\'%s\' and kodeuk=\'%s\'', $periode, $data->kodeuk);
				$res_keg = db_query($sql);
				while ($datakeg = db_fetch_object($res_keg)) {


					$kodekeg = $data->kodedinas . '.' . $datakeg->kodepro . '.' . substr($datakeg->kodekeg, -3);

					$dpanolengkap = str_replace('NNN', $nomor, $dpabtlformat);
					$dpanolengkap = str_replace('NOKEG', $kodekeg, $dpanolengkap);
					$dpanolengkap = str_replace('TAHUN', apbd_tahun(), $dpanolengkap);
					
					drupal_set_message('dppa : ' . $dpanolengkap);
					
					$sql = sprintf("insert into kegiatandpa (kodekeg, dpano, dpatgl) values ('%s', '%s', '%s')", $datakeg->kodekeg, $dpanolengkap, $tanggal);
					$res_i = db_query($sql);
				}	
				
			}	
			
			if ($data->blok == '1') {
				
				$blno = 0;
				$sql = 'select blno from {dpanomor' . $revisi . '} order by blno desc limit 1';
				$resnum = db_query($sql);
				if ($resnum) {
					while ($datanum = db_fetch_object($resnum)) {
						$blno = $datanum->blno;
					}
				}	
				$blno++;
				
				$nomor = sprintf("%03d", $blno);
				$sql = sprintf("update {dpanomor" . $revisi . "} set blno='%s', bltgl='%s' where kodeuk='%s'", $nomor, $tanggal, $data->kodeuk);
				$resx = db_query($sql);
				drupal_set_message('no : ' . $blno);
				
						
				$sql = sprintf('select kodekeg,kodepro from {kegiatanperubahan} where jenis=2 and periode=\'%s\' and kodeuk=\'%s\'', $periode, $data->kodeuk);
				$res_keg = db_query($sql);
				while ($datakeg = db_fetch_object($res_keg)) {


					$kodekeg = $data->kodedinas . '.' . $datakeg->kodepro . '.' . substr($datakeg->kodekeg, -3);

					$dpanolengkap = str_replace('NNN', $nomor, $dpablformat);
					$dpanolengkap = str_replace('NOKEG', $kodekeg, $dpanolengkap);
					$dpanolengkap = str_replace('TAHUN', apbd_tahun(), $dpanolengkap);
					
					drupal_set_message('dppa : ' . $dpanolengkap);
					
					$sql = sprintf("insert into kegiatandpa (kodekeg, dpano, dpatgl) values ('%s', '%s', '%s')", $datakeg->kodekeg, $dpanolengkap, $tanggal);
					$res_i = db_query($sql);
				}	
				
			}	
		}		
	}
	
}	

?>
