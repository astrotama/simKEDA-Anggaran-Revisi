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

	drupal_set_title('Laporan');
    $header = array (
       array('data' => 'Laporan'),
       array('data' => 'Keterangan'),
    );

	if (isSuperuser()) {
		$rows[] = array (array('data' => ''),
						array('data' => 'Belum tersedia'),
						);
	} else {
		$kodeuk = apbd_getuseruk();
		$rows[] = array (array('data' => '/apbd/laporanpenetapan/rka/ringkasananggaran/' . $kodeuk),
						array('data' => 'Ringkasan APBD'),);
		$rows[] = array (array('data' => '/apbd/laporanpenetapan/rka/rekapaggpad/' . $kodeuk),
						array('data' => 'Rekapitulasi Anggaran Pendapatan'),);
		$rows[] = array (array('data' => '/apbd/laporanpenetapan/rka/rekapaggbelanja/' . $kodeuk),
						array('data' => 'Rekapitulasi Anggaran Belanja'),);
		$rows[] = array (array('data' => '/apbd/laporanpenetapan/rka/rekapaggbl/' . $kodeuk  . '/51'),
						array('data' => 'Rekapitulasi Anggaran Belanja Tidak Langsung'),);
		$rows[] = array (array('data' => '/apbd/laporanpenetapan/rka/rekapaggbl/' . $kodeuk . '/52'),
						array('data' => 'Rekapitulasi Anggaran Belanja Langsung'),);
		$rows[] = array (array('data' => '/apbd/laporanpenetapan/rka/rekapaggblprogram/' . $kodeuk),
						array('data' => 'Rekapitulasi Anggaran Belanja Langsung per Program/Kegiatan'),);
		$rows[] = array (array('data' => '/apbd/laporanpenetapan/rka/rekapaggblprogramtw/' . $kodeuk),
						array('data' => 'Rekapitulasi Anggaran Belanja Langsung per Program/Kegiatan per Triwulan'),);
	}
				   
    $output .= theme_box('SIM Perencanaan Keuangan Daerah', theme_table($header, $rows));
    return $output;
}
?>