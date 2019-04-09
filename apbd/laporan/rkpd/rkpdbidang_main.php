<?php
    function rkpdbidang_main() {
    $h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
    $h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
    drupal_add_css('files/css/kegiatancam.css');
        
        $kodebid = arg(3);
        $kodeuk = arg(4);
        $tahun = arg(5);
        $limit = arg(6);
        $exportpdf = arg(7);
        if (!isset($tahun)) 
            return drupal_get_form('rkpdbidang_form');

        if (isUserKecamatan()) {
            if ($kodeuk != apbd_getuseruk())
                return drupal_get_form('rkpdbidang_form');
        }	

        if (isset($exportpdf) && ($exportpdf=='pdf'))  {
            //require_once('test.php');
            //myt();
            $htmlContent = GenReportForm(1);
            $pdfFile = 'rkpdbidang2.pdf';
            apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
            
        } else {
            $url = 'apbd/laporan/rkpdbidang/' . $kodebid . '/' . $kodeuk . '/'. $tahun . "/0/pdf";
            $output .= drupal_get_form('rkpdbidang_form');
            $output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
            $output .= GenReportForm();
            return $output;
        }

    }
    function GenReportForm($print=0) {
        $kodebid = arg(3);
        $kodeuk = arg(4);
        $tahun = arg(5);
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

        
        $tablesort=' order by k.kodebid, k.kodeuk, p.kodeu, p.np';
        $customwhere = ' and k.tahun=\'%s\' ';
        if ($kodeuk!='00') {
            $customwhere .= sprintf(' and k.kodeuk=\'%s\' ', db_escape_string($kodeuk));
        }
        if ($kodebid !='99') {
            $customwhere .= sprintf(' and k.kodebid=\'%s\' ', db_escape_string($kodebid));
        }
        $where = ' where true' . $customwhere . $qlike ;
    
        $sql = 'select b.bidang, d.kodedinas, p.kodeu, u.urusansingkat, p.program, p.np, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodebid, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, k.total, k.totalsebelum, k.totalsebelum2, k.totalpenetapan, k.apbdkab, k.pnpm, d.namasingkat from {kegiatanrkpd} k left join {unitkerja} d on(k.kodeuk=d.kodeuk) left join {program} p on (k.kodepro = p.kodepro) left join {urusan} u on (p.kodeu = u.kodeu) left join {bidang} b on (k.kodebid=b.kodebid)' . $where;
        $fsql = sprintf($sql, db_escape_string($tahun));
        //$limit = 13;
        
        //drupal_set_message( $fsql);
        $countsql = "select count(*) as cnt from {kegiatanrkpd} k" . $where;
        $fcountsql = sprintf($countsql, db_escape_string($tahun), db_escape_string($kodeuk));
        if ($limit>0) {
            $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
        } else {
            $fsql .= ' ORDER BY k.kodebid, k.kodeuk, p.kodeu, p.np, k.nomorkeg';
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
        $kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
        
        $headers[] = array (array ('data'=>'REKAPITULASI RENCANA KERJA PEMERINTAH DAERAH TAHUN ANGGARAN ' . $tahun, 'width'=>'898px', 'colspan'=>'10', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers[] = array (array ('data'=>$namauk . "&nbsp;" .$kabupaten, 'colspan'=>'10', 'width'=>'898px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers[] = array (array ('data'=>'&nbsp;', 'colspan'=>'10', 'width'=>'898px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers[] = array (
                             array('data' => 'NO', 'rowspan'=> '2', 'width'=> '30px', 'style' => 'border: 1px solid black; text-align:center;'),
                             array('data' => 'USULAN KEGIATAN', 'rowspan'=> '2', 'width' => '144px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'SASARAN/TARGET', 'rowspan'=> '2', 'width' => '122px', 'rowspan' => '2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'LOKASI', 'rowspan'=> '2', 'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'PERKIRAAN BIAYA', 'colspan' => '5', 'width' => '425px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             array('data' => 'DINAS TERKAIT', 'rowspan'=> '2', 'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                             );
        $headers[] = array (
                             array('data' => 'ADD/ADK', 'width'=> '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'PNPM', 'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'APBD KAB', 'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'APBD PROV', 'width'=> '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                             array('data' => 'APBN', 'width'=> '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
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
            $bidanguk='';
            $uknama = '';
            $bidang_nama='';
            $jaddadk = (double)0;
            $jpnpm=(double)0;
            $japbdkab=(double)0;
            $japbp=(double)0;
            $japbn=(double)0;
            $numerasibidang=1;
            
            
            $first=true;
            
            $u_data = array();
            $u2_data = array();
            $u3_data = array();
            $temp_data = array();
            
            while ($data = db_fetch_object($result)) {                
                $no++;
                $r_u = substr($data->kodeu,0,1);
                $r_u2= $r_u . "." . substr($data->kodeu, 1,2);
                $r_bidanguk= $data->kodebid . $data->kodeuk;
                $r_uknama = $data->namasingkat;
                //drupal_set_message($data->kegiatan);

                if ($first) {
                    $u = $r_u;
                    $u2 = $r_u2;
                    $bidanguk = $r_bidanguk;
                    $uknama = $r_uknama;
                    $u_nama = $u_array[$u];                    
                    $u2_nama = $data->urusansingkat;
                    $bidang_nama = $data->bidang;
                    
                    $ju = $data->apbdkab;
                    $ju_sebelum = $data->pnpm;
                    $ju_sebelum2 = $data->totalpenetapan;

                    $ju2 = $data->apbdkab;
                    $ju2_sebelum = $data->pnpm;
                    $ju2_sebelum2 = $data->totalpenetapan;
                    
                    $jaddadk = $data->addadk;
                    $jpnpm = $data->pnpm;
                    $japbdkab = $data->apbdkab;
                    $japbp = $data->apbp;
                    $japbn = $data->apbn;
                    $first=false;
                } else {
                    if ($r_bidanguk != $bidanguk) {
                        //$tkode = $upro . "-" . $bidang_nama;
                        $temp = array (
                            array('data' => $numerasibidang, 'width' => '30px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                            array('data' => $bidang_nama, 'colspan'=>'3', 'width' => '346px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '' , 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            //array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($jaddadk), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($jpnpm), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbdkab), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbp), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($japbn), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => $uknama, 'align' => 'left', 'width' => '90px', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                        );
                        $temp_data[count($temp_data)-1][9]['style'] = 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;';
                        array_unshift($temp_data, $temp);
                        $u3_data= array_merge($u3_data, $temp_data);
                        //
                        
                        $temp_data=array();
                        
                        $bidanguk = $r_bidanguk;
                        $bidang_nama = $data->bidang;
                        $uknama = $r_uknama;
                        
                        $jaddadk = (double) $data->addadk;
                        $jpnpm = (double) $data->pnpm;
                        $japbdkab = (double) $data->apbdkab;
                        $apbp = (double) $data->apbp;
                        $apbn = (double) $data->apbn;
                        $numerasibidang++;
                    } else {
                        $jaddadk += (double) $data->addadk;
                        $jpnpm += (double) $data->pnpm;
                        $japbdkab += (double) $data->apbdkab;
                        $japbp += (double) $data->apbp;
                        $japbn += (double) $data->apbn;
                    }
                
                    
                    //if ($u2 != $r_u2) {
                    //    $tkode = $u2 . '-' . $u2_nama;
                    //    $temp = array (
                    //        array('data' => $u2, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),
                    //        array('data' => $u2_nama, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //        array('data' => '' , 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //        array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //        array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //        array('data' => apbd_fn($ju2), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //        array('data' => apbd_fn($ju2_sebelum), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //        array('data' => apbd_fn($ju2_sebelum2), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //        array('data' => '', 'align' => 'left', 'width' => '79px', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    //    );
                    //    array_unshift($u3_data, $temp);
                    //    $u2_data=array_merge($u2_data, $u3_data); 
                    //    $u3_data=array();
                    //    
                    //    $u2 = $r_u2;
                    //    $u2_nama = $data->urusansingkat;
                    //    $ju2 = $data->apbdkab;
                    //    $ju2_sebelum = (double) $data->pnpm;
                    //    $ju2_sebelum2 = (double) $data->totalpenetapan;
                    //} else {
                    //    $ju2 += (double) $data->apbdkab;
                    //    $ju2_sebelum += (double) $data->pnpm;
                    //    $ju2_sebelum2 += (double) $data->totalpenetapan;
                    //}

                    //if ($u != $r_u) {
                    //    $tnama = $u_array[$u];
                    //    $temp = array (
                    //        array('data' => $u, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),
                    //        array('data' => $tnama, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                    //        array('data' => '', 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                    //        array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                    //        array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                    //        array('data' => apbd_fn($ju), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                    //        array('data' => apbd_fn($ju_sebelum), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                    //        array('data' => apbd_fn($ju_sebelum2), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                    //        array('data' => '', 'width' => '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                    //    );
                    //    array_unshift($u2_data, $temp);
                    //    $u_data= array_merge($u_data, $u2_data);
                    //    $u2_data=array();
                    //    
                    //    $u = $r_u;
                    //    $u_nama = $u_array[$u];
                    //    $ju = $data->apbdkab;
                    //    $ju_sebelum = (double) $data->pnpm;
                    //    $ju_sebelum2 = (double) $data->totalpenetapan;
                    //} else {
                    //    $ju += (double) $data->apbdkab;
                    //    $ju_sebelum += (double) $data->pnpm;
                    //    $ju_sebelum2 += (double) $data->totalpenetapan;
                    //}

                }
                
                $tkode = $r_bidanguk . "." .$data->kodedinas .'.' . $data->nomorkeg;
                
                $indikator = $data->sasaran . "/" . $data->target;
                $temp_data[] = array (
                    array('data' => '', 'width' => '30px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),                    
                    array('data' => $data->kegiatan, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => $indikator, 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => $data->lokasi, 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn ($data->addadk), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->pnpm), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->apbdkab), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->apbp), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->apbn), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => '', 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-right: 1px solid black; font-size: 1em;'),
                );
                
            }
            
            if (count($temp_data)>0) {
                $tkode = $bidanguk . "-" . $bidang_nama;
                $temp = array (
                    array('data' => $numerasibidang, 'width' => '30px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    array('data' => $bidang_nama, 'colspan'=>'3', 'width' => '346px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '' , 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    //array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($jaddadk), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($jpnpm), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbdkab), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbp), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($japbn), 'width' => '85px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => $uknama, 'width' => '90px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                );
                $temp_data[count($temp_data)-1][9]['style'] = 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;';
                array_unshift($temp_data, $temp);
                $u3_data= array_merge($u3_data, $temp_data);
                //$u3_data[]= $temp_data;
                //$tkode = $u2 . '-' . $u2_nama;
                //
                //$temp = array (
                //    array('data' => $u2, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                //    array('data' => $u2_nama, 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                //    array('data' => '' , 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                //    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                //    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                //    array('data' => apbd_fn($ju2), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                //    array('data' => apbd_fn($ju2_sebelum), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                //    array('data' => apbd_fn($ju2_sebelum2), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                //    array('data' => '', 'align' => 'left', 'width' => '79px', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                //);
                //array_unshift($u3_data, $temp);
                //$u2_data=array_merge($u2_data, $u3_data);
                //
                //$tnama = $u_array[$u];
                //$temp = array (
                //    array('data' => $u, 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
                //    array('data' => $tnama , 'width' => '144px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                //    array('data' => '', 'width' => '122px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                //    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                //    array('data' => '', 'width' => '80px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                //    array('data' => apbd_fn($ju), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                //    array('data' => apbd_fn($ju_sebelum), 'width' => '95px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                //    array('data' => apbd_fn($ju_sebelum2), 'width' => '103px', 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                //    array('data' => '', 'width' => '79px', 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                //);
                //array_unshift($u2_data, $temp);
                //$u_data= array_merge($u_data, $u2_data);
            }
            $rows = array_merge($rows, $u3_data);
            
            
            
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
        
        $output .= theme_box('', apbd_theme_table($headers, $rows, $opttbl));
        $output .= $toutput;
        if ($limit >0)
            $output .= theme ('pager', NULL, $limit, 0);
        
        return $output;
        
    }
    
    function rkpdbidang_form () {
        $form['formdata'] = array (
            '#type' => 'fieldset',
            '#title'=> 'Parameter Laporan',
            '#collapsible' => TRUE,
            '#collapsed' => FALSE,        
        );
        $kodebid = arg(3);
        $kodeuk = arg(4);
        $tahun = arg(5);
        $limit = arg(6);

        if (isset($kodeuk)) {
            $form['formdata']['#collapsed'] = TRUE;
            //if (isUserKecamatan())
            //    if ($kodeuk != apbd_getuseruk())
            //        $form['formdata']['#collapsed'] = FALSE;
        }
        
      
        //KELOMPOK BIDANG
        $pquery = "select kodebid, bidang from {bidang} order by bidang" ;
        $pres = db_query($pquery);
        $bidang = array();
        
        
        $bidang['99'] ='SEMUA BIDANG';
        while ($data = db_fetch_object($pres)) {
            $bidang[$data->kodebid] = $data->bidang;
        }
        
        $form['formdata']['kodebid']= array(
            '#type'         => 'select', 
            '#title'        => 'Kelompok Bidang',
            '#options'	=> $bidang,
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $kodebid, 
        );

        $pquery = "select kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 and iskecamatan=0 order by namauk" ;
        $pres = db_query($pquery);
        $dinas = array();
        
        
        $dinas['00'] ='SEMUA DINAS';
        while ($data = db_fetch_object($pres)) {
            $dinas[$data->kodeuk] = $data->namasingkat;
        }
        $type='select';
        if (isUserKecamatan()) {
            $type = 'hidden';
            $kodeuk = apbd_getuseruk();
            //drupal_set_message('user kec');
        }
        
        $form['formdata']['kodeuk']= array(
            '#type'         => $type, 
            '#title'        => 'Dinas',
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
        $recordopt = array();
        $recordopt['0'] = 'Tampilkan semua';
        $recordopt['13'] = '13 Record/Halaman';
        $recordopt['26'] = '26 Record/Halaman';
        $limit = 0;
        $form['formdata']['record']= array(
            '#type'         => 'hidden', 
            '#title'        => 'Record/Halaman',
            //'#options'	=> $recordopt,
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
    function rkpdbidang_form_submit($form, &$form_state) {
        //$kodeuk = $form_state['values']['kodeuk'];
        $tahun = $form_state['values']['tahun'];
        $record = $form_state['values']['record'];
        $kodebid= $form_state['values']['kodebid'];
        $kodeuk= $form_state['values']['kodeuk'];
        $uri = 'apbd/laporan/rkpdbidang/' . $kodebid . '/' . $kodeuk . '/' . $tahun . '/' . $record;
        //drupal_set_message($uri);
        drupal_goto($uri);
        
    }



?>