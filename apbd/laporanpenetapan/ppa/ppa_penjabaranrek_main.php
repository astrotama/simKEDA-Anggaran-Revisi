<?php
    function ppa_penjabaranrek_main() {
        $h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
        $h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
        drupal_set_html_head($h);
        drupal_add_css('files/css/kegiatancam.css');
            
        $kodeuk = arg(3);
        $tahun = arg(4);
        $limit = arg(5);
        $exportpdf = arg(6);
        if (!isset($tahun)) 
            return drupal_get_form('ppa_penjabaranrek_form');

        //if (isUserKecamatan()) {
        //    if ($kodeuk != apbd_getuseruk())
        //        return drupal_get_form('musrenbangcam_form');
        //}	

        if (isset($exportpdf) && ($exportpdf=='pdf'))  {
            //require_once('test.php');
            //myt();
            $htmlContent = GenReportForm(1);
            $pdfFile = 'ppa_penjabaranrek_form.pdf';
            apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
            
        } else {
            $url = 'apbd/laporanpenetapan/ppa_penjabaranrek/'. $kodeuk .'/'. $tahun . "/0/pdf";
            $output .= drupal_get_form('ppa_penjabaranrek_form');
            $output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
            $output .= GenReportForm();
            return $output;
        }        
    }

    function GenReportForm($print=0) {
        
        $kodeuk = arg(3);
        $tahun = intval(arg(4));
        $limit = arg(5);
        $namauk = '';
        $pimpinannama='';
        $pimpinannip='';
        $pimpinanjabatan='';
        $pquery = sprintf("select k.kodeuk, u.kodeu, u.urusansingkat, k.namauk, k.kodedinas,k.pimpinannama, k.pimpinannip, k.pimpinanjabatan from {unitkerja} k left join {urusan} u on (substr(k.kodedinas,1,3) = u.kodeu) where kodeuk='%s'", db_escape_string($kodeuk)) ;
        $pres = db_query($pquery);
        if ($data = db_fetch_object($pres)) {
            $kodeu = $data->kodeu;
            $kodeu =substr($kodeu,0,1) . "." . substr($kodeu,1);
            $namau = $data->urusansingkat;
            $kodedinas = $data->kodedinas;
            $kodedinas = substr($kodedinas,0,1) . "." . substr($kodedinas,1,2) . "." . substr($kodedinas,3);
            $namadinas = $data->namauk;
            $pimpinannama=$data->pimpinannama;
            $pimpinannip= $data->pimpinannip;
            $pimpinanjabatan=$data->pimpinanjabatan;			
        }
        //---
        $col1 = '50px';
        $col2 = '190px';		//'175px';
        $colstarget = '135px';	//'150px';	//'125px';

        $colnompegawai = '90px';
		$colnombarangjasa = '90px';
		$colnommodal = '90px';
        $colplafon = '90px';
        
		$coltotal = '510px';	//'540px';
        
        
        $tablesort=' order by k.kodepro, k.nomorkeg';
        $customwhere = ' and k.tahun=\'%s\' ';
        $customwhere .= ' and k.kodeuk=\'%s\' ';
        $customwhere .= ' and k.jenis=\'2\' ';
        $where = ' where true' . $customwhere . $qlike ;
    
        $sql = 'select d.namasingkat, d.kodedinas, p.kodeu, u.urusan, p.program, p.sasaran as pro_sasaran, p.target as pro_target, p.np, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, k.total, k.totalsebelum, k.totalsebelum2, k.totalpenetapan, k.apbdkab, k.pnpm, d.namasingkat, k.nompegawai, k.nombarangjasa, k.nommodal from {kegiatanppa} k left join unitkerja d on(k.kodeuk=d.kodeuk) left join program p on (k.kodepro = p.kodepro) left join urusan u on (p.kodeu = u.kodeu)' . $where;
        $fsql = sprintf($sql, db_escape_string($tahun), db_escape_string($kodeuk));

        $countsql = "select count(*) as cnt from {kegiatanppa} k" . $where;
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
        $kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
        $headers1[] = array (
            array('data' => 'URUSAN', 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border: 0px solid white; font-weight: bold; font-size: 110%;'),                    
            array('data' => ': ' . $kodeu . " - " . $namau, 'colspan' => '4', 'width' => '600px', 'align' => 'left', 'valign'=>'top', 'style' => 'border: 0px solid white; font-weight: bold; font-size: 110%;'),
        );

        $headers1[] = array (
            array('data' => 'SKPD', 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border: 0px solid white; font-weight: bold; font-size: 110%;'),                    
            array('data' => ': ' . $kodedinas . " - " . $namadinas, 'colspan' => '4', 'width' => '600px', 'align' => 'left', 'valign'=>'top', 'style' => 'border: 0px solid white; font-weight: bold; font-size: 110%;'),
        );

        //$output .= apbd_theme_table(array(), $rheader);
        
        $rows= array();
        $headers[] = array (
                    array('data' => 'NO',  'width'=> $col1, 'style' => 'border: 1px solid black; text-align:center;font-weight: bold; font-size: 100%'),
                    array('data' => 'PROGRAM / KEGIATAN', 'width' => $col2, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                    array('data' => 'SASARAN', 'width' => $colstarget, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                    array('data' => 'TARGET', 'width' => $colstarget, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                    array('data' => 'PEGAWAI',  'width' => $colnompegawai, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                    array('data' => 'BARANG JASA',  'width' => $colnombarangjasa, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                    array('data' => 'MODAL',  'width' => $colnommodal, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                    array('data' => 'PLAFON ANGGARAN',  'width' => $colplafon, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                );

        if ($result) {
            $jprogram = (double)0;
            $total = (double) 0;
			
			$jppegawai = (double)0;
            $jpbarangjasa = (double)0;
			$jpmodal = (double)0;
			
			$totalpegawai = (double)0;
            $totalbarangjasa = (double)0;
			$totalmodal = (double)0;
			
            $first=true;
            
            $u_data = array();
            $temp_data = array();
            
            while ($data = db_fetch_object($result)) {                
                $no++;
                $r_kodepro = $data->kodepro;
                $r_namapro = $data->program;
               
                $totalpegawai += (double) $data->nompegawai;
				$totalbarangjasa += (double) $data->nombarangjasa;
				$totalmodal += (double) $data->nommodal;
				$total += (double) $data->total;

                if ($first) {
                    $kodepro = $r_kodepro;
                    $namapro = $r_namapro;
                    
					$jppegawai = (double) $data->nompegawai;
					$jpbarangjasa = (double) $data->nombarangjasa;
					$jpmodal = (double) $data->nommodal;
					$jprogram = (double) $data->total;
                    
					$first=false;
					
                } else {
                    if ($r_kodepro != $kodepro) {
                        //echo 't1';
                        $temp = array (
                            array('data' => $kodepro, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; border-left: 1px solid black; font-weight:bold;font-size: 100%;'),                            
                            array('data' => $namapro, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                            array('data' => $data->pro_sasaran , 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                            array('data' => $data->pro_target, 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                            array('data' => apbd_fn($jppegawai), 'width' => $colnompegawai, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                            array('data' => apbd_fn($jpbarangjasa), 'width' => $colnombarangjasa, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                            array('data' => apbd_fn($jpmodal), 'width' => $colnommodal, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                            array('data' => apbd_fn($jprogram), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                        );
                        
                        array_unshift($temp_data, $temp);
                        $u_data= array_merge($u_data, $temp_data);

                        $temp_data=array();
                        $kodepro = $r_kodepro;
                        $namapro = $r_namapro;
						
                        $jprogram = (double) $data->total;
						$jppegawai = (double) $data->nompegawai;
						$jpbarangjasa = (double) $data->nombarangjasa;
						$jpmodal = (double) $data->nommodal;
						
                    } else {
                        $jprogram += (double) $data->total;
						$jppegawai += (double) $data->nompegawai;
						$jpbarangjasa += (double) $data->nombarangjasa;
						$jpmodal += (double) $data->nommodal;
                    }                
                }
                
                $tkode = $kodepro. "." . $data->nomorkeg;

                $temp_data[] = array (
                    array('data' => $tkode, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; border-left: 1px solid black; font-size: 100%;'),                    
                    array('data' => $data->kegiatan , 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 100%;'),
                    array('data' => $data->sasaran, 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 100%;'),
                    array('data' => $data->target, 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 100%;'),
                    array('data' => apbd_fn($data->nompegawai), 'width' => $colnompegawai, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 100%;'),
                    array('data' => apbd_fn($data->nombarangjasa), 'width' => $colnombarangjasa, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 100%;'),
                    array('data' => apbd_fn($data->nommodal), 'width' => $colnommodal, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 100%;'),
                    array('data' => apbd_fn($data->total), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 100%;'),
                );                
            }
            
            if (count($temp_data)>0) {                
                $temp = array (
                    array('data' => $kodepro, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; border-left: 1px solid black; font-weight:bold;font-size: 100%;'),                            
                    array('data' => $namapro, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                    array('data' => $data->pro_sasaran , 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                    array('data' => $data->pro_target, 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                    array('data' => apbd_fn($jppegawai), 'width' => $colnompegawai, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                    array('data' => apbd_fn($jpbarangjasa), 'width' => $colnombarangjasa, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                    array('data' => apbd_fn($jpmodal), 'width' => $colnommodal, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                    array('data' => apbd_fn($jprogram), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                );


                array_unshift($temp_data, $temp);
                $u_data = array_merge($u_data, $temp_data);
            }
            $rows = array_merge($rows, $u_data);
            
            if (count($rows) > 0) {
                //total
                $rows[] = array (
                    array('data' => 'Total', 'colspan'=>'4', 'width' => $coltotal, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: medium;font-weight:bold;'),                            
                    array('data' => apbd_fn($totalpegawai), 'width' => $colnompegawai, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: medium;'),
                    array('data' => apbd_fn($totalbarangjasa), 'width' => $colnombarangjasa, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: medium;'),
                    array('data' => apbd_fn($totalmodal), 'width' => $colnommodal, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: medium;'),
                    array('data' => apbd_fn($total), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: medium;'),
                );
            }
            
            
            
        } 
        
        $opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

		$rows1[] = array (array('data' => '', 'colspan'=>'2'));
		$output .= theme_box('', apbd_theme_table($headers1, $rows1, $opttbl));

		$toutput='';
        //if ($kodeuk!='00') {
		if (!isSuperuser()) {
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
                        array('data' => '', 'width' => '50px'),                    
                        array('data' => '', 'width' => '190px'),
                        array('data' => '', 'width' => '150px'),
                        array('data' => '', 'width' => '150px'),
                        array('data' => $pimpinanjabatan , 'width' => '360px', 'colspan'=>'4', 'height'=>'50px', 'style'=>'text-align:center'),
                    );
                    $rows[] = array (
                        array('data' => '', 'width' => '50px'),                    
                        array('data' => '', 'width' => '190px'),
                        array('data' => '', 'width' => '150px'),
                        array('data' => '', 'width' => '150px'),
                        array('data' => $pimpinannama , 'width' => '360px', 'colspan'=>'4', 'style'=>'text-decoration: underline;text-align:center'),
                    );
                    $rows[] = array (
                        array('data' => '', 'width' => '50px'),                    
                        array('data' => '', 'width' => '190px'),
                        array('data' => '', 'width' => '150px'),
                        array('data' => '', 'width' => '150px'),
                        array('data' => "NIP. " . $pimpinannip , 'width' => '360px', 'colspan'=>'4', 'style'=>'text-align:center'),
                    );
                  
                }
				
        }
		
		$output .= theme_box('', apbd_theme_table($headers, $rows, $opttbl));
        $output .= $toutput;
        if ($limit >0)
            $output .= theme ('pager', NULL, $limit, 0);
        
        return $output;
        
    }

    function ppa_penjabaranrek_form () {
        $form['formdata'] = array (
            '#type' => 'fieldset',
            '#title'=> 'Parameter Laporan',
            '#collapsible' => TRUE,
            '#collapsed' => FALSE,        
        );
         
        $kodeuk = arg(3);
        $tahun = arg(4);
        $limit = arg(5);

        if (isset($kodeuk)) {
            $form['formdata']['#collapsed'] = TRUE;
            //if (isUserKecamatan())
            //    if ($kodeuk != apbd_getuseruk())
            //        $form['formdata']['#collapsed'] = FALSE;
        }
        
       
        $pquery = "select kodeuk, kodedinas, namasingkat from {unitkerja} where aktif=1 order by kodedinas" ;
        $pres = db_query($pquery);
        $dinas = array();
        
        
        while ($data = db_fetch_object($pres)) {
            $dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
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
        //FILTER TAHUN-----
        //$tahun = variable_get('apbdtahun', 0);
		$tahunopt = array();
		$tahunopt['2015'] = '2015';
		$tahunopt['2016'] = '2016';
		$tahunopt['2017'] = '2017';
		$tahunopt['2018'] = '2018';
		$tahunopt['2019'] = '2019';
		$tahunopt['2020'] = '2020';	        
        $form['formdata']['tahun']= array(
            '#type'         => 'select', 
            '#title'        => 'Tahun',
            '#options'	=> $tahunopt,
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $tahun, 
        );
        $recordopt = array();
        $recordopt['0'] = 'Tampilkan semua';
        $recordopt['15'] = '15 Baris/Halaman';
        $recordopt['30'] = '30 Baris/Halaman';
        $form['formdata']['record']= array(
            '#type'         => 'select', 
            '#title'        => 'Baris/Halaman',
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
            '#value' => 'Tampilkan'
        );
        
        return $form;
    }
    function ppa_penjabaranrek_form_submit($form, &$form_state) {
        //$kodeuk = $form_state['values']['kodeuk'];
        $tahun = $form_state['values']['tahun'];
        $record = $form_state['values']['record'];
        $kodeuk = $form_state['values']['kodeuk'];
        $uri = 'apbd/laporanpenetapan/ppa_penjabaranrek/' .$kodeuk . '/' . $tahun . '/' . $record;
        drupal_goto($uri);
        
    }

?>
