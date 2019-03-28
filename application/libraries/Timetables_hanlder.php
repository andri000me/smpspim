<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Timetables_hanlder {

    var $dom;
    var $timetables;
    var $days;
    var $day;
    var $periods;
    var $period;
    var $teachers;
    var $teacher;
    var $classes;
    var $class;
    var $subjects;
    var $subject;

    public function __construct() {
        $this->CI = & get_instance();

        $this->CI->load->model(array(
            'hari_model' => 'hari',
            'jam_pelajaran_model' => 'jam_pelajaran',
            'pegawai_model' => 'pegawai',
            'kelas_model' => 'kelas',
            'mapel_akad_model' => 'mapel',
            'jadwal_model' => 'jadwal',
            'guru_mapel_model' => 'guru_mapel',
        ));

        $this->CI->load->helper('xml');
    }

    public function write_xml($jenjang, $jk) {
        $this->dom = xml_dom();

        $this->write_timetables();
        $this->write_hari();
        $this->write_hari_data();
        $this->write_jam_pelajaran();
        $this->write_jam_pelajaran_data($jenjang, $jk);
        $this->write_guru();
        $this->write_guru_data();
        $this->write_kelas();
        $this->write_kelas_data($jenjang, $jk);
        $this->write_mapel();
        $this->write_mapel_data($jenjang);

//	xml_print($this->dom);
        xml_print($this->dom, TRUE);

        return $this->dom;
    }

    private function write_timetables() {
        $this->timetables = xml_add_child($this->dom, 'timetable');
    }

    private function write_hari() {
        $this->days = xml_add_child($this->timetables, 'daysdefs');
        xml_add_attribute($this->days, 'options', 'canadd');
        xml_add_attribute($this->days, 'columns', 'id,days,name,short');
    }

    private function write_hari_data() {
        $where = array(
            'LIBUR_HARI' => 0
        );
        $data = $this->CI->hari->get_rows($where);

        $i = 0;
        $max = 6;
        foreach ($data as $detail) {
            $this->day = xml_add_child($this->days, 'daysdef');

            $days_def = '';
            for ($k = 0; $k < $max; $k++) {
                if ($i == $k)
                    $days_def .= '1';
                else
                    $days_def .= '0';
            }

            xml_add_attribute($this->day, 'name', $detail->NAMA_HARI);
            xml_add_attribute($this->day, 'short', $detail->ID_HARI);
            xml_add_attribute($this->day, 'days', $days_def);
            xml_add_attribute($this->day, 'id', $detail->ID_HARI);

            $i++;
        }
    }

    private function write_jam_pelajaran() {
        $this->periods = xml_add_child($this->timetables, 'periods');
        xml_add_attribute($this->periods, 'options', 'canadd');
        xml_add_attribute($this->periods, 'columns', 'period,starttime,endtime');
    }

    private function write_jam_pelajaran_data($jenjang, $jk) {
        $where = array();
        if ($jenjang != 'ALL')
            $where['DEPT_MJP'] = $jenjang;
        else
            $where['DEPT_MJP'] = 'AL';
        $where['JK_MJP'] = $jk;
        $data = $this->CI->jam_pelajaran->get_rows($where);

        $id = 1;
        foreach ($data as $detail) {
            $this->period = xml_add_child($this->periods, 'period');

            xml_add_attribute($this->period, 'period', $id++);
            xml_add_attribute($this->period, 'short', $detail->ID_MJP);
            xml_add_attribute($this->period, 'starttime', $detail->MULAI_MJP);
            xml_add_attribute($this->period, 'endtime', $detail->AKHIR_MJP);
        }
    }

    private function write_guru() {
        $this->teachers = xml_add_child($this->timetables, 'teachers');
        xml_add_attribute($this->teachers, 'options', 'canadd');
        xml_add_attribute($this->teachers, 'columns', 'id,name,short,gender,color');
    }

    private function write_guru_data() {
        $data = $this->CI->pegawai->get_all(FALSE);

        foreach ($data as $detail) {
            $this->teacher = xml_add_child($this->teachers, 'teacher');

            xml_add_attribute($this->teacher, 'id', '*' . $detail->ID_PEG);
            xml_add_attribute($this->teacher, 'name', $detail->NAMA_PEG);
            xml_add_attribute($this->teacher, 'short', $detail->ID_PEG);
            xml_add_attribute($this->teacher, 'gender', $detail->JK_PEG);
            xml_add_attribute($this->teacher, 'color', '#' . $this->CI->crypt->randomColor());
        }
    }

    private function write_kelas() {
        $this->classes = xml_add_child($this->timetables, 'classes');
        xml_add_attribute($this->classes, 'options', 'canadd');
        xml_add_attribute($this->classes, 'columns', 'id,name,short,classroomids,teacherid,grade');
    }

    private function write_kelas_data($jenjang, $jk) {
        $where = array(
            'TA_KELAS' => $this->CI->session->userdata('ID_TA_ACTIVE'),
            'JK_KELAS' => $jk,
        );
        if ($jenjang != 'ALL')
            $where['DEPT_TINGK'] = $jenjang;
        $data = $this->CI->kelas->get_rows($where);

        foreach ($data as $detail) {
            $this->class = xml_add_child($this->classes, 'class');

            xml_add_attribute($this->class, 'id', '*' . $detail->ID_KELAS);
            xml_add_attribute($this->class, 'name', $detail->NAMA_KELAS);
            xml_add_attribute($this->class, 'short', $detail->ID_KELAS);
            xml_add_attribute($this->class, 'teacherid', '');
            xml_add_attribute($this->class, 'classroomids', '');
            xml_add_attribute($this->class, 'grade', '');
        }
    }

    private function write_mapel() {
        $this->subjects = xml_add_child($this->timetables, 'subjects');
        xml_add_attribute($this->subjects, 'options', 'canadd');
        xml_add_attribute($this->subjects, 'columns', 'id,name,short');
    }

    private function write_mapel_data($jenjang) {
        $where = array(
            'AKTIF_MAPEL' => 1
        );
        if ($jenjang != 'ALL')
            $where['DEPT_MAPEL'] = $jenjang;
        $data = $this->CI->mapel->get_rows($where);

        foreach ($data as $detail) {
            $this->subject = xml_add_child($this->subjects, 'subject');

            xml_add_attribute($this->subject, 'id', '*' . $detail->ID_MAPEL);
            xml_add_attribute($this->subject, 'name', $detail->NAMA_MAPEL);
            xml_add_attribute($this->subject, 'short', $detail->ID_MAPEL);
        }
    }

    public function read_xml($file, $jenjang) {
        $xml = simplexml_load_file($file) or $this->CI->generate->output_JSON(array("status" => FALSE, 'msg' => 'Tidak bisa membaca file xml'));

        $where_mjp = array(
            'DEPT_MJP' => $jenjang == 'ALL' ? 'AL' : $jenjang
        );
        $data_mjp = $this->CI->jam_pelajaran->get_rows($where_mjp);

        $jam_pelajaran_db = array();
        $x = 1;
        foreach ($data_mjp as $detail_mjp) {
            $jam_pelajaran_db[$x] = $detail_mjp->ID_MJP;
            $x++;
        }
//        echo json_encode($jam_pelajaran_db);
        // MENGAMBIL GURU - PELAJARAN
        $data_lesson = array();
        $data_kelas = array();
        foreach ($xml->lessons as $lesson) {
            foreach ($lesson as $element) {
                $id_lesson = (string) $element->attributes()['id'];
                $kelas = $this->get_id($xml->classes, (string) $element->attributes()['classids']);
                $mapel = $this->get_id($xml->subjects, (string) $element->attributes()['subjectid']);
                $guru = $this->get_id($xml->teachers, (string) $element->attributes()['teacherids']);
                if ($kelas == NULL || $mapel == NULL || $guru == NULL)
                    $this->CI->generate->output_JSON(array("status" => FALSE, 'msg' => 'File tidak lengkap. Silahkan import file yang lain.'));

                $data_kelas[$kelas] = true;
                $data_lesson[$id_lesson] = array(
                    'KELAS' => $kelas,
                    'MAPEL' => $mapel,
                    'GURU' => $guru,
                );
            }
        }

        // MENGAMBIL JADWAL
        $data_card = array();
        foreach ($xml->cards as $card) {
            foreach ($card as $element) {
                $id_lesson = (string) $element->attributes()['lessonid'];
                $hari = $this->get_day($xml->days, (string) $element->attributes()['day']);
                if ($hari == NULL)
                    $hari = $this->get_daydef($xml->daysdefs, (string) $element->attributes()['days']);

                $jam_pelajaran = (string) $element->attributes()['period'];

                if ($hari == NULL || !isset($data_lesson[$id_lesson]))
                    $this->CI->generate->output_JSON(array("status" => FALSE, 'msg' => 'File tidak lengkap. Silahkan import file yang lain.'));

                if (!isset($jam_pelajaran_db[intval($jam_pelajaran)]))
                    $this->CI->generate->output_JSON(array("status" => FALSE, 'msg' => 'Jam pelajaran tidak terdaftar di sistem. Silahakan lengkapi jam pelajaran terlebih dahulu.'));

                $data_card[] = array(
                    'LESSON' => $id_lesson,
                    'HARI' => $hari,
                    'JAM_PELAJARAN' => $jam_pelajaran_db[intval($jam_pelajaran)],
                );
            }
        }

//        exit();
        // IMPORT DATA KE DATABASE
        foreach ($data_kelas as $kelas => $temp) {
            $this->CI->guru_mapel->delete_by_where(array(
                'TA_AGM' => $this->CI->session->userdata("ID_TA_ACTIVE"),
                'KELAS_AGM' => $kelas
            ));
        }

        $id_db_lesson = array();
        foreach ($data_lesson as $index => $detail_lesson) {
            $data_insert_lesson = array(
                'TA_AGM' => $this->CI->session->userdata("ID_TA_ACTIVE"),
                'KELAS_AGM' => $detail_lesson['KELAS'],
                'MAPEL_AGM' => $detail_lesson['MAPEL'],
                'GURU_AGM' => $detail_lesson['GURU'],
                'FILE_AGM' => $file,
                'USER_AGM' => $this->CI->session->userdata("ID_USER"),
            );

            $data_delete_lesson = $data_insert_lesson;
            unset($data_delete_lesson['FILE_AGM']);
            unset($data_delete_lesson['USER_AGM']);
            $data_agm = $this->CI->guru_mapel->get_row($data_delete_lesson);
            $this->CI->guru_mapel->delete_by_where($data_delete_lesson);
            $id_db_lesson[$index] = $this->CI->guru_mapel->save($data_insert_lesson);

            if ($data_agm != NULL) {
                foreach ($id_db_lesson as $key => $value) {
                    if ($value == $data_agm->ID_AGM)
                        $id_db_lesson[$key] = $id_db_lesson[$index];
                }
            }
        }

        foreach ($data_card as $detail_card) {
            $data_insert_card = array(
                'GURU_MAPEL_AJ' => $id_db_lesson[$detail_card['LESSON']],
                'HARI_AJ' => $detail_card['HARI'],
                'JAM_AJ' => $detail_card['JAM_PELAJARAN'],
                'USER_AJ' => $this->CI->session->userdata("ID_USER"),
            );
            $this->CI->jadwal->save($data_insert_card);
        }
    }

    private function get_id($parent, $id) {
        foreach ($parent as $child) {
            foreach ($child as $element) {
                $attr = $element->attributes();
                if (((string) $attr->id) == $id)
                    return ((string) $attr->short);
            }
        }
    }

    private function get_day($parent, $id) {
        foreach ($parent as $child) {
            foreach ($child as $element) {
                $attr = $element->attributes();
                if (((string) $attr->day) == $id)
                    return ((string) $attr->short);
            }
        }
    }

    private function get_daydef($parent, $id) {
        foreach ($parent as $child) {
            foreach ($child as $element) {
                $attr = $element->attributes();
                if (((string) $attr->days) == $id)
                    return ((string) $attr->short);
            }
        }
    }

}
