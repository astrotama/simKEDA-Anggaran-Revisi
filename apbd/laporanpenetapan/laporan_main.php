<?php

function laporan_main($arg=NULL) {
	
	if ($arg)
		drupal_access_denied();

	/*
    $header = array (
       array('data' => 'Menu'),
       array('data' => 'Keterangan'),
    );
    $t = apbd_menu();
    while ($v=each($t)) {
        //echo $v[0] . " - " . substr($v[0],0,7) . "<br/>";
		if (($v[1]['type'] == MENU_NORMAL_ITEM) && ( substr($v[0],0,12) =='apbd/laporan'))
		{
			if (user_access($v[1]['access arguments'][0])) {
                $linkmenu = l($v[1]['title callback'](), $v['key']);
                $rows[] = array (
                   array('data' => $linkmenu),
                   array('data' => $v[1]['description']),
                );
            }
            //echo($v[0]);
            //echo "<br/>";
		}	
    }
	*/

    $header = array (
       array('data' => 'Laporan',  'colspan'=>'2'),
       array('data' => 'Keterangan'),
    );

	if (isSuperuser()) {
		$rows[] = array (array('data' => 'LAPORAN RKA-SKPD',  'colspan'=>'3', 'style' => 'font-weight:bold;'),
						array('data' => 'LAPORAN RKA-PPKD',  'colspan'=>'3', 'style' => 'font-weight:bold;'),
						array('data' => 'LAPORAN APBD',  'colspan'=>'3', 'style' => 'font-weight:bold;'),
						);
		//1
		$rows[] = array (array('data' => "<img src='/files/lap2.png'>", 'valign'=>'top'),
						array('data' => l('Ringkasan APBD', 'apbd/laporanpenetapan/rka/ringkasananggaran/00'), 'valign'=>'top'),
						array('data' => 'Menampilkan ringkasan APBD sampai dengan rekening jenis', 'valign'=>'top'),

						array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('Ringkasan RKA-PPKD', 'apbd/laporanpenetapan/rka/ringkasananggaranppkd'), 'valign'=>'top'),
						array('data' => 'Menampilkan ringkasan RKA-PPKD', 'valign'=>'top'),		
						
						array('data' => "<img src='/files/lap5.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran I - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran1'), 'valign'=>'top'),
						array('data' => 'Menampilkan lampiran I, ringkasan APBD', 'valign'=>'top'),

						);

		//2			
		$rows[] = array (array('data' => "<img src='/files/lap2.png'>", 'valign'=>'top'),
						array('data' => l('Rekapitulasi Anggaran Pendapatan', 
						'apbd/laporanpenetapan/rka/rekapaggpad/00'), 'valign'=>'top'),
						array('data' => 'Menampilkan rekapitulasi anggaran pendapatan', 'valign'=>'top'),
						
						array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('RKA-PPKD Pendapatan', 
						'apbd/pendapatanppkd/print'), 'valign'=>'top'),
						array('data' => 'Menampilkan rincian RKA-PPKD Pendapatan', 'valign'=>'top'),
						
						array('data' => "<img src='/files/lap5.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran II - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran2'), 'valign'=>'top'),
						array('data' => 'Menampilkan ringkasan apbd menurut urusan pemerintahan daerah dan oanisasi', 'valign'=>'top'),
						
						);
		//3
		$rows[] = array (array('data' => "<img src='/files/lap2.png'>", 'valign'=>'top'),
						array('data' => l('Rekapitulasi Anggaran Belanja', 
						'apbd/laporanpenetapan/rka/rekapaggbelanja/00'), 'valign'=>'top'),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja', 'valign'=>'top'),
						
						array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('RKA-PPKD Belanja', 
						'apbd/kegiatanppkd/print'), 'valign'=>'top'),
						array('data' => 'Menampilkan RKA-PPKD Belanja', 'valign'=>'top'),
						
						array('data' => "<img src='/files/lap5.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran III - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran3'), 'valign'=>'top'),
						array('data' => 'Menampilkan anggaran pendapatan dan belanja SKPD', 'valign'=>'top'),
						);
		
		//4
		$rows[] = array (array('data' => "<img src='/files/lap2.png'>", 'valign'=>'top'),
						array('data' => l('Rekapitulasi Anggaran Belanja Tidak Langsung', 
						'apbd/laporanpenetapan/rka/rekapaggbl/00/51'), 'valign'=>'top'),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja tidak langsung (Gaji)', 'valign'=>'top'),
						
						array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('RKA-PPKD Penerimaan Pembiayaan', 
						'apbd/pembiayaan/print/61'), 'valign'=>'top'),
						array('data' => 'Menampilkan RKA-PPKD Penerimaan Pembiayaan', 'valign'=>'top'),
						
						array('data' => "<img src='/files/lap5.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran IV - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran4'), 'valign'=>'top'),
						array('data' => 'Menampilkan rekapitulasi belanja menurut urusan pemerintahan daerah - organisasi - program & kegiatan', 'valign'=>'top'),
						);
		
		//5
		$rows[] = array (array('data' => "<img src='/files/lap2.png'>", 'valign'=>'top'),
						array('data' => l('Rekapitulasi Anggaran Belanja Langsung', 
						'apbd/laporanpenetapan/rka/rekapaggbl/00/52'), 'valign'=>'top'),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja langsung', 'valign'=>'top'),
						
						array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('RKA-PPKD Pengeluaran Pembiayaan', 
						'apbd/pembiayaan/print/62'), 'valign'=>'top'),
						array('data' => 'Menampilkan RKA-PPKD Pengeluaran Pembiayaan', 'valign'=>'top'),
						
						array('data' => "<img src='/files/lap5.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran V - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran5'), 'valign'=>'top'),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja daerah untuk keselarasan dan keterpaduan urusan pemerintahan daerah dan fungsi dalam kerangka pengelolaan keuangan negara', 'valign'=>'top'),
						);
		//6
		$rows[] = array (array('data' => "<img src='/files/lap2.png'>", 'valign'=>'top'),
						array('data' => l('Rekapitulasi Anggaran Belanja Langsung per Program/Kegiatan',		
						'apbd/laporanpenetapan/rka/rekapaggblprogram/00'), 'valign'=>'top'),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja langsung per program/kegiatan/jenis rekening', 'valign'=>'top'),
						
						array('data' => ''),
						array('data' => '', 'valign'=>'top'),
						array('data' => '', 'valign'=>'top'),
						
						array('data' => "<img src='/files/lap5.png'>", 'valign'=>'top'),
						array('data' => l('Perda/Perbup APBD', 
						'apbd/laporanpenetapan/apbd/perdabup'), 'valign'=>'top'),
						array('data' => 'Menampilkan lampiran Peraturan Daerah atau Pelaturan Bupati tentang APBD', 'valign'=>'top'),
						);
		//7
		$rows[] = array (array('data' => "<img src='/files/lap2.png'>", 'valign'=>'top'),
						array('data' => l('Rekapitulasi Anggaran Belanja Langsung per Program/Kegiatan per Triwulan', 
						'apbd/laporanpenetapan/rka/rekapaggblprogramtw/00'), 'valign'=>'top'),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja langsung per program/kegiatan per triwulan', 'valign'=>'top'),
						
						array('data' => ''),
						array('data' => '', 'valign'=>'top'),
						array('data' => '', 'valign'=>'top'),
						
						array('data' => ''),
						array('data' => '', 'valign'=>'top'),
						array('data' => '', 'valign'=>'top'),
						);						
		//8
		$rows[] = array (array('data' => "<img src='/files/lap2.png'>", 'valign'=>'top'),
						array('data' => l('Rekapitulasi Plafon dan Anggaran SKPD', 
						'apbd/laporanpenetapan/rka/rekapplafonagg'), 'valign'=>'top'),
						array('data' => 'Menampilkan rekapitulasi plafon dan anggaran SKPD', 'valign'=>'top'),
						
						array('data' => ''),
						array('data' => '', 'valign'=>'top'),
						array('data' => '', 'valign'=>'top'),
						
						array('data' => ''),
						array('data' => '', 'valign'=>'top'),
						array('data' => '', 'valign'=>'top'),
						);						

						
					
		$rows[] = array (array('data' => 'LAPORAN DPA-SKPD'),
						array('data' => ''),);
		$rows[] = array (array('data' => l('Sampul Halaman Depan', 'apbd/kegiatanskpd/print/' . $kodeuk . '/10/dpa/pdf/sampuld')),
						array('data' => 'Menampilkan sampul depan DPA-SKPD'),);
		$rows[] = array (array('data' => l('Sampul DPA-SKPD Pendapatan', 'apbd/kegiatanskpd/print/' . $kodeuk . '/10/dpa/pdf/sampulp')),
						array('data' => 'Menampilkan sampul DPA-SKPD Pendapatan'),);
		$rows[] = array (array('data' => l('Ringkasan APBD', 'apbd/laporanpenetapan/rka/ringkasananggaran/' . $kodeuk . '/3/10/dpa')),
						array('data' => 'Menampilkan ringkasan APBD sampai dengan rekening jenis'),);
		$rows[] = array (array('data' => l('DPA-SKPD 1 Rincian DPA-SKPD Pendapatan', 'apbd/pendapatan/print/' . $kodeuk . '/10/dpa')),
						array('data' => 'Menampilkan rincian DPA-SKPD Pendapatan'),);
		$rows[] = array (array('data' => l('DPA-SKPD 2.2 Rekap DPA-SKPD Belanja Langsung', 'apbd/laporanpenetapan/rka/rekapaggblprogramtw/' . $kodeuk . '/10/dpa')),
						array('data' => 'Menampilkan Rekap DPA-SKPD Belanja Langsung per tri wulan'),);
		
						
		/*				
		$rows[] = array (array('data' => 'LAPORAN APBD',  'colspan'=>'2', 'style' => 'font-weight:bold;'),
						array('data' => ''),);
						
		$rows[] = array (array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran I - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran1')),
						array('data' => 'Menampilkan Perda/Perbup APBD'),);						
		$rows[] = array (array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran II - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran2')),
						array('data' => 'Menampilkan ringkasan apbd menurut urusan pemerintahan daerah dan organisasi'),);						
		$rows[] = array (array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran III - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran3')),
						array('data' => 'Menampilkan anggaran pendapatan dan belanja SKPD'),);
		$rows[] = array (array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran IV - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran4')),
						array('data' => 'Menampilkan rekapitulasi belanja menurut urusan pemerintahan daerah - organisasi - program & kegiatan'),);
		$rows[] = array (array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('Lampiran V - APBD', 
						'apbd/laporanpenetapan/apbd/lampiran5')),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja daerah untuk keselarasan dan keterpaduan urusan pemerintahan daerah dan fungsi dalam kerangka pengelolaan keuangan negara'),);
		$rows[] = array (array('data' => "<img src='/files/lap3.png'>", 'valign'=>'top'),
						array('data' => l('Perda/Perbup APBD', 
						'apbd/laporanpenetapan/apbd/perdabup')),
						array('data' => 'Menampilkan lampiran Peraturan Daerah atau Pelaturan Bupati tentang APBD'),);
		*/
		
	} else {
		$kodeuk = apbd_getuseruk();
		
		//$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('html' =>TRUE));
		//$linkmenu = l('Ringkasan APBD', 'apbd/laporanpenetapan/rka/ringkasananggaran/' . $kodeuk); 
		$rows[] = array (array('data' => 'LAPORAN RKA-SKPD'),
						array('data' => ''),);

		$rows[] = array (array('data' => l('Ringkasan APBD', 'apbd/laporanpenetapan/rka/ringkasananggaran/' . $kodeuk)),
						array('data' => 'Menampilkan ringkasan APBD sampai dengan rekening jenis'),);
						
		$rows[] = array (array('data' => l('Rekapitulasi Anggaran Pendapatan', 
						'apbd/laporanpenetapan/rka/rekapaggpad/' . $kodeuk)),
						array('data' => 'Menampilkan rekapitulasi anggaran pendapatan'),);
		
		$rows[] = array (array('data' => l('Rekapitulasi Anggaran Belanja', 
						'apbd/laporanpenetapan/rka/rekapaggbelanja/' . $kodeuk)),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja'),);
		
		$rows[] = array (array('data' => l('Rekapitulasi Anggaran Belanja Tidak Langsung', 
						'apbd/laporanpenetapan/rka/rekapaggbl/' . $kodeuk  . '/51')),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja tidak langsung (Gaji)'),);
		
		$rows[] = array (array('data' => l('Rekapitulasi Anggaran Belanja Langsung', 
						'apbd/laporanpenetapan/rka/rekapaggbl/' . $kodeuk . '/52')),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja langsung'),);
		
		$rows[] = array (array('data' => l('Rekapitulasi Anggaran Belanja Langsung per Program/Kegiatan',		
						'apbd/laporanpenetapan/rka/rekapaggblprogram/' . $kodeuk)),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja langsung per program/kegiatan/jenis rekening'),);
		
		$rows[] = array (array('data' => l('Rekapitulasi Anggaran Belanja Langsung per Program/Kegiatan per Triwulan', 
						'apbd/laporanpenetapan/rka/rekapaggblprogramtw/' . $kodeuk)),
						array('data' => 'Menampilkan rekapitulasi anggaran belanja langsung per program/kegiatan per triwulan'),);
		
		/*	
		if ($kodeuk=='37') {
		$rows[] = array (array('data' => 'LAPORAN DPA-SKPD'),
						array('data' => ''),);
		$rows[] = array (array('data' => l('Sampul Halaman Depan', 'apbd/kegiatanskpd/print/' . $kodeuk . '/10/dpa/pdf/sampuld')),
						array('data' => 'Menampilkan sampul depan DPA-SKPD'),);
		$rows[] = array (array('data' => l('Sampul DPA-SKPD Pendapatan', 'apbd/kegiatanskpd/print/' . $kodeuk . '/10/dpa/pdf/sampulp')),
						array('data' => 'Menampilkan sampul DPA-SKPD Pendapatan'),);
		$rows[] = array (array('data' => l('Ringkasan APBD', 'apbd/laporanpenetapan/rka/ringkasananggaran/' . $kodeuk . '/3/10/dpa')),
						array('data' => 'Menampilkan ringkasan APBD sampai dengan rekening jenis'),);
		$rows[] = array (array('data' => l('DPA-SKPD 1 Rincian DPA-SKPD Pendapatan', 'apbd/pendapatan/print/' . $kodeuk . '/10/dpa')),
						array('data' => 'Menampilkan rincian DPA-SKPD Pendapatan'),);
		$rows[] = array (array('data' => l('DPA-SKPD 2.2 Rekap DPA-SKPD Belanja Langsung', 'apbd/laporanpenetapan/rka/rekapaggblprogramtw/' . $kodeuk . '/10/dpa')),
						array('data' => 'Menampilkan Rekap DPA-SKPD Belanja Langsung per tri wulan'),);
		
		}
		*/
	}
				   
    //$output .= theme_box('Penyusunan RKA-SKPD', theme_table($header, $rows));
	$output .= theme_box('Penyusunan APBD', theme_table('', $rows));
    return $output;
}
?>