<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Generate_HTML {

//put your code here

    public function __construct() {
        $this->CI = & get_instance();
    }

    // ========================================= LOAD VIEW ========================================================================================================

    public function backend_view($file, $data = '') {
        $this->CI->load->view('layout/main/header');
        $this->CI->load->view('layout/backend/header');
        $this->CI->load->view('layout/backend/sidebar');
        if (is_array($file)) {
            foreach ($file as $view) {
                $this->CI->load->view('backend/' . $view, $data);
            }
        } else {
            $this->CI->load->view('backend/' . $file, $data);
        }
        $this->CI->load->view('layout/backend/footer');
    }

    public function backend_form_view($file) {
        $this->CI->load->view('backend/' . $file);
    }

    // ========================================= SETTING JSON ======================================================================================================

    public function set_header_JSON() {
        if (!$this->CI->input->is_ajax_request()) {
            show_error("Your request is not valid", "403", "ERROR");
        } else {
            header('Content-Type: application/json');
        }
    }

    public function output_JSON($data) {
        if (is_array($data)) {
            if (isset($data['status']) && $data['status'] > 0)
                $this->CI->session->unset_userdata('TOKEN');
        }

        echo json_encode($data, JSON_PRETTY_PRINT);
        
        $this->CI->database_logging->logQueries();
        
        exit();
    }

    public function set_header_form_JSON($model) {
        $this->set_header_JSON();

        if ($this->CI->input->post("ID") === NULL) {
            return NULL;
        } else {
            $id = $this->CI->input->post("ID");

            return $model->get_by_id($id);
        }
    }

    public function output_form_JSON($data, $field, $data_html, $input_id = FALSE, $show_id = FALSE, $edit_id = FALSE, $no_auth = FALSE) {
        if ($data == NULL) {
            $status_check = 'add';
        } else {
            $status_check = 'edit';
        }
        
        if ($no_auth || (!$no_auth && $this->CI->auth->crud_validation($status_check))) {
            $return = array(
                'STATUS' => TRUE,
            );

            $token = array(
                'hidden' => TRUE,
                'data' => array(
                    'name' => 'TOKEN',
                    'value' => $this->CI->auth->generate_token()
                )
            );

            array_unshift($data_html, $token);

            if ($status_check == 'add') {
                if ($input_id) {
                    $data_id = array(
                        'label' => 'ID', // WAJIB
                        'required' => TRUE,
                        'keterangan' => 'Wajib diisi',
                        'data' => array(
                            'type' => 'text', // WAJIB
                            'name' => $field, // WAJIB
                        )
                    );

                    array_unshift($data_html, $data_id);
                }
            } else if ($status_check == 'edit') {
                if ($show_id && !$edit_id) {
                    $data_id = array(
                        'label' => 'ID', // WAJIB
                        'required' => TRUE,
                        'data' => array(
                            'type' => 'text', // WAJIB
                            'name' => $field, // WAJIB
                            'readonly' => 'true',
                            'value' => $data->$field
                        )
                    );
                } else if ($show_id && $edit_id) {
                    $data_id = array(
                        'label' => 'ID', // WAJIB
                        'required' => TRUE,
                        'data' => array(
                            'type' => 'text', // WAJIB
                            'name' => $field,
                            'value' => $data->$field
                        )
                    );
                    array_unshift($data_html, $data_id);
                    $data_id = array(
                        'hidden' => TRUE, // WAJIB
                        'data' => array(
                            'name' => 'TEMP_' . $field, // WAJIB
                            'value' => $data->$field                                     // WAJIB
                        )
                    );
                } else {
                    $data_id = array(
                        'hidden' => TRUE, // WAJIB
                        'data' => array(
                            'name' => $field, // WAJIB
                            'value' => $data->$field                                     // WAJIB
                        )
                    );
                }

                array_unshift($data_html, $data_id);
            }

            $return['DATA'] = $data_html;
        } else {
            $return = array(
                'STATUS' => FALSE,
                'MESSAGE' => 'Anda tidak memiliki hak akses.'
            );
        }
        
        $this->output_JSON($return);
    }

    public function cek_update_id($edit_id, $pk, $post) {
        $return = array();

        if ($edit_id) {
            if ($post['TEMP_' . $pk] == $post[$pk]) {
                $return['where'][$pk] = $post[$pk];
            } else {
                $return['where'][$pk] = $post['TEMP_' . $pk];
                $return['data'][$pk] = $post[$pk];
            }
        } else {
            $return['where'][$pk] = $post[$pk];
        }

        return $return;
    }

    public function filter_data_post($edit_id, $pk, $post) {
        unset($post['TOKEN']);

        if ($edit_id) {
            unset($post[$pk]);
            unset($post['TEMP_' . $pk]);
        }

        return $post;
    }

    public function cek_validation_simple($type) {
        if (!$this->CI->auth->crud_validation($type)) {
            $this->output_JSON(array(
                'STATUS' => FALSE,
                'MESSAGE' => 'Akses tidak sah atau Anda tidak memiliki hak akses'
            ));
        }
    }

    public function cek_validation_form($type) {
        if (($this->CI->session->userdata('TOKEN') != $this->CI->crypt->encryptPassword($this->CI->input->post('TOKEN')) and $type != 'delete') or ! $this->CI->auth->crud_validation($type)) {
            $this->output_JSON(array(
                'STATUS' => FALSE,
                'MESSAGE' => 'Akses tidak sah atau Anda tidak memiliki hak akses '
            ));
        }
    }

    public function clear_token($data) {
        unset($data['TOKEN']);

        return $data;
    }

    // ========================================= CONTENT - PANEL ====================================================================================================

    public function generate_panel_content($title, $subtitle = '') {
        // normalheader
        echo '<div class="small-header transition animated fadeIn">
            <div class="hpanel">
                <div class="panel-body">
                    <a class="small-header-action" href="">
                        <div class="clip-header">
                            <i class="fa fa-arrow-up"></i>
                        </div>
                    </a>
                    <h2 class="font-light m-b-xs">
                        ' . $title . '
                    </h2>
                    <small>' . $subtitle . '</small>
                </div>
            </div>
        </div>';
    }

    // ========================================= CONTENT - CHART ===============================================================================================
    
    public function chart($id_chart, $title, $single) {
        $this->content_chart($id_chart, $title, $single);
    }
    
    private function content_chart($id_chart, $title, $single = FALSE) {
        echo '<div class="content animate-panel panel-'.$id_chart.'" style="margin-top: -60px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="hpanel hblue">
                        <div class="panel-heading hbuilt">
                            <div class="pull-right">
                                <div class="btn-group">
                                    <!--<button onclick="reload_chart_'.$id_chart.'(chart_'.$id_chart.');" class="btn btn-success btn-xs">Reload</button>-->
                                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">Rubah grafik ke <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a onclick="'.($single ? 'request_chart_'.$id_chart.'(\'spline\')' : 'chart_transform_spline(chart_'.$id_chart.')').';">Grafik Spline</a></li>
                                        <li><a onclick="'.($single ? 'request_chart_'.$id_chart.'(\'line\')' : 'chart_transform_line(chart_'.$id_chart.')').';">Grafik Line</a></li>
                                        <li><a onclick="'.($single ? 'request_chart_'.$id_chart.'(\'area\')' : 'chart_transform_area(chart_'.$id_chart.')').';">Grafik Area</a></li>
                                        <li><a onclick="'.($single ? 'request_chart_'.$id_chart.'(\'area-spline\')' : 'chart_transform_area_spline(chart_'.$id_chart.')').';">Grafik Area Spline</a></li>
                                        <li><a onclick="'.($single ? 'request_chart_'.$id_chart.'(\'bar\')' : 'chart_transform_bar(chart_'.$id_chart.')').';">Grafik Bar</a></li>
                                        <li><a onclick="'.($single ? 'request_chart_'.$id_chart.'(\'pie\')' : 'chart_transform_pie(chart_'.$id_chart.')').';">Grafik Pie</a></li>
                                        <li><a onclick="'.($single ? 'request_chart_'.$id_chart.'(\'donut\')' : 'chart_transform_donut(chart_'.$id_chart.')').';">Grafik Donut</a></li>
                                    </ul>
                                </div>
                            </div>
                            '.$title.'
                        </div>
                        <div class="panel-body">
                                <div>
                                    <div id="'.$id_chart.'"></div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    // ========================================= CONTENT - DATATABLES ===============================================================================================

    public function datatables($id_datatables, $title, $columns) {
        $this->table_start($title, $id_datatables);
        $this->table_header($columns);
        $this->table_end();
        $this->request_datatabales();
    }

    private function request_datatabales() {
        echo '
            <!-- DataTables -->
            <script src="' . base_url() . 'assets/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables/plugins/pagination/input.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables/plugins/pagination/extjs.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables/plugins/pagination/select.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
            <script src="' . base_url() . 'assets/vendor/pdfmake/build/pdfmake.min.js"></script>
            <script src="' . base_url() . 'assets/vendor/pdfmake/build/vfs_fonts.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables-buttons/dataTables.buttons.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables-buttons/buttons.colVis.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables-buttons/buttons.flash.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables-buttons/jszip.min.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables-buttons/pdfmake.min.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables-buttons/vfs_fonts.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables-buttons/buttons.html5.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables-buttons/buttons.print.js"></script>
            <!-- DataTables buttons scripts 
            <script src="' . base_url() . 'assets/vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
            <script src="' . base_url() . 'assets/vendor/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>-->
        ';
    }

    private function table_start($title, $id_datatables) {
        echo '<div class="content animate-panel table-'.$id_datatables.'">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    Tabel ' . $title . '
                </div>
                <div class="panel-body">
                    <table id="' . $id_datatables . '" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>';
    }

    private function table_header($columns) {
        $tag_html = "";
        foreach ($columns as $column) {
            $tag_html .= '<th>' . $column . '</th>';
        }

        echo $tag_html . '</tr></thead><tfoot><tr>' . $tag_html;
    }

    private function table_end() {
        echo '
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>';
    }

    // ========================================= CONTENT - FORM MODAL ===============================================================================================

    public function form_modal($id_modal, $title, $id_form, $id_datatables) {
        $this->form_start($id_modal, $title);
        $this->form_content($id_form, $id_datatables);
        $this->form_end($id_form);
    }

    public function button_modal($id, $title) {
        echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#' . $id . '">' . $title . '</button>';
    }

    private function form_start($id, $title) {
        echo ' <div class="modal fade" id="' . $id . '" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="color-line"></div>
                                <div class="modal-header">
                                    <h4 class="modal-title">' . $title . '</h4>
                                </div>';
    }

    private function form_content($id_form, $id_datatables) {
        echo '<div class="modal-body text-center">' . $this->form_open($id_form, $id_datatables) . '<p>Sedang memuat form ...</p> <div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div></div>' . $this->form_close() . '</div>';
    }

    private function form_end($id_form) {
        echo '<div class="modal-footer">
                                    <span class="pull-left info-field"><strong>&nbsp;&nbsp;&nbsp;&nbsp;Field bertanda * wajib diisi.</strong></span>
                                    <button type="button" class="btn btn-default ladda-bind" data-dismiss="modal">Keluar</button>
                                    <button type="button" class="btn btn-primary ladda-button ladda-button-save" data-style="expand-right" onclick="save_form(\'' . $id_form . '\')">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        function save_form(id_form) {
                            create_ladda("ladda-button-save");
                            $("#" + id_form).trigger("submit");
                        }
                    </script>                    
        ';
    }

    public function form_open($id_form, $id_datatables) {
        return '<form action="#" method="post" class="form-horizontal" enctype="multipart/form-data" id="' . $id_form . '" onsubmit="return action_save_' . $id_datatables . '(\'' . $id_form . '\');">';
    }

    public function form_close() {
        return '</form>';
    }

    private function open_input($label, $length, $required) {
        $html = '<div class="form-group"><label class="col-sm-2 control-label">' . $label;
        if ($required)
            $html .= ' *';
        $html .= '</label><div class="col-sm-' . $length . '">';

        return $html;
    }

    private function close_input($required, $code_extra) {
        $html = "";
        if ($required)
            $html .= '<span class="help-block m-b-none text-left">Wajib diisi</span>';
        return $html . '</div>' . $code_extra . '</div>';
    }

    public function input_hidden($name, $value) {
        echo '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />';
    }

    public function input_text($label, $data, $required = FALSE, $length = 7, $code_extra = '') {
        $html = $this->open_input($label, $length, $required);
        $html .= '<input class="form-control ';
        if ($required)
            $html .= 'required';
        $html .= '" type="text" ';

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $html .= $key . '="' . $value . '" ';
            }
        }

        $html .= '/>' . $this->close_input($required, $code_extra);

        echo $html;
    }

    public function input_date($label, $data, $required = FALSE, $length = 7, $code_extra = '') {
        $value_set = FALSE;
        $id = $data["name"];
        $html = $this->open_input($label." (YYYY-MM-DD)", $length, $required);
        $html .= '<input data-date-format="yyyy-mm-dd" class="form-control ';
        if ($required)
            $html .= 'required';
        $html .= '" type="text" ';

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key == 'value') {
//                    $value = date("d/m/Y", strtotime($value));
                    $value_set = TRUE;
                }
                if($key == 'id') {
                    $id = $value;
                    continue;
                }
                $html .= $key . '="' . $value . '" ';
            }
        }

        $html .= ' placeholder="YYYY-MM-DD" ';

        $html .= ' id="' . $id . '" />' . $this->close_input($required, $code_extra);

        $html .= '<script type="text/javascript">$("#' . $id . '").datepicker()'.($value_set ? '' : '.datepicker("setDate", new Date())').';</script>';

        echo $html;
    }

    public function input_time($label, $data, $required = FALSE, $length = 7, $code_extra = '') {
        $id = $data["name"];
        $html = $this->open_input($label, $length, $required);
        $html .= '<input class="form-control ';
        if ($required)
            $html .= 'required';
        $html .= '" type="text" ';

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key == 'value')
                    $value = date("H:i:s", strtotime($value));
                if($key == 'id') {
                    $id = $value;
                    continue;
                }
                $html .= $key . '="' . $value . '" ';
            }
        }

        $html .= ' placeholder="HH:MM:SS" ';

        $html .= ' id="' . $id . '" />' . $this->close_input($required, $code_extra);

        $html .= '<script type="text/javascript">$("#' . $id . '").clockpicker({autoclose: true});</script>';

        echo $html;
    }

    public function input_dropdown($label, $name, $data, $required = FALSE, $length = 7, $code_extra = '') {
        $html = $this->open_input($label, $length, $required);
        $html .= '<select class="form-control ';
        if ($required)
            $html .= 'required';
        $html .= '" name="'.$name.'" id="'.$name.'">';

        if (is_array($data)) {
            foreach ($data as $value) {
                $html .= '<option value="'.$value['id'].'" ';
                if (isset($value['selected'])) $html .= ' selected ';
                $html .= '>'.$value['text'].'</option>';
            }
        }

        $html .= '</select>' . $this->close_input($required, $code_extra);

        echo $html;
    }
    
    public function input_checkbok($label, $data, $required = FALSE, $length = 7, $code_extra = '') {
        $html = $this->open_input($label, $length, $required);

        foreach ($data['value'] as $value) {
            $html .= '<div class="checkbox checkbox-info"><input name="' . $data['name'] . '" value="' . $value['value'] . '" type="checkbox"';
            if ($required)
                $html .= ' class="required"';
            if ($data['checked'] == $value['value'])
                $html .= ' checked';
            $html .= '><label>' . $value['label'] . '</label></div>';
        }

        $html .= $this->close_input($required, $code_extra);

        echo $html;
    }

    public function input_radio($label, $data, $required = FALSE, $length = 7, $code_extra = '') {
        $html = $this->open_input($label, $length, $required);

        foreach ($data['value'] as $value) {
            $html .= '<div class="radio radio-info"><input name="' . $data['name'] . '" value="' . $value['value'] . '" type="radio"';
            if ($required)
                $html .= ' class="required"';
            if ($data['checked'] == $value['value'])
                $html .= ' checked';
            $html .= '><label>' . $value['label'] . '</label></div>';
        }

        $html .= $this->close_input($required, $code_extra);

        echo $html;
    }

    public function input_select2($label, $data, $required = FALSE, $length = 7, $show_all = FALSE, $value = NULL, $code_extra = '') {
        $html = $this->open_input($label, $length, $required);
        
        if (isset($data['id'])) $id = $data['id'];
        else $id = $data['name'];
        
        $html .= '<input class="form-control js-source-multi js-source-states-' . $id;
        if ($required)
            $html .= ' required';
        $html .= '" name="' . $data['name'] . '" style="width: 100%" multiple="multiple" >';
        $html .= $this->close_input($required, $code_extra);
        $html .= '<script type="text/javascript">
            $(".js-source-states-' . $id . '").select2({';
        if ($show_all)
            $html .= 'minimumInputLength: 1,';
        $html .= 'escapeMarkup: function (markup) { return markup; },
                ajax: {
                    url: "' . $data['url'] . '",
                    dataType: "json",
                    type: "POST",
                    delay: 100,
                    cache: true,
                    data: function (term, page) {
                        return {
                            q: term
                        }
                    },
                    results: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id
                                }
                            })
                        };
                    }
                },
                formatResult: function(element){
                    return element.text;
                },
                formatSelection: function(element){
                    return element.text;
                },
            });';
        if ($value !== NULL)
            $html .= '$(".js-source-states-' . $id . '").select2("data", {id: "' . $value['id'] . '", text: "' . $value['text'] . '"});';
        $html .= '</script>';

        echo $html;
    }

    function input_photo($name) {
        $base_url = base_url();

        echo '<div class="form-group">
            <label class="col-sm-3 control-label"> Upload Foto</label>
              <input type="hidden" name="from_upload" id="from_upload" value="2" />
              <input type="hidden" name="TAKE_'.$name.'" id="data_image" />
              <div class="col-sm-5" id="photo">
                  <input type="file" name="UPLOAD_'.$name.'" id="UPLOAD_'.$name.'" class="form-control" onchange="set_status_upload();"/>
                    <span class="help-block m-b-none text-left">File ext. harus png dengan ukuran maksimal 2400x2400 pixel dan ukuran maksimal 2MB</span>
            </div>
            <div class="col-sm-1">
                <img src="' . $base_url . '/assets/images/camera.png" alt="camera" width="35" style="cursor: pointer" title="Klik untuk mengambil gambar." onclick="open_camera();" class="pull-right"/>
            </div>
            <div class="col-sm-1">
                <img src="' . $base_url . '/assets/images/cancel-solid-icon.png" alt="camera" width="35" style="cursor: pointer" title="Klik untuk menghapus gambar." onclick="remove_image();" id="remove_image_id"/>
            </div>
          </div>
            <script type="text/javascript">
                function set_status_upload(){
                    $("#from_upload").val("1");
                }
            </script>
            ';
    }

    function content_webcam() {
        echo '
        <div class="row" class="camera_on">
            <div id="camera" class="col-sm-6 camera_on"  style="width: 400px; height: 300px;"></div>
            <div id="galeri" class="col-sm-6 camera_on"></div>
        </div>
        <hr class="camera_on"/>
        <div class="row camera_on">
            <div class="col-sm-12">
                <button type="button" id="crop_photo" onclick="crop_foto();" class="btn btn-primary">Crop</button>
                <button type="button" id="take_photo" onclick="ambil_foto();" class="btn btn-primary">Ambil</button>
                <button type="button" id="save_image" onclick="simpan_gambar();" class="btn btn-primary" disabled="true" style="margin-left: 25px">OK</button>
                <button type="button" onclick="camera_close();" class="btn btn-danger" style="margin-left: 25px">Batal</button>
            </div>
        </div>
        <script type="text/javascript">
        var url_img_cam = null;

        window.onload = camera_ready();   

        $("#camera").photobooth(); 

        function camera_ready() {
            $("#remove_image_id").hide();

            camera_close();
        }

        function camera_close() {
            $("#myForm").fadeIn();
            $(".camera_on").hide();
            $("#save_image").attr("disabled", "true");
            $("#galeri").html("");

            $("#camera").data( "photobooth" ).destroy();
        }
        
        function ambil_foto() {
            $(".trigger").trigger("click");
        }
        
        function crop_foto() {
            $(".crop").trigger("click");
        }

        function simpan_gambar() {
            if(url_img_cam != null) {
                $("#from_upload").val("0");
                $("#photo").html(\'<img src="\' + url_img_cam + \'" >\');
                $("#remove_image_id").fadeIn();
                $("#data_image").val(url_img_cam);
            } else {
                notify_error("Gagal mengambil url gambar. Silahkan muat ulang halaman ini.");
            }

            camera_close();
        }

        function open_camera(){
            $("#myForm").hide();
            $(".camera_on").fadeIn();

            $("#camera").photobooth().on( "image", function( event, dataUrl ){
                url_img_cam = dataUrl;
                $("#save_image").removeAttr("disabled");
                $( "#galeri" ).show().html( \'<img src="\' + dataUrl + \'" >\');
            });
        }

        function remove_image() {
            $("#from_upload").val("2");
            $("#photo").html(\'<input type="file" name="fileBukti" id="fileBukti" class="form-control"/>\');
            $("#remove_image_id").hide();

            url_img_cam = null;
        }

        </script>';
    }
}
