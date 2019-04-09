<?php
    function ppa_plafonkab_main() {
        $h .= '<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>';
        drupal_set_html_head($h);
        drupal_add_css('files/css/kegiatancam.css');
            
        $kodeu = arg(3);
        $tahun = arg(4);
        $limit = arg(5);
        $exportpdf = arg(6);
        if (!isset($tahun)) 
            return drupal_get_form('ppa_plafonkab_form');

        //if (isUserKecamatan()) {
        //    if ($kodeuk != apbd_getuseruk())
        //        return drupal_get_form('musrenbangcam_form');
        //}	

        if (isset($exportpdf) && ($exportpdf=='pdf'))  {
            //require_once('test.php');
            //myt();
            $htmlContent = GenReportForm(1);
            $pdfFile = "ppa_plafonkab_form.pdf";
            apbd_ExportPDF("P", "F4", $htmlContent, $pdfFile);
            
        } else {
            $url = 'apbd/laporan/ppaplafonkab/'. $kodeu .'/'. $tahun . "/0/pdf";
            $output .= drupal_get_form('ppa_plafonkab_form');
            $output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
            $output .= GenReportForm();
            return $output;
        }        
    }

    function GenReportForm($print=0) {
        
        $kodeu = arg(3);
        $tahun = intval(arg(4));
        $limit = arg(5);
        //---
        $col1 = '65px';
        $col2 = '240px';
        $colplafon = '120px';
        
        $coltotal = '305px';
        
        
        $tablesort=' order by p.kodeu, k.kodeuk';
        $customwhere = ' and k.tahun=\'%s\' ';
        switch ($kodeu) {
            case '00':
                break;
            case 'aaa':
                $customwhere .= ' and p.kodeu=\'000\' ';
                break;
            default:
                $customwhere .= ' and p.kodeu=\'%s\' ';
                break;
        }
        $customwhere .= ' and k.jenis=\'2\' ';
        $where = ' where true' . $customwhere . $qlike ;
    
        $sql = 'select d.namasingkat, d.kodedinas, p.kodeu, u.urusansingkat, p.program, p.sasaran as pro_sasaran, p.target as pro_target, p.np, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, k.total, k.totalsebelum, k.totalsebelum2, k.totalpenetapan, k.apbdkab, k.pnpm, d.namasingkat from {kegiatanppa} k left join unitkerja d on(k.kodeuk=d.kodeuk) left join program p on (k.kodepro = p.kodepro) left join urusan u on (p.kodeu = u.kodeu)' . $where;
        $fsql = sprintf($sql, db_escape_string($tahun), db_escape_string($kodeu));
        //echo $fsql;

        $countsql = "select count(*) as cnt from {kegiatanppa} k" . $where;
        $fcountsql = sprintf($countsql, db_escape_string($tahun), db_escape_string($kodeu));
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
        //$headers[] = array (
        //    array('data' => 'URUSAN', 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border: 0px solid white; font-weight: bold; font-size: 110%;'),                    
        //    array('data' => ': ' . $kodeu . " - " . $namau, 'colspan' => '4', 'width' => '600px', 'align' => 'left', 'valign'=>'top', 'style' => 'border: 0px solid white; font-weight: bold; font-size: 110%;'),
        //);
        //
        //$headers[] = array (
        //    array('data' => 'SKPD', 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border: 0px solid white; font-weight: bold; font-size: 110%;'),                    
        //    array('data' => ': ' . $kodedinas . " - " . $namadinas, 'colspan' => '4', 'width' => '600px', 'align' => 'left', 'valign'=>'top', 'style' => 'border: 0px solid white; font-weight: bold; font-size: 110%;'),
        //);

        //$output .= apbd_theme_table(array(), $rheader);
        
        $rows= array();
        $headers[] = array (array('data'=>'Plafon Anggaran Sementara Berdasarkan Urusan Pemerintahan', 'colspan'=>'4', 'style'=>'border: 0px solid white; text-align:center;font-weight: bold; font-size: 100%'));
        $headers[] = array (
                    array('data' => 'NO',  'width'=> $col1, 'style' => 'border: 1px solid black; text-align:center;font-weight: bold; font-size: 100%'),
                    array('data' => 'URUSAN/SKPD', 'width' => $col2, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                    array('data' => 'PLAFON ANGGARAN SEMENTARA (Rp)',  'width' => $colplafon, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                    array('data' => 'KET',  'width' => $colplafon, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;font-weight:bold;font-size: 100%;'),
                );

        if ($result) {
            $jprogram = (double)0;
            $jprogram_up = (double)0;
            $total = (double) 0;
            
            $first=true;
            
            $u_data = array();
            $u_data_2 = array();
            $temp_data = array();
            
            while ($data = db_fetch_object($result)) {                
                $no++;
                $namaurarray = array('URUSAN PILIHAN', 'URUSAN WAJIB');
                $r_kodeu = $data->kodeu;
                $r_ur = substr($r_kodeu,0,1);
                $r_nama_ur = $namaurarray[$r_ur];
                $r_kodeu = substr($r_kodeu,0,1) . "." . substr($r_kodeu,1);
                $r_namau = $data->urusansingkat;
                if ($r_kodeu=='0.00')
                    $r_namau = 'URUSAN PADA SEMUA SKPD';
               
                $total += (double) $data->total;

                if ($first) {
                    $kodeu = $r_kodeu;
                    $namau = $r_namau;
                    $ur = $r_ur;
                    $nama_ur = $r_nama_ur;
                    
                    $jprogram = (double) $data->total;
                    $jprogram_up = (double) $data->total;
                    $first=false;
                } else {
                    if ($r_kodeu != $kodeu) {
                        //echo 't1';
                        $temp = array (
                            array('data' => $kodeu, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:bold;font-size: 100%;'),                            
                            array('data' => $namau, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                            array('data' => apbd_fn($jprogram), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                            array('data' => '', 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                        );
                        
                        array_unshift($temp_data, $temp);
                        $u_data= array_merge($u_data, $temp_data);

                        $temp_data=array();
                        $kodeu = $r_kodeu;
                        $namau = $r_namau;
                        $jprogram = (double) $data->total;
                    } else {
                        $jprogram += (double) $data->total;
                    }
                    
                    if ($r_ur != $ur) {
                        //echo 't1';
                        $temp = array (
                            array('data' => $ur, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:bold;font-size: 120%;'),                            
                            array('data' => $nama_ur, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 120%;'),
                            array('data' => apbd_fn($jprogram_up), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 120%;'),
                            array('data' => '', 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 120%;'),
                        );
                        
                        array_unshift($u_data, $temp);
                        $u_data_2= array_merge($u_data_2, $u_data);

                        $u_data=array();
                        
                        $ur = $r_ur;
                        $nama_ur = $r_nama_ur;
                        $jprogram_up = (double) $data->total;
                    } else {
                        $jprogram_up += (double) $data->total;
                    }
                }
                
                $tkode = $kodeu. ".." . substr($data->kodedinas,0,3) . "." . substr($data->kodedinas,3);

                $temp_data[] = array (
                    array('data' => $tkode, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 100%;'),                    
                    array('data' => $data->namasingkat, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 100%;'),
                    array('data' => apbd_fn($data->total), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 100%;'),
                    array('data' => '', 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 100%;'),
                );                
            }
            
            if (count($temp_data)>0) {                
                $temp = array (
                    array('data' => $kodeu, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:bold;font-size: 100%;'),                            
                    array('data' => $namau, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                    array('data' => apbd_fn($jprogram), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                    array('data' => '', 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                );


                array_unshift($temp_data, $temp);
                $u_data = array_merge($u_data, $temp_data);
                
                $temp = array (
                    array('data' => $ur, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:bold;font-size: 120%;'),                            
                    array('data' => $nama_ur, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 120%;'),
                    array('data' => apbd_fn($jprogram_up), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 120%;'),
                    array('data' => '', 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 120%;'),
                );
                
                array_unshift($u_data, $temp);
                $u_data_2= array_merge($u_data_2, $u_data);                
            }
            $rows = array_merge($rows, $u_data_2);
            
            if (count($rows) > 0) {
                //total
                $rows[] = array (
                    array('data' => 'TOTAL', 'colspan'=>'2', 'width' => $coltotal, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 100%;font-weight:bold;'),                            
                    array('data' => apbd_fn($total), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                    array('data' => '', 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:bold;font-size: 100%;'),
                );
            }
            
            
            
        } 
        
        $opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');


        $output .= theme_box('', apbd_theme_table($headers, $rows, $opttbl));
        $output .= $toutput;
        if ($limit >0)
            $output .= theme ('pager', NULL, $limit, 0);
        
        return $output;
        
    }

    function ppa_plafonkab_form () {
        $form['formdata'] = array (
            '#type' => 'fieldset',
            '#title'=> 'Parameter Laporan',
            '#collapsible' => TRUE,
            '#collapsed' => FALSE,        
        );
        
        $kodeu = arg(3);
        $tahun = arg(4);
        $limit = arg(5);

        if (isset($kodeuk)) {
            $form['formdata']['#collapsed'] = TRUE;
            //if (isUserKecamatan())
            //    if ($kodeuk != apbd_getuseruk())
            //        $form['formdata']['#collapsed'] = FALSE;
        }
        
      
        $pquery = "select kodeu, urusansingkat, urusan from {urusan} order by urusansingkat" ;
        $pres = db_query($pquery);
        $dinas = array();
        
        $dinas['00'] = 'SEMUA URUSAN';
        $dinas['aaa'] = 'SEMUA URUSAN PADA SKPD';
        while ($data = db_fetch_object($pres)) {
            $dinas[$data->kodeu] = $data->urusansingkat;
        }
        $type='select';
        
        $form['formdata']['kodeu']= array(
            '#type'         => $type, 
            '#title'        => 'URUSAN PEMERINTAHAN',
            '#options'	=> $dinas,
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $kodeu, 
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
    function ppa_plafonkab_form_submit($form, &$form_state) {
        //$kodeuk = $form_state['values']['kodeuk'];
        $tahun = $form_state['values']['tahun'];
        $record = $form_state['values']['record'];
        $kodeu = $form_state['values']['kodeu'];
        $uri = 'apbd/laporan/ppaplafonkab/' .$kodeu . '/' . $tahun . '/' . $record;
        drupal_goto($uri);
        
    }

?>