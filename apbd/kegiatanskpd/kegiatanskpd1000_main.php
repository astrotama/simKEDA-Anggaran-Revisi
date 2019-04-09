<?php
function kegiatanskpd1000_main($arg=NULL, $nama=NULL) {

	drupal_add_css('files/css/kegiatancam.css');
	$limit = 15;
	
	$tahun = variable_get('apbdtahun', 0);

	$customwhere = sprintf(' and k.tahun=%s ', $tahun);
	if (isSuperuser()) {
		$adminok = true;
							
	} else {
		$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
		$adminok = false;
	}

	$customwhere .= sprintf(' and k.inaktif=0 ');

			
	
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Target', 'field'=> 'target', 'valign'=>'top'),
			array('data' => 'Lokasi',  'valign'=>'top'),
			array('data' => 'Sumberdana', 'field'=> 'sumberdana', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'plafon','width' => '90px', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Target', 'field'=> 'target', 'valign'=>'top'),
			array('data' => 'Lokasi',  'valign'=>'top'),
			array('data' => 'Sumberdana', 'field'=> 'sumberdana', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'plafon','width' => '90px', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total', 'width' => '90px','valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by koderesmi';
    }
	
    $where = ' where true' . $customwhere . $qlike ;

	$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.lokasi,k.programtarget,k.total,
			k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanskpd} k inner join {unitkerja} u on ( k.kodeuk=u.kodeuk) inner join {program} p on (k.kodepro = p.kodepro) inner join {kegiatan1000} ks on k.kodekeg=ks.kodekeg " . $where;
	//$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanskpd} k inner join {kegiatan1000} ks on k.kodekeg=ks.kodekeg " . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	$fcountsql = $countsql;
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);

	$allowedit = true;		//(($selisih>0) || (isSuperuser()));
	
	//CEK TAHUN
	$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';

			
			if (user_access('kegiatanskpd edit')) {
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('html' =>TRUE));
			} else {
				$kegname = $data->kegiatan ;
			}

			//$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/program/edit/' . $data->kodepro, array('html'=>TRUE));
			//$progname = l($data->program, 'apbd/program/edit/' . $data->kodepro , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
			
			if ($allowedit) 
				//if (user_access('kegiatanskpd penghapusan'))			"&nbsp;" .
				//$editlink =l('Rekening', 'apbd/kegiatanskpd/subkegiatan/' . $data->kodekeg, array('html'=>TRUE));
				
				//Baru boleh mengisi rekening ketika adminok
				if ($adminok or $data->adminok) {
					if ($data->total==0) {
						$editlink =l('Rekening', 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg, array('html'=>TRUE));
					} else {
						$editlink =l('Rekening', 'apbd/kegiatanskpd/rekening/' . $data->kodekeg, array('html'=>TRUE));
					}
					$editlink .= "&nbsp;" .  l('Triwulan', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg, array('html'=>TRUE));
				
				} else {
					$editlink = 'Rekening';
					$editlink .= "&nbsp;" . 'Triwulan';
				}
				
			if (isSuperuser()) {
				$editlink .= "&nbsp;" . l('Edit', 'apbd/kegiatanskpd/editadmin/' . $data->kodekeg, array('html'=>TRUE));
				$editlink .= "&nbsp;" . l('Hapus', 'apbd/kegiatanskpd/delete/' . $data->kodekeg, array('html'=>TRUE));
				
			}
			
            $no++;
			
			
			if (isSuperuser()) { 
				if ($data->inaktif) 
					//$inaktif = 'x';
					$inaktif = "<img src='/files/inaktif.png'>";
				
				else
					$inaktif ='';
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $inaktif, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			}
		}
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	if ($allowedit)
		//
		if (isSuperuser()) {
			$btn .= l('Baru', 'apbd/kegiatanskpd/editadmin/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
		}

	$status = 0;
	$record = 0;

    $btn = "&nbsp;" . l("Daftar Kegiatan", 'apbd/kegiatanskpd' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	
	
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanskpd/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);

    return $output;
}

?>