<?php
// Script to process background email queue
$scriptpath = dirname(__DIR__);

// LOAD FUNCTIONS
require($scriptpath . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'functions.php');

// AUTOLOAD CLASSES
spl_autoload_register('vendorClassAutoload');
spl_autoload_register('appClassAutoload');

require $scriptpath . '/vendor/autoload.php';

# LOAD CONFIGURAGION FILE
require($scriptpath . DIRECTORY_SEPARATOR . 'config.php');

# INITIALIZE MEDOO
$database = new medoo($config);

# DATE & TIME
date_default_timezone_set(getConfigValue("timezone"));

// Ambil maksimal 10 email yang masih Pending
$emails = $database->select("email_queue", "*", [
    "status" => "Pending",
    "LIMIT" => 10
]);

if ($emails) {
    foreach ($emails as $email) {
        // Tandai sebagai Processing (mencegah dikirim ganda oleh cron lain)
        $database->update("email_queue", ["status" => "Processing"], ["id" => $email['id']]);

        // Extract unserialized data
        $ccs = $email['ccs'] ? unserialize($email['ccs']) : array();
        $attachments = $email['attachments'] ? unserialize($email['attachments']) : array();

        // Eksekusi pengiriman sebenarnya menggunakan functions utama sendEmailNow
        $result = sendEmailNow(
            $email['to_address'],
            $email['subject'],
            $email['message'],
            $email['clientid'],
            $email['peopleid'],
            $ccs,
            $attachments
        );

        // Jika berhasil (1), hapus dari antrean agar bersih (karena sudah tercatat juga di emaillog asli)
        if ($result == 1) {
            $database->delete("email_queue", ["id" => $email['id']]);
        } else {
            // Jika gagal (0), tandai sebagai gagal agar tidak mengulang dan nyangkut terus
            $database->update("email_queue", ["status" => "Failed"], ["id" => $email['id']]);
        }
    }
}
?>