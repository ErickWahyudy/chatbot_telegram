<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook extends CI_controller
{
    private $bot_token;
    private $chat_id_default;

    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('m_chatbot');

        // Simpan token dan chat_id sebagai variabel
        $this->bot_token = '1306451202:AAFL84nqcQjbAsEpRqVCziQ0VGty4qIAxt4'; // Ganti dengan token bot Telegram Anda
        $this->chat_id_default = '1136312864'; // Ganti dengan chat_id default Anda
    }

    // Fungsi utama untuk webhook chatbot
    public function index() {
        $update = file_get_contents("php://input");
        $update = json_decode($update, TRUE);

        if (isset($update["message"])) {
            $chat_id = $update["message"]["chat"]["id"];
            $message = $update["message"]["text"];

            // Ambil data perintah dari database
            $query = $this->db->get_where('tb_bot_commands', array('command' => $message));
            $command = $query->row();

            if ($command) {
                // Mengirim file atau pesan sesuai dengan response_type
                if (in_array($command->response_type, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'])) {
                    $this->sendFile($chat_id, $command->response_data); // Mengirim file
                } else {
                    $this->sendMessage($chat_id, "Tipe file tidak dikenali."); // Tipe tidak valid
                }
            } else {
                $this->sendUnknownCommand($chat_id); // Perintah tidak ditemukan
            }
        }
    }

    // Fungsi untuk mengirim file
    private function sendFile($chat_id, $file_name) {
        $file_path = FCPATH . 'themes/chatbot/' . $file_name; // Menggunakan FCPATH untuk path yang benar

        // Cek apakah file ada
        if (!file_exists($file_path)) {
            $this->sendMessage($chat_id, "File tidak ditemukan.");
            return;
        }

        // Ambil ekstensi file untuk menentukan jenis pengiriman
        $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        // Tentukan URL dan jenis pengiriman berdasarkan ekstensi
        if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $url = "https://api.telegram.org/bot" . $this->bot_token . "/sendPhoto";
            $post_fields = array(
                'chat_id' => $chat_id,
                'photo' => new CURLFile(realpath($file_path)) // Mengirim gambar
            );
        } else if (in_array($file_ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'])) {
            $url = "https://api.telegram.org/bot" . $this->bot_token . "/sendDocument";
            $post_fields = array(
                'chat_id' => $chat_id,
                'document' => new CURLFile(realpath($file_path)) // Mengirim dokumen
            );
        } else {
            $this->sendMessage($chat_id, "Format file tidak didukung.");
            return;
        }

        // Kirim ke Telegram
        $this->sendToTelegram($url, $post_fields);
    }

    // Fungsi untuk mengirim pesan ke pengguna
    private function sendMessage($chat_id, $message) {
        $url = "https://api.telegram.org/bot" . $this->bot_token . "/sendMessage";
        $post_fields = array(
            'chat_id' => $chat_id,
            'text' => $message
        );
        $this->sendToTelegram($url, $post_fields);
    }

    // Fungsi untuk mengirim pesan jika perintah tidak dikenal
    private function sendUnknownCommand($chat_id) {
        $url = "https://api.telegram.org/bot" . $this->bot_token . "/sendMessage";
        $post_fields = array(
            'chat_id' => $chat_id,
            'text' => "Perintah tidak dikenali. Silakan coba lagi."
        );
        $this->sendToTelegram($url, $post_fields);
    }

    // Fungsi umum untuk mengirim request ke Telegram API
    private function sendToTelegram($url, $post_fields) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $response = curl_exec($ch); // Ambil respons dari Telegram API
        curl_close($ch);

        // Debugging untuk memeriksa apakah pengiriman berhasil
        error_log($response);
    }
}

//https://api.telegram.org/bot<YOUR_TOKEN>/setWebhook?url=<YOUR_WEBHOOK_URL>
//https://api.telegram.org/bot1306451202:AAFL84nqcQjbAsEpRqVCziQ0VGty4qIAxt4/setWebhook?url=https://form.kassandra.my.id/superadmin/webhook


//https://api.telegram.org/bot<YOUR_TOKEN>/getWebhookInfo
//https://api.telegram.org/bot1306451202:AAFL84nqcQjbAsEpRqVCziQ0VGty4qIAxt4/getWebhookInfo
