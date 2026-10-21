<?php

/*
 * --------------------------------------------------------------------
 * Guru Routes
 * --------------------------------------------------------------------
 */

$routes->group('guru', ['filter' => 'auth', 'args' => ['guru']], function ($routes) {
    $routes->get('/', 'Guru\DashboardController::index');

    // Profile routes
    $routes->get('profile/edit', 'Guru\ProfileController::edit');
    $routes->post('profile/update', 'Guru\ProfileController::update');
    $routes->post('profile/update-photo', 'Guru\ProfileController::updatePhoto');

    // Jadwal routes
    $routes->get('jadwal', 'Guru\JadwalController::index');

    // Hasil routes
    $routes->get('hasil', 'Guru\HasilController::index');
    $routes->get('hasil/(:num)', 'Guru\HasilController::detail/$1');
    $routes->get('hasil/cetak/(:num)', 'Guru\PdfController::cetak/$1');
    $routes->get('hasil/cetak-kurmer/(:num)', 'Guru\PdfController::cetakKurmer/$1');
    $routes->get('hasil/cetak-telaah/(:num)', 'Guru\PdfController::cetakTelaah/$1');
    $routes->get('hasil/cetak-telaah/(:num)/(:num)', 'Guru\PdfController::cetakTelaah/$1/$2');
    $routes->get('hasil/cetak-coaching/(:num)', 'Guru\PdfController::cetakCoaching/$1');


    // Dokumen Ajar routes
    $routes->get('dokumen-ajar', 'Guru\DokumenAjarController::list');
    $routes->get('dokumen-ajar/manage/(:num)', 'Guru\DokumenAjarController::index/$1');
    $routes->post('dokumen-ajar/save', 'Guru\DokumenAjarController::save');
    $routes->match(['get', 'post', 'delete'], 'dokumen-ajar/delete/(:num)', 'Guru\DokumenAjarController::delete/$1');

    // Formulir A (Pra-Observasi) routes
    $routes->get('pra-observasi/(:num)', 'Guru\PraObservasiController::index/$1');
    $routes->post('pra-observasi/save', 'Guru\PraObservasiController::save');

    // Formulir C & D (Tindak Lanjut & Refleksi) routes
    $routes->get('tindak-lanjut/(:num)', 'Guru\TindakLanjutController::index/$1');
    $routes->post('tindak-lanjut/save-c', 'Guru\TindakLanjutController::saveFormC');
    $routes->post('tindak-lanjut/save-d', 'Guru\TindakLanjutController::saveFormD');
});


