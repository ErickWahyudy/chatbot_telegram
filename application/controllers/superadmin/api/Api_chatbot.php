<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_chatbot extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->library('session');
        $this->load->model('m_chatbot');
        $this->load->library('form_validation');
    }

    private $valid_token_api = '123chatbot'; // Token API yang valid


    public function api_get_view() {
        $token = $this->input->get_request_header('Authorization');
        $valid_token = $this->valid_token_api;

        if ($token !== 'Bearer ' . $valid_token) {
            $response = [
                'status' => false,
                'message' => 'Unauthorized access'
            ];
            return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode($response))
                        ->set_status_header(401);
        }

        $data_chatbot = $this->m_chatbot->view()->result_array();

        $response = [
            'status' => true,
            'message' => 'Data chatbot',
            'data' => $data_chatbot
        ];

        return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
    }

    private function acak_id($panjang)
    {
        $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $string = '';
        for ($i = 0; $i < $panjang; $i++) {
            $pos = rand(0, strlen($karakter) - 1);
            $string .= $karakter{$pos};
        }
        return $string;
    }
    
     //mengambil id chatbot urut terakhir
     private function id_chatbot_urut($value='')
     {
     $this->m_chatbot->id_urut();
     $query   = $this->db->get();
     $data    = $query->row_array();
     $id      = $data['id_bot_commands'];
     $karakter= $this->acak_id(6);
     $urut    = substr($id, 1, 3);
     $tambah  = (int) $urut + 1;
     
     if (strlen($tambah) == 1){
     $newID = "C"."00".$tambah.$karakter;
         }else if (strlen($tambah) == 2){
         $newID = "C"."0".$tambah.$karakter;
             }else (strlen($tambah) == 3){
             $newID = "C".$tambah.$karakter
             };
         return $newID;
     }



     // Fungsi untuk mengompres ukuran gambar
    private function compress($source, $destination, $quality) 
    {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') 
            $image = imagecreatefromjpeg($source);
        elseif ($info['mime'] == 'image/gif') 
            $image = imagecreatefromgif($source);
        elseif ($info['mime'] == 'image/png') 
            $image = imagecreatefrompng($source);
        //jika file pdf / doc / docx / xls / xlsx / ppt / pptx / txt maka tidak di kompres
        elseif ($info['mime'] == 'application/pdf' || $info['mime'] == 'application/msword' || $info['mime'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $info['mime'] == 'application/vnd.ms-excel' || $info['mime'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $info['mime'] == 'application/vnd.ms-powerpoint' || $info['mime'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation' || $info['mime'] == 'text/plain') 
            return $source;
        else
            return false;
        imagejpeg($image, $destination, $quality);
        return $destination;
    }

    // Fungsi untuk upload dan kompres berkas
    private function berkas()
    {
        if ($_FILES['foto']['name'] != '') {
            $config['upload_path']          = './themes/chatbot/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg|pdf|doc|docx|xls|xlsx|ppt|pptx|txt|zip|rar'; // Sesuaikan dengan tipe berkas
            $config['max_size']             = 10000; // Sesuaikan ukuran maksimal
            //$config['file_name']            = 'files_' . uniqid();
            $config['file_name']            = $_FILES['foto']['name'];

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('foto')) {
                // Logging error jika upload gagal
                log_message('error', 'Upload Error: ' . $this->upload->display_errors());
                return false;
            } else {
                $data = array('upload_data' => $this->upload->data());
                // Kompres gambar
                $this->compress($data['upload_data']['full_path'], $data['upload_data']['full_path'], 90);
                return $data['upload_data']['file_name'];
            }
        } else {
            return '';
        }
    }

    // API untuk menambah data dengan upload file
    public function api_add($value = '')
    {
        $token = $this->input->get_request_header('Authorization');
        $valid_token = $this->valid_token_api;

        if ($token !== 'Bearer ' . $valid_token) {
            $response = [
                'status' => false,
                'message' => 'Unauthorized access'
            ];
            return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode($response))
                        ->set_status_header(401);
        }

        $rules = array(
            array(
                'field' => 'command',
                'label' => 'Command',
                'rules' => 'required',
                'errors' => array(
                    'required' => 'Command tidak boleh kosong',
                ),
            ),
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status' => false,
                'message' => validation_errors()
            ];
        } else {
            $file_name = $this->berkas(); // Upload dan ambil nama file

            if (!$file_name) {
                $response = [
                    'status' => false,
                    'message' => 'Gagal upload file'
                ];
            } else {
                $SQLinsert = [
                    'id_bot_commands'      => $this->id_chatbot_urut(),
                    'command'              => $this->input->post('command'),
                    'response_type'        => $this->input->post('response_type'),
                    'response_data'        => $file_name // Nama file yang di-upload
                ];

                if ($this->m_chatbot->add($SQLinsert)) {
                    $response = [
                        'status' => true,
                        'message' => 'Berhasil menambahkan data'
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gagal menambahkan data'
                    ];
                }
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }




     public function api_edit($id = '', $SQLupdate = '')
     {
      $token = $this->input->get_request_header('Authorization');
      $valid_token = $this->valid_token_api;
  
      if ($token !== 'Bearer ' . $valid_token) {
          $response = [
              'status' => false,
              'message' => 'Unauthorized access'
          ];
          return $this->output
                      ->set_content_type('application/json')
                      ->set_output(json_encode($response))
                      ->set_status_header(401);
      };
  
      $rules = array(
          array(
              'field' => 'command',
              'label' => 'Command',
              'rules' => 'required',
                'errors' => array(
                    'required' => 'Command tidak boleh kosong',
                ),
            ),
      );
  
      $this->form_validation->set_rules($rules);
  
      if ($this->form_validation->run() == FALSE) {
          $response = [
              'status' => false,
              'message' => validation_errors()
          ];
      } else {
          $SQLupdate = [
              'command'              => $this->input->post('command'),
              'response_type'        => $this->input->post('response_type'),
              'response_data'        => $this->input->post('response_data')
          ];
  
          if ($this->m_chatbot->update($id, $SQLupdate)) {
              $response = [
                  'status' => true,
                  'message' => 'Berhasil menambahkan data'
              ];
          } else {
              $response = [
                  'status' => false,
                  'message' => 'Gagal menambahkan data'
              ];
          }
      }
  
      $this->output
          ->set_content_type('application/json')
          ->set_output(json_encode($response));
  }     

    
      //API hapus data dari database dan folder
        public function api_delete($id='')
        {
            $token = $this->input->get_request_header('Authorization');
            $valid_token = $this->valid_token_api;
    
            if ($token !== 'Bearer ' . $valid_token) {
                $response = [
                    'status' => false,
                    'message' => 'Unauthorized access'
                ];
                return $this->output
                            ->set_content_type('application/json')
                            ->set_output(json_encode($response))
                            ->set_status_header(401);
            }
    
            if(empty($id)){
            $response = [
                'status' => false,
                'message' => 'Data kosong'
            ];
            } else {
                $data_chatbot = $this->m_chatbot->view_id($id)->row_array();
                $file_path = './themes/chatbot/' . $data_chatbot['response_data'];
    
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
    
                if ($this->m_chatbot->delete($id)) {
                    $response = [
                        'status' => true,
                        'message' => 'Berhasil menghapus data'
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gagal menghapus data'
                    ];
                }
            }
    
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        }
            

     
}
