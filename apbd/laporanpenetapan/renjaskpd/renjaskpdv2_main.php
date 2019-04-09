<?php
    function renjaskpdv2_main() {
    $h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
    $h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
    drupal_add_css('files/css/kegiatancam.css');
        
        $kodeuk = arg(3);
        $sumberdana = arg(4);
        $tahun = arg(5);
        $limit = arg(6);
        $exportpdf = arg(7);
        if (!isset($tahun)) 
            return drupal_get_form('renjaskpdv2_form');

        if (isset($exportpdf) && ($exportpdf=='pdf'))  {

            $htmlContent = GenReportForm(1);
            $pdfFile = 'renjaskpdv2.pdf';
            apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
            
        } else {
            $url = 'apbd/laporanpenetapan/renjaskpdv2/'. $kodeuk .'/'. $sumberdana . '/'. $tahun . "/0/pdf";
            $output .= drupal_get_form('renjaskpdv2_form');
            $output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
            $output .= GenReportForm();
            return $output;
        }

    }
    function GenReportForm($print=0) {
        
        $kodeuk = arg(3);
        $sumberdana = arg(4);
        $tahun = intval(arg(5));
        $limit = arg(6);
        $namauk = '';
        $pimpinannama='';
        $pimpinannip='';
        $pimpinanjabatan='';
        $pquery = sprintf("select kodeuk, namauk, pimpinannama, pimpinannip, pimpinanjabatan from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
        $pres = db_query($pquery);
        if ($data = db_fetch_object($pres)) {
            $namauk = $data->namauk;
            $pimpinannama=$data->pimpinannama;
            $pimpinannip=$data->pimpinannip;
            $pimpinanjabatan=$data->pimpinanjabatan;
        }

        
        $tablesort=' order by p.kodeu, p.np';
        $customwhere = ' and k.tahun=\'%s\' ';
        if ($kodeuk!='00') {
            $customwhere .= ' and k.kodeuk=\'%s\' ';
        }
        /*
		switch($status) {
            case 0: //status = keseluruhan  
				$sqlsub = 'total';	
                $sqltotal = 'k.total';
				break;
            case 1: //status = lolos
				$sqlsub = 'total';
				$sqltotal = 'k.totalpenetapan as total';
                $customwhere .= ' and k.lolos=1';
                break;
            case 2: //status = tidak lolos
				$sqlsub = 'total';
				$sqltotal = 'k.total';
                $customwhere .= ' and k.lolos=0';
                break;
        }
		*/
		
		$sqltotal = 'k.total';
		switch($sumberdana) {
			case 'dau':
				$qlike .= sprintf(' and (k.apbdkab>0) ');
				break;
			case 'banprov':
				$qlike .= sprintf(' and (k.apbdprov>0) ');
				break;
			case 'dak':
				$qlike .= sprintf(' and (k.apbdnas>0) ');
				break;

			case 'apbdkab':
				$qlike .= sprintf(' and ((k.apbdkab>0) or (k.apbdprov>0) or (k.apbdnas>0)) ');
				break;
			case 'apbdprov':
				$qlike .= sprintf(' and (k.apbp>0) ');
				break;
			case 'apbn':
				$qlike .= sprintf(' and (k.apbn>0) ');
				break;
		}		
        $where = ' where true' . $customwhere . $qlike ;
    
        //$sql = 'select d.namasingkat, d.kodedinas, p.kodeu, u.urusansingkat, p.program, p.np, p.s' . $tahun . ' sas_pn, p.t' . $tahun . ' tar_pn, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, ' . $sqltotal . ', k.totalsebelum, k.totalsebelum2, k.totalpenetapan, k.apbdkab, k.pnpm, k.sumberdana, k.catatan, d.namasingkat from {kegiatanskpd} k left join unitkerja d on(k.kodeuk=d.kodeuk) inner join program p on (k.kodepro = p.kodepro) left join urusan u on (p.kodeu = u.kodeu)' . $where;

        $sql = 'select d.namasingkat, d.kodedinas, p.kodeu, u.urusansingkat, p.program, p.np, p.s' . $tahun . ' sas_pn, p.t' . $tahun . ' tar_pn, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, k.target1,' . $sqltotal . ', k.totalsebelum, k.totalsebelum2, k.totalpenetapan, k.apbdkab, k.pnpm, k.sumberdana, k.catatan, d.namasingkat from {kegiatanskpd} k inner join unitkerja d on(k.kodeuk=d.kodeuk) inner join program p on (k.kodepro = p.kodepro) inner join urusan u on (p.kodeu = u.kodeu)' . $where;

        $fsql = sprintf($sql, db_escape_string($tahun), db_escape_string($kodeuk));
        //$limit = 13;
        
        //drupal_set_message( $fsql);
        $countsql = "select count(*) as cnt from {kegiatanskpd} k" . $where;
        $fcountsql = sprintf($countsql, db_escape_string($tahun), db_escape_string($kodeuk));
        if ($limit>0) {
            $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
        } else {
            $fsql .= ' ORDER BY p.kodeu, p.np, k.nomorkeg';
            $result = db_query($fsql);
        }
        
        $no=0;
        $page = $_GET['page'];
        if (isset($page)) {
            $no = $page * $limit;
        } else {
            $no = 0;
        }
        //$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
		$kabupaten = 'KABUPATEN JEPARA'; 
        $rows= array();
        $headers1[] = array (array ('data'=>'RENCANA KERJA SKPD ', 'width'=>'900px', 'colspan'=>'8', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=> $namauk . "&nbsp;" . $kabupaten , 'width'=>'900px', 'colspan'=>'8', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=> 'TAHUN ' . $tahun, 'width'=>'900px', 'colspan'=>'8', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=>'&nbsp;', 'colspan'=>'8', 'width'=>'900px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
		
        //$headers[] = array (
        //                     array('data' => 'Kode',  'width'=> '99px', 'style' => 'border: 1px solid black; text-align:center;'),
        //                    array('data' => 'Urusan /Program /Kegiatan', 'width' => '121px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
        //                     array('data' => 'Indikator Kinerja', 'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
        //                     array('data' => 'Lokasi', 'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
        //                     array('data' => 'Target ' . ($tahun) ,  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
        //                     array('data' => 'Pagu Indikatif '  . ($tahun) ,  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
        //                     array('data' => 'Sumber Dana ' . ($tahun) ,  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
        //                     array('data' => 'Catatan Penting' , 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
		//					 array('data' => 'Target ' . ($tahun + 1)  , 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
		//					 array('data' => 'Pagu Indikatif ' . ($tahun + 1) , 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
        //                    );
        $headers[] = array (
                             array('data' => 'KODE', 'rowspan'=>'2',  'width'=> '99px', 'style' => 'border: 1px solid black; text-align:center; vertical-align: middle;'),
                             array('data' => 'URUSAN /PROGRAM /KEGIATAN', 'rowspan'=>'2',  'width' => '121px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;text-valign:middle'),
                             array('data' => 'INDIKATOR KINERJA', 'rowspan'=>'2',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'RENCANA TAHUN ' . ($tahun), 'colspan'=>'4',  'width' => '310px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'CATATAN PENTING' ,  'rowspan'=>'2', 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							 array('data' => 'PRAKIRAAN MAJU RENCANA TAHUN ' . ($tahun+1), 'colspan'=>'2',  'width' => '170px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             );							 
        $headers[] = array (
                             array('data' => 'LOKASI', 'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'TARGET',   'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'PAGU INDIKATIF',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'SUMBER DANA',  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							 array('data' => 'TARGET', 'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							 array('data' => 'PAGU INDIKATIF', 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             );
		
        $headers[] = array (
                             array('data' => '1',  'width'=> '99px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '2', 'width' => '121px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '3', 'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '4', 'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '5',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '6 ',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '7',  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '8' , 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 array('data' => '9' , 'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 array('data' => '10' , 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             );
        if ($result) {
            $u_array = array('URUSAN PADA SEMUA SKPD','URUSAN WAJIB','URUSAN PILIHAN');
            $pu=(double)0;
            $u ='';
            $u_nama='';
            $ju=(double)0;
            $ju_sebelum2=(double)0;
            $ju_sebelum=(double)0;
            
            $pu2=0;
            $u2='';
            $u2_nama='';
            $ju2=(double)0;
            $ju2_sebelum=(double)0;
            $ju2_sebelum2=(double)0;
            
            $pupro=0;
            $upro='';
			$upro_nama='';
			$sprogram = '';
			$tprogram = '';

            $jangthnusul=(double)0;
            $jangthnplus1=(double)0;
            $jangthnminus1=(double)0;
            
            $total = (double) 0;
            $totalsebelum = (double) 0;
            $totalsebelum2 = (double) 0;
            
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
                $total += (double) $data->total;
                $totalsebelum += (double) $data->totalsebelum;
                $totalsebelum2 += (double) $data->totalsebelum2;

                if ($first) {
                    $u = $r_u;
                    $u2 = $r_u2;
                    $upro = $r_upro;
                    $u_nama = $u_array[$u];                    
                    $u2_nama = $data->urusansingkat;
                    if ($u2=='0.00')
                        $u2_nama = 'URUSAN PADA SEMUA SKPD';
                    $upro_nama = $data->program;
                    $sprogram = $data->sas_pn;
                    $tprogram = $data->tar_pn;
                    
                    $ju = (double)$data->total;
                    $ju_sebelum = (double)$data->totalsebelum;
                    $ju_sebelum2 = (double)$data->totalsebelum2;

                    $ju2 = (double)$data->total;
                    $ju2_sebelum = (double)$data->totalsebelum;
                    $ju2_sebelum2 = (double)$data->totalsebelum2;

                    $jangthnusul = $data->total;
                    $jangthnminus1 = $data->totalsebelum;
                    $jangthnplus1 = $data->totalsebelum2;
                    $first=false;
                } else {
                    if ($r_upro != $upro) {
                        //$tkode = $upro . "-" . $upro_nama;
						
						//** PROGRAM
                        $temp = array (
                            array('data' => $upro, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),
                            array('data' => $upro_nama, 'width' => '121px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                            array('data' => $sprogram, 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                            array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                            array('data' => $tprogram, 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                            array('data' => apbd_fn($jangthnusul), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '50px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                            array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
							array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
							array('data' => apbd_fn($jangthnplus1), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                        );
                        array_unshift($temp_data, $temp);
                        $u3_data= array_merge($u3_data, $temp_data);
                        //
                        //array_unshift($temp_data, $temp);
                        //$u3_data[] = $temp_data;
                        $temp_data=array();
                        
                        $upro = $r_upro;
                        $upro_nama = $data->program;
						$sprogram = $data->sas_pn;
						$tprogram = $data->tar_pn;
						
                        $jangthnusul = (double)$data->total;
                        $jangthnplus1 = (double) $data->totalsebelum2;
                        $jangthnminus1 = (double) $data->totalsebelum;
                    } else {
                        $jangthnusul += (double) $data->total;
                        $jangthnplus1 += (double) $data->totalsebelum2;
                        $jangthnminus1 += (double) $data->totalsebelum;
                    }
                
                    //** SUB URUSAN
                    if ($u2 != $r_u2) {
                        $tkode = $u2 . '-' . $u2_nama;
                        $temp = array (
                            array('data' => $u2, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                            array('data' => $u2_nama, 'width' => '121px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '' , 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($ju2), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '50px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
							array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
							array('data' => apbd_fn($ju2_sebelum2), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),	
						);
                        array_unshift($u3_data, $temp);
                        $u2_data=array_merge($u2_data, $u3_data); 
                        $u3_data=array();
                        
                        $u2 = $r_u2;
                        $u2_nama = $data->urusansingkat;
                        if ($u2=='0.00')
                            $u2_nama = 'URUSAN PADA SEMUA SKPD';
                        
                        $ju2 = (double)$data->total;
                        $ju2_sebelum = (double) $data->totalsebelum;
                        $ju2_sebelum2 = (double) $data->totalsebelum2;
                    } else {
                        $ju2 += (double) $data->total;
                        $ju2_sebelum += (double) $data->totalsebelum;
                        $ju2_sebelum2 += (double) $data->totalsebelum2;
                    }

                    if ($u != $r_u) {
                        $tnama = $u_array[$u];
                        $temp = array (
                            //array('data' => $u, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => $tnama, 'width' => '210px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => apbd_fn($ju), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => apbd_fn($ju_sebelum2), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => apbd_fn($ju_sebelum), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),

                            array('data' => $u, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                            array('data' => $tnama, 'width' => '121px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '' , 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($ju), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '50px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
							array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
							array('data' => apbd_fn($ju_sebelum), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),	

							
						);
                        array_unshift($u2_data, $temp);
                        $u_data= array_merge($u_data, $u2_data);
                        $u2_data=array();
                        
                        $u = $r_u;
                        $u_nama = $u_array[$u];
                        $ju = (double) $data->total;
                        $ju_sebelum = (double) $data->totalsebelum;
                        $ju_sebelum2 = (double) $data->totalsebelum2;
                    } else {
                        $ju += (double) $data->total;
                        $ju_sebelum += (double) $data->totalsebelum;
                        $ju_sebelum2 += (double) $data->totalsebelum2;
                    }

                }

				//** KEGIATAN
                $tkode = $r_upro . "." .$data->kodedinas .'.' . $data->nomorkeg;
                
                $uk = '';
                if ($kodeuk=='00')
                    $uk = ' (' . $data->namasingkat . ")";
                $temp_data[] = array (
                    //array('data' => $tkode, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),                    
                    //array('data' => $data->kegiatan . $uk, 'width' => '210px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    //array('data' => $data->sasaran, 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    //array('data' => $data->target, 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    //array('data' => apbd_fn($data->total), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    //array('data' => apbd_fn($data->totalsebelum2), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    //array('data' => apbd_fn($data->totalsebelum), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    //array('data' => str_replace("||", ", ", $data->lokasi), 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),

					array('data' => $tkode, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),
					array('data' => $data->kegiatan, 'width' => '121px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => $data->sasaran , 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => str_replace("||", ", ", $data->lokasi), 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => $data->target, 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => apbd_fn($data->total), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => $data->sumberdana, 'width' => '50px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => $data->catatan, 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => $data->target1, 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => apbd_fn($data->totalsebelum2), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					
					
				);
				//** SUB KEGIATAN
                //$q = sprintf("select * from {kegiatanskpdsub} where kodekeg='%s'", db_escape_string($data->kodekeg));
                $q = sprintf("select uraian," . $sqlsub . " from {kegiatanskpdsub} where kodekeg='%s'", db_escape_string($data->kodekeg));
                $r = db_query($q);
                $numrows =0;
                $currentrows = count($temp_data);
                while ($d= db_fetch_object($r)) {
                    $numrows++;
                    $temp_data[] = array (
                        //array('data' => '', 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),                    
                        //array('data' => "<span style='width:30px;text-alignment:right;padding-right:10px;'>" . $numrows . ".</span>" . $d->uraian, 'width' => '210px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right: 1px solid black; font-size: 1em;'),
                        //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => ' border-right: 1px solid black; font-size: 1em;'),
                        //array('data' =>'', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right: 1px solid black; font-size: 1em;'),
                        //array('data' => apbd_fn($d->total), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right: 1px solid black; font-size: 1em;'),
                        //array('data' => '', 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right: 1px solid black; font-size: 1em;'),
                        //array('data' => '', 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right: 1px solid black; font-size: 1em;'),
                        //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right: 1px solid black; font-size: 1em;'),

						array('data' => '', 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
						array('data' => "<span style='width:30px;text-alignment:right;padding-right:10px;'>" . $numrows . ".</span>" . $d->uraian, 'width' => '121px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => '' , 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($d->total), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => '', 'width' => '50px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
						array('data' => apbd_fn($d->total), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),	
						
						
					);
                }
                if ($numrows>0) {
                    $temp_data[$currentrows-1][0]['style'] ="border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;";
                    $temp_data[$currentrows-1][1]['style'] ="border-right: 1px solid black; font-size: 1em;";
                    $temp_data[$currentrows-1][2]['style'] ="border-right: 1px solid black; font-size: 1em;";
                    $temp_data[$currentrows-1][3]['style'] ="border-right: 1px solid black; font-size: 1em;";
                    $temp_data[$currentrows-1][4]['style'] ="border-right: 1px solid black; font-size: 1em;";
                    $temp_data[$currentrows-1][5]['style'] ="border-right: 1px solid black; font-size: 1em;";
                    $temp_data[$currentrows-1][6]['style'] ="border-right: 1px solid black; font-size: 1em;";
                    $temp_data[$currentrows-1][7]['style'] ="border-right: 1px solid black; font-size: 1em;";
                    $temp_data[$currentrows-1][8]['style'] ="border-right: 1px solid black; font-size: 1em;";
                    $temp_data[$currentrows-1][9]['style'] ="border-right: 1px solid black; font-size: 1em;";
                    
                    $temp_data[count($temp_data)-1][0]['style'] .= "border-bottom: 1px solid black;";
                    $temp_data[count($temp_data)-1][1]['style'] .= "border-bottom: 1px solid black;";
                    $temp_data[count($temp_data)-1][2]['style'] .= "border-bottom: 1px solid black;";
                    $temp_data[count($temp_data)-1][3]['style'] .= "border-bottom: 1px solid black;";
                    $temp_data[count($temp_data)-1][4]['style'] .= "border-bottom: 1px solid black;";
                    $temp_data[count($temp_data)-1][5]['style'] .= "border-bottom: 1px solid black;";
                    $temp_data[count($temp_data)-1][6]['style'] .= "border-bottom: 1px solid black;";
                    $temp_data[count($temp_data)-1][7]['style'] .= "border-bottom: 1px solid black;";
                    $temp_data[count($temp_data)-1][8]['style'] .= "border-bottom: 1px solid black;";
                    $temp_data[count($temp_data)-1][9]['style'] .= "border-bottom: 1px solid black;";
                }
            }
            
            if (count($temp_data)>0) {
                $tkode = $upro . "-" . $upro_nama;
                $temp = array (
                    //array('data' => $upro, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    //array('data' => $upro_nama, 'width' => '210px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '' , 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'id' => 'aa', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => apbd_fn($jangthnusul), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => apbd_fn($jangthnplus1), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => apbd_fn($jangthnminus1), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),

					array('data' => $upro, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
					array('data' => $upro_nama, 'width' => '121px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => $sprogram, 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => $tprogram, 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => apbd_fn($jangthnusul), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '50px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => apbd_fn($jangthnplus1), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					
					
				);
                array_unshift($temp_data, $temp);
                $u3_data= array_merge($u3_data, $temp_data);
                //$u3_data[]= $temp_data;
                $tkode = $u2 . '-' . $u2_nama;

                $temp = array (
                    //array('data' => $u2, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    //array('data' => $u2_nama, 'width' => '210px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '' , 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => apbd_fn($ju2), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => apbd_fn($ju2_sebelum2), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => apbd_fn($ju2_sebelum), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),

					array('data' => $u2, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
					array('data' => $u2_nama, 'width' => '121px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '' , 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => apbd_fn($ju2), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '50px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => apbd_fn($ju2_sebelum2), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),	

					
				);
                array_unshift($u3_data, $temp);
                $u2_data=array_merge($u2_data, $u3_data);
                
                $tnama = $u_array[$u];
                $temp = array (
                    //array('data' => $u, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
                    //array('data' => $tnama , 'width' => '210px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => apbd_fn($ju), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => apbd_fn($ju_sebelum2), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => apbd_fn($ju_sebelum), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),

					array('data' => $u, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
					array('data' => $tnama, 'width' => '121px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '' , 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => apbd_fn($ju), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '50px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					array('data' => apbd_fn($ju_sebelum2), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),	
					
				
				);
                
                array_unshift($u2_data, $temp);
                $u_data= array_merge($u_data, $u2_data);
            }
            $rows = array_merge($rows, $u_data);
            
            if (count($rows) > 0) {
                //total
                $rows[] = array (
                    //array('data' => 'Total', 'colspan'=>'4', 'width' => '529px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
                    //array('data' => apbd_fn($total), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => apbd_fn($totalsebelum2), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => apbd_fn($totalsebelum), 'width' => '87px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => '', 'width' => '110px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),

                    //array('data' => 'Total', 'colspan'=>'5', 'width' => '490px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
					//array('data' => apbd_fn($total), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					//array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					//array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					//array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
					//array('data' => apbd_fn($totalsebelum2), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),	
					
					//****XXXX	490
                    array('data' => 'TOTAL TAHUN ' . ($tahun), 'colspan'=>'5', 'width' => '490px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
					array('data' => apbd_fn($total), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 0px solid black; font-weight:700;font-size: 1em;'),
					array('data' => 'TOTAL PRAKIRAAN TAHUN ' . ($tahun+1), 'colspan'=>'3', 'width' => '220px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
					array('data' => apbd_fn($totalsebelum2), 'width' => '80px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),	
					
				);
            }
            
            
            
        } else {
            $rows[] = array (
                array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'9')
            );
        }
        
        $opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
        if ($print) {
            //$opttbl['bgcolor'] = '#FF00FF';
            //for ($i=0; $i<count($headers); $i++) {
            //    for ($j=0;$j<count($headers[$i]); $j++) {
            //        if ($headers[$i][$j]['print'] )
            //            $headers[$i][$j]['style'] = $headers[$i][$j]['print'];
            //        else
            //            $headers[$i][$j]['style'] = "text-align: center;";
            //    }
            //}
            //
            //for ($i=0; $i<count($rows); $i++) {
            //    for ($j=0;$j<count($rows[$i]); $j++) {
            //        if ($rows[$i][$j]['print'])
            //            $rows[$i][$j]['style'] = $rows[$i][$j]['print'];
            //        else
            //            $rows[$i][$j]['style'] = '';
            //        
            //    }
            //}
        } 
        $toutput='';
        if ($kodeuk!='00') {
                if ($print==0) {
                    $toutput = "        
                    <div style='clear:both'></div>
                    <div style='float:right; width:200px;border: 1px solid #eee'>
                        <div style='text-align:center;margin-bottom: 75px;'>" . $pimpinanjabatan . "</div>
                        <div style='text-align:center;text-decoration: underline;'>". $pimpinannama."</div>
                        <div style='text-align:center;'>NIP. ".$pimpinannip."</div>                        
                    </div>
                    <div style='clear:both'></div>
                    ";
                } else {
                    $rows[] = array (
                        array('data' => '', 'width' => '100px'),                    
                        array('data' => '', 'width' => '144px'),
                        array('data' => '', 'width' => '122px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => $pimpinanjabatan , 'width' => '182px', 'colspan'=>'2', 'height'=>'50px', 'style'=>'text-align:center'),
                    );
                    $rows[] = array (
                        array('data' => '', 'width' => '100px'),                    
                        array('data' => '', 'width' => '144px'),
                        array('data' => '', 'width' => '122px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => $pimpinannama , 'width' => '182px', 'colspan'=>'2', 'style'=>'text-decoration: underline;text-align:center'),
                    );
                    $rows[] = array (
                        array('data' => '', 'width' => '100px'),                    
                        array('data' => '', 'width' => '144px'),
                        array('data' => '', 'width' => '122px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => 'NIP.' . $pimpinannip , 'width' => '182px', 'colspan'=>'2', 'style'=>'text-align:center'),
                    );
                  
                }
        }
		$rows1[] = array (array('data' => '', 'colspan'=>'2'));
		$output .= theme_box('', apbd_theme_table($headers1, $rows1, $opttbl));
        
        $output .= theme_box('', apbd_theme_table($headers, $rows, $opttbl));
        $output .= $toutput;
        if ($limit >0)
            $output .= theme ('pager', NULL, $limit, 0);
        
        return $output;
        
    }
    
    function renjaskpdv2_form () {
        $form['formdata'] = array (
            '#type' => 'fieldset',
            '#title'=> 'Parameter Laporan',
            '#collapsible' => TRUE,
            '#collapsed' => FALSE,        
        );
        
        $kodeuk = arg(3);
        $sumberdana=arg(4);
        $tahun = arg(5);
        $limit = arg(6);

        if (isset($kodeuk)) {
            $form['formdata']['#collapsed'] = TRUE;
            //if (isUserKecamatan())
            //    if ($kodeuk != apbd_getuseruk())
            //        $form['formdata']['#collapsed'] = FALSE;
        }
        
      
        $pquery = "select kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by namasingkat" ;
        $pres = db_query($pquery);
        $dinas = array();
        
        
        $dinas['00'] ='SEMUA SKPD/SELURUH KABUPATEN';
        while ($data = db_fetch_object($pres)) {
            $dinas[$data->kodeuk] = $data->namasingkat;
        }
        $type='select';
        if (!isSuperuser()) {
            $type = 'hidden';
            $kodeuk = apbd_getuseruk();
            //drupal_set_message('user kec');
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
		/*
        $form['formdata']['status']= array(
            '#type'         => 'select', 
            '#title'        => 'Status',
            '#options'	=> array('Keseluruhan', 'Lolos', 'Tidak Lolos'),
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $status, 
        );
		*/
		$recordopt = array();
		$recordopt['00'] = 'SEMUA';
		$recordopt['dau'] = 'DAU/PAD/LAINNYA';
		$recordopt['banprov'] = 'BANPROV';
		$recordopt['dak'] = 'DAK';
		$recordopt['apbdkab'] = 'APBD KAB';
		$recordopt['apbdprov'] = 'APBD PROV';
		$recordopt['apbn'] = 'APBN';
		$form['formdata']['sumberdana']= array(
			'#type'         => 'select', 
			'#title'        => 'Sumber Pendanaan',
			'#options'	=> $recordopt,
			'#width'         => 20, 
			'#default_value'=> $sumberdana, 
		);

		
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
        $recordopt = array();
        $recordopt['0'] = 'Tampilkan semua';
        $recordopt['15'] = '15 Record/Halaman';
        $recordopt['30'] = '30 Record/Halaman';
        $form['formdata']['record']= array(
            '#type'         => 'select', 
            '#title'        => 'Record/Halaman',
            '#options'	=> $recordopt,
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $limit, 
        ); 
        $form['formdata']['submit'] = array (
            '#type' => 'submit',
            '#value' => 'Proses'
        );
        
        return $form;
    }
    function renjaskpdv2_form_submit($form, &$form_state) {
        //$kodeuk = $form_state['values']['kodeuk'];
        $tahun = $form_state['values']['tahun'];
        $record = $form_state['values']['record'];
        $kodeuk = $form_state['values']['kodeuk'];
        $sumberdana = $form_state['values']['status'];
        $uri = 'apbd/laporanpenetapan/renjaskpdv2/' .$kodeuk .'/'. $sumberdana . '/' . $tahun . '/' . $record;
        drupal_goto($uri);
        
    }
?>