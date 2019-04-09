<?php
function musrenbangcamv3_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$kodeuk = arg(3);
	$tahun = arg(4);
	$exportpdf = arg(5);
	
	//drupal_set_message($kodeuk);
	if (!isset($tahun)) 
		return drupal_get_form('musrenbangcamv3_form');

	//if (isUserKecamatan()) {
	//    if ($kodeuk != apbd_getuseruk())
	//        return drupal_get_form('musrenbangcam_form');
	//}	

	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		$htmlContent = GenReportForm(1);
		$pdfFile = 'analisappa.pdf';
		apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
		
	} elseif (isset($exportpdf) && ($exportpdf=='xls'))  {
		musrenbangcamv3_exportexcel($kodeuk, $tahun);
		
	} else {
		//PDF
		$urlpdf = 'apbd/laporan/musrenbangcamv3/' . $kodeuk . '/' . $tahun . '/pdf';
		$urlxls = 'apbd/laporan/musrenbangcamv3/' . $kodeuk . '/' . $tahun . '/xls';
		$output .= drupal_get_form('musrenbangcamv3_form');
		$output .=   l('Cetak (PDF)', $urlpdf , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		
		if (isSuperuser())
			$output .="&nbsp;" . l('Excel', $urlxls , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;


		$output .= GenReportForm();
		return $output;
	}

}
function GenReportForm($print=0) {

	$kodeuk = arg(3);
	if ($kodeuk=='') $kodeuk='00';
	$tahun = arg(4);

	if ($kodeuk !='00') {
		$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf("select kodeuk, namasingkat from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk));
		$presult = db_query($pquery);
		if ($data=db_fetch_object($presult)) {
			$namauk = $data->namasingkat ;
		}
	} 
	
	//---
	$col1 = '70px';
	$col2 = '300';	
	$colrekening = '124px';
	$colplafon = '128px';
	$coltotal = '370px';	//'750px';
	
	$tablesort=' order by p.kodeu, p.kodepro, u.kodedinas';
	$customwhere = ' and k.tahun=\'%s\' ';
	$where = ' where true' . $customwhere . $qlike ;

	$sql = 'SELECT p.kodeu, r.urusansingkat, p.kodepro, p.np, p.program, k.kodeuk, u.kodedinas, u.namasingkat, sum(k.nompegawai) jpegawai, sum(k.nombarangjasa) jbarangjasa, sum(k.nommodal) jmodal, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro inner join unitkerja u on k.kodeuk=u.kodeuk left join urusan r on p.kodeu=r.kodeu ' . $where . ' group by p.kodeu, r.urusansingkat, p.kodepro, p.np, p.program, k.kodeuk, u.kodedinas, u.namasingkat ' . $tablesort;
	$fsql = sprintf($sql, db_escape_string($tahun));
	//drupal_set_message( $fsql);
	$result = db_query($fsql);
	
	$no = 0;
	
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$headers1[] = array (array ('data'=>'PENJABARAN BELANJA PPAS PER URUSAN - PROGRAM - SKPD', 'width'=>'870px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers1[] = array (array ('data'=> $namauk . "&nbsp;" . $kabupaten , 'width'=>'870px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers1[] = array (array ('data'=> 'TAHUN ' . $tahun, 'width'=>'870px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers1[] = array (array ('data'=>'&nbsp;', 'colspan'=>'6', 'width'=>'870px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers[] = array (
							array('data' => 'KODE',  'width'=> $col1, 'style' => 'border: 1px solid black; text-align:center;'),
							array('data' => 'URUSAN - PROGRAM - SKPD', 'width' => $col2, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							array('data' => 'PEGAWAI', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							array('data' => 'BARANG JASA', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							array('data' => 'MODAL', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							array('data' => 'JUMLAH',  'width' => $colplafon, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						);

	$headers[] = array (
							array('data' => '1',  'width'=> $col1, 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '2', 'width' => $col2, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '3', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '4', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '5', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '6',  'width' => $colplafon, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						);

	if ($result) {
		$u_array = array('URUSAN PADA SEMUA SKPD','URUSAN WAJIB','URUSAN PILIHAN');
		$pu=(double)0;
		$u ='';
		$u_nama='';
		$ju=(double)0;
		$ju_modal=(double)0;
		$ju_barangjasa=(double)0;
		$ju_pegawai=(double)0;
		
		$pu2=0;
		$u2='';
		$u2_nama='';
		$ju2=(double)0;
		$ju2_barangjasa=(double)0;
		$ju2_modal=(double)0;
		$ju2_pegawai=(double)0;
		
		$pupro=0;
		$upro='';
		$upro_nama='';
		$jang_total=(double)0;
		$jang_modal=(double)0;
		$jang_barangjasa=(double)0;
		$jang_pegawai=(double)0;
		
		$total = (double) 0;
		$totaljbarangjasa = (double) 0;
		$totaljmodal = (double) 0;
		$totaljpegawai = (double) 0;
		
		$first=true;
		
		$u_data = array();
		$u2_data = array();
		$u3_data = array();
		$temp_data = array();
		
		while ($data = db_fetch_object($result)) {                
			$no++;
			$r_u = substr($data->kodeu,0,1);
			$r_u2= $r_u . "." . substr($data->kodeu, 1,2);
			$r_upro= $r_u2 . "." . $data->np;
			//drupal_set_message($data->kegiatan);
			$total += (double) $data->jtotal;
			$totaljbarangjasa += (double) $data->jbarangjasa;
			$totaljmodal += (double) $data->jmodal;
			$totaljpegawai += (double) $data->jpegawai;

			if ($first) {
				$u = $r_u;
				$u2 = $r_u2;
				$upro = $r_upro;
				$u_nama = $u_array[$u];                    
				$u2_nama = $data->$data->urusansingkat;
				if ($u2=='0.00')
					$u2_nama = 'URUSAN PADA SEMUA SKPD';
				$upro_nama = $data->program;
				
				$ju = (double)$data->jtotal;
				$ju_jbarangjasa = (double)$data->jbarangjasa;
				$ju_jmodal = (double)$data->jmodal;
				$ju_jpegawai = (double)$data->jpegawai;

				$ju2 = (double)$data->jtotal;
				$ju2_jbarangjasa = (double)$data->jbarangjasa;
				$ju2_jmodal = (double)$data->jmodal;
				$ju2_jpegawai = (double)$data->jpegawai;

				$jang_total = (double)$data->jtotal;
				$jang_barangjasa = (double)$data->jbarangjasa;
				$jang_modal = (double)$data->jmodal;
				$jang_pegawai = (double)$data->jpegawai;
				$first=false;
				
			} else {
				if ($r_upro != $upro) {
					
					//PROGRAM
					$temp = array (
						array('data' => $upro, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
						array('data' => $upro_nama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($jang_pegawai) , 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($jang_barangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($jang_modal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($jang_total), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					);
					
					array_unshift($temp_data, $temp);
					$u3_data= array_merge($u3_data, $temp_data);
					//
					//array_unshift($temp_data, $temp);
					//$u3_data[] = $temp_data;
					$temp_data=array();
					
					$upro = $r_upro;
					$upro_nama = $data->program;
					$jang_total = (double)$data->jtotal;
					$jang_modal = (double) $data->jmodal;
					$jang_barangjasa = (double) $data->jbarangjasa;
					$jang_pegawai = (double) $data->jpegawai;
					
				} else {
					$jang_total += (double) $data->jtotal;
					$jang_modal += (double) $data->jmodal;
					$jang_barangjasa += (double) $data->jbarangjasa;
					$jang_pegawai += (double) $data->jpegawai;
				}
			
				
				if ($u2 != $r_u2) {
					//URUSAN
					$tkode = $u2 . '-' . $u2_nama;
					$temp = array (
						array('data' => $u2, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => $u2_nama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($ju2_jpegawai) , 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($ju2_jbarangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($ju2_jmodal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($ju2), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					);

					array_unshift($u3_data, $temp);
					$u2_data=array_merge($u2_data, $u3_data); 
					$u3_data=array();
					
					$u2 = $r_u2;
					$u2_nama = $data->urusansingkat;
					if ($u2=='0.00')
						$u2_nama = 'URUSAN PADA SEMUA SKPD';
					
					$ju2 = (double)$data->jtotal;
					$ju2_jbarangjasa = (double) $data->jbarangjasa;
					$ju2_jmodal = (double) $data->jmodal;
					$ju2_jpegawai = (double) $data->jpegawai;
					
				} else {
					$ju2 += (double) $data->jtotal;
					$ju2_jbarangjasa += (double) $data->jbarangjasa;
					$ju2_jmodal += (double) $data->jmodal;
					$ju2_jpegawai += (double) $data->jpegawai;
				}

				if ($u != $r_u) {
					//TOP URUSAN
					$tnama = $u_array[$u];
					$temp = array (
						array('data' => $u, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),
						array('data' => $tnama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
						array('data' => apbd_fn($ju_jpegawai), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
						array('data' => apbd_fn($ju_jbarangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
						array('data' => apbd_fn($ju_jmodal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
						array('data' => apbd_fn($ju), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
					);

					
					array_unshift($u2_data, $temp);
					$u_data= array_merge($u_data, $u2_data);
					$u2_data=array();
					
					$u = $r_u;
					$u_nama = $u_array[$u];
					$ju = (double) $data->jtotal;
					$ju_jbarangjasa = (double) $data->jbarangjasa;
					$ju_jmodal = (double) $data->jmodal;
					$ju_jpegawai = (double) $data->jpegawai;
					
				} else {
					$ju += (double) $data->jtotal;
					$ju_jbarangjasa += (double) $data->jbarangjasa;
					$ju_jmodal += (double) $data->jmodal;
					$ju_jpegawai += (double) $data->jpegawai;
					
				}
			}
			
			//SKPD
			$tkode = $r_upro . "." .$data->kodedinas;
			$temp_data[] = array (
				array('data' => $tkode, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),                    
				array('data' => $data->namasingkat , 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
				array('data' => apbd_fn($data->jpegawai), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
				array('data' => apbd_fn($data->jbarangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
				array('data' => apbd_fn($data->jmodal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
				array('data' => apbd_fn($data->jtotal), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
			);
			
		}
		
		if (count($temp_data)>0) {
			//PROGRAM
			$tkode = $upro . "-" . $upro_nama;
			$temp = array (
				array('data' => $upro, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
				array('data' => $upro_nama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($jang_pegawai) , 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'id' => 'aa', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($jang_barangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($jang_modal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($jang_total), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
			);

			array_unshift($temp_data, $temp);
			$u3_data= array_merge($u3_data, $temp_data);
			//$u3_data[]= $temp_data;
			$tkode = $u2 . '-' . $u2_nama;

			$temp = array (
				//URUSAN
				array('data' => $u2, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
				array('data' => $u2_nama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($ju2_jpegawai) , 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($ju2_jbarangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($ju2_jmodal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($ju2), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
			);

			array_unshift($u3_data, $temp);
			$u2_data=array_merge($u2_data, $u3_data);
			
			$tnama = $u_array[$u];
			$temp = array (
				//TOP URUSAN
				array('data' => $u, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
				array('data' => $tnama , 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
				array('data' => apbd_fn($ju_jpegawai), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
				array('data' => apbd_fn($ju_jbarangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
				array('data' => apbd_fn($ju_jmodal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
				array('data' => apbd_fn($ju), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
			);

			array_unshift($u2_data, $temp);
			$u_data= array_merge($u_data, $u2_data);
		}
		$rows = array_merge($rows, $u_data);
		
		if (count($rows) > 0) {
			//total
			$rows[] = array (
				array('data' => 'Total', 'colspan'=>'2', 'width' => $coltotal, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),

				array('data' => apbd_fn($totaljpegawai), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
				array('data' => apbd_fn($totaljbarangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
				array('data' => apbd_fn($totaljmodal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
				
				array('data' => apbd_fn($total), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
			);
			
		}
		
	} else {
		$rows[] = array (
			array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'9')
		);
	}
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$rows1[] = array (array('data' => '', 'colspan'=>'2'));
	$output .= theme_box('', apbd_theme_table($headers1, $rows1, $opttbl));
	
	$toutput='';

	$output .= theme_box('', apbd_theme_table($headers, $rows, $opttbl));
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
}

function musrenbangcamv3_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Parameter Laporan',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$kodeuk = arg(2);        
	if ($kodeuk=='') $kodeuk='00';
	
	//FILTER TAHUN-----
	$tahun = variable_get('apbdtahun', 0);
	$form['formdata']['tahun']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Tahun',
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $tahun, 
	);

	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		//drupal_set_message('user kec');
	} else {
		$type='select';
		$pquery = "select kodedinas,kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}	
	}	
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk,
	);		
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan'
	);
	
	return $form;
}

function musrenbangcamv3_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$tahun = $form_state['values']['tahun'];
	$kodeuk = $form_state['values']['kodeuk'];
	$uri = 'apbd/laporan/musrenbangcamv3/' . $kodeuk . '/' . $tahun;
	drupal_goto($uri);
	

}

function musrenbangcamv3_exportexcel($kodeuk, $tahun) {
error_reporting(E_ALL);
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
$objPHPExcel->getProperties()->setCreator("SiPPD Online")
							 ->setLastModifiedBy("SiPPD Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SiPPD Online.")
							 ->setKeywords("office 2007 SiPPD openxml php")
							 ->setCategory("SiPPD Online PPA");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'KODE')
			->setCellValue('B' . $row ,'URAIAN')
            ->setCellValue('C' . $row ,'PEGAWAI')
            ->setCellValue('D' . $row ,'BARANG JASA')
			->setCellValue('E' . $row ,'MODAL')
			->setCellValue('F' . $row ,'JUMLAH');

//Open data							 
$kodeuk = arg(3);
if ($kodeuk=='') $kodeuk='00';
$tahun = arg(4);

if ($kodeuk !='00') {
	$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
}
$customwhere = ' and k.tahun=\'%s\' ';
$where = ' where true' . $customwhere . $qlike ;

$sql = 'select r.kodeu, r.urusansingkat, sum(k.nompegawai) jpegawai, sum(k.nombarangjasa) jbarangjasa, sum(k.nommodal) jmodal, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro inner join urusan r on p.kodeu=r.kodeu ' . $where . ' group by r.kodeu, r.urusansingkat order by r.kodeu';

$fsql = sprintf($sql, db_escape_string($tahun));
//drupal_set_message( $fsql);
$result = db_query($fsql);	

while ($data = db_fetch_object($result)) {
	//URUSAN
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, "'" . $data->kodeu)
				->setCellValue('B' . $row, $data->urusansingkat)
				->setCellValue('C' . $row, $data->jpegawai)
				->setCellValue('D' . $row, $data->jbarangjasa)
				->setCellValue('E' . $row, $data->jmodal)
				->setCellValue('F' . $row, $data->jtotal);
	
	//PROGRAM
	$wheresub = sprintf(' and p.kodeu=\'%s\' ', $data->kodeu);
	$sql = 'select p.kodeu, p.kodepro, p.np, p.program, sum(k.nompegawai) jpegawai, sum(k.nombarangjasa) jbarangjasa, sum(k.nommodal) jmodal, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro ' . $where . $wheresub . ' group by p.kodeu, p.kodepro, p.np, p.program order by p.kodeu, p.np';
	
	$fsql_1 = sprintf($sql, db_escape_string($tahun));
	//drupal_set_message( $fsql_1);
	$result_1 = db_query($fsql_1);

	while ($data_1 = db_fetch_object($result_1)) {	
		$row++;
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A' . $row, "'" . $data->kodeu . '.' . $data_1->np)
					->setCellValue('B' . $row, $data_1->program)
					->setCellValue('C' . $row, $data_1->jpegawai)
					->setCellValue('D' . $row, $data_1->jbarangjasa)
					->setCellValue('E' . $row, $data_1->jmodal)
					->setCellValue('F' . $row, $data_1->jtotal);	

		//SKPD
		$wheresub = sprintf(' and p.kodeu=\'%s\' and p.kodepro=\'%s\' ', $data->kodeu, $data_1->kodepro);
		$sql = 'select p.kodeu, s.kodedinas, s.namasingkat, sum(k.nompegawai) jpegawai, sum(k.nombarangjasa) jbarangjasa, sum(k.nommodal) jmodal, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro inner join unitkerja s on k.kodeuk=s.kodeuk ' . $where . $wheresub . ' group by p.kodeu, s.kodedinas, s.namasingkat order by p.kodeu, s.kodedinas';
		
		$fsql_2 = sprintf($sql, db_escape_string($tahun));
		//drupal_set_message( $fsql_1);
		$result_2 = db_query($fsql_2);

		while ($data_2 = db_fetch_object($result_2)) {	
			$row++;
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A' . $row, "'" . $data->kodeu . '.' . $data_1->np . '.' . $data_2->kodedinas)
						->setCellValue('B' . $row, $data_2->namasingkat)
						->setCellValue('C' . $row, $data_2->jpegawai)
						->setCellValue('D' . $row, $data_2->jbarangjasa)
						->setCellValue('E' . $row, $data_2->jmodal)
						->setCellValue('F' . $row, $data_2->jtotal);	
		}								
	}
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Penjabaran PPAS');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Penjabaran_PPAS_Urusan_SKPD.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

?>