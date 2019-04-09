<?php
    function renjaskpdmusrenbangda_main() {
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
            return drupal_get_form('renjaskpd_form');

        //if (isUserKecamatan()) {
        //    if ($kodeuk != apbd_getuseruk())
        //        return drupal_get_form('musrenbangcam_form');
        //}	

        if (isset($exportpdf) && ($exportpdf=='pdf'))  {
            //require_once('test.php');
            //myt();
            $htmlContent = GenReportForm(1);
            $pdfFile = 'renjaskpd.pdf';
            apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
            
        } else {
            $url = 'apbd/laporan/renjaskpdmusrenbangda/'. $kodeuk .'/'. $status . '/'. $tahun . "/0/pdf";
            $output .= drupal_get_form('renjaskpd_form');
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

        
        $tablesort=' order by d.kodedinas, k.kodepro, p.np, k.nomorkeg';
        $customwhere = ' and k.tahun=\'%s\' ';
        if ($kodeuk!='00') {
            $customwhere .= ' and k.kodeuk=\'%s\' ';
        }
        switch($status) {
            case 0: //status = keseluruhan                
                break;
            case 1: //status = lolos
                $customwhere .= ' and k.lolos=1';
                break;
            case 2: //status = tidak lolos
                $customwhere .= ' and k.lolos=0';
                break;
        }
        $where = ' where true' . $customwhere . $qlike ;
    
        $sql = 'select d.namasingkat, d.kodedinas, p.kodeu, u.urusansingkat, p.program, p.np, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, k.total, k.totalsebelum, k.totalsebelum2, k.totalpenetapan, k.apbdkab, k.pnpm, d.namasingkat from {kegiatanskpd} k left join unitkerja d on(k.kodeuk=d.kodeuk) left join program p on (k.kodepro = p.kodepro) left join urusan u on (d.kodeu = u.kodeu)' . $where;
        $fsql = sprintf($sql, db_escape_string($tahun), db_escape_string($kodeuk));
        //$limit = 13;
        
        //drupal_set_message( $fsql);
        $countsql = "select count(*) as cnt from {kegiatanskpd} k" . $where;
        $fcountsql = sprintf($countsql, db_escape_string($tahun), db_escape_string($kodeuk));
        if ($limit>0) {
            $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
        } else {
            $fsql .= ' ORDER BY d.kodedinas, k.kodepro, p.np, k.nomorkeg';
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
        $headers1[] = array (array ('data'=>'MUSRENBANGDA', 'width'=>'900px', 'colspan'=>'8', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=> $namauk . "&nbsp;" . $kabupaten , 'width'=>'900px', 'colspan'=>'8', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=> 'TAHUN ' . $tahun, 'width'=>'900px', 'colspan'=>'8', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
		
        $headers[] = array (array ('data'=>'&nbsp;', 'colspan'=>'8', 'width'=>'900px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers[] = array (
                             array('data' => 'KODE',  'rowspan'=>'2', 'width'=> '99px', 'style' => 'border: 1px solid black; text-align:center;'),
                             array('data' => 'URUSAN PEMERINTAHAN, PROGRAM & KEGIATAN',  'rowspan'=>'2', 'width' => '170px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'SASARAN',  'rowspan'=>'2', 'width' => '95px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'LOKASI' ,  'rowspan'=>'2', 'width' => '95px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'TARGET',  'rowspan'=>'2', 'width' => '95px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'PAGU INDIKATIF (Rp)', 'colspan'=>'4',  'width' => '340px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             );
        $headers[] = array (
                             array('data' => 'APBD KAB',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'APBD PROV' ,  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'APBN',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'JUMLAH',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             );
        $headers[] = array (
                             array('data' => '1',  'width'=> '99px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '2', 'width' => '170px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '3', 'width' => '95px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '4' , 'width' => '95px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '5', 'width' => '95px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '6',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '7 ',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '8',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => '9',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             );
        if ($result) {
            
            
            
            $sifat_urusan_array = array('URUSAN PADA SEMUA SKPD','URUSAN WAJIB','URUSAN PILIHAN');
            
            $sifat ='';
            $sifat_deskripsi='';
            $japbdkab_sifat=(double)0;
            $japbdp_sifat=(double)0;
            $japbn_sifat=(double)0;
            
            
            $urusan='';
            $urusan_deskripsi='';
            $japbdkab_urusan=(double)0;
            $japbn_urusan=(double)0;
            $japbp_urusan=(double)0;
            
            
            $dinas_deskripsi="";
            $japbdkab_dinas=(double)0;
            $japbn_dinas=(double)0;
            $japbp_dinas=(double)0;
            
            
            
            $program='';
            $program_deskripsi='';
            $japbdkab_program=(double)0;
            $japbp_program=(double)0;
            $japbn_program=(double)0;
            
            $total_apbdkab = (double) 0;
            $total_apbn = (double) 0;
            $total_apbp = (double) 0;
            
            $first=true;
            
            $sifat_data = array();
            $urusan_data = array();
            $program_data = array();
            $dinas_data = array();
            $temp_data = array();
            
            while ($data = db_fetch_object($result)) {                
                $no++;
                $r_sifat = substr($data->kodedinas,0,1);
                $r_urusan= $r_sifat . "." . substr($data->kodedinas, 1,2);
                $r_dinas = $r_urusan . "." . substr($data->kodedinas, 3);
                $r_program= $r_dinas . "." . $data->np;                
                //drupal_set_message($data->kegiatan);
                
                $apbdkab =  (double) $data->apbdkab + (double) $data->apbdprov + (double) $data->apbdnas;
                $total_apbdkab += (double) $apbdkab;
                $total_apbn += (double) $data->apbn;
                $total_apbp += (double) $data->apbp;

                if ($first) {
                    $sifat = $r_sifat;
                    $urusan = $r_urusan;
                    $dinas = $r_dinas;
                    $program = $r_program;
                    $sifat_deskripsi = $sifat_urusan_array[$sifat];                    
                    $urusan_deskripsi = $data->urusansingkat;
                    if ($urusan=='0.00')
                        $urusan_deskripsi = 'URUSAN PADA SEMUA SKPD';
                    $program_deskripsi = $data->program;
                    $dinas_deskripsi = $data->namasingkat;
                    
                    $japbdkab_sifat = (double)$apbdkab;
                    $japbn_sifat = (double)$data->apbn;
                    $japbdp_sifat = (double)$data->apbp;

                    $japbdkab_urusan = (double)$apbdkab;
                    $japbn_urusan = (double)$data->apbn;
                    $japbp_urusan = (double)$data->apbp;
                    

                    $japbdkab_program = $apbdkab;
                    $japbn_program = $data->apbn;
                    $japbp_program = $data->apbp;
                    $first=false;
                } else {
                    if ($r_program != $program) {
                        $temp = array (
                            array('data' => $program, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                            array('data' => $program_deskripsi, 'colspan' => '4', 'width' => '455px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '' , 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbdkab_program), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbp_program), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbn_program), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbdkab_program+$japbp_program+$japbn_program), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                        );
                        array_unshift($temp_data, $temp);
                        $program_data= array_merge($program_data, $temp_data);
                        //
                        //array_unshift($temp_data, $temp);
                        //$program_data[] = $temp_data;
                        $temp_data=array();
                        
                        $program = $r_program;
                        $program_deskripsi = $data->program;
                        $japbdkab_program = (double)$apbdkab;
                        $japbp_program = (double) $data->apbp;
                        $japbn_program = (double) $data->apbn;
                    } else {
                        $japbdkab_program += (double) $apbdkab;
                        $japbp_program += (double) $data->apbp;
                        $japbn_program += (double) $data->apbn;
                    }
                
                    if ($r_dinas != $dinas) {
                        $temp = array (
                            array('data' => $dinas, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                            array('data' => $dinas_deskripsi, 'colspan' => '4', 'width' => '455px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '' , 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbdkab_dinas), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbp_dinas), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbn_dinas), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbdkab_dinas+$japbp_dinas+$japbn_dinas), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                        );
                        array_unshift($program_data, $temp);
                        $dinas_data=array_merge($dinas_data, $program_data); 
                        $program_data=array();                        
                        
                        $dinas = $r_dinas;
                        $dinas_deskripsi = $data->namasingkat;
                        $japbdkab_dinas = (double)$apbdkab;
                        $japbp_dinas = (double) $data->apbp;
                        $japbn_dinas = (double) $data->apbn;
                    } else {
                        $japbdkab_dinas += (double) $apbdkab;
                        $japbp_dinas += (double) $data->apbp;
                        $japbn_dinas += (double) $data->apbn;
                    }
                    
                    if ($urusan != $r_urusan) {
                        $tkode = $urusan . '-' . $urusan_deskripsi;
                        $temp = array (
                            array('data' => $urusan, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => $urusan_deskripsi, 'colspan' => '4', 'width' => '455px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '' , 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbdkab_urusan), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbp_urusan), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbn_urusan), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbdkab_urusan+$japbp_urusan+$japbn_urusan), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                        );
                        array_unshift($dinas_data, $temp);
                        $urusan_data=array_merge($urusan_data, $dinas_data); 
                        $dinas_data=array();
                        
                        $urusan = $r_urusan;
                        $urusan_deskripsi = $data->urusansingkat;
                        if ($urusan=='0.00')
                            $urusan_deskripsi = 'URUSAN PADA SEMUA SKPD';
                        
                        $japbdkab_urusan = (double)$apbdkab;
                        $japbn_urusan = (double) $data->totalsebelum;
                        $japbp_urusan = (double) $data->apbp;
                    } else {
                        $japbdkab_urusan += (double) $apbdkab;
                        $japbn_urusan += (double) $data->apbn;
                        $japbp_urusan += (double) $data->apbp;
                    }

                    if ($sifat != $r_sifat) {
                        $tnama = $sifat_urusan_array[$sifat];
                        $temp = array (
                            array('data' => $sifat, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => $tnama, 'colspan' => '4', 'width' => '455px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => apbd_fn($japbdkab_sifat), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => apbd_fn($japbdp_sifat), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => apbd_fn($japbn_sifat), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => apbd_fn($japbdkab_sifat+$japbdp_sifat+$japbn_sifat), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                        );
                        array_unshift($urusan_data, $temp);
                        $sifat_data= array_merge($sifat_data, $urusan_data);
                        $urusan_data=array();
                        
                        $sifat = $r_sifat;
                        $sifat_deskripsi = $sifat_urusan_array[$sifat];
                        $japbdkab_sifat = (double) $apbdkab;
                        $japbn_sifat = (double) $data->apbn;
                        $japbdp_sifat = (double) $data->apbp;
                    } else {
                        $japbdkab_sifat += (double) $apbdkab;
                        $japbn_sifat += (double) $data->apbn;
                        $japbdp_sifat += (double) $data->apbp;
                    }

                }
                
                $tkode = $data->nomorkeg;
                
                $indikator = $data->sasaran . "/" . $data->target;
                $nuk = '';
                if ($kodeuk=='00')
                    $nuk = ' (' . $data->namasingkat . ")";
                
                $jumlah = (double) $apbdkab + (double) $data->apbp + (double) $data->apbn;
                $temp_data[] = array (
                    array('data' => $tkode, 'width' => '99px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),                    
                    array('data' => $data->kegiatan . $nuk, 'width' => '170px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => $data->sasaran, 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => str_replace("||", ", ", $data->lokasi), 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => $data->target, 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($apbdkab), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->apbp), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->apbn), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($jumlah), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                );
                
            }
            
            if (count($temp_data)>0) {
                $tkode = $program . "-" . $program_deskripsi;
                $temp = array (
                    array('data' => $program, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    array('data' => $program_deskripsi, 'colspan' => '4', 'width' => '455px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '' , 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'id' => 'aa', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab_program), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbp_program), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbn_program), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab_program+$japbp_program+$japbn_program), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                );
                array_unshift($temp_data, $temp);
                $program_data= array_merge($program_data, $temp_data);
                //$program_data[]= $temp_data;
                $tkode = $urusan . '-' . $urusan_deskripsi;

                $temp = array (
                    array('data' => $dinas, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    array('data' => $dinas_deskripsi, 'colspan' => '4', 'width' => '455px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '' , 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'id' => 'aa', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab_dinas), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbp_dinas), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbn_dinas), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab_dinas+$japbp_dinas+$japbndinas), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                );
                array_unshift($program_data, $temp);
                $dinas_data= array_merge($dinas_data, $program_data);
                //$program_data[]= $temp_data;
                $tkode = $urusan . '-' . $urusan_deskripsi;

                $temp = array (
                    array('data' => $urusan, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    array('data' => $urusan_deskripsi, 'colspan' => '4', 'width' => '455px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '' , 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab_urusan), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbp_urusan), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbn_urusan), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab_urusan+$japbp_urusan+$japbn_urusan), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                );
                array_unshift($dinas_data, $temp);
                $urusan_data=array_merge($urusan_data, $dinas_data);
                
                $tnama = $sifat_urusan_array[$sifat];
                $temp = array (
                    array('data' => $sifat, 'width' => '99px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
                    array('data' => $tnama , 'colspan' => '4', 'width' => '455px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    //array('data' => '', 'width' => '95px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab_sifat), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($japbdp_sifat), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($japbn_sifat), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab_sifat+$japbdp_sifat+$japbn_sifat), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                );
                array_unshift($urusan_data, $temp);
                $sifat_data= array_merge($sifat_data, $urusan_data);
            }
            $rows = array_merge($rows, $sifat_data);
            
            if (count($rows) > 0) {
                //total
                $rows[] = array (
                    array('data' => 'Total', 'colspan'=>'5', 'width' => '554px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
                    array('data' => apbd_fn($total_apbdkab), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($total_apbp), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($total_apbn), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($total_apbdkab+$total_apbp+$total_apbn), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
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
        //                array('data' => '', 'width' => '95px'),                    
        //                array('data' => '', 'width' => '144px'),
        //                array('data' => '', 'width' => '122px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => $pimpinanjabatan , 'width' => '182px', 'colspan'=>'2', 'height'=>'50px', 'style'=>'text-align:center'),
        //            );
        //            $rows[] = array (
        //                array('data' => '', 'width' => '95px'),                    
        //                array('data' => '', 'width' => '144px'),
        //                array('data' => '', 'width' => '122px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '80px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => '', 'width' => '95px'),
        //                array('data' => $pimpinannama , 'width' => '182px', 'colspan'=>'2', 'style'=>'text-decoration: underline;text-align:center'),
        //            );
        //            $rows[] = array (
        //                array('data' => '', 'width' => '95px'),                    
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
    
    function renjaskpd_form () {
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
    function renjaskpd_form_submit($form, &$form_state) {
        //$kodeuk = $form_state['values']['kodeuk'];
        $tahun = $form_state['values']['tahun'];
        $record = $form_state['values']['record'];
        $kodeuk = $form_state['values']['kodeuk'];
        $status = $form_state['values']['status'];
        $uri = 'apbd/laporan/renjaskpdmusrenbangda/' .$kodeuk .'/'. $status . '/' . $tahun . '/' . $record;
        drupal_goto($uri);
        
    }
?>