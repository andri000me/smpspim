<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi SIMAPES
 * PIM KAJEN
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */

class Surat_pemanggilan extends CI_Controller {

    var $table = "(SELECT *, SUM(POIN_KSH) AS JUMLAH_POIN FROM `komdis_siswa_header` WHERE `TA_KSH` = '3' GROUP BY SISWA_KSH, TA_KSH) AS data_ksh";
    var $joins = array(
        array('md_siswa', 'SISWA_KSH=ID_SISWA'),
        array('akad_siswa', 'TA_KSH=TA_AS AND SISWA_AS=SISWA_KSH'),
        array('akad_kelas', 'KELAS_AS=ID_KELAS'),
        array('md_pegawai', 'WALI_KELAS=ID_PEG'),
        array('(SELECT bk_pemanggilan.* FROM 
bk_pemanggilan
INNER JOIN
(SELECT MAX(ID_PANGGIL) AS ID_BKP_MAX FROM
bk_pemanggilan
GROUP BY SISWA_PANGGIL, TA_PANGGIL) bkp
ON ID_BKP_MAX=ID_PANGGIL) bkp', 'TA_KSH=TA_PANGGIL AND SISWA_KSH=SISWA_PANGGIL AND JUMLAH_POIN<(POIN_PANGGIL + (SELECT NAMA_PENGATURAN FROM md_pengaturan WHERE ID_PENGATURAN="bk_poin_kelipatan_pemanggilan"))', 'LEFT'),
    );
    var $params = array();
    var $primary_key = "ID_KSH";
    var $name_of_pk = "JUMLAH_POIN";
    var $edit_id = FALSE;
    var $id_datatables = 'datatable1';

    public function __construct() {
        parent::__construct();
        $this->auth->validation(array(14));

        $this->load->model(array(
            'pelanggaran_header_model' => 'laporan_poin',
            'pelanggaran_model' => 'pelanggaran',
        ));

        $this->params = array(
            'where' => array(
                'TA_KSH' => $this->session->userdata('ID_TA_ACTIVE'),
//                'CAWU_KSH' => $this->session->userdata('ID_CAWU_ACTIVE'),
                'ID_PANGGIL' => NULL,
                'JUMLAH_POIN > ' => $this->pengaturan->getBkPoinMinimalDipanggil(),
            ),
        );
    }

    public function index() {
        $data = array(
            'title' => 'Surat pemanggilan',
            'subtitle' => 'Daftar poin siswa',
            'columns' => array(
                'ABS',
                'NIS',
                'NAMA',
                'KELAS',
                'WALI',
                'POIN',
                'AKSI',
            ),
            'id_modal' => "modal-data",
            'title_form' => "Tambah Surat_pemanggilan",
            'id_form' => "form-data",
            'id_datatables' => $this->id_datatables,
            'url' => 'bk/' . strtolower($this->router->fetch_class()),
            'url_action' => 'bk/' . strtolower($this->router->fetch_class()) . '/action',
        );

        $this->generate->datatables_view($data, 'bk/surat_pemanggilan/index');
    }

    public function get_datatables() {
        $this->generate->set_header_JSON();

        $columns = array('NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'JUMLAH_POIN', 'ID_KSH', 'ID_SISWA');
        $select = $columns;
        $orders = $columns;
        $order = array("ID_KSH" => 'ASC');
        $datatables = $this->db_handler->get_data_tables($this->table, $this->input->post(), $columns, $orders, $order, $this->joins, $select, $this->params);

        $data = array();
        $no = $_POST['start'];
        foreach ($datatables['data'] as $item) {
            $no++;
            $row = array();
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->JUMLAH_POIN;

            $row[] = '<button type="button" class="btn btn-success btn-xs" onclick="pilih_siswa(this)" data-ksh="' . $item->ID_KSH . '" data-nis="' . $item->NIS_SISWA . '" data-id="' . $item->ID_SISWA . '"data-nama="' . $item->NAMA_SISWA . '" data-kelas="' . $item->NAMA_KELAS . '"><i class="fa fa-check"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $datatables['count_all'],
            "recordsFiltered" => $datatables['count_filtered'],
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function simpan_surat() {
        $this->generate->set_header_JSON();

        $tanggal = $this->input->post('tanggal');
        $data = json_decode($this->input->post('data'), true);
        $no_surat = $this->pengaturan->getBkNomorSuratPemanggilan();

        foreach ($data as $detail) {
            if ($detail != NULL) {
                $data_ksh = $this->db_handler->get_row('komdis_siswa_header', array(
                    'where' => array(
                        'SISWA_KSH' => $detail['id'],
                        'TA_KSH' => $this->session->userdata('ID_TA_ACTIVE')
                    )
                        ), '*, SUM(POIN_KSH) AS JUMLAH_POIN');
                $data_ks = $this->db_handler->get_rows('komdis_siswa', array(
                    'where' => array(
                        'SISWA_KS' => $data_ksh->SISWA_KSH,
                        'TA_KS' => $data_ksh->TA_KSH,
//                        'CAWU_KS' => $data_ksh->CAWU_KSH,
                    ),
                        ), 'ID_KS');
                $this->db_handler->insert('bk_pemanggilan', array(
                    'TA_PANGGIL' => $this->session->userdata('ID_TA_ACTIVE'),
                    'CAWU_PANGGIL' => $this->session->userdata('ID_CAWU_ACTIVE'),
                    'SISWA_PANGGIL' => $data_ksh->SISWA_KSH,
                    'TANGGAL_PANGGIL' => $tanggal,
                    'NO_SURAT_PANGGIL' => $no_surat,
                    'POIN_PANGGIL' => $data_ksh->JUMLAH_POIN,
                    'DATA_KOMDIS_PANGGIL' => json_encode($data_ks),
                    'USER_PANGGIL' => $this->session->userdata('ID_USER'),
                ));
            }
        }

        $this->pengaturan->setBkNomorSuratPemanggilan($no_surat + 1);

        $this->generate->output_JSON(array('no_surat' => $no_surat));
    }

    public function cetak_surat($no_surat, $id_panggil = NULL) {
        if ($id_panggil == NULL) {
            $data_panggil = $this->db_handler->get_rows('bk_pemanggilan', array(
                'where' => array(
                    'NO_SURAT_PANGGIL' => $no_surat
                )
            ));
        } else {
            $data_panggil = $this->db_handler->get_rows('bk_pemanggilan', array(
                'where' => array(
                    'ID_PANGGIL' => $id_panggil
                )
            ));
        }

        if ($data_panggil == NULL) {
            echo 'NO SURAT TIDAK DITEMUKAN';
            exit();
        }

        $data = array();
        foreach ($data_panggil as $detail) {
            $where = array(
                'SISWA_KSH' => $detail->SISWA_PANGGIL
            );
            $siswa = $this->laporan_poin->get_data_pemanggilan($where);
            $data_komdis = json_decode($detail->DATA_KOMDIS_PANGGIL, TRUE);

            $q = "";
            $start = true;
            foreach ($data_komdis as $field => $value) {
                if (!$start)
                    $q .= " OR ";
                $q .= " ID_KS='" . $value['ID_KS'] . "' ";
                $start = false;
            }

            if (count($siswa) == 1) {
                foreach ($siswa as $detail) {
                    $where = array(
                        'TA_KS' => $detail->TA_KSH,
                        'SISWA_KS' => $detail->SISWA_KSH,
                    );
                    $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran($q);

                    $data['pelanggaran'][]['data'][] = array(
                        'siswa' => $detail,
                        'pelanggaran' => $pelanggaran
                    );
                }
            }
        }

        $this->load->view('backend/bk/surat_pemanggilan/cetak', $data);
    }

}
