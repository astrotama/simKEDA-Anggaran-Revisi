<?php
    function musrenbangcam_main() {
        drupal_set_title('Laporan Usulan Data Kegiatan Musrenbangcam');
        drupal_set_html_head('<style>tr.odd {background-color: #ffffff;}</style>');
        drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
        drupal_add_css('files/css/kegiatancam.css');
        
        $kodeuk = arg(3);
        $tahun = arg(4);
		$kodeuktujuan = arg(5);
		$sumberdana= arg(6);
        $limit = arg(7);
        $exportpdf = arg(8);
        if (!isset($kodeuk)) 
            return drupal_get_form('musrenbangcam_form');

        if (isUserKecamatan()) {
            if ($kodeuk != apbd_getuseruk())
                return drupal_get_form('musrenbangcam_form');
        }	
        if (isset($exportpdf) && ($exportpdf=='pdf'))  {
            //echo 'test';
            $htmlContent = GenReportForm(1);
            $pdfFile = "musrenbangcam2.pdf";
            apbd_ExportPDF("L", "F4", $htmlContent, $pdfFile);            
        } else {
            $url = 'apbd/laporanpenetapan/musrenbangcam/'. $kodeuk . '/'. $tahun . '/' . $kodeuktujuan . '/'. $sumberdana . "/0/pdf";
            $output .= drupal_get_form('musrenbangcam_form');
            $output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
            $output .= GenReportForm();
            return $output;
        }

    }
    function GenReportForm($print=0) {
        
        $kodeuk = arg(3);
        $tahun = arg(4);
		$kodeuktujuan = arg(5);
		$sumberdana= arg(6);
        $limit = arg(7);
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
        $customwhere = ' and k.tahun=\'%s\'';
        if ($kodeuk!='00') {
            $customwhere .= ' and k.kodeuk=\'%s\' ';
        }
		if ($kodeuktujuan !='') {
			$qlike .= sprintf(' and k.kodeuktujuan=\'%s\' ', $kodeuktujuan);
		}

		switch($sumberdana) {
			case 'apbd':
				$qlike .= sprintf(' and k.apbdkab>0 ');
				break;
			case 'pnpm':
				$qlike .= sprintf(' and k.pnpm>0 ');
				break;
			case 'pik':
				$qlike .= sprintf(' and k.pik>0 ');
				break;
		}
				
        $where = ' where true' . $customwhere . $qlike ;
    
        $sql = 'select uu.kodedinas, p.kodeu, u.urusansingkat, p.program, p.np, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, k.total, k.totalsebelum, k.totalsebelum2, d.namasingkat from {kegiatankec} k left join unitkerjaskpd d on(k.kodeuktujuan=d.kodeuk) left join program p on (k.kodepro = p.kodepro) left join urusan u on (p.kodeu = u.kodeu) left join unitkerja uu on(k.kodeuk=uu.kodeuk)' . $where;
        $fsql = sprintf($sql, db_escape_string($tahun), db_escape_string($kodeuk));
        //$limit = 13;
        //drupal_set_message( $fsql);
        $countsql = "select count(*) as cnt from {kegiatankec} k" . $where;
        $fcountsql = sprintf($countsql, db_escape_string($tahun), db_escape_string($kodeuk));
        if ($limit>0) {
            //drupal_set_message($tablesort);
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
        $rows= array();
        $headers = array();
        $headers1[] = array (array ('data'=>'MUSRENBANG KECAMATAN', 'colspan' =>'8', 'style' =>'border: 0px solid white;font-weight:900;text-align: center;font-size: 1.3em;'));
        $headers1[] = array (array ('data'=> $namauk . ' PEMERINTAH ' . variable_get('apbdwilayah', ''), 'colspan' =>'8', 'style' =>'border: 0px solid white;font-weight:900;text-align: center;font-size: 1.3em;'));
        $headers1[] = array (array ('data'=>'TAHUN '  . $tahun, 'align'=>'center', 'colspan' =>'8', 'style' =>'border: 0px solid white;font-weight:900;text-align: center;font-size: 1.3em;'));

        $headers[] = array (
                             array('data' => 'KODE', 'rowspan' => '2', 'width'=>'100px', 'align' => 'center', 'style' => 'border: 1px solid black; text-align: center;font-weight: 900;font-size: 1.3em;'),
                             array('data' => 'USULAN KEGIATAN', 'rowspan' => '2', 'width'=>'184px', 'align' => 'center', 'style' => 'border-right:1px solid black; border-bottom: 1px solid black;border-top: 1px solid black; text-align: center;font-weight: 900;font-size: 1.3em;'),
                             array('data' => 'INDIKATOR KELUARAN', 'rowspan' => '2', 'width'=>'122px', 'align' => 'center', 'style' => 'border-right:1px solid black; border-bottom: 1px solid black;border-top: 1px solid black; text-align: center;font-weight: 900;font-size: 1.3em;'),
                             array('data' => 'LOKASI', 'rowspan' => '2', 'width'=>'79px', 'align' => 'center', 'style' => 'border-right:1px solid black; border-bottom: 1px solid black;border-top: 1px solid black; text-align: center;font-weight: 900;font-size: 1.3em;'),
                             array('data' => 'ANGGARAN TAHUN ' . $tahun . '(Rp)', 'rowspan' => '2', 'width'=>'95px', 'align' => 'center', 'style' => 'border-right:1px solid black; border-bottom: 1px solid black;border-top: 1px solid black; text-align: center;font-weight: 900;font-size: 1.3em;'),
                             array('data' => 'ANGGARAN SEBELUMNYA (Rp)', 'colspan'=> '2', 'width'=>'190px', 'align' => 'center', 'style' => 'border-right:1px solid black; border-bottom: 1px solid black;border-top: 1px solid black; text-align: center;font-weight: 900;font-size: 1.3em;'),
                             array('data' => 'DINAS TEKNIS', 'rowspan' => '2', 'width'=>'100px', 'align' => 'center', 'style' => 'border-right:1px solid black; border-bottom: 1px solid black;border-top: 1px solid black; text-align: center;font-weight: 900;font-size: 1.3em;'),
                             );
        $headers[] = array (
                             array('data' => 'TAHUN ' . ($tahun-1), 'width'=>'95px', 'align' => 'center', 'style'=>'border-right:1px solid black; border-bottom: 1px solid black;text-align: center;font-size: 1.3em;'),
                             array('data' => 'TAHUN ' . ($tahun+1), 'width'=>'95px', 'align' => 'center', 'style'=>'border-right:1px solid black; border-bottom: 1px solid black;text-align: center;font-size: 1.3em;'),
                             );
        $headers[] = array (
                             array('data' => '1', 'align' => 'center', 'width'=> '100px', 'style' => 'border-right:1px solid black; border-bottom: 1px solid black; border-left:1px solid black;text-align: center;font-size: 1.3em;'),
                             array('data' => '2', 'align' => 'center', 'width'=>'184px', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;text-align: center;font-size: 1.3em;'),
                             array('data' => '3', 'align' => 'center', 'width'=>'122px', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;text-align: center;font-size: 1.3em;'),
                             array('data' => '4', 'align' => 'center', 'width'=>'79px', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;text-align: center;font-size: 1.3em;'),
                             array('data' => '5', 'align' => 'center', 'width'=>'95px', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;text-align: center;font-size: 1.3em;'),
                             array('data' => '6', 'align' => 'center', 'width'=>'95px', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;text-align: center;font-size: 1.3em;'),
                             array('data' => '7', 'align' => 'center', 'width'=> '95px', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;text-align: center;font-size: 1.3em;'),
                             array('data' => '8', 'align' => 'center', 'width'=>'100px', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;text-align: center;font-size: 1.3em;'),
                             );
        if ($result) {
            $u_array = array('URUSAN PADA SEMUA SKPD','URUSAN WAJIB','URUSAN PILIHAN');
            $pu=(double)0;
            $u ='';
            $u_nama='';
            $ju=(double)0;
            $ju_sebelum=(double)0;
            $ju_sebelum2=(double)0;
            
            $pu2=0;
            $u2='';
            $u2_nama='';
            $ju2=(double)0;
            $ju2_sebelum=(double)0;
            $ju2_sebelum2=(double)0;
            
            $pupro=0;
            $upro='';
            $upro_nama='';
            $jupro=(double)0;
            $jupro_sebelum=(double)0;
            $jupro_sebelum2=(double)0;
            
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

                if ($first) {
                    $u = $r_u;
                    $u2 = $r_u2;
                    $upro = $r_upro;
                    $u_nama = $u_array[$u];                    
                    $u2_nama = $data->urusansingkat;
                    $upro_nama = $data->program;
                    
                    $ju = $data->total;
                    $ju_sebelum = $data->totalsebelum;
                    $ju_sebelum2 = $data->totalsebelum2;

                    $ju2 = $data->total;
                    $ju2_sebelum = $data->totalsebelum;
                    $ju2_sebelum2 = $data->totalsebelum2;

                    $jupro = $data->total;
                    $jupro_sebelum = $data->totalsebelum;
                    $jupro_sebelum2 = $data->totalsebelum2;
                    $first=false;
                } else {
                    if ($r_upro != $upro) {                        
                        $temp = array (
                            array('data' => $upro, 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;border-left:1px solid black;font-weight:600;font-size: 1em;'),
                            array('data' => $upro_nama, 'width'=> '184px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                            array('data' => '', 'width'=> '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                            array('data' => '', 'width'=> '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                            array('data' => apbd_fn($jupro), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                            array('data' => apbd_fn($jupro_sebelum), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                            array('data' => apbd_fn($jupro_sebelum2), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                            array('data' => '', 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),                            
                        );
                        array_unshift($temp_data, $temp);
                        $u3_data= array_merge($u3_data, $temp_data);
                        //
                        //array_unshift($temp_data, $temp);
                        //$u3_data[] = $temp_data;
                        $temp_data=array();
                        
                        $upro = $r_upro;
                        $upro_nama = $data->program;
                        $jupro = $data->total;
                        $jupro_sebelum = (double) $data->totalsebelum;
                        $jupro_sebelum2 = (double) $data->totalsebelum2;
                    } else {
                        $jupro += (double) $data->total;
                        $jupro_sebelum += (double) $data->totalsebelum;
                        $jupro_sebelum2 += (double) $data->totalsebelum2;
                    }
                
                    
                    if ($u2 != $r_u2) {
                        $temp = array (
                            array('data' => $u2, 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;border-left:1px solid black;font-weight:700;font-size: 1em;'),
                            array('data' => $u2_nama , 'width'=> '184px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width'=> '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width'=> '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($ju2), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($ju2_sebelum), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($ju2_sebelum2), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),                            
                        );
                        array_unshift($u3_data, $temp);
                        $u2_data=array_merge($u2_data, $u3_data); 
                        //array_unshift($u3_data, $temp);
                        //$u2_data[]= $u3_data;
                        $u3_data=array();
                        
                        $u2 = $r_u2;
                        $u2_nama = $data->urusansingkat;
                        $ju2 = $data->total;
                        $ju2_sebelum = (double) $data->totalsebelum;
                        $ju2_sebelum2 = (double) $data->totalsebelum2;
                    } else {
                        $ju2 += (double) $data->total;
                        $ju2_sebelum += (double) $data->totalsebelum;
                        $ju2_sebelum2 += (double) $data->totalsebelum2;
                    }

                    if ($u != $r_u) {
                        $temp = array (
                            array('data' => $u, 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;border-left:1px solid black;font-weight: 900;font-size: 1em;'),
                            array('data' => $u_nama, 'width'=> '184px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight: 900;font-size: 1em;'),
                            array('data' => '', 'width'=> '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight: 900;font-size: 1em;'),
                            array('data' => '', 'width'=> '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight: 900;font-size: 1em;'),
                            array('data' => apbd_fn($ju), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight: 900;font-size: 1em;'),
                            array('data' => apbd_fn($ju_sebelum), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight: 900;font-size: 1em;'),
                            array('data' => apbd_fn($ju_sebelum2), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight: 900;font-size: 1em;'),
                            array('data' => '', 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight: 900;font-size: 1em;'),                            
                        );
                        array_unshift($u2_data, $temp);
                        $u_data= array_merge($u_data, $u2_data);
                        //array_unshift($u2_data, $temp);
                        //$u_data[]= $u2_data;
                        $u2_data=array();
                        
                        $u = $r_u;
                        $u_nama = $u_array[$u];
                        $ju = $data->total;
                        $ju_sebelum = (double) $data->totalsebelum;
                        $ju_sebelum2 = (double) $data->totalsebelum2;
                    } else {
                        $ju += (double) $data->total;
                        $ju_sebelum += (double) $data->totalsebelum;
                        $ju_sebelum2 += (double) $data->totalsebelum2;
                    }

                }
                
                //$kodeitem = $r_upro . "." .$data->kodeuk;
                $tkode = $r_upro . "." .$data->kodedinas .'.' . $data->nomorkeg;
                
                $indikator = $data->sasaran . "/" . $data->target;
                $temp_data[] = array (
                    //array('data' => $no, 'align' => 'right'),
                    
                    array('data' => $tkode, 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;border-left:1px solid black;'),
                    array('data' => $data->kegiatan, 'width'=> '184px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;'),
                    array('data' => $indikator, 'width'=> '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;'),
                    array('data' => str_replace("||", ", ", $data->lokasi), 'width'=> '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;'),
                    array('data' => apbd_fn($data->total), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;'),
                    array('data' => apbd_fn($data->totalsebelum), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;'),
                    array('data' => apbd_fn($data->totalsebelum2), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;'),
                    array('data' => $data->namasingkat, 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;'),
                );
                
            }
            
            if (count($temp_data)>0) {
                $temp = array (
                    array('data' => $upro, 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;border-left:1px solid black;font-weight:600;font-size: 1em;'),
                    array('data' => $upro_nama, 'width'=> '184px', 'align' => 'left', 'valign'=>'top', 'id' => 'aa', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                    array('data' => '', 'width'=> '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                    array('data' => '', 'width'=> '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                    array('data' => apbd_fn($jupro), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                    array('data' => apbd_fn($jupro_sebelum), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                    array('data' => apbd_fn($jupro_sebelum2), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),
                    array('data' => '', 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:600;font-size: 1em;'),                            
                );
                array_unshift($temp_data, $temp);
                $u3_data= array_merge($u3_data, $temp_data);
                //$u3_data[]= $temp_data;

                $temp = array (
                    array('data' => $u2, 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;border-left:1px solid black;font-weight:700;font-size: 1em;'),
                    array('data' => $u2_nama, 'width'=> '184px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width'=> '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width'=> '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($ju2), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($ju2_sebelum), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($ju2_sebelum2), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:700;font-size: 1em;'),                            
                );
                array_unshift($u3_data, $temp);
                $u2_data=array_merge($u2_data, $u3_data); 

                $temp = array (
                    array('data' => $u, 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;border-left:1px solid black;font-weight:900;font-size: 1em;'),
                    array('data' => $u_nama, 'width'=> '184px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:900;font-size: 1em;'),
                    array('data' => '', 'width'=> '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:900;font-size: 1em;'),
                    array('data' => '', 'width'=> '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($ju), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($ju_sebelum), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($ju_sebelum2), 'width'=> '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:900;font-size: 1em;'),
                    array('data' => '', 'width'=> '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right:1px solid black;border-bottom: 1px solid black;font-weight:900;font-size: 1em;'),                            
                );
                array_unshift($u2_data, $temp);
                $u_data= array_merge($u_data, $u2_data);
            }
            $rows = array_merge($rows, $u_data);
            //print_r($u_data);
            //echo count($u_data);
            
            
            
        } else {
            $rows[] = array (
                array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
            );
        }        
        if ($print) {
            //for ($i=0; $i<count($headers); $i++) {
            //    for ($j=0;$j<count($headers[$i]); $j++) {
            //        if ($headers[$i][$j]['print'] )
            //            $headers[$i][$j]['style'] .= $headers[$i][$j]['print'];
            //    }
            //}
            //
            //for ($i=0; $i<count($rows); $i++) {
            //    for ($j=0;$j<count($rows[$i]); $j++) {
            //        if ($rows[$i][$j]['print'])
            //            $rows[$i][$j]['style'] .= $rows[$i][$j]['print'];
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
                        array('data' => '', 'width' => '184px'),
                        array('data' => '', 'width' => '122px'),
                        array('data' => '', 'width' => '79px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => $pimpinanjabatan , 'width' => '195px', 'colspan'=>'2', 'height'=>'50px', 'style'=>'text-align:center'),
                    );
                    $rows[] = array (
                        array('data' => '', 'width' => '100px'),                    
                        array('data' => '', 'width' => '184px'),
                        array('data' => '', 'width' => '122px'),
                        array('data' => '', 'width' => '79px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => $pimpinannama , 'width' => '195px', 'colspan'=>'2', 'style'=>'text-decoration: underline;text-align:center'),
                    );
                    $rows[] = array (
                        array('data' => '', 'width' => '100px'),                    
                        array('data' => '', 'width' => '184px'),
                        array('data' => '', 'width' => '122px'),
                        array('data' => '', 'width' => '79px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => 'NIP.' . $pimpinannip , 'width' => '195px', 'colspan'=>'2', 'style'=>'text-align:center'),
                    );
                  
                }
        }
        
		$rows1[] = array (array('data' => '', 'colspan'=>'2'));
		$output .= theme_box('', apbd_theme_table($headers1, $rows1, $opttbl));

        $output .= theme_box('', apbd_theme_table($headers, $rows, array('cellspacing' => '1', 'cellpadding' => '1')));
        $output .= $toutput;
        if ($limit >0)
            $output .= theme ('pager', NULL, $limit, 0);
        //$r = drupal_get_css();
        //print_r($r);
        return $output;
        
    }
    
    function musrenbangcam_form () {
        $form['formdata'] = array (
            '#type' => 'fieldset',
            '#title'=> 'Parameter Laporan',
            '#collapsible' => TRUE,
            '#collapsed' => FALSE,        
        );
        
        $kodeuk = arg(3);
        $tahun = arg(4);
		$kodeuktujuan = arg(5);
		$sumberdana= arg(6);
        $limit = arg(7);

        if (isset($kodeuk)) {
            $form['formdata']['#collapsed'] = TRUE;
            if (isUserKecamatan())
                if ($kodeuk != apbd_getuseruk())
                    $form['formdata']['#collapsed'] = FALSE;
        }
        
      
        $pquery = "select kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 and iskecamatan=1 order by namauk" ;
        $pres = db_query($pquery);
        $dinas = array();
        
        
        $dinas['00'] = 'SEMUA KECAMATAN';
        while ($data = db_fetch_object($pres)) {
            $dinas[$data->kodeuk] = $data->namauk;
        }
        $type='select';
        if (isUserKecamatan()) {
            $type = 'hidden';
            $kodeuk = apbd_getuseruk();
            //drupal_set_message('user kec');
        }
        
        $form['formdata']['kodeuk']= array(
            '#type'         => $type, 
            '#title'        => 'Kecamatan',
            '#options'	=> $dinas,
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $kodeuk, 
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

		//uktujuan
		$pquery1 = "select kodeuk, namasingkat from {unitkerjaskpd} order by namasingkat" ;
		$pres1 = db_query($pquery1);
		$dinastujuan = array();        
		
		$dinastujuan[''] ='SEMUA DINAS TEKNIS';
		while ($data1 = db_fetch_object($pres1)) {
			$dinastujuan[$data1->kodeuk] = $data1->namasingkat;
		} 
		$type='select';
		if (!isSuperuser() and !isUserKecamatan()) {
			$type = 'hidden';
			$kodeuktujuan = apbd_getuseruk();
			//drupal_set_message('user kec');
		}	
		$form['formdata']['kodeuktujuan']= array(
			'#type'         => $type, 
			'#title'        => 'Dinas Teknis',
			'#options'	=> $dinastujuan,
			//'#description'  => 'kodeuktujuan', 
			//'#maxlength'    => 60, 
			'#width'         => 20, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $kodeuktujuan, 
		);

		$recordopt = array();
		$recordopt['00'] = 'APBD+PIK+PNPM';
		$recordopt['apbd'] = 'APBD';
		$recordopt['pik'] = 'PIK';
		$recordopt['pnpm'] = 'PNPM';
		$form['formdata']['sumberdana']= array(
			'#type'         => 'select', 
			'#title'        => 'Sumber Pendanaan',
			'#options'	=> $recordopt,
			'#width'         => 20, 
			'#default_value'=> $sumberdana, 
		);
		
        $recordopt = array();
        $recordopt['0'] = 'Tampilkan semua';
        $recordopt['13'] = '13 Record/Halaman';
        $recordopt['26'] = '26 Record/Halaman';
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
    function musrenbangcam_form_submit($form, &$form_state) {
        $kodeuk = $form_state['values']['kodeuk'];
		$kodeuktujuan = $form_state['values']['kodeuktujuan'];
        $tahun = $form_state['values']['tahun'];
        $record = $form_state['values']['record'];
		$sumberdana = $form_state['values']['sumberdana'];
        
        $uri = 'apbd/laporanpenetapan/musrenbangcam/' .$kodeuk . '/' . $tahun . '/' . $kodeuktujuan . '/'. $sumberdana . '/' . $record;
        drupal_goto($uri);
        
    }
    function privGetLokasi($loc) {
        $ks = split(",", $loc);
        $iks = "";
        if (count($ks)>0) {
            $iks = join("','", $ks);
        }
        $iks = "('" . $iks . "')";
        $pquery = sprintf("select kodedesa, namadesa from desa where kodedesa in %s", $iks);
        //drupal_set_message($pquery);
        
        $pres = db_query($pquery);
        $out ="";
        while ($data = db_fetch_object($pres)) {
            $out .= $data->namadesa . ", ";
        }
        if (strlen($out)>0) {
            $out = substr($out,0, strlen($out)-2);
        }
        return $out;
        
    }
?>
