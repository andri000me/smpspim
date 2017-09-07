<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sekolah
 *
 * @author rohmad
 */
class Jadwal_um extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_PUJ";
    var $tipe = 'UM';

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'jadwal_pu_model' => 'jadwal',
            'aturan_denah_model' => 'aturan_denah',
            'denah_model' => 'denah',
            'jenjang_sekolah_model' => 'jenjang_sekolah',
            'pegawai_model' => 'pegawai',
            'pengawas_pu_model' => 'pengawas',
            'mapel_pu_model' => 'mapel',
            'tingkat_model' => 'tingkat',
            'nilai_um_model' => 'nilai_um',
        ));
        $this->load->library('denah_handler');
        $this->auth->validation(6);
    }

    public function index() {
        $data['validasi_denah'] = $this->aturan_denah->is_um_validasi();
        
        $this->generate->backend_view('pu/jadwal_um/index', $data);
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->jadwal->get_datatables($this->tipe);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_PUJ;
            $row[] = $item->NAMA_TA;
            $row[] = $item->TANGGAL_PUJ;
            $row[] = $item->JAM_MULAI_PUJ;
            $row[] = $item->JAM_SELESAI_PUJ;

            $row[] = ($this->session->userdata('ID_PSB_ACTIVE') == $item->ID_TA) ? '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Cetak Sampul" onclick="cetak_sampul_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Sampul Sesi Ini</a></li>
                        <hr class="line-divider">
                        <li><a href="javascript:void()" title="Cetak Jadwal Tanggal Ini" onclick="cetak_jadwal_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Jadwal Tanggal Ini</a></li>
                        <li><a href="javascript:void()" title="Cetak Denah Tanggal Ini" onclick="cetak_denah_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Denah Tanggal Ini</a></li>
                        <li><a href="javascript:void()" title="Cetak Absen Pengawas" onclick="cetak_absen_pengawas_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Absen Pengawas</a></li>
                        <li><a href="javascript:void()" title="Cetak Absen Peserta" onclick="cetak_absen_peserta_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Absen Peserta</a></li>
                        <li><a href="javascript:void()" title="Cetak Blangko Nilai" onclick="cetak_blangko_nilai_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Blangko Nilai</a></li>
                        <li><a href="javascript:void()" title="Cetak Ruangan Siswa" onclick="cetak_siswa_ruang_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Ruangan Siswa</a></li>
                        <hr class="line-divider">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>' : '-';

            $data[] = $row;
        }

        /*

        <li><a href="javascript:void()" title="Cetak Kartu Siswa" onclick="cetak_kertu_siswa_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Kartu Siswa</a></li>
        <li><a href="javascript:void()" title="Cetak Kartu Meja" onclick="cetak_kertu_meja_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Kartu Meja</a></li>
        <hr class="line-divider">
         *          */
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jadwal->count_all($this->tipe),
            "recordsFiltered" => $this->jadwal->count_filtered($this->tipe),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function form($ID = NULL, $view = FALSE) {
        $data['validasi_denah'] = $this->aturan_denah->is_um_validasi();
        
        if ($ID !== NULL) {
            $data['jadwal'] = $this->jadwal->get_by_id($this->tipe, $ID);
            $data['mapel'] = $this->mapel->get_by_jadwal($ID);
            $data['pengawas_lk'] = $this->pengawas->get_by_jadwal_lk($ID);
            $data['pengawas_pr'] = $this->pengawas->get_by_jadwal_pr($ID);
        } else {
            $data['jadwal'] = NULL;
        }
        
        $data['denah'] = $this->aturan_denah->get_denah_psb();

        $data['tingkat_um'] = json_decode($this->pengaturan->getUjianPSB(), TRUE);
        $dept = $this->jenjang_sekolah->relasi_jenjang_departemen();

        foreach ($dept as $value) {
            $data['dept'][$value['JENJANG_MJD']] = $value['DEPT_MJD'];
        }
        
        if ($view)
            $data['mode_view'] = TRUE;

        $this->generate->backend_view('pu/jadwal_um/form', $data);
    }
    
    private function generate_denah_siswa($tanggal_ujian) {
        if(!$this->denah->is_denah_exist($tanggal_ujian)) {
            $data = array(
                'ATURAN_DENAH' => $this->aturan_denah->get_id_um(),
                'JADWAL_DENAH' => $tanggal_ujian,
                'SISWA_DENAH' => json_encode($this->denah_handler->generate_denah_siswa($this->tipe)),
            );
            $this->denah->save($data);
        }
    }
    
    private function cek_pengawas($data, $jk) {
        foreach ($data as $key => $value) {
            $temp_data = $data;
            unset($temp_data[$key]);
            if($value == '')
                    $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Guru tidak boleh kosong.'));
            if(in_array($value, $temp_data)) 
                    $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Guru atas nama '.$this->pegawai->get_name($value).' tidak boleh mengawas lebih dari satu ruang'));
        }
    }
    
    private function save_data($insert,$data) {
        $this->mapel->delete_by_jadwal($insert);
        $this->pengawas->delete_by_jadwal($insert);
        
        foreach ($data['DEPT_TINGK'] as $key => $value) {
            $data_save = array(
                'JADWAL_PUM' => $insert,
                'TINGKAT_PUM' => $this->tingkat->get_id($data['DEPT_TINGK'][$key], $data['NAMA_TINGK'][$key]),
                'MAPEL_PUM' => $data['MAPEL_PUM'][$key],
                'JENIS_PUM' => $data['JENIS_PUM'][$key],
            );
            
            $this->mapel->save($data_save);
        }
        
        foreach ($data['RUANGAN_PENG_LK'] as $key => $value) {
            $data_save = array(
                'JADWAL_PENG' => $insert,
                'RUANGAN_PENG' => $value,
                'JK_PENG' => 'L',
                'PEGAWAI_PENG' => $data['PEGAWAI_PENG_LK'][$key]
            );
            
            $this->pengawas->save($data_save);
        }
        
        foreach ($data['RUANGAN_PENG_PR'] as $key => $value) {
            $data_save = array(
                'JADWAL_PENG' => $insert,
                'RUANGAN_PENG' => $value,
                'JK_PENG' => 'P',
                'PEGAWAI_PENG' => $data['PEGAWAI_PENG_PR'][$key]
            );
            
            $this->pengawas->save($data_save);
        }
    }
    
    private function cek_jam_bentrok($data, $update = FALSE) {
        $jam_database = $this->jadwal->get_by_tanggal($this->tipe, $data['TANGGAL_PUJ']);
        
        foreach ($jam_database as $jam) {
            if($update && ($data['ID_PUJ'] == $jam['ID_PUJ']))
                continue;
            
            $input_mulai = new DateTime($data['JAM_MULAI_PUJ']);
            $input_selesai = new DateTime($data['JAM_SELESAI_PUJ']);
            $db_mulai = new DateTime($jam['JAM_MULAI_PUJ']);
            $db_selesai = new DateTime($jam['JAM_SELESAI_PUJ']);
            
            if(!(($input_mulai < $input_selesai) && ((($db_selesai <= $input_mulai) && ($db_selesai < $input_selesai)) || (($db_mulai >= $input_selesai) && ($db_selesai > $input_selesai))))) {
                $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Tanggal dan jam bentrok dengan yang ada didalam database.'));
            }
        }
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

        $data = $this->input->post();
        $this->cek_pengawas($data['PEGAWAI_PENG_LK'], 'lk');
        $this->cek_pengawas($data['PEGAWAI_PENG_PR'], 'pr');
        
        $this->cek_jam_bentrok($data);
        if($data['JAM_MULAI_PUJ'] == $data['JAM_SELESAI_PUJ']) $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Jam mulai dan jam selesai tidak boleh sama.'));

        $data_jadwal = array(
            'TA_PUJ' => $this->session->userdata('ID_PSB_ACTIVE'),
            'TIPE_PUJ' => $this->tipe,
            'TANGGAL_PUJ' => $this->date_format->to_store_db($data['TANGGAL_PUJ']),
            'JAM_MULAI_PUJ' => $data['JAM_MULAI_PUJ'],
            'JAM_SELESAI_PUJ' => $data['JAM_SELESAI_PUJ'],
        );
        $insert = $this->jadwal->save($data_jadwal);
        $this->generate_denah_siswa($this->date_format->to_store_db($data['TANGGAL_PUJ']));
        
        if($insert > 0) $this->save_data($insert, $data);
        else $this->generate->output_JSON(array("status" => FALSE, 'msg' => ''));
        
        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $data = $this->input->post();
        $this->cek_pengawas($data['PEGAWAI_PENG_LK'], 'lk');
        $this->cek_pengawas($data['PEGAWAI_PENG_PR'], 'pr');
        
        $this->cek_jam_bentrok($data, TRUE);
        if($data['JAM_MULAI_PUJ'] == $data['JAM_SELESAI_PUJ']) $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Jam mulai dan jam selesai tidak boleh sama.'));

        $data_jadwal = array(
            'TANGGAL_PUJ' => $this->date_format->to_store_db($data['TANGGAL_PUJ']),
            'JAM_MULAI_PUJ' => $data['JAM_MULAI_PUJ'],
            'JAM_SELESAI_PUJ' => $data['JAM_SELESAI_PUJ'],
        );
        $where_jadwal = array(
            'ID_PUJ' => $data['ID_PUJ']
        );
        $status = $this->jadwal->update($where_jadwal, $data_jadwal);
        $this->save_data($data['ID_PUJ'], $data);
        $this->generate_denah_siswa($this->date_format->to_store_db($data['TANGGAL_PUJ']));

        $this->generate->output_JSON(array("status" => 1));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->jadwal->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }
    
    public function cetak_jadwal($id) {
        $data['data'] = $this->mapel->get_all_by_jadwal();
        $data['ketua'] = $this->pengaturan->getDataKetuaPU();
        
        $this->load->view('backend/pu/jadwal_um/cetak_jadwal', $data);
    }
    
    public function cetak_denah($id) {
        $data_jadwal = $this->jadwal->get_all_group_tanggal($this->tipe);
        $data['ketua'] = $this->pengaturan->getDataKetuaPU();
        
        foreach ($data_jadwal as $index => $detail) {
            $data['data'][$index]['TANGGAL'] = $detail['TANGGAL_PUJ'];
            $data['data'][$index]['DENAH'] = $this->denah->get_denah_by_tanggal($detail['TANGGAL_PUJ']);
        }
        
        $this->load->view('backend/pu/jadwal_um/cetak_denah', $data);
    }
    
    public function cetak_absen_pengawas($id) {
        $data['jadwal'] = $this->jadwal->get_by_id($this->tipe, $id);
        $data['data']['L'] = $this->pengawas->get_by_jadwal_lk($id);
        $data['data']['P'] = $this->pengawas->get_by_jadwal_pr($id);
        $data['ketua'] = $this->pengaturan->getDataKetuaPU();
        
        $this->load->view('backend/pu/jadwal_um/cetak_absen_pengawas', $data);
    }
    
    public function cetak_absen_peserta($id) {
        $data_jadwal = $this->jadwal->get_by_id($this->tipe, $id);
        $data['ID'] = $id;
        $data['data'][0]['TANGGAL'] = $data_jadwal->TANGGAL_PUJ;
        $data['data'][0]['JAM_MULAI'] = $data_jadwal->JAM_MULAI_PUJ;
        $data['data'][0]['JAM_SELESAI'] = $data_jadwal->JAM_SELESAI_PUJ;
        $data['data'][0]['DENAH'] = $this->denah->get_denah_by_tanggal($data_jadwal->TANGGAL_PUJ);
        
        $this->load->view('backend/pu/jadwal_um/cetak_absen_peserta', $data);
    }
    
    public function cetak_siswa_ruang($id) {
        $data_jadwal = $this->jadwal->get_by_id($this->tipe, $id);
        $data['ID'] = $id;
        $data['data'][0]['TANGGAL'] = $data_jadwal->TANGGAL_PUJ;
        $data['data'][0]['JAM_MULAI'] = $data_jadwal->JAM_MULAI_PUJ;
        $data['data'][0]['JAM_SELESAI'] = $data_jadwal->JAM_SELESAI_PUJ;
        $data['data'][0]['DENAH'] = $this->denah->get_denah_by_tanggal($data_jadwal->TANGGAL_PUJ);
        
        $data['siswa'] = $this->nilai_um->get_data_all();
        
        $this->load->view('backend/pu/jadwal_um/cetak_siswa_ruang', $data);
    }
    
    public function cetak_kertu_meja($id) {
        $data_jadwal = $this->jadwal->get_all_group_tanggal($this->tipe);
        $data['ketua'] = $this->pengaturan->getDataKetuaPU();
        
        foreach ($data_jadwal as $index => $detail) {
            $data['data'][$index]['TANGGAL'] = $detail['TANGGAL_PUJ'];
            $data['data'][$index]['DENAH'] = $this->denah->get_denah_by_tanggal($detail['TANGGAL_PUJ']);
        }
        
        $this->load->view('backend/pu/jadwal_um/cetak_kertu_meja', $data);
    }
    
    public function cetak_kertu_siswa($id) {
        $data_jadwal = $this->jadwal->get_all_group_tanggal($this->tipe);
        $data['ketua'] = $this->pengaturan->getDataKetuaPU();
        
        foreach ($data_jadwal as $index => $detail) {
            $data['data'][$index]['TANGGAL'] = $detail['TANGGAL_PUJ'];
            $data['data'][$index]['DENAH'] = $this->denah->get_denah_by_tanggal($detail['TANGGAL_PUJ']);
        }
        
        $this->load->view('backend/pu/jadwal_um/cetak_kertu_siswa', $data);
    }
    
    public function cetak_sampul($id) {
        $data_jadwal = $this->jadwal->get_by_id($this->tipe, $id);
        $data['ID'] = $id;
        $data['data'][0]['TANGGAL'] = $data_jadwal->TANGGAL_PUJ;
        $data['data'][0]['JAM_MULAI'] = $data_jadwal->JAM_MULAI_PUJ;
        $data['data'][0]['JAM_SELESAI'] = $data_jadwal->JAM_SELESAI_PUJ;
        $data['data'][0]['DENAH'] = $this->denah->get_denah_by_tanggal($data_jadwal->TANGGAL_PUJ);

        $this->load->view('backend/pu/jadwal_um/cetak_sampul', $data);
    }

}
