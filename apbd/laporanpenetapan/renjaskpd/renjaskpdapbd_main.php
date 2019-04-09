<?php
    function renjaskpdapbd_main() {
    $h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
    $h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
    drupal_add_css('files/css/kegiatancam.css');
        
        $kodeuk = arg(3);
        $status = arg(4);
        $tahun = arg(5);
        $limit = arg(6);
        $exportpdf = arg(7);
        if (!isset($tahun)) 
            return drupal_get_form('renjaskpdapbd_form');

        //if (isUserKecamatan()) {
        //    if ($kodeuk != apbd_getuseruk())
        //        return drupal_get_form('musrenbangcam_form');
        //}	

        if (isset($exportpdf) && ($exportpdf=='pdf'))  {
            //require_once('test.php');
            //myt();
            $htmlContent = GenReportForm(1);
            $pdfFile = 'renjaskpdapbd.pdf';
            apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
            
        } else {
            $url = 'apbd/laporanpenetapan/renjaskpdapbd/'. $kodeuk .'/'. $status . '/'. $tahun . "/0/pdf";
            $output .= drupal_get_form('renjaskpdapbd_form');
            $output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
            $output .= GenReportForm();
            return $output;
        }

    }
    function GenReportForm($print=0) {
        
        $kodeuk = arg(3);
        $status = arg(4);
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
        switch($status) {
            case 0: //status = keseluruhan           
				$sqltotal = 'k.total';	
                break;
            case 1: //status = lolos
				$sqltotal = 'k.totalpenetapan as total';
                $customwhere .= ' and k.lolos=1';
                break;
            case 2: //status = tidak lolos
				$sqltotal = 'k.total';
                $customwhere .= ' and k.lolos=0';
                break;
        }
        $customwhere .= ' and k.dekon=0 ';
        $where = ' where true' . $customwhere . $qlike ;
    
        $sql = 'select d.namasingkat, d.kodedinas, p.kodeu, u.urusansingkat, p.program, p.np, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, ' . $sqltotal . ', k.totalsebelum, k.totalsebelum2, k.totalpenetapan, k.apbdkab, k.apbdprov, k.apbdnas, k.pnpm, d.namasingkat from {kegiatanskpd} k left join unitkerja d on(k.kodeuk=d.kodeuk) left join program p on (k.kodepro = p.kodepro) left join urusan u on (p.kodeu = u.kodeu)' . $where;
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
        $kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
        $rows= array();
        $headers1[] = array (array ('data'=>'RENJA SKPD - USULAN KEGIATAN YANG DIDANAI DARI APBD', 'width'=>'900px', 'colspan'=>'10', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=> $namauk . "&nbsp;" . $kabupaten , 'width'=>'900px', 'colspan'=>'10', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=> 'TAHUN ' . $tahun, 'width'=>'900px', 'colspan'=>'10', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=>'&nbsp;', 'colspan'=>'10', 'width'=>'900px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        
		$headers[] = array (
                             array('data' => 'NO', 'rowspan'=>'2', 'width'=> '25px', 'style' => 'border: 1px solid black; text-align:center;'),
                             array('data' => 'KEGIATAN', 'rowspan'=>'2', 'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'ANGGARAN TAHUN ' . $tahun . ' (Rp)',  'rowspan'=>'2', 'width' => '82px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'ANGGARAN SEBELUMNYA (Rp)',  'colspan'=> '3', 'width' => '246px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'SUMBER DANA (Rp)', 'colspan'=>'3', 'width' => '246px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'LOKASI' , 'rowspan'=>'2', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             );
        $headers[] = array (
                             array('data' => 'TAHUN ' . ($tahun -1) . " (Rp)",  'width' => '82px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'TAHUN ' . ($tahun -2) . " (Rp)",  'width' => '82px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'TAHUN ' . ($tahun -3) . " (Rp)",  'width' => '82px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),

                             array('data' => 'APBD',  'width' => '82px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'BANPROV',  'width' => '82px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'DAK',  'width' => '82px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),

                             );
        $headers[] = array (
            array('data' => '1', 'width' => '25px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),                    
            array('data' => '2', 'width' => '200px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
            array('data' => '3', 'width' => '82px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
            array('data' => '4', 'width' => '82px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
            array('data' => '5', 'width' => '82px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
            array('data' => '6', 'width' => '82px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
            array('data' => '7', 'width' => '82px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
            array('data' => '8', 'width' => '82px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
            array('data' => '9', 'width' => '82px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
            array('data' => '10', 'width' => '100px', 'valign'=>'top', 'style' => 'text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
        );

        $total = (double) 0;
        $totalsebelum = (double) 0;
        $apbdkab = (double) 0;
        $apbdprov = (double) 0;
        $apbdnas = (double) 0;
        if ($result) {
            
            while($data=db_fetch_object($result)) {
                $no++;
                $rows[] = array (
                    array('data' => $no, 'width' => '25px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),                    
                    array('data' => $data->kegiatan . $uk, 'width' => '200px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->total), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->totalsebelum), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn(0), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn(0), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->apbdkab), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->apbdprov), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->apbdnas), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => str_replace("||", ", ", $data->lokasi), 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                );

                $total += (double) $data->total;
                $totalsebelum += (double) $data->totalsebelum;
                $apbdkab += (double) $data->apbdkab;
                $apbdprov += (double) $data->apabdprov;
                $apbdnas += (double) $data->apbdnas;
            }            
        }
                        
        if (count($rows) > 0) {
            //total
            $rows[] = array (
                array('data' => 'Total', 'colspan'=>'2', 'width' => '225px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
                array('data' => apbd_fn($total), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                array('data' => apbd_fn($totalsebelum), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                array('data' => apbd_fn(0), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                array('data' => apbd_fn(0), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                array('data' => apbd_fn($apbdkab), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                array('data' => apbd_fn($apbdprov), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                array('data' => apbd_fn($apbdnas), 'width' => '82px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                array('data' => '', 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
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
        //if ($kodeuk!='00') {
        //        if ($print==0) {
        //            $toutput = "        
        //            <div style='clear:both'></div>
        //            <div style='float:right; width:200px;border: 1px solid #eee'>
        //                <div style='text-align:center;margin-bottom: 75px;'>" . $pimpinanjabatan . "</div>
        //                <div style='text-align:center;text-decoration: underline;'>". $pimpinannama."</div>
        //                <div style='text-align:center;'>NIP. ".$pimpinannip."</div>                        
        //            </div>
        //            <div style='clear:both'></div>
        //            ";
        //        } else {
        //            $rows[] = array (
        //                array('data' => '', 'width' => '100px'),                    
        //                array('data' => '', 'width' => '144px'),
        //                array('data' => '', 'width' => '122px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => $pimpinanjabatan , 'width' => '182px', 'colspan'=>'2', 'height'=>'50px', 'style'=>'text-align:center'),
        //            );
        //            $rows[] = array (
        //                array('data' => '', 'width' => '100px'),                    
        //                array('data' => '', 'width' => '144px'),
        //                array('data' => '', 'width' => '122px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => $pimpinannama , 'width' => '182px', 'colspan'=>'2', 'style'=>'text-decoration: underline;text-align:center'),
        //            );
        //            $rows[] = array (
        //                array('data' => '', 'width' => '100px'),                    
        //                array('data' => '', 'width' => '144px'),
        //                array('data' => '', 'width' => '122px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => 'NIP.' . $pimpinannip , 'width' => '182px', 'colspan'=>'2', 'style'=>'text-align:center'),
        //            );
        //          
        //        }
        //}
        //
		$rows1[] = array (array('data' => '', 'colspan'=>'2'));
		$output .= theme_box('', apbd_theme_table($headers1, $rows1, $opttbl));
		
        $output .= theme_box('', apbd_theme_table($headers, $rows, $opttbl));
        $output .= $toutput;
        if ($limit >0)
            $output .= theme ('pager', NULL, $limit, 0);
        
        return $output;
        
    }
    
    function renjaskpdapbd_form () {
        $form['formdata'] = array (
            '#type' => 'fieldset',
            '#title'=> 'Parameter Laporan',
            '#collapsible' => TRUE,
            '#collapsed' => FALSE,        
        );
        
        $kodeuk = arg(3);
        $status=arg(4);
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
    function renjaskpdapbd_form_submit($form, &$form_state) {
        //$kodeuk = $form_state['values']['kodeuk'];
        $tahun = $form_state['values']['tahun'];
        $record = $form_state['values']['record'];
        $kodeuk = $form_state['values']['kodeuk'];
        $status = $form_state['values']['status'];
        $uri = 'apbd/laporanpenetapan/renjaskpdapbd/' .$kodeuk .'/'. $status . '/' . $tahun . '/' . $record;
        drupal_goto($uri);
        
    }

?>