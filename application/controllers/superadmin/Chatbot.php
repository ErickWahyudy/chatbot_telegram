<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Chatbot extends CI_controller
{
	function __construct()
	{
	 parent:: __construct();
     $this->load->helper('url');
      // needed ???
      $this->load->database();
      $this->load->library('session');
      $this->load->library('form_validation');
      
	 // error_reporting(0);
	 if($this->session->userdata('superadmin') != TRUE){
     redirect(base_url(''));
     exit;
	};
   $this->load->model('m_chatbot');
	}

    private $token_api = '123chatbot'; //ganti dengan token API yang valid

    //chatbot
    public function index($value='')
    {
        $api_url = base_url('superadmin/api/api_chatbot/api_get_view');
        $token = $this->token_api;
    
        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);
    
        $response = curl_exec($curl);
    
        curl_close($curl);
    
        $data_chatbot = json_decode($response, true);
    
        if (isset($data_chatbot['status']) && $data_chatbot['status'] === true) {
            $view = array(
                'judul' => 'Data chatbot',
                'aksi'  => 'chatbot',
                'data'  => $data_chatbot['data'],
            );
        } else {
            $view = array(
                'judul' => 'Data chatbot',
                'aksi'  => 'chatbot',
                'data'  => [],  
                'error_message' => $data_chatbot['message'] ?? 'Gagal mengambil data dari API'
            );
        }
    
        $this->load->view('superadmin/chatbot/lihat', $view);
    }

    
    // Controller: api_add
    public function api_add()
    {
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
            // Mengambil file dari form POST
            $files = $_FILES['foto'];
            $file_data = new CURLFile($files['tmp_name'], $files['type'], $files['name']);

            $postData = [
                'command'        => $this->input->post('command'),
                'response_type'  => $this->input->post('response_type'),
                'foto'           => $file_data // File foto
            ];

            $api_url = base_url('superadmin/api/api_chatbot/api_add');
            $token = $this->token_api;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData); // Tidak menggunakan http_build_query

            // Menggunakan multipart/form-data untuk upload file
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token, 
                'Content-Type: multipart/form-data'  
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            $data_response = json_decode($response, true);

            if (isset($data_response['status']) && $data_response['status'] === true) {
                $response = [
                    'status' => true,
                    'message' => 'Berhasil menambahkan data'
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => $data_response['message'] ?? 'Gagal menambahkan data'
                ];
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
}

     

      //API edit
      public function api_edit($id='', $SQLupdate='')
      {
        $rules = array(
          array(
            'field' => 'command',
            'label' => 'Command',
            'rules' => 'required',
            'errors' => array(
              'required' => 'Command tidak boleh kosong'
            )
            )

        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
          $response = [
            'status' => false,
            'message' => 'Tidak ada data'
          ];
        } else {
          $postData = [
            'command'       => $this->input->post('command'),
            'response_type' => $this->input->post('response_type'),
            'response_data' => $this->input->post('response_data')

            ];

          $api_url = base_url('superadmin/api/api_chatbot/api_edit/'.$id);
          $token = $this->token_api;
          
          $curl = curl_init();
     
          curl_setopt($curl, CURLOPT_URL, $api_url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData)); 
          curl_setopt($curl, CURLOPT_HTTPHEADER, [
              'Authorization: Bearer ' . $token, 
              'Content-Type: application/x-www-form-urlencoded' 
          ]);
  
          $response = curl_exec($curl);
  
          curl_close($curl);
  
          $data_response = json_decode($response, true);
  
          if (isset($data_response['status']) && $data_response['status'] === true) {
              $response = [
                  'status' => true,
                  'message' => 'Berhasil menambahkan data'
              ];
          } else {
              $response = [
                  'status' => false,
                  'message' => $data_response['message'] ?? 'Gagal menambahkan data'
              ];
          }
      }
  
      $this->output
          ->set_content_type('application/json')
          ->set_output(json_encode($response));
  }

      //API hapus
      public function api_hapus($id='')
      {
  
          if(empty($id)){
           $response = [
             'status' => false,
             'message' => 'Data kosong'
           ];
          } else {
              $api_url = base_url('superadmin/api/api_chatbot/api_delete/'.$id);
              $token = $this->token_api;
  
              $curl = curl_init();
  
              curl_setopt($curl, CURLOPT_URL, $api_url);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($curl, CURLOPT_HTTPHEADER, [
                  'Authorization: Bearer ' . $token  
              ]);
  
              $response = curl_exec($curl);

              curl_close($curl);
  
              $data_response = json_decode($response, true);
  
              if (isset($data_response['status']) && $data_response['status'] === true) {
                  $response = [
                      'status' => true,
                      'message' => 'Berhasil menghapus data'
                  ];
              } else {
                  $response = [
                      'status' => false,
                      'message' => $data_response['message'] ?? 'Gagal menghapus data'
                  ];
              }
          }
  
          $this->output
              ->set_content_type('application/json')
              ->set_output(json_encode($response));
      }
	
}