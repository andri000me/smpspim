<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pelanggaran_handler {

    public function __construct() {
        $this->CI = & get_instance();

        $this->CI->load->model(array(
            'pelanggaran_model' => 'pelanggaran',
            'pelanggaran_catatan_model' => 'pelanggaran_catatan',
            'laporan_tindakan_model' => 'tindakan',
            'pelanggaran_header_model' => 'pelanggaran_header',
            'jenis_pelanggaran_model' => 'jenis_pelanggaran'
        ));
    }

    public function tambah($ID_TA, $ID_CAWU, $ID_SISWA, $ID_PELANGGARAN, $TANGGAL_KS, $SUMBER_KS, $KETERANGAN_KS, $KEHADIRAN_KS = NULL, $ALASAN_AKH = NULL, $JENIS_AKH = NULL) {
        $data = array(
            'TA_KS' => $ID_TA,
            'CAWU_KS' => $ID_CAWU,
            'SISWA_KS' => $ID_SISWA,
            'PELANGGARAN_KS' => $ID_PELANGGARAN,
            'TANGGAL_KS' => $this->CI->date_format->to_store_db($TANGGAL_KS),
            'KETERANGAN_KS' => $KETERANGAN_KS,
            'KEHADIRAN_KS' => $KEHADIRAN_KS,
            'SUMBER_KS' => $SUMBER_KS,
            'USER_KS' => $this->CI->session->userdata('ID_USER'),
        );

        $insert = $this->CI->pelanggaran->save($data);

        if ($insert) {
            unset($data);
            $data_ksh = $this->CI->pelanggaran_header->get_poin_siswa($ID_TA, $ID_CAWU, $ID_SISWA);
            $jumlah_lari = ($data_ksh == NULL) ? 0 : $data_ksh->LARI_KSH;
            $data = array(
                'TA_KSH' => $ID_TA,
                'CAWU_KSH' => $ID_CAWU,
                'SISWA_KSH' => $ID_SISWA,
                'POIN_KSH' => ((($data_ksh == NULL) ? 0 : $data_ksh->POIN_KSH) + $this->CI->jenis_pelanggaran->get_poin($ID_PELANGGARAN)),
                'LARI_KSH' => ($ALASAN_AKH == 'ALPHA' && $JENIS_AKH == 1) ? ($jumlah_lari + 1) : $jumlah_lari,
                'USER_KSH' => $this->CI->session->userdata('ID_USER'),
            );

            $where = array(
                'TA_KSH' => $ID_TA,
                'CAWU_KSH' => $ID_CAWU,
                'SISWA_KSH' => $ID_SISWA
            );

            if ($data_ksh == NULL)
                $insert = $this->CI->pelanggaran_header->save($data);
            else {
                unset($data['TA_KSH']);
                unset($data['CAWU_KSH']);
                unset($data['SISWA_KSH']);
                $insert = $this->CI->pelanggaran_header->update($where, $data);
            }
        }

        return $insert;
    }

    public function hapus($id, $form_kehadiran = FALSE, $ALASAN_AKH = NULL, $JENIS_AKH = NULL) {
        $catatan = FALSE;
        if ($form_kehadiran) {
            $data = $this->CI->pelanggaran->get_pelanggaran_siswa(array('KEHADIRAN_KS' => $id));
            if ($data == NULL) {
                $data = $this->CI->pelanggaran_catatan->get_pelanggaran_siswa(array('KEHADIRAN_KS' => $id));
                $catatan = TRUE;
            }

            if ($data == NULL) {
                $this->CI->pelanggaran_header->fix_kehadiran_komdis();

                $data = $this->CI->pelanggaran->get_pelanggaran_siswa(array('KEHADIRAN_KS' => $id));
            }

            $id_pelanggaran = $data->ID_KS;
        } else {
            $data = $this->CI->pelanggaran->get_pelanggaran_siswa(array('ID_KS' => $id));
            $id_pelanggaran = $id;
        }

        if ($catatan) {
            $affected_row = $this->CI->pelanggaran_catatan->delete_by_id($id_pelanggaran);
        } else {
            $affected_row = $this->CI->pelanggaran->delete_by_id($id_pelanggaran);

            if ($affected_row) {
                $data_ksh = $this->CI->pelanggaran_header->get_poin_siswa($data->TA_KS, $data->CAWU_KS, $data->SISWA_KS);
                $data_update = array(
                    'POIN_KSH' => ($data_ksh->POIN_KSH - $this->CI->jenis_pelanggaran->get_poin($data->PELANGGARAN_KS)),
                    'LARI_KSH' => ($ALASAN_AKH == 'ALPHA' && $JENIS_AKH == 1) ? ($data_ksh->LARI_KSH - 1) : $data_ksh->LARI_KSH,
                    'USER_KSH' => $this->CI->session->userdata('ID_USER'),
                );

                $where = array(
                    'TA_KSH' => $data->TA_KS,
                    'CAWU_KSH' => $data->CAWU_KS,
                    'SISWA_KSH' => $data->SISWA_KS
                );

                $affected_row = $this->CI->pelanggaran_header->update($where, $data_update);
            }

            if ($affected_row) {
                $affected_row = $this->CI->pelanggaran_header->reset_taqlik_mutasi($data);
            }
        }

        return $affected_row;
    }

    public function cek_pelanggaran_syariah($ID_SISWA) {
        $where = array(
            'ID_KS' => $this->CI->session->userdata('ID_TA_ACTIVE'),
            'SISWA_KS' => $ID_SISWA,
        );

        $data_siswa = $this->CI->pelanggaran->get_rows($where);

        foreach ($data_siswa as $detail) {
            if ($this->CI->pengaturan->getPoinPelanggaranSyariat() == $detail->POIN_KJP) {
                return $detail->NAMA_KJP;
            }
        }

        return NULL;
    }

    public function proses_poin_tahun_lalu($ID_SISWA, $TA) {
        $poin = $this->CI->pelanggaran_header->get_total_poin_siswa($this->CI->session->userdata('ID_TA_ACTIVE'), $ID_SISWA);

        for ($cawu = 1; $cawu <= 3; $cawu++) {
            $data_pelanggaran = array(
                'TA_KSH' => $TA,
                'CAWU_KSH' => $cawu,
                'SISWA_KSH' => $ID_SISWA,
                'POIN_TAHUN_LALU_KSH' => ($poin == NULL ? 0 : $poin),
                'USER_KSH' => $this->CI->session->userdata('ID_USER')
            );
            $this->CI->pelanggaran_header->save($data_pelanggaran);
        }
    }

}
