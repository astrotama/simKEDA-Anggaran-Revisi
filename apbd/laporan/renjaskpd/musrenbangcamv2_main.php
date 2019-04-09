<?php
    function laprenjaskpd_main() {
    $h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
    $h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
    drupal_add_css('files/css/kegiatancam.css');
        
        $kodeuk = arg(3);
        $tahun = arg(4);
        $limit = arg(5);
        $exportpdf = arg(6);
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
            $url = 'apbd/laporan/renjaskpd/'. $kodeuk . '/'. $tahun . "/0/pdf";
            $output .= drupal_get_form('renjaskpd_form');
            $output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
            $output .= GenReportForm();
            return $output;
        }

    }
    function GenReportForm($print=0) {
        
        $kodeuk = arg(3);
        $tahun = arg(4);
        $limit = arg(5);
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
        $where = ' where true' . $customwhere . $qlike ;
    
        $sql = 'select d.kodedinas, p.kodeu, u.urusansingkat, p.program, p.np, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, k.total, k.totalsebelum, k.totalsebelum2, k.totalpenetapan, k.apbdkab, k.pnpm, d.namasingkat from {kegiatankec} k left join unitkerja d on(k.kodeuk=d.kodeuk) left join program p on (k.kodepro = p.kodepro) left join urusan u on (p.kodeu = u.kodeu)' . $where;
        $fsql = sprintf($sql, db_escape_string($tahun), db_escape_string($kodeuk));
        //$limit = 13;
        
        //drupal_set_message( $fsql);
        $countsql = "select count(*) as cnt from {kegiatankec} k" . $where;
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
        $rows= array();
        $headers1[] = array (array ('data'=>'HASIL PEMBAHASAN MUSRENBANG KECAMATAN TAHUN ' . $tahun, 'width'=>'898px', 'colspan'=>'9', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=>'&nbsp;', 'colspan'=>'9', 'width'=>'898px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
		
        $headers[] = array (
                             array('data' => 'KODE', 'rowspan'=> '2', 'width'=> '100px', 'style' => 'border: 1px solid black; text-align:center;'),
                             array('data' => 'PROGRAM', 'width' => '144px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'SASARAN YANG AKAN DICAPAI', 'width' => '122px', 'rowspan' => '2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'USULAN', 'colspan' => '2', 'width' => '160px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'SUMBER DANA', 'colspan' => '2', 'width' => '190px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'HASIL PEMBAHASAN', 'rowspan'=> '2', 'width' => '103px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'DESA PENGUSUL', 'rowspan' => '2', 'width' => '79px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             );
        $headers[] = array (
                             array('data' => 'KEGIATAN', 'width'=> '144px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'DESA', 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'SKPD', 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'APBD', 'width'=> '95px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'PNPM', 'width'=> '95px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
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
            $japbdkab=(double)0;
            $jpnpm=(double)0;
            $jpenetapan=(double)0;
            
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

                if ($first) {
                    $u = $r_u;
                    $u2 = $r_u2;
                    $upro = $r_upro;
                    $u_nama = $u_array[$u];                    
                    $u2_nama = $data->urusansingkat;
                    $upro_nama = $data->program;
                    
                    $ju = $data->apbdkab;
                    $ju_sebelum = $data->pnpm;
                    $ju_sebelum2 = $data->totalpenetapan;

                    $ju2 = $data->apbdkab;
                    $ju2_sebelum = $data->pnpm;
                    $ju2_sebelum2 = $data->totalpenetapan;

                    $japbdkab = $data->apbdkab;
                    $jpnpm = $data->pnpm;
                    $jpenetapan = $data->totalpenetapan;
                    $first=false;
                } else {
                    if ($r_upro != $upro) {
                        //$tkode = $upro . "-" . $upro_nama;
                        $temp = array (
                            array('data' => $upro, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                            array('data' => $upro_nama, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '' , 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbdkab), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($jpnpm), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($jpenetapan), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'align' => 'left', 'width' => '79px', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                        );
                        array_unshift($temp_data, $temp);
                        $u3_data= array_merge($u3_data, $temp_data);
                        //
                        //array_unshift($temp_data, $temp);
                        //$u3_data[] = $temp_data;
                        $temp_data=array();
                        
                        $upro = $r_upro;
                        $upro_nama = $data->program;
                        $japbdkab = $data->apbdkab;
                        $jpnpm = (double) $data->pnpm;
                        $jpenetapan = (double) $data->totalpenetapan;
                    } else {
                        $japbdkab += (double) $data->apbdkab;
                        $jpnpm += (double) $data->pnpm;
                        $jpenetapan += (double) $data->totalpenetapan;
                    }
                
                    
                    if ($u2 != $r_u2) {
                        $tkode = $u2 . '-' . $u2_nama;
                        $temp = array (
                            array('data' => $u2, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => $u2_nama, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '' , 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($ju2), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($ju2_sebelum), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($ju2_sebelum2), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'align' => 'left', 'width' => '79px', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),                            
                        );
                        array_unshift($u3_data, $temp);
                        $u2_data=array_merge($u2_data, $u3_data); 
                        $u3_data=array();
                        
                        $u2 = $r_u2;
                        $u2_nama = $data->urusansingkat;
                        $ju2 = $data->apbdkab;
                        $ju2_sebelum = (double) $data->pnpm;
                        $ju2_sebelum2 = (double) $data->totalpenetapan;
                    } else {
                        $ju2 += (double) $data->apbdkab;
                        $ju2_sebelum += (double) $data->pnpm;
                        $ju2_sebelum2 += (double) $data->totalpenetapan;
                    }

                    if ($u != $r_u) {
                        $tnama = $u_array[$u];
                        $temp = array (
                            array('data' => $u, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => $tnama, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => '', 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => apbd_fn($ju), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => apbd_fn($ju_sebelum), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => apbd_fn($ju_sebelum2), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => '', 'width' => '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                        );
                        array_unshift($u2_data, $temp);
                        $u_data= array_merge($u_data, $u2_data);
                        $u2_data=array();
                        
                        $u = $r_u;
                        $u_nama = $u_array[$u];
                        $ju = $data->apbdkab;
                        $ju_sebelum = (double) $data->pnpm;
                        $ju_sebelum2 = (double) $data->totalpenetapan;
                    } else {
                        $ju += (double) $data->apbdkab;
                        $ju_sebelum += (double) $data->pnpm;
                        $ju_sebelum2 += (double) $data->totalpenetapan;
                    }

                }
                
                $tkode = $r_upro . "." .$data->kodedinas .'.' . $data->nomorkeg;
                
                $indikator = $data->sasaran . "/" . $data->target;
                $temp_data[] = array (
                    array('data' => $tkode, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),                    
                    array('data' => $data->kegiatan, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => $indikator, 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => privGetLokasi($data->lokasi), 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => $data->namasingkat, 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->apbdkab), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->pnpm), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->totalpenetapan), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => privGetLokasi($data->lokasi), 'width' => '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                );
                
            }
            
            if (count($temp_data)>0) {
                $tkode = $upro . "-" . $upro_nama;
                $temp = array (
                    array('data' => $upro, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    array('data' => $upro_nama, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '' , 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'id' => 'aa', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($jpnpm), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($jpenetapan), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width' => '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                );
                array_unshift($temp_data, $temp);
                $u3_data= array_merge($u3_data, $temp_data);
                //$u3_data[]= $temp_data;
                $tkode = $u2 . '-' . $u2_nama;

                $temp = array (
                    array('data' => $u2, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    array('data' => $u2_nama, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '' , 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($ju2), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($ju2_sebelum), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($ju2_sebelum2), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '', 'align' => 'left', 'width' => '79px', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                );
                array_unshift($u3_data, $temp);
                $u2_data=array_merge($u2_data, $u3_data);
                
                $tnama = $u_array[$u];
                $temp = array (
                    array('data' => $u, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
                    array('data' => $tnama , 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => '', 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($ju), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($ju_sebelum), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($ju_sebelum2), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => '', 'width' => '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                );
                array_unshift($u2_data, $temp);
                $u_data= array_merge($u_data, $u2_data);
            }
            $rows = array_merge($rows, $u_data);
            
            
            
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
    
    function renjaskpd_form () {
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
        
      
        $pquery = "select kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 and iskecamatan=1 order by namauk" ;
        $pres = db_query($pquery);
        $dinas = array();
        
        
        $dinas['00'] ='SEMUA KECAMATAN';
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
        $tahun = '2012';
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
        $kodeuk= $form_state['values']['kodeuk'];
        $uri = 'apbd/laporan/musrenbangcamv2/' .$kodeuk . '/' . $tahun . '/' . $record;
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
    
    function PrintDesa () {
        
        $query = sprintf("select kodedesa, namadesa from {desa} order by kodedesa");
        $header[] = array (
            array ('data' => 'Kode', 'colspan'=>'2', 'width' => '352px', 'style' => 'text-align: center;'),
        );
        $header[] = array (
            array ('data' => 'Kode', 'width' => '72px', 'style' => ''),
            array ('data' => 'Nama', 'width' => '280px'),                   
        );
        $res = db_query($query);
        $rows = array();
        while ($data = db_fetch_object($res)) {
            $rows[] = array (
                array('data' => $data->kodedesa, 'width' => '72px', 'style'=> 'background-color: #ffffff;'),
                array('data' => $data->namadesa, 'width' => '280px', 'style'=> 'background-color: #ffffff;'),
            );
        }
        $output .= theme_box('', apbd_theme_table($header, $rows, array('style' => 'background-color: #000000', 'cellspacing'=>'1', 'cellpadding'=>'1')));
        return $output;        
    }

?>