<?php

function kirimWaStatusPengajuan($nomor, $nama, $status, $catatan = "") {
    $token = "nPEeq7dM6sKgscJsQ6aD"; // Ganti dengan API Key kamu

    if ($status == "Disetujui") {
        $pesan = "Halo $nama 👋\n\nPengajuan peminjaman peralatan Anda telah *Disetujui*. Silakan ambil peralatan sesuai jadwal yang telah ditentukan.\n\nTerima kasih 🙏";
    } elseif ($status == "Ditolak") {
        $pesan = "Halo $nama 👋\n\nMohon maaf, pengajuan peminjaman peralatan Anda *Ditolak*.\nSilakan hubungi admin untuk informasi lebih lanjut.";
    } else {
        $pesan = "Status pengajuan Anda: $status";
    }

    $data = [
        'target' => $nomor,
        'message' => $pesan,
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => [
            "Authorization: $token",
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

function kirimWaNotifikasiAdminPengajuan($nomor_admin, $nama, $tanggal_pinjam, $tanggal_kembali) {
    $token = "nPEeq7dM6sKgscJsQ6aD"; // API Key kamu

    $pesan = "📢 *Notifikasi Pengajuan*\n\nPengguna *$nama* telah mengajukan peminjaman peralatan.\n\n Tanggal Peminjaman: *$tanggal_pinjam*\n Tanggal Pengembalian: *$tanggal_kembali*\n\nSilakan cek dan proses pengajuan di sistem admin.";

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array(
            'target' => $nomor_admin,
            'message' => $pesan,
        ),
        CURLOPT_HTTPHEADER => array(
            "Authorization: $token"
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}





function kirimWaPengembalian($nomor, $nama) {
    $token = "nPEeq7dM6sKgscJsQ6aD";

    $pesan = "Halo $nama 👋\n\nTerima kasih telah mengembalikan peralatan ke Laboratorium. Kami telah menerima pengembalian Anda dan status peminjaman telah *Selesai* ✅.
    \n\nJika ada kendala atau pertanyaan, silakan hubungi admin.\n\nSalam,\nStaff Lab Seni dan Budaya 🙏";


    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array(
            'target' => $nomor,
            'message' => $pesan,
        ),
        CURLOPT_HTTPHEADER => array(
            "Authorization: $token"
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

// Reminder jatuh tempo
function kirimWaPengingatPengembalian($nomor, $nama, $nama_peralatan, $tanggal_kembali) {
    $token = "nPEeq7dM6sKgscJsQ6aD"; // Ganti dengan API Key kamu

    $pesan = "Halo $nama 👋\n\nPengingat dari Lab Seni dan Budaya 🎭\n\nPeralatan *$nama_peralatan* yang Anda pinjam seharusnya dikembalikan pada *$tanggal_kembali*.\n\nMohon segera lakukan pengembalian sesuai tanggal pengembalian.\n\nTerima kasih atas kerjasamanya. 🙏";

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array(
            'target' => $nomor,
            'message' => $pesan,
        ),
        CURLOPT_HTTPHEADER => array(
            "Authorization: $token"
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


?>
