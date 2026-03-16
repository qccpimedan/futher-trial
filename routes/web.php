<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChillroomController;
use App\Http\Controllers\SeasoningController;
use App\Http\Controllers\ShoestringController;
use App\Http\Controllers\ReboxController;
use App\Http\Controllers\PenyimpananBahanController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\JenisProdukController;
use App\Http\Controllers\NomorFormulaController;
use App\Http\Controllers\BahanEmulsiController;
use App\Http\Controllers\BahanFormingController;
use App\Http\Controllers\PersiapanBahanFormingController;
use App\Http\Controllers\PersiapanBahanNonFormingController;
use App\Http\Controllers\TotalPemakaianEmulsiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PembuatanEmulsiController;
use App\Http\Controllers\JenisEmulsiController;
use App\Http\Controllers\NomorEmulsiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\JenisBetterController;
use App\Http\Controllers\PersiapanBahanBetterController;
use App\Http\Controllers\StdSalinitasViskositasController;
use App\Http\Controllers\SuhuAdonanController;
use App\Http\Controllers\DataShiftController;
use App\Http\Controllers\PenggorenganController;
use App\Http\Controllers\JenisBreaderController;
use App\Http\Controllers\ProsesBreaderController;
use App\Http\Controllers\SuhuFrayer1Controller;
use App\Http\Controllers\Frayer3Controller;
use App\Http\Controllers\Frayer4Controller;
use App\Http\Controllers\Frayer5Controller;
use App\Http\Controllers\WaktuPenggorenganController;
use App\Http\Controllers\JenisMarinadeController;
use App\Http\Controllers\InputRoastingController;
use App\Http\Controllers\BahanBakuRoastingController;
use App\Http\Controllers\SuhuBlokController;
use App\Http\Controllers\StdFanController;
use App\Http\Controllers\StdSuhuPusatController;
use App\Http\Controllers\QcSistem\VerifCipController;
use App\Http\Controllers\BahanBakuTumblingController;
use App\Http\Controllers\PengemasanKartonController;
use App\Http\Controllers\ProsesBatteringController;
use App\Http\Controllers\DataGramasiController;
use App\Http\Controllers\DataBagController;
use App\Http\Controllers\DataBoxController;
use App\Http\Controllers\ProsesFrayerController;
use App\Http\Controllers\Frayer2Controller;
use App\Http\Controllers\HasilPenggorenganController;
use App\Http\Controllers\HasilProsesRoastingController;
use App\Http\Controllers\DataTumblingController;
use App\Http\Controllers\ProsesTumblingController;
use App\Http\Controllers\ProsesMarinadeController;
use App\Http\Controllers\ProsesRoastingFanController;
use App\Http\Controllers\BeratProdukController;
use App\Http\Controllers\PengemasanProdukController;
use App\Http\Controllers\PembuatanSampleController;
use App\Http\Controllers\MetalDetectorController;
use App\Http\Controllers\DokumentasiController;
use App\Http\Controllers\PengemasanPlastikController;
use App\Http\Controllers\KetidaksesuaianPlastikController;
use App\Http\Controllers\KetidaksesuaianBendaAsingController;
use App\Http\Controllers\InputAreaController;
use App\Http\Controllers\AreaProsesController;
use App\Http\Controllers\KontrolSanitasiController;
use App\Http\Controllers\BarangMudahPecahController;
use App\Http\Controllers\PemeriksaanBendaAsingController;
use App\Http\Controllers\GmpKaryawanController;
use App\Http\Controllers\PemeriksaanBahanKemasController;
use App\Http\Controllers\PemeriksaanProsesProduksiController;
use App\Http\Controllers\TimbanganController;
use App\Http\Controllers\ThermometerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProdukFormingController;
use App\Http\Controllers\ProdukNonFormingController;
use App\Http\Controllers\ProdukYumController;
use App\Http\Controllers\JenisPredustController;
use App\Http\Controllers\PembuatanPredustController;
use App\Http\Controllers\SuhuFrayer2Controller;
use App\Http\Controllers\WaktuPenggorengan2Controller;
use App\Http\Controllers\NamaFormulaFlaController;
use App\Http\Controllers\NomorStepFormulaFlaController;
use App\Http\Controllers\BahanFormulaFlaController;
use App\Http\Controllers\PemeriksaanProdukCookingMixerFlaController;    
use App\Http\Controllers\PemasakanNasiController;    
use App\Http\Controllers\PembekuanIqfPenggorenganController;
use App\Http\Controllers\PembekuanIqfRoastingController;
use App\Http\Controllers\PemeriksaanRheonMachineController;
use App\Http\Controllers\PemeriksaanRiceBitesController;
use App\Http\Controllers\VerifikasiBeratProdukController;
use App\Http\Controllers\DataBarangController;
use App\Http\Controllers\StdSuhuPusatRoastingController;
use App\Http\Controllers\DataTimbanganController;
use App\Http\Controllers\DataThermoController;
use App\Http\Controllers\DataRmController;
use App\Http\Controllers\DataSeasoningController;
use App\Http\Controllers\DataDefectController;
use App\Http\Controllers\BahanNonFormingController;
use App\Http\Controllers\ProsesAgingController;
use App\Http\Controllers\InputMesinPeralatanController;
use App\Http\Controllers\StdBeratRheonController;
use App\Http\Controllers\VerifPeralatanController;
use App\Http\Controllers\ProsesTwahingController;
use App\Models\SuhuFrayer1;
use App\Models\JenisBetter;
use Illuminate\Support\Facades\Auth;

// Halaman utama
// Route root redirect ke login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Lindungi semua route penting dengan auth
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('super-admin.dashboard');
    });
    
        // Untuk Import Excel
        Route::get('produk/download-template', [JenisProdukController::class, 'downloadTemplate'])->name('produk.download-template');
        Route::post('produk/import-excel', [JenisProdukController::class, 'importExcel'])->name('produk.import-excel');
        Route::get('bahan-forming/download-template', [BahanFormingController::class, 'downloadTemplate'])->name('bahan-forming.download-template');
        Route::post('bahan-forming/import-excel', [BahanFormingController::class, 'importExcel'])->name('bahan-forming.import-excel');
        Route::get('pembuatan-sample/download-template', [PembuatanSampleController::class, 'downloadTemplate'])->name('pembuatan-sample.download-template');
        Route::post('pembuatan-sample/import-excel', [PembuatanSampleController::class, 'importExcel'])->name('pembuatan-sample.import-excel');
        Route::get('seasoning/download-template', [SeasoningController::class, 'downloadTemplate'])->name('seasoning.download-template');
        Route::post('seasoning/import-excel', [SeasoningController::class, 'importExcel'])->name('seasoning.import-excel');
        Route::get('shoestring/download-template', [ShoestringController::class, 'downloadTemplate'])->name('shoestring.download-template');
        Route::post('shoestring/import-excel', [ShoestringController::class, 'importExcel'])->name('shoestring.import-excel');
        Route::get('rebox/download-template', [ReboxController::class, 'downloadTemplate'])->name('rebox.download-template');
        Route::post('rebox/import-excel', [ReboxController::class, 'importExcel'])->name('rebox.import-excel');
        Route::get('pemeriksaan-bahan-kemas/download-template', [PemeriksaanBahanKemasController::class, 'downloadTemplate'])->name('pemeriksaan-bahan-kemas.download-template');
        Route::post('pemeriksaan-bahan-kemas/import-excel', [PemeriksaanBahanKemasController::class, 'importExcel'])->name('pemeriksaan-bahan-kemas.import-excel');
        Route::get('pemeriksaan-proses-produksi/download-template', [PemeriksaanProsesProduksiController::class, 'downloadTemplate'])->name('pemeriksaan-proses-produksi.download-template');
        Route::post('pemeriksaan-proses-produksi/import-excel', [PemeriksaanProsesProduksiController::class, 'importExcel'])->name('pemeriksaan-proses-produksi.import-excel');
        Route::get('gmp-karyawan/download-template', [GmpKaryawanController::class, 'downloadTemplate'])->name('gmp-karyawan.download-template');
        Route::post('gmp-karyawan/import-excel', [GmpKaryawanController::class, 'importExcel'])->name('gmp-karyawan.import-excel');
        Route::get('thermometer/download-template', [ThermometerController::class, 'downloadTemplate'])->name('thermometer.download-template');
        Route::post('thermometer/import-excel', [ThermometerController::class, 'importExcel'])->name('thermometer.import-excel');
        Route::get('timbangan/download-template', [TimbanganController::class, 'downloadTemplate'])->name('timbangan.download-template');
        Route::post('timbangan/import-excel', [TimbanganController::class, 'importExcel'])->name('timbangan.import-excel');
        Route::get('kontrol-sanitasi/download-template', [KontrolSanitasiController::class, 'downloadTemplate'])->name('kontrol-sanitasi.download-template');
        Route::post('kontrol-sanitasi/import-excel', [KontrolSanitasiController::class, 'importExcel'])->name('kontrol-sanitasi.import-excel');
        Route::get('bahan-non-forming/download-template', [BahanNonFormingController::class, 'downloadTemplate'])->name('bahan-non-forming.download-template');
        Route::post('bahan-non-forming/import-excel', [BahanNonFormingController::class, 'importExcel'])->name('bahan-non-forming.import-excel');
        // End

        //export pdf bahan kemas
          Route::post('pemeriksaan-bahan-kemas/bulk-export-pdf', [PemeriksaanBahanKemasController::class, 'bulkExportPdf'])
            ->name('pemeriksaan-bahan-kemas.bulk-export-pdf');
        // untuk Eksport PDF EXCEL Chillroom
        Route::post('/chillroom-export-pdf', [ChillroomController::class, 'exportPdf'])
            ->name('chillroom.export-pdf');
        Route::post('chillroom/save-kode', [ChillroomController::class, 'saveKode'])
            ->name('chillroom.save-kode');
        Route::post('chillroom/bulk-export-pdf', [ChillroomController::class, 'bulkExportPdf'])
            ->name('chillroom.bulk-export-pdf');
        // untuk Eksport PDF EXCEL Seasoning
        Route::post('/seasoning-export-pdf', [SeasoningController::class, 'exportPdf'])
            ->name('seasoning.export-pdf');
        Route::post('seasoning/save-kode', [SeasoningController::class, 'saveKode'])
            ->name('seasoning.save-kode');
        Route::post('seasoning/bulk-export-pdf', [SeasoningController::class, 'bulkExportPdf'])
            ->name('seasoning.bulk-export-pdf');
        // untuk Eksport PDF EXCEL Shoestring
        Route::post('/shoestring-export-pdf', [ShoestringController::class, 'exportPdf'])
            ->name('shoestring.export-pdf');
        Route::post('shoestring/save-kode', [ShoestringController::class, 'saveKode'])
            ->name('shoestring.save-kode');
        Route::post('shoestring/bulk-export-pdf', [ShoestringController::class, 'bulkExportPdf'])
            ->name('shoestring.bulk-export-pdf');
        // untuk Eksport PDF EXCEL Rebox
        Route::post('/rebox-export-pdf', [ReboxController::class, 'exportPdf'])
            ->name('rebox.export-pdf');
        Route::post('rebox/save-kode', [ReboxController::class, 'saveKode'])
            ->name('rebox.save-kode');
        Route::post('rebox/bulk-export-pdf', [ReboxController::class, 'bulkExportPdf'])
            ->name('rebox.bulk-export-pdf');
        // untuk Eksport PDF Bahan-Baku-Tumbling, Marinade dan Parameter Tumbling
        Route::post('/proses-tumbling-export-pdf', [ProsesTumblingController::class, 'exportPdf'])
            ->name('proses-tumbling.export-pdf');
        Route::post('proses-tumbling/save-kode', [ProsesTumblingController::class, 'saveKode'])
            ->name('proses-tumbling.save-kode');
        Route::post('proses-tumbling/bulk-export-pdf', [ProsesTumblingController::class, 'bulkExportPdf'])
            ->name('proses-tumbling.bulk-export-pdf');
         // Untuk Export PDF Pengemasan Produk
        // untuk Eksport PDF Bahan-Baku-Tumbling, Marinade dan Parameter Tumbling
        Route::post('/dokumentasi-export-pdf', [DokumentasiController::class, 'exportPdf'])
            ->name('dokumentasi.export-pdf');
        Route::post('dokumentasi/save-kode', [DokumentasiController::class, 'saveKode'])
            ->name('dokumentasi.save-kode');
        Route::post('dokumentasi/bulk-export-pdf', [DokumentasiController::class, 'bulkExportPdf'])
            ->name('dokumentasi.bulk-export-pdf');

        //Untuk Export Pdf Pemeriksaan Produk Cooking Mixer Fla
        Route::post('/pemeriksaan-produk-cooking-mixer-fla-export-pdf', [PemeriksaanProdukCookingMixerFlaController::class, 'exportPdf'])
            ->name('pemeriksaan-produk-cooking-mixer-fla.export-pdf');
        Route::post('pemeriksaan-produk-cooking-mixer-fla/save-kode', [PemeriksaanProdukCookingMixerFlaController::class, 'saveKode'])
            ->name('pemeriksaan-produk-cooking-mixer-fla.save-kode');
        Route::post('pemeriksaan-produk-cooking-mixer-fla/bulk-export-pdf', [PemeriksaanProdukCookingMixerFlaController::class, 'bulkExportPdf'])
            ->name('pemeriksaan-produk-cooking-mixer-fla.bulk-export-pdf');

        //Untuk Export pdf metal detector
        Route::post('input-metal-detector/bulk-export-pdf', [MetalDetectorController::class, 'bulkExportPdf'])
        ->name('input-metal-detector.bulk-export-pdf');

        // Untuk Export PDF EXCEL Persiapan bahan forming
        Route::get('persiapan-bahan-forming-export-pdf/{uuid?}', [PersiapanBahanFormingController::class, 'exportPdf'])->name('persiapan-bahan-forming.export-pdf');
        Route::get('persiapan-bahan-forming-export-excel/{uuid?}', [PersiapanBahanFormingController::class, 'exportExcel'])->name('persiapan-bahan-forming.export-excel');
        Route::post('persiapan-bahan-forming/save-kode', [PersiapanBahanFormingController::class, 'saveKode'])->name('persiapan-bahan-forming.save-kode');
        Route::post('persiapan-bahan-forming/bulk-export-pdf', [PersiapanBahanFormingController::class, 'bulkExportPdf'])->name('persiapan-bahan-forming.bulk-export-pdf');

        // Untuk Export PDF Persiapan bahan non-forming
        Route::get('persiapan-bahan-non-forming-export-pdf/{uuid}', [PersiapanBahanNonFormingController::class, 'exportPdf'])->name('persiapan-bahan-non-forming.export-pdf');
        // untuk Eksport PDF EXCEL Persiapan Bahan emulsi
        Route::post('/persiapan-bahan-emulsi-export-pdf', [PembuatanEmulsiController::class, 'exportPdf'])
            ->name('persiapan-bahan-emulsi.export-pdf');
        Route::post('persiapan-bahan-emulsi/save-kode', [PembuatanEmulsiController::class, 'saveKode'])
            ->name('persiapan-bahan-emulsi.save-kode');
        Route::post('persiapan-bahan-emulsi/bulk-export-pdf', [PembuatanEmulsiController::class, 'bulkExportPdf'])
            ->name('persiapan-bahan-emulsi.bulk-export-pdf');
        // untuk Eksport PDF EXCEL Persiapan Bahan better
        Route::post('/persiapan-bahan-better-export-pdf', [PersiapanBahanBetterController::class, 'exportPdf'])
            ->name('persiapan-bahan-better.export-pdf');
        Route::post('persiapan-bahan-better/save-kode', [PersiapanBahanBetterController::class, 'saveKode'])
            ->name('persiapan-bahan-better.save-kode');
        Route::post('persiapan-bahan-better/bulk-export-pdf', [PersiapanBahanBetterController::class, 'bulkExportPdf'])
            ->name('persiapan-bahan-better.bulk-export-pdf');
        // untuk Eksport PDF EXCEL Ketidaksesuaian Plastik
        Route::post('/ketidaksesuaian-plastik-export-pdf', [KetidaksesuaianPlastikController::class, 'exportPdf'])
            ->name('ketidaksesuaian-plastik.export-pdf');
        Route::post('ketidaksesuaian-plastik/save-kode', [KetidaksesuaianPlastikController::class, 'saveKode'])
            ->name('ketidaksesuaian-plastik.save-kode');
        Route::post('ketidaksesuaian-plastik/bulk-export-pdf', [KetidaksesuaianPlastikController::class, 'bulkExportPdf'])
            ->name('ketidaksesuaian-plastik.bulk-export-pdf');
        // untuk Eksport PDF EXCEL Ketidaksesuaian Benda Asing
        Route::post('/ketidaksesuaian-benda-asing-export-pdf', [KetidaksesuaianBendaAsingController::class, 'exportPdf'])
            ->name('ketidaksesuaian-benda-asing.export-pdf');
        Route::post('ketidaksesuaian-benda-asing/save-kode', [KetidaksesuaianBendaAsingController::class, 'saveKode'])
            ->name('ketidaksesuaian-benda-asing.save-kode');
        Route::post('ketidaksesuaian-benda-asing/bulk-export-pdf', [KetidaksesuaianBendaAsingController::class, 'bulkExportPdf'])
            ->name('ketidaksesuaian-benda-asing.bulk-export-pdf');
        // Untuk eksport pdf excel Pembekuan Iqf Penggorengan dan Pembekuan Iqf Roasting
        Route::post('pembekuan-iqf-penggorengan/bulk-export-pdf', [PembekuanIqfPenggorenganController::class, 'bulkExportPdf'])->name('pembekuan-iqf-penggorengan.bulk-export-pdf');
      
        Route::post('pembekuan-iqf-roasting/bulk-export-pdf', [PembekuanIqfRoastingController::class, 'bulkExportPdf'])->name('pembekuan-iqf-roasting.bulk-export-pdf');
        // Untuk Export PDF Pembuatan Sample
        Route::post('pembuatan-sample/bulk-export-pdf', [PembuatanSampleController::class, 'bulkExportPdf'])
            ->name('pembuatan-sample.bulk-export-pdf');
        // Untuk Export PDF Verifikasi Berat Produk
        Route::post('verifikasi-berat-produk/bulk-export-pdf', [VerifikasiBeratProdukController::class, 'bulkExportPdf'])
    ->name('verifikasi-berat-produk.bulk-export-pdf');
        //Untuk Export PDF Pergantian Produksi forming
        Route::post('produk-forming/bulk-export-pdf', [ProdukFormingController::class, 'bulkExportPdf'])->name('produk-forming.bulk-export-pdf');
        //Untuk Export PDF Pergantian Produksi non forming
        Route::post('produk-non-forming/bulk-export-pdf', [ProdukNonFormingController::class, 'bulkExportPdf'])->name('produk-non-forming.bulk-export-pdf');
        //Untuk Export PDf Produk Yum
        Route::post('produk-yum/bulk-export-pdf', [ProdukYumController::class, 'bulkExportPdf'])->name('produk-yum.bulk-export-pdf');
        // Untuk Eksport PDF Pemeriksaan Benda Asing
        Route::post('/pemeriksaan-benda-asing/bulk-export-pdf', [PemeriksaanBendaAsingController::class, 'bulkExportPdf'])->name('pemeriksaan-benda-asing.bulk-export-pdf');
        //Untuk Export PDF Pemeriksaan Proses Produksi
        Route::post('/pemeriksaan-proses-produksi/bulk-export-pdf', [PemeriksaanProsesProduksiController::class, 'bulkExportPdf'])->name('pemeriksaan-proses-produksi.bulk-export-pdf');
        // Untuk Export Pdf Timbangan      
        Route::post('/timbangan/bulk-export-pdf', [TimbanganController::class, 'bulkExportPdf'])->name('timbangan.bulk-export-pdf');
        //Untuk Export Pdf Thermometer
            Route::post('/thermometer/bulk-export-pdf', [ThermometerController::class, 'bulkExportPdf'])->name('thermometer.bulk-export-pdf');
        //Untuk Eksport PDF GMP-karyawan 
        Route::post('gmp-karyawan/bulk-export-pdf', [GmpKaryawanController::class, 'bulkExportPdf'])->name('gmp-karyawan.bulk-export-pdf');

        //Untuk Export PDF Proses Thawing
        Route::post('proses-twahing/bulk-export-pdf', [ProsesTwahingController::class, 'bulkExportPdf'])->name('proses-twahing.bulk-export-pdf');

        //Untuk Export PDF Verifikasi Peralatan
        Route::post('verif-peralatan/bulk-export-pdf', [VerifPeralatanController::class, 'bulkExportPdf'])->name('verif-peralatan.bulk-export-pdf');

        //Untuk Export PDF Verif CIP
        Route::post('verif-cip/bulk-export-pdf', [VerifCipController::class, 'bulkExportPdf'])->name('verif-cip.bulk-export-pdf');
        // Untuk Eksport PDF Kontrol Sanitasi
        Route::post('kontrol-sanitasi/bulk-export-pdf', [KontrolSanitasiController::class, 'bulkExportPdf'])->name('kontrol-sanitasi.bulk-export-pdf');
        // Untuk Eksoprt PDF Barang Mudah Pecah
        Route::post('barang-mudah-pecah/bulk-export-pdf', [BarangMudahPecahController::class, 'bulkExportPdf'])->name('barang-mudah-pecah.bulk-export-pdf');
        //Untuk export pdf pemeriksaan Rheon Machine
        Route::post('/pemeriksaan-rheon-machine/bulk-export-pdf', [PemeriksaanRheonMachineController::class, 'bulkExportPdf'])->name('pemeriksaan-rheon-machine.bulk-export-pdf');
        //Untuk export pdf pemeriksaan Rice Bites
        Route::post('/pemeriksaan-rice-bites/bulk-export-pdf', [PemeriksaanRiceBitesController::class, 'bulkExportPdf'])->name('pemeriksaan-rice-bites.bulk-export-pdf');
        //PDF Export Area Proses
        Route::post('/area-proses/bulk-export-pdf', [AreaProsesController::class, 'bulkExportPdf'])->name('area-proses.bulk-export-pdf');
        Route::post('/pemasakan-nasi/{uuid}/approve', [PemasakanNasiController::class, 'approve'])->name('pemasakan-nasi.approve');
        Route::post('/pemasakan-nasi/bulk-export-pdf', [PemasakanNasiController::class, 'bulkExportPdf'])->name('pemasakan-nasi.bulk-export-pdf');
        // PDF Exprot Aging
        Route::post('/proses-aging/export-pdf', [ProsesAgingController::class, 'exportPdf'])->name('proses-aging.export-pdf');
        Route::post('/proses-aging/approve', [ProsesAgingController::class, 'approve'])->name('proses-aging.approve');
        
        // Route Profile
        Route::get('/profile/edit/{uuid}', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/update/{uuid}', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/super-admin/dashboard', function () {
            return view('super-admin.dashboard');
        });
        Route::get('/super-admin/profile', [UserController::class, 'index'])->name('users.profile');

        // Access Control Routes
        Route::get('/access-control', [\App\Http\Controllers\AccessControlController::class, 'index'])->name('access-control.index');
        Route::put('/access-control/update', [\App\Http\Controllers\AccessControlController::class, 'update'])->name('access-control.update');

    // =========================
    // QC-SISTEM ROUTES
    // =========================
    Route::prefix('qc-sistem')->middleware('checkQcPermission')->group(function () {
        // =========================
        // AJAX ROUTES (PASTIKAN DI ATAS RESOURCE)
        // =========================
        Route::get('/get-emulsi-by-produk/{id_produk}', [PembuatanEmulsiController::class, 'getEmulsiByProduk'])->name('get-emulsi-by-produk');
        Route::get('/get-total-pemakaian-by-emulsi-produk/{id_produk}/{nama_emulsi_id}', [PembuatanEmulsiController::class, 'getTotalPemakaianByEmulsiProduk'])->name('get-total-pemakaian-by-emulsi-produk');
        Route::get('/get-nomor-emulsi-by-total-pemakaian/{total_pemakaian_id}', [PembuatanEmulsiController::class, 'getNomorEmulsiByTotalPemakaian'])->name('get-nomor-emulsi-by-total-pemakaian');
        Route::get('/get-bahan-emulsi-by-nomor-emulsi/{nomor_emulsi_id}', [PembuatanEmulsiController::class, 'getBahanEmulsiByNomorEmulsi'])->name('get-bahan-emulsi-by-nomor-emulsi');
        Route::get('/ajax/get-total-pemakaian', [PembuatanEmulsiController::class, 'getTotalPemakaian'])->name('ajax.get-total-pemakaian');
        Route::get('/ajax/get-nomor-emulsi', [PembuatanEmulsiController::class, 'getNomorEmulsi'])->name('ajax.get-nomor-emulsi');
        // AJAX routes untuk filter dropdown Nomor Emulsi (by id_plan & id_produk)
        Route::get('/ajax/emulsi-by-plan-produk', function (\Illuminate\Http\Request $r) {
            $query = \App\Models\JenisEmulsi::query();
            if ($r->id_plan)   $query->where('id_plan', $r->id_plan);
            if ($r->id_produk) $query->where('id_produk', $r->id_produk);
            return $query->orderBy('nama_emulsi')->get(['id', 'nama_emulsi']);
        })->name('ajax.emulsi-by-plan-produk');
        Route::get('/ajax/total-pemakaian', function (\Illuminate\Http\Request $r) {
            $query = \App\Models\TotalPemakaianEmulsi::query();
            if ($r->id_plan)        $query->where('id_plan', $r->id_plan);
            if ($r->id_produk)      $query->where('id_produk', $r->id_produk);
            if ($r->nama_emulsi_id) $query->where('nama_emulsi_id', $r->nama_emulsi_id);
            return $query->orderBy('total_pemakaian')->get(['id', 'total_pemakaian']);
        })->name('ajax.total-pemakaian');
        Route::get('/ajax/get-total-pemakaian-by-produk/{id_produk}', [PembuatanEmulsiController::class, 'getTotalPemakaianByProduk']);
        Route::get('/ajax/bahan-forming-by-formula/{id_formula}', [PersiapanBahanFormingController::class, 'getBahanFormingByFormula']);
        Route::get('/ajax/nomor-formula-options', [PersiapanBahanFormingController::class, 'getNomorFormulaOptions']);
        Route::get('/ajax/nomor-formula-non-forming-by-produk/{id_produk}', [PersiapanBahanNonFormingController::class, 'getFormulaByProduk']);
        Route::get('/ajax/bahan-non-forming-by-formula/{id_no_formula_non_forming}', [PersiapanBahanNonFormingController::class, 'getBahanByFormula']);
        Route::get('/persiapan-bahan/redirect-by-produk/{id_produk}', [PersiapanBahanNonFormingController::class, 'redirectByProduk']);
        Route::get('/ajax/nomor-formula-by-produk/{id_produk}', function($id_produk) {
            return \App\Models\NomorFormula::where('id_produk', $id_produk)->get(['id', 'nomor_formula']);
        });

        Route::get('/ajax/area-by-plan/{plan_id}', function($plan_id) {
            $user = auth()->user();
            $effectivePlanId = ($user->role === 'superadmin') ? $plan_id : $user->id_plan;

            return \App\Models\InputArea::where('id_plan', $effectivePlanId)
                ->orderBy('area')
                ->get(['id', 'area']);
        })->name('qc.ajax.area-by-plan');

        Route::get('/ajax/data-barang-by-area/{area_id}', function($area_id) {
            $user = auth()->user();
            $area = \App\Models\InputArea::findOrFail($area_id);

            if ($user->role !== 'superadmin' && (int) $area->id_plan !== (int) $user->id_plan) {
                abort(403);
            }

            return \App\Models\DataBarang::where('id_plan', $area->id_plan)
                ->where('id_area', $area->id)
                ->orderBy('nama_barang')
                ->get(['id', 'nama_barang', 'jumlah']);
        })->name('qc.ajax.data-barang-by-area');

        Route::get('/ajax/produk-by-plan/{plan_id}', function($plan_id) {
            $user = auth()->user();
            $effectivePlanId = ($user->role === 'superadmin') ? $plan_id : $user->id_plan;

            $query = \App\Models\JenisProduk::where('id_plan', $effectivePlanId);
            if ($statusBahan = request('status_bahan')) {
                $query->where('status_bahan', $statusBahan);
            }

            return $query
                ->orderBy('nama_produk')
                ->get(['id', 'nama_produk']);
        });
        Route::get('/ajax/get-bahan-emulsi-by-nomor-emulsi/{nomor_emulsi_id}', [PembuatanEmulsiController::class, 'getBahanEmulsiByNomorEmulsi']);
        Route::get('/ajax/better-by-produk-better', function (\Illuminate\Http\Request $r) {
            $user = auth()->user();
            $query = \App\Models\JenisBetter::query();
            if ($r->id_produk) $query->where('id_produk', $r->id_produk);
            if ($user->role !== 'superadmin') $query->where('id_plan', $user->id_plan);
            return $query->get(['id', 'nama_better']);
        })->name('ajax.better-by-produk-better');

        Route::get('/ajax/better-detail-better', function (\Illuminate\Http\Request $r) {
            $user = auth()->user();
            $query = \App\Models\JenisBetter::query()->where('id', $r->id_better);
            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }
            return $query->firstOrFail(['id', 'nama_better', 'berat', 'nama_formula_better', 'better_items']);
        })->name('ajax.better-detail-better');
        
        Route::get('/ajax/std-by-better-better', function (\Illuminate\Http\Request $r) {
            return \App\Models\StdSalinitasViskositas::where('id_better', $r->id_better)->get();
        })->name('ajax.std-by-better-better');

        Route::get('/ajax/jenis-breader-by-produk/{id_produk}', [ProsesBreaderController::class, 'getJenisBreaderByProduk']);
        // Route::get('/get-suhu-frayer-1-by-produk/{id_produk}', [ProsesFrayerController::class, 'getSuhuFrayerByProduk'])->name('getSuhuFrayerByProduk');
        // Route::get('/get-waktu-penggorengan-by-suhu/{id_suhu}', [ProsesFrayerController::class, 'getWaktuPenggorenganBySuhu'])->name('getWaktuPenggorenganBySuhu');
        Route::get('/ajax/get-suhu-frayer-by-produk/{id_produk}', [ProsesFrayerController::class, 'getSuhuFrayerByProduk'])->name('get-suhu-frayer-by-produk');
        Route::get('/ajax/get-waktu-penggorengan-by-suhu/{id_suhu}', [ProsesFrayerController::class, 'getWaktuPenggorenganBySuhu'])->name('get-waktu-penggorengan-by-suhu');        
        Route::get('/ajax/get-suhu-frayer-2-by-produk/{id_produk}', [WaktuPenggorengan2Controller::class, 'getSuhuFrayer2ByProduk'])->name('get-suhu-frayer-2-by-produk');
        Route::get('/ajax/get-waktu-penggorengan-2-by-suhu/{id_suhu}', [WaktuPenggorengan2Controller::class, 'getWaktuPenggorengan2BySuhu'])->name('get-waktu-penggorengan-2-by-suhu');
        Route::get('/get-std-suhu-pusat-by-produk/{id_produk}', [HasilPenggorenganController::class, 'getStdSuhuPusatByProduk'])->name('get-std-suhu-pusat-by-produk');
        Route::get('/get-std-suhu-pusat-roasting/{id_produk}/{id_plan?}', [HasilProsesRoastingController::class, 'getStdSuhuPusatByProduk'])->name('get-std-suhu-pusat-roasting');
        Route::get('/get-tumbling-by-product/{productId}', [ProsesTumblingController::class, 'getTumblingByProduct'])->name('get-tumbling-by-product');
        Route::get('/ajax/get-suhu-blok-by-produk/{produk_id}', function($produk_id) {
            $plan_id = request('plan_id');
            return \App\Models\SuhuBlok::where('id_produk', $produk_id)
                ->when($plan_id, function($q) use ($plan_id) {
                    $q->where('id_plan', $plan_id);
                })
                ->get(['id', 'suhu_blok']);
        });

        Route::get('/get-jenis-marinade-by-produk', [ProsesMarinadeController::class, 'getJenisMarinadeByProduk'])->name('get-jenis-marinade-by-produk');
        Route::get('/get-product-details', [ProsesRoastingFanController::class, 'getProductDetails'])->name('get-product-details');
        Route::get('/get-data-bag-by-produk/{id}', [BeratProdukController::class, 'getDataBagByProduk'])
        ->name('get-data-bag-by-produk');
        Route::get('/get-data-box-by-produk/{id}', [BeratProdukController::class, 'getDataBoxByProduk'])
        ->name('get-data-box-by-produk');

        Route::get('/ajax/suhu-adonan-by-produk/{produk_id}', function($produk_id) {
            $query = \App\Models\SuhuAdonan::query()->where('id_produk', $produk_id);
            // Filter plan hanya jika diminta (plan_id dikirim), agar halaman yang tidak memiliki plan tetap dapat data
            if ($planId = request('plan_id')) {
                $query->where('id_plan', $planId);
            }
            // Dedup jika ada STD sama untuk produk/plan berbeda; ambil id terkecil per std_suhu
            return $query
                ->selectRaw('MIN(id) as id, std_suhu')
                ->groupBy('std_suhu')
                ->orderBy('std_suhu')
                ->get();
        });
        Route::get('/get-suhu-blok-by-produk/{id}', [ProsesRoastingFanController::class, 'getSuhuBlokByProduct'])->name('get-suhu-blok-by-produk');
        Route::get('/get-fan-by-suhu/{id}', [ProsesRoastingFanController::class, 'getFanBySuhu'])->name('get-fan-by-suhu');
        Route::get('/get-formula-by-produk/{id_produk}', [PersiapanBahanFormingController::class, 'getFormulaByProduk'])->name('get-formula-by-produk');
        Route::get('/get-suhu-adonan-by-produk/{id_produk}', [PersiapanBahanFormingController::class, 'getSuhuAdonanByProduk'])->name('get-suhu-adonan-by-produk');
        // Frayer 3 routes
        Route::get('/ajax/get-suhu-frayer-3-by-produk/{id_produk}', [Frayer3Controller::class, 'getSuhuFrayerByProduk']);
        Route::get('/ajax/get-waktu-penggorengan-3-by-suhu/{id_suhu}', [Frayer3Controller::class, 'getWaktuPenggorenganBySuhu']);
        // Frayer 4 routes
        Route::get('/ajax/get-suhu-frayer-4-by-produk/{id_produk}', [Frayer4Controller::class, 'getSuhuFrayerByProduk'])->name('get-suhu-frayer-4-by-produk');
        Route::get('/ajax/get-waktu-penggorengan-4-by-suhu/{id_suhu}', [Frayer4Controller::class, 'getWaktuPenggorenganBySuhu'])->name('get-waktu-penggorengan-4-by-suhu');
        // Frayer 5 routes
        Route::get('/ajax/get-suhu-frayer-5-by-produk/{id_produk}', [Frayer5Controller::class, 'getSuhuFrayerByProduk'])->name('get-suhu-frayer-5-by-produk');
        Route::get('/ajax/get-waktu-penggorengan-5-by-suhu/{id_suhu}', [Frayer5Controller::class, 'getWaktuPenggorenganBySuhu'])->name('get-waktu-penggorengan-5-by-suhu');
        Route::get('/ajax/get-suhu-frayer-1-by-produk/{id_produk}', [ProsesFrayerController::class, 'getSuhuFrayer1ByProduk']);
        Route::get('/ajax/get-suhu-frayer-3-by-produk/{id_produk}', [ProsesFrayerController::class, 'getSuhuFrayer3ByProduk']);
        Route::get('/ajax/get-suhu-frayer-4-by-produk/{id_produk}', [ProsesFrayerController::class, 'getSuhuFrayer4ByProduk']);
        Route::get('/ajax/get-suhu-frayer-5-by-produk/{id_produk}', [ProsesFrayerController::class, 'getSuhuFrayer5ByProduk']);
        // AJAX routes for Pemeriksaan Produk Cooking Mixer FLA cascading dropdowns
        Route::get('/ajax/formula-by-product-pemeriksaan/{product_id}', [PemeriksaanProdukCookingMixerFlaController::class, 'getFormulaByProduct']);
        Route::get('/ajax/steps-by-formula-pemeriksaan/{formula_id}', [PemeriksaanProdukCookingMixerFlaController::class, 'getStepsByFormula']);
        Route::get('/ajax/bahan-by-step-pemeriksaan/{step_id}', [PemeriksaanProdukCookingMixerFlaController::class, 'getBahanByStep']);
        Route::get('/get-jumlah-by-barang/{id}', [DataBarangController::class, 'getJumlahById'])->name('get-jumlah-by-barang');
        Route::get('/get-sub-areas-by-area/{areaId}', [BarangMudahPecahController::class, 'getSubAreasByArea'])->name('get-sub-areas-by-area');
        // {{ AJAX SEARCH HALAMAN Bahan Forming }}
        Route::get('/persiapan-bahan-forming-search', [PersiapanBahanFormingController::class, 'searchAjax'])->name('persiapan-bahan-forming.search');
        // {{ AJAX SEARCH HALAMAN Bahan Emulsi }}
        Route::get('/persiapan-bahan-emulsi-search', [PembuatanEmulsiController::class, 'searchAjax'])->name('persiapan-bahan-emulsi.search');
        
        // untuk approval chillroom
        Route::post('chillroom/{uuid}/approve', [ChillroomController::class, 'approve'])
            ->name('chillroom.approve');
        
        // untuk approval seasoning
        Route::post('seasoning/{uuid}/approve', [SeasoningController::class, 'approve'])
            ->name('seasoning.approve');
        
        // untuk approval shoestring
        Route::post('shoestring/{uuid}/approve', [ShoestringController::class, 'approve'])
            ->name('shoestring.approve');
        
        // untuk approval rebox
        Route::post('rebox/{uuid}/approve', [ReboxController::class, 'approve'])
            ->name('rebox.approve');

        // untuk approval pemeriksaan bahan kemas
        Route::post('pemeriksaan-bahan-kemas/{uuid}/approve', [PemeriksaanBahanKemasController::class, 'approve'])
            ->name('pemeriksaan-bahan-kemas.approve');

        //Untuk approval pengemasan 
        Route::post('dokumentasi/{uuid}/approve', [DokumentasiController::class, 'approve'])
        ->name('dokumentasi.approve');

        //Untuk approval ketidaksesuaian plastik 
        Route::post('ketidaksesuaian-plastik/{uuid}/approve', [KetidaksesuaianPlastikController::class, 'approve'])
        ->name('ketidaksesuaian-plastik.approve');

        //Untuk approval ketidaksesuaian benda asing 
        Route::post('ketidaksesuaian-benda-asing/{uuid}/approve', [KetidaksesuaianBendaAsingController::class, 'approve'])
        ->name('ketidaksesuaian-benda-asing.approve');
        
        // untuk approval persiapan bahan forming
        Route::post('persiapan-bahan-forming/{uuid}/approve', [PersiapanBahanFormingController::class, 'approve'])
            ->name('persiapan-bahan-forming.approve');
        
        //Untuk approval metal detector 
        Route::post('input-metal-detector/{uuid}/approve', [MetalDetectorController::class, 'approve'])
        ->name('input-metal-detector.approve');

        // untuk approval persiapan bahan emulsi
        Route::post('persiapan-bahan-emulsi/{uuid}/approve', [PembuatanEmulsiController::class, 'approve'])
            ->name('persiapan-bahan-emulsi.approve');
        
        // untuk approval persiapan bahan better
        Route::post('persiapan-bahan-better/{uuid}/approve', [PersiapanBahanBetterController::class, 'approve'])
            ->name('persiapan-bahan-better.approve');

        // untuk approval produk forming
        Route::post('produk-forming/{uuid}/approve', [ProdukFormingController::class, 'approve'])
            ->name('produk-forming.approve');
        
        // untuk approval produk non forming
        Route::post('produk-non-forming/{uuid}/approve', [ProdukNonFormingController::class, 'approve'])
        ->name('produk-non-forming.approve');

        //Untuk approval produk Yum
        Route::post('produk-yum/{uuid}/approve', [ProdukYumController::class, 'approve'])->name('produk-yum.approve');
        //approval pemeriksaan benda asing
        Route::post('pemeriksaan-benda-asing/{uuid}/approve', [PemeriksaanBendaAsingController::class, 'approve'])
        ->name('pemeriksaan-benda-asing.approve');
        //approval pemeriksaaan proses produksi
        Route::post('pemeriksaan-proses-produksi/{uuid}/approve', [PemeriksaanProsesProduksiController::class, 'approve'])
        ->name('pemeriksaan-proses-produksi.approve');
        
        //approval pemeriksaan produk cooking mixer fla
        Route::post('pemeriksaan-produk-cooking-mixer-fla/{uuid}/approve', [PemeriksaanProdukCookingMixerFlaController::class, 'approve'])
        ->name('pemeriksaan-produk-cooking-mixer-fla.approve');
        
        //Approval Timbangan
        Route::post('/timbangan/{uuid}/approve', [TimbanganController::class, 'approve'])->name('timbangan.approve');
         // Approval route for Pembuatan Sample
        Route::post('pembuatan-sample/{uuid}/approve', [PembuatanSampleController::class, 'approve'])
        ->name('pembuatan-sample.approve');
         //Approval Route untuk Verifikasi Berat Produk
        Route::post('verifikasi-berat-produk/bulk-export-pdf', [VerifikasiBeratProdukController::class, 'bulkExportPdf'])
        ->name('verifikasi-berat-produk.bulk-export-pdf');
        Route::post('verifikasi-berat-produk/approve', [VerifikasiBeratProdukController::class, 'approve'])
        ->name('verifikasi-berat-produk.approve');
           //   Approval route untuk thermometer
        Route::post('/thermometer/{uuid}/approve', [ThermometerController::class, 'approve'])->name('thermometer.approve');
       //Approval Pemeriksaan Rheon Machine
        Route::post('/pemeriksaan-rheon-machine/{uuid}/approve', [PemeriksaanRheonMachineController::class, 'approve'])->name('pemeriksaan-rheon-machine.approve');
     
        //Approval Pemeriksaan Rice Bites
        Route::post('/pemeriksaan-rice-bites/{uuid}/approve', [PemeriksaanRiceBitesController::class, 'approve'])->name('pemeriksaan-rice-bites.approve');
         // Additional routes for pemeriksaan-rice-bites verification
        Route::patch('pemeriksaan-rice-bites/{uuid}/verify-qc', [PemeriksaanRiceBitesController::class, 'verifyQC'])
             ->name('pemeriksaan-rice-bites.verify-qc');
        Route::patch('pemeriksaan-rice-bites/{uuid}/acknowledge-produksi', [PemeriksaanRiceBitesController::class, 'acknowledgeProduksi'])
             ->name('pemeriksaan-rice-bites.acknowledge-produksi');
             // Approval route for GMP Karyawan
        Route::post('/gmp-karyawan/{uuid}/approve', [GmpKaryawanController::class, 'approve'])->name('gmp-karyawan.approve');

        // Approval route for Verifikasi Peralatan
        Route::post('/verif-peralatan/{uuid}/approve', [VerifPeralatanController::class, 'approve'])->name('verif-peralatan.approve');

        // Approval route for Verif CIP
        Route::post('/verif-cip/{uuid}/approve', [VerifCipController::class, 'approve'])->name('verif-cip.approve');

        // Approval route for Proses Thawing
        Route::post('/proses-twahing/{uuid}/approve', [ProsesTwahingController::class, 'approve'])->name('proses-twahing.approve');
        
        // Approval route for Kontrol Sanitasi
        Route::post('/kontrol-sanitasi/{uuid}/approve', [KontrolSanitasiController::class, 'approve'])->name('kontrol-sanitasi.approve');

        // Approval route for Barang Mudah Pecah
        Route::post('/barang-mudah-pecah/{uuid}/approve', [BarangMudahPecahController::class, 'approve'])->name('barang-mudah-pecah.approve');
        //Approval Area Proses
                Route::post('/area-proses/{uuid}/approve', [AreaProsesController::class, 'approve'])->name('area-proses.approve');
        // Approval route for Pembekuan IQF Penggorengan
        Route::post('pembekuan-iqf-penggorengan/{uuid}/approve', [PembekuanIqfPenggorenganController::class, 'approve'])->name('pembekuan-iqf-penggorengan.approve');
        
        // Approval route for Pembekuan IQF Roasting
        Route::post('pembekuan-iqf-roasting/{uuid}/approve', [PembekuanIqfRoastingController::class, 'approve'])->name('pembekuan-iqf-roasting.approve');
        
        // untuk logging chillroom
        Route::get('chillroom/{uuid}/logs', [ChillroomController::class, 'showLogs'])
            ->name('chillroom.logs');
        Route::get('chillroom/{uuid}/logs-json', [ChillroomController::class, 'getLogsJson'])
            ->name('chillroom.logs-json');
          //  untuk Logging Seasoning
        Route::get('seasoning/{uuid}/logs', [SeasoningController::class, 'showLogs'])
            ->name('seasoning.logs');
        Route::get('seasoning/{uuid}/logs-json', [SeasoningController::class, 'getLogsJson'])
            ->name('seasoning.logs-json');
        // Route Untuk logging Shoestring
        Route::get('shoestring/{uuid}/logs', [ShoestringController::class, 'showLogs'])
            ->name('shoestring.logs');
        Route::get('shoestring/{uuid}/logs-json', [ShoestringController::class, 'getLogsJson'])
            ->name('shoestring.logs-json');
        // Route Untuk logging Rebox
        Route::get('rebox/{uuid}/logs', [ReboxController::class, 'showLogs'])
            ->name('rebox.logs');
        Route::get('rebox/{uuid}/logs-json', [ReboxController::class, 'getLogsJson'])
            ->name('rebox.logs-json');

        // Route Untuk logging Pemeriksaan Bahan Kemas
        Route::get('pemeriksaan-bahan-kemas/{uuid}/logs', [PemeriksaanBahanKemasController::class, 'showLogs'])
            ->name('pemeriksaan-bahan-kemas.logs');
        // Route Untuk logging Bahan Forming
        Route::get('persiapan-bahan-forming/{uuid}/logs', [PersiapanBahanFormingController::class, 'showLogs'])
            ->name('persiapan-bahan-forming.logs');
        Route::get('persiapan-bahan-forming/{uuid}/logs-json', [PersiapanBahanFormingController::class, 'getLogsJson'])
            ->name('persiapan-bahan-forming.logs-json');
        // Route Untuk logging Bahan Emulsi
        Route::get('persiapan-bahan-emulsi/{uuid}/logs', [PembuatanEmulsiController::class, 'showLogs'])
            ->name('persiapan-bahan-emulsi.logs');
        Route::get('persiapan-bahan-emulsi/{uuid}/logs-json', [PembuatanEmulsiController::class, 'getLogsJson'])
            ->name('persiapan-bahan-emulsi.logs-json');
        // Route Untuk Loogging Bahan Better
        Route::get('persiapan-bahan-better/{uuid}/logs', [PersiapanBahanBetterController::class, 'showLogs'])
            ->name('persiapan-bahan-better.logs');
        Route::get('persiapan-bahan-better/{uuid}/logs-json', [PersiapanBahanBetterController::class, 'getLogsJson'])
            ->name('persiapan-bahan-better.logs-json');
        // Route Untuk logging Metal Detector
        Route::get('input-metal-detector/{uuid}/logs', [MetalDetectorController::class, 'showLogs'])
            ->name('input-metal-detector.logs');
        Route::get('input-metal-detector/{uuid}/logs-json', [MetalDetectorController::class, 'getLogsJson'])
            ->name('input-metal-detector.logs-json');
        // Route Untuk logging Bahan Baku Tumbling
        Route::get('bahan-baku-tumbling/{uuid}/logs', [BahanBakuTumblingController::class, 'showLogs'])
            ->name('bahan-baku-tumbling.logs');
        Route::get('bahan-baku-tumbling/{uuid}/logs-json', [BahanBakuTumblingController::class, 'getLogsJson'])
            ->name('bahan-baku-tumbling.logs-json');
        // Route Untuk logging Proses Marinade
        Route::get('proses-marinade/{uuid}/logs', [ProsesMarinadeController::class, 'showLogs'])
        ->name('proses-marinade.logs');
        Route::get('proses-marinade/{uuid}/logs-json', [ProsesMarinadeController::class, 'getLogsJson'])
        ->name('proses-marinade.logs-json');
        // Route Untuk logging Proses Parameter Tumbling
        Route::get('proses-tumbling/{uuid}/logs', [ProsesTumblingController::class, 'showLogs'])
            ->name('proses-tumbling.logs');
        Route::get('proses-tumbling/{uuid}/logs-json', [ProsesTumblingController::class, 'getLogsJson'])
            ->name('proses-tumbling.logs-json');
        // Route Untuk logging Pengemasan Produk
        Route::get('pengemasan-produk/{uuid}/logs', [PengemasanProdukController::class, 'showLogs'])
            ->name('pengemasan-produk.logs');
        Route::get('pengemasan-produk/{uuid}/logs-json', [PengemasanProdukController::class, 'getLogsJson'])
            ->name('pengemasan-produk.logs-json');
        // Route Untuk Logging Pengemasan Plastik
        Route::get('pengemasan-plastik/{uuid}/logs', [PengemasanPlastikController::class, 'showLogs'])
            ->name('pengemasan-plastik.logs');
        Route::get('pengemasan-plastik/{uuid}/logs-json', [PengemasanPlastikController::class, 'getLogsJson'])
            ->name('pengemasan-plastik.logs-json');
        // Route Untuk Logging Produk YUM
        Route::get('produk-yum/{uuid}/logs', [ProdukYumController::class, 'showLogs'])->name('produk-yum.logs');
        Route::get('produk-yum/{uuid}/logs-json', [ProdukYumController::class, 'getLogsJson'])->name('produk-yum.logs-json');
        // Logging routes for Bag
        Route::get('berat-produk/bag/{uuid}/logs', [BeratProdukController::class, 'showBagLogs'])->name('berat-produk.bag-logs');
        Route::get('berat-produk/bag/{uuid}/logs-json', [BeratProdukController::class, 'getBagLogsJson'])->name('berat-produk.bag-logs-json');
        // Logging routes for Box
        Route::get('berat-produk/box/{uuid}/logs', [BeratProdukController::class, 'showBoxLogs'])->name('berat-produk.box-logs');
        Route::get('berat-produk/box/{uuid}/logs-json', [BeratProdukController::class, 'getBoxLogsJson'])->name('berat-produk.box-logs-json');
        // Logging routes for Pengemasan Karton
        Route::get('pengemasan-karton/{uuid}/logs', [PengemasanKartonController::class, 'showLogs'])->name('pengemasan-karton.logs');
        Route::get('pengemasan-karton/{uuid}/logs-json', [PengemasanKartonController::class, 'getLogsJson'])->name('pengemasan-karton.logs-json');
        // Logging routes for Produk Forming
        Route::get('produk-forming/{uuid}/logs', [ProdukFormingController::class, 'showLogs'])->name('produk-forming.logs');
        Route::get('produk-forming/{uuid}/logs-json', [ProdukFormingController::class, 'getLogsJson'])->name('produk-forming.logs-json');
        // Logging routes for Produk Non Forming
        Route::get('produk-non-forming/{uuid}/logs', [ProdukNonFormingController::class, 'showLogs'])->name('produk-non-forming.logs');
        Route::get('produk-non-forming/{uuid}/logs-json', [ProdukNonFormingController::class, 'getLogsJson'])->name('produk-non-forming.logs-json');
        // Logging routes for Produk Yum
        Route::get('produk-yum/{uuid}/logs', [ProdukYumController::class, 'showLogs'])->name('produk-yum.logs');
        Route::get('produk-yum/{uuid}/logs-json', [ProdukYumController::class, 'getLogsJson'])->name('produk-yum.logs-json');
        // Logging routes for Penggorengan
        Route::get('penggorengan/{uuid}/logs', [PenggorenganController::class, 'showLogs'])->name('penggorengan.logs');
        Route::get('penggorengan/{uuid}/logs-json', [PenggorenganController::class, 'getLogsJson'])->name('penggorengan.logs-json');
        // Logging routes for Pembuatan Predust
        Route::get('pembuatan-predust/{uuid}/logs', [PembuatanPredustController::class, 'showLogs'])->name('pembuatan-predust.logs'); 
        Route::get('pembuatan-predust/{uuid}/logs-json', [PembuatanPredustController::class, 'getLogsJson'])->name('pembuatan-predust.logs-json');
        // Logging routes for Proses Battering
        Route::get('proses-battering/{uuid}/logs', [ProsesBatteringController::class, 'showLogs'])->name('proses-battering.logs'); 
        Route::get('proses-battering/{uuid}/logs-json', [ProsesBatteringController::class, 'getLogsJson'])->name('proses-battering.logs-json');
        // Logging routes for Proses Breader
        Route::get('proses-breader/{uuid}/logs', [ProsesBreaderController::class, 'showLogs'])->name('proses-breader.logs'); 
        Route::get('proses-breader/{uuid}/logs-json', [ProsesBreaderController::class, 'getLogsJson'])->name('proses-breader.logs-json');
        // Logging routes for Hasil Penggorengan
        Route::get('hasil-penggorengan/{uuid}/logs', [HasilPenggorenganController::class, 'showLogs'])->name('hasil-penggorengan.logs'); 
        Route::get('hasil-penggorengan/{uuid}/logs-json', [HasilPenggorenganController::class, 'getLogsJson'])->name('hasil-penggorengan.logs-json');
        // Logging routes for Hasil Proses Roasting
        Route::get('hasil-proses-roasting/{uuid}/logs', [HasilProsesRoastingController::class, 'showLogs'])->name('hasil-proses-roasting.logs'); 
        Route::get('hasil-proses-roasting/{uuid}/logs-json', [HasilProsesRoastingController::class, 'getLogsJson'])->name('hasil-proses-roasting.logs-json');
        // Logging routes for Pembekuan IQF Penggorengan
        Route::get('pembekuan-iqf-penggorengan/{uuid}/logs', [PembekuanIqfPenggorenganController::class, 'showLogs'])->name('pembekuan-iqf-penggorengan.logs'); 
        Route::get('pembekuan-iqf-penggorengan/{uuid}/logs-json', [PembekuanIqfPenggorenganController::class, 'getLogsJson'])->name('pembekuan-iqf-penggorengan.logs-json');
        // Logging routes for Input Roasting
        Route::get('/input-roasting/{uuid}/logs', [InputRoastingController::class, 'showLogs'])->name('input-roasting.logs');
        Route::get('/input-roasting/{uuid}/logs-json', [InputRoastingController::class, 'getLogsJson'])->name('input-roasting.logs-json');
        // Logging routes for Bahan Baku Roasting
        Route::get('/bahan-baku-roasting/{uuid}/logs', [BahanBakuRoastingController::class, 'showLogs'])->name('bahan-baku-roasting.logs');
        Route::get('/bahan-baku-roasting/{uuid}/logs-json', [BahanBakuRoastingController::class, 'getLogsJson'])->name('bahan-baku-roasting.logs-json');
        // Logging routes for Proses Roasting Fan
        Route::get('/proses-roasting-fan/{uuid}/logs', [ProsesRoastingFanController::class, 'showLogs'])->name('proses-roasting-fan.logs');
        Route::get('/proses-roasting-fan/{uuid}/logs-json', [ProsesRoastingFanController::class, 'getLogsJson'])->name('proses-roasting-fan.logs-json');
        // Logging routes for Dokumentasi
        Route::get('/dokumentasi/{uuid}/logs', [DokumentasiController::class, 'showLogs'])->name('dokumentasi.logs');
        Route::get('/dokumentasi/{uuid}/logs-json', [DokumentasiController::class, 'getLogsJson'])->name('dokumentasi.logs-json');
        // Logging routes for Pembuatan Sample
        Route::get('/pembuatan-sample/{uuid}/logs', [PembuatanSampleController::class, 'showLogs'])->name('pembuatan-sample.logs');
        Route::get('/pembuatan-sample/{uuid}/logs-json', [PembuatanSampleController::class, 'getLogsJson'])->name('pembuatan-sample.logs-json');
        // Logging routes for Pemeriksaan Produk Cooking Mixer FLA
        Route::get('/pemeriksaan-produk-cooking-mixer-fla/{uuid}/logs', [PemeriksaanProdukCookingMixerFlaController::class, 'showLogs'])->name('pemeriksaan-produk-cooking-mixer-fla.logs');
        Route::get('/pemeriksaan-produk-cooking-mixer-fla/{uuid}/logs-json', [PemeriksaanProdukCookingMixerFlaController::class, 'getLogsJson'])->name('pemeriksaan-produk-cooking-mixer-fla.logs-json');
        // Logging routes for Pemeriksaan Produk Cooking Mixer FLA
        Route::get('/pemeriksaan-produk-cooking--fla/{uuid}/logs', [PemeriksaanProdukCookingMixerFlaController::class, 'showLogs'])->name('pemeriksaan-produk-cooking-mixer-fla.logs');
        Route::get('/pemeriksaan-produk-cooking-mixer-fla/{uuid}/logs-json', [PemeriksaanProdukCookingMixerFlaController::class, 'getLogsJson'])->name('pemeriksaan-produk-cooking-mixer-fla.logs-json');
        // Logging routes for Pemeriksaan Rheon Machine
        Route::get('/pemeriksaan-rheon-machine/{uuid}/logs', [PemeriksaanRheonMachineController::class, 'showLogs'])->name('pemeriksaan-rheon-machine.logs');
        Route::get('/pemeriksaan-rheon-machine/{uuid}/logs-json', [PemeriksaanRheonMachineController::class, 'getLogsJson'])->name('pemeriksaan-rheon-machine.logs-json');
        // Logging routes for Pemeriksaan Rice Bites
        Route::get('/pemeriksaan-rice-bites/{uuid}/logs', [PemeriksaanRiceBitesController::class, 'showLogs'])->name('pemeriksaan-rice-bites.logs');
        Route::get('/pemeriksaan-rice-bites/{uuid}/logs-json', [PemeriksaanRiceBitesController::class, 'getLogsJson'])->name('pemeriksaan-rice-bites.logs-json');
        // Logging routes for Pemasakan Nasi
        Route::get('/pemasakan-nasi/{uuid}/logs', [PemasakanNasiController::class, 'showLogs'])->name('pemasakan-nasi.logs');
        Route::get('/pemasakan-nasi/{uuid}/logs-json', [PemasakanNasiController::class, 'getLogsJson'])->name('pemasakan-nasi.logs-json');
        // Logging routes for Ketidaksesuaian Plastik
        Route::get('/ketidaksesuaian-plastik/{uuid}/logs', [KetidaksesuaianPlastikController::class, 'showLogs'])->name('ketidaksesuaian-plastik.logs');
        Route::get('/ketidaksesuaian-plastik/{uuid}/logs-json', [KetidaksesuaianPlastikController::class, 'getLogsJson'])->name('ketidaksesuaian-plastik.logs-json');
        // Logging routes for Ketidaksesuaian Benda Asing
        Route::get('/ketidaksesuaian-benda-asing/{uuid}/logs', [KetidaksesuaianBendaAsingController::class, 'showLogs'])->name('ketidaksesuaian-benda-asing.logs');
        Route::get('/ketidaksesuaian-benda-asing/{uuid}/logs-json', [KetidaksesuaianBendaAsingController::class, 'getLogsJson'])->name('ketidaksesuaian-benda-asing.logs-json');
        // Logging routes for GMP Karyawan
        Route::get('/gmp-karyawan/{uuid}/logs', [GmpKaryawanController::class, 'showLogs'])->name('gmp-karyawan.logs');
        Route::get('/gmp-karyawan/{uuid}/logs-json', [GmpKaryawanController::class, 'getLogsJson'])->name('gmp-karyawan.logs-json');
    
        // Logging routes for Kontrol Sanitasi
        Route::get('/kontrol-sanitasi/{uuid}/logs', [KontrolSanitasiController::class, 'showLogs'])->name('kontrol-sanitasi.logs');
        Route::get('/kontrol-sanitasi/{uuid}/logs-json', [KontrolSanitasiController::class, 'getLogsJson'])->name('kontrol-sanitasi.logs-json');
        // Logging routes for Barang Mudah Pecah
        Route::get('/barang-mudah-pecah/{uuid}/logs', [BarangMudahPecahController::class, 'showLogs'])->name('barang-mudah-pecah.logs');
        Route::get('/barang-mudah-pecah/{uuid}/logs-json', [BarangMudahPecahController::class, 'getLogsJson'])->name('barang-mudah-pecah.logs-json');
        // Logging routes for Pemeriksaan Benda Asing
        Route::get('/pemeriksaan-benda-asing/{uuid}/logs', [PemeriksaanBendaAsingController::class, 'showLogs'])->name('pemeriksaan-benda-asing.logs');
        Route::get('/pemeriksaan-benda-asing/{uuid}/logs-json', [PemeriksaanBendaAsingController::class, 'getLogsJson'])->name('pemeriksaan-benda-asing.logs-json');
        // Logging routes for Pemeriksaan Proses Produksi
        Route::get('/pemeriksaan-proses-produksi/{uuid}/logs', [PemeriksaanProsesProduksiController::class, 'showLogs'])->name('pemeriksaan-proses-produksi.logs');
        Route::get('/pemeriksaan-proses-produksi/{uuid}/logs-json', [PemeriksaanProsesProduksiController::class, 'getLogsJson'])->name('pemeriksaan-proses-produksi.logs-json');
        // Logging routes for Timbangan
        Route::get('/timbangan/{uuid}/logs', [TimbanganController::class, 'showLogs'])->name('timbangan.logs');
        Route::get('/timbangan/{uuid}/logs-json', [TimbanganController::class, 'getLogsJson'])->name('timbangan.logs-json');
        // Logging routes for Thermometer
        Route::get('/thermometer/{uuid}/logs', [ThermometerController::class, 'showLogs'])->name('thermometer.logs');
        Route::get('/thermometer/{uuid}/logs-json', [ThermometerController::class, 'getLogsJson'])->name('thermometer.logs-json');
        // Logging routes for Verifikasi Berat Produk
        Route::get('/verifikasi-berat-produk/{uuid}/logs', [VerifikasiBeratProdukController::class, 'showLogs'])->name('verifikasi-berat-produk.logs');
        Route::get('/verifikasi-berat-produk/{uuid}/logs-json', [VerifikasiBeratProdukController::class, 'getLogsJson'])->name('verifikasi-berat-produk.logs-json');
        
        // Logging routes for Produk Yum
        Route::get('/produk-yum/{uuid}/logs', [ProdukYumController::class, 'showLogs'])->name('produk-yum.logs');
        Route::get('/produk-yum/{uuid}/logs-json', [ProdukYumController::class, 'getLogsJson'])->name('produk-yum.logs-json');
        // =========================
        
            // RESOURCE ROUTES
        // =========================
        Route::resource('chillroom', ChillroomController::class);
        Route::resource('seasoning', SeasoningController::class);
        Route::resource('shoestring', ShoestringController::class);
        Route::resource('rebox', ReboxController::class);
        Route::resource('penyimpanan-bahan', PenyimpananBahanController::class);
         // Penyimpanan Bahan - Edit per 2 jam (clone-as-new)
        Route::get('penyimpanan-bahan/{uuid}/two-hour', [PenyimpananBahanController::class, 'twoHourEdit'])->name('penyimpanan-bahan.twohour.edit');
        Route::post('penyimpanan-bahan/{uuid}/two-hour', [PenyimpananBahanController::class, 'twoHourStore'])->name('penyimpanan-bahan.twohour.store');

        Route::resource('penggorengan', PenggorenganController::class, [
            'parameters' => ['penggorengan' => 'uuid']
        ]);
        Route::resource('persiapan-bahan-forming', PersiapanBahanFormingController::class, [
            'parameters' => ['persiapan-bahan-forming' => 'uuid']
        ]);
        Route::resource('persiapan-bahan-non-forming', PersiapanBahanNonFormingController::class, [
            'parameters' => ['persiapan-bahan-non-forming' => 'uuid']
        ])->only(['create', 'store', 'show', 'edit', 'update']);
        Route::resource('persiapan-bahan-emulsi', PembuatanEmulsiController::class, [
            'parameters' => ['persiapan-bahan-emulsi' => 'uuid']
        ]);
        Route::resource('persiapan-bahan-better', PersiapanBahanBetterController::class, [
            'parameters' => ['persiapan-bahan-better' => 'uuid']
        ]);
        Route::resource('proses-breader', ProsesBreaderController::class, [
            'parameters' => ['proses-breader' => 'uuid']
        ]);
        Route::resource('proses-battering', ProsesBatteringController::class, [
            'parameters' => ['proses-battering' => 'uuid']
        ]);
        Route::resource('input-roasting', InputRoastingController::class, [
            'parameters' => ['input-roasting' => 'uuid']
        ]);
        Route::resource('bahan-baku-roasting', BahanBakuRoastingController::class, [
            'parameters' => ['bahan-baku-roasting' => 'uuid']
        ]);
        Route::resource('bahan-baku-tumbling', BahanBakuTumblingController::class, [
            'parameters' => ['bahan-baku-tumbling' => 'uuid']
        ]);
        
        Route::resource('pengemasan-karton', PengemasanKartonController::class, [
            'parameters' => ['pengemasan-karton' => 'uuid']
        ]);
        Route::resource('proses-frayer', ProsesFrayerController::class, [
            'parameters' => ['proses-frayer' => 'uuid']
        ]);
        Route::resource('frayer-2', Frayer2Controller::class, [
            'parameters' => ['frayer-2' => 'uuid']
        ]);
        Route::resource('hasil-penggorengan', HasilPenggorenganController::class, [
            'parameters' => ['hasil-penggorengan' => 'uuid']
        ]);
        Route::resource('pembekuan-iqf-penggorengan', PembekuanIqfPenggorenganController::class, [
            'parameters' => ['pembekuan-iqf-penggorengan' => 'uuid']
        ]);
        Route::resource('pembekuan-iqf-roasting', PembekuanIqfRoastingController::class, [
            'parameters' => ['pembekuan-iqf-roasting' => 'uuid']
        ]);
        Route::resource('hasil-proses-roasting', HasilProsesRoastingController::class, [
            'parameters' => ['hasil-proses-roasting' => 'uuid']
        ]);
        Route::resource('proses-tumbling', ProsesTumblingController::class, [
            'parameters' => ['proses-tumbling' => 'uuid']
        ]);
        Route::resource('proses-marinade', ProsesMarinadeController::class, [
            'parameters' => ['proses-marinade' => 'uuid']
        ]);
        Route::resource('proses-roasting-fan', ProsesRoastingFanController::class, [
            'parameters' => ['proses-roasting-fan' => 'uuid']
        ]);
        // Pengemasan Produk
        Route::resource('pengemasan-produk', PengemasanProdukController::class, [
            'parameters' => ['pengemasan-produk' => 'uuid']
        ]);
        Route::resource('pengemasan-plastik', PengemasanPlastikController::class, [
            'parameters' => ['pengemasan-plastik' => 'uuid']
        ]);
        Route::resource('pembuatan-sample', PembuatanSampleController::class, [
            'parameters' => ['pembuatan-sample' => 'uuid']
        ]);
        Route::resource('input-metal-detector', MetalDetectorController::class, [
            'parameters' => ['input-metal-detector' => 'uuid']
        ]);
        Route::resource('dokumentasi', DokumentasiController::class, [
            'parameters' => ['dokumentasi' => 'uuid']
        ]);
        Route::resource('ketidaksesuaian-plastik', KetidaksesuaianPlastikController::class, [
            'parameters' => ['ketidaksesuaian-plastik' => 'uuid']
        ]);
        Route::resource('ketidaksesuaian-benda-asing', KetidaksesuaianBendaAsingController::class, [
            'parameters' => ['ketidaksesuaian-benda-asing' => 'uuid']
        ]);
        
        Route::resource('area-proses', AreaProsesController::class, [
            'parameters' => ['area-proses' => 'uuid']
        ]);
        Route::resource('pemeriksaan-produk-cooking-mixer-fla', PemeriksaanProdukCookingMixerFlaController::class, [
            'parameters' => ['pemeriksaan-produk-cooking-mixer-fla' => 'uuid']
        ]);
        Route::resource('pemeriksaan-rheon-machine', PemeriksaanRheonMachineController::class, [
            'parameters' => ['pemeriksaan-rheon-machine' => 'uuid']
        ]);

        Route::get('pemeriksaan-rheon-machine/std-berat/{id_produk}', [PemeriksaanRheonMachineController::class, 'getStdBeratByProduk'])
            ->name('pemeriksaan-rheon-machine.std-berat');
       
        Route::resource('pemeriksaan-rice-bites', PemeriksaanRiceBitesController::class, [
            'parameters' => ['pemeriksaan-rice-bites' => 'uuid']
        ]);
        Route::resource('verifikasi-berat-produk', VerifikasiBeratProdukController::class, [
            'parameters' => ['verifikasi-berat-produk' => 'uuid']
        ]);
        Route::resource('verif-peralatan', VerifPeralatanController::class, [
            'parameters' => ['verif-peralatan' => 'uuid']
        ]);

        Route::resource('verif-cip', VerifCipController::class, [
            'parameters' => ['verif-cip' => 'uuid']
        ]);

         // Routes untuk edit per 2 jam area proses
        Route::get('area-proses/{uuid}/twohour-edit', [AreaProsesController::class, 'twoHourEdit'])
         ->name('area-proses.twohour.edit');
        Route::post('area-proses/{uuid}/twohour-store', [AreaProsesController::class, 'twoHourStore'])
         ->name('area-proses.twohour.store');
        
      
        Route::resource('kontrol-sanitasi', KontrolSanitasiController::class, [
            'parameters' => ['kontrol-sanitasi' => 'uuid']
        ]);
        Route::resource('barang-mudah-pecah', BarangMudahPecahController::class, [
            'parameters' => ['barang-mudah-pecah' => 'uuid']
        ]);
        Route::resource('pemeriksaan-bahan-kemas', PemeriksaanBahanKemasController::class, [
            'parameters' => ['pemeriksaan-bahan-kemas' => 'uuid']
        ]);
        Route::resource('pemeriksaan-benda-asing', PemeriksaanBendaAsingController::class, [
            'parameters' => ['pemeriksaan-benda-asing' => 'uuid']
        ]);
        Route::resource('gmp-karyawan', GmpKaryawanController::class, [
            'parameters' => ['gmp-karyawan' => 'uuid']
        ]);
        Route::resource('pemeriksaan-proses-produksi', PemeriksaanProsesProduksiController::class, [
            'parameters' => ['pemeriksaan-proses-produksi' => 'uuid']
        ]);
        Route::resource('timbangan', TimbanganController::class, [
            'parameters' => ['timbangan' => 'uuid']
        ]);
      
        Route::resource('thermometer', ThermometerController::class, [
            'parameters' => ['thermometer' => 'uuid']
        ]);
        Route::resource('produk-forming', ProdukFormingController::class, [
            'parameters' => ['produk-forming' => 'uuid']
        ]);
        Route::resource('produk-non-forming', ProdukNonFormingController::class, [
            'parameters' => ['produk-non-forming' => 'uuid']
        ]);
        Route::resource('produk-yum', ProdukYumController::class, [
            'parameters' => ['produk-yum' => 'uuid']
        ]);
        Route::resource('pembuatan-predust', PembuatanPredustController::class, [
            'parameters' => ['pembuatan-predust' => 'uuid']
        ]);
        Route::resource('pemasakan-nasi', PemasakanNasiController::class, [
            'parameters' => ['pemasakan-nasi' => 'uuid']
        ]);
        Route::resource('proses-aging', ProsesAgingController::class, [
            'parameters' => ['proses-aging' => 'uuid']
        ]);

        Route::resource('proses-twahing', ProsesTwahingController::class, [
            'parameters' => ['proses-twahing' => 'uuid']
        ]);
        
        Route::resource('frayer-3', Frayer3Controller::class)->parameters(['frayer-3' => 'uuid']);
        Route::resource('frayer-4', Frayer4Controller::class)->parameters(['frayer-4' => 'uuid']);
        Route::resource('frayer-5', Frayer5Controller::class)->parameters(['frayer-5' => 'uuid']);
        
        // AJAX route for getting jenis predust by product
        Route::get('/ajax/get-jenis-predust-by-produk', [PembuatanPredustController::class, 'getJenisPredustByProduk'])->name('ajax.get-jenis-predust-by-produk');
        
        
        // Berat Produk (Bag Pack & Box) - Grup route yang benar
        Route::controller(BeratProdukController::class)->prefix('berat-produk')->group(function () {
            Route::get('/', 'index')->name('berat-produk.index');
            Route::get('/create', 'create')->name('berat-produk.create');
            Route::post('/store-bag', 'store_bag')->name('berat-produk.store_bag');
            Route::post('/store-box', 'store_box')->name('berat-produk.store_box');
            Route::get('/edit-bag/{uuid}', 'edit_bag')->name('berat-produk.edit_bag');
            Route::get('/edit-box/{uuid}', 'edit_box')->name('berat-produk.edit_box');
            Route::put('/update-bag/{uuid}', 'update_bag')->name('berat-produk.update_bag');
            Route::put('/update-box/{uuid}', 'update_box')->name('berat-produk.update_box');
            Route::delete('/destroy-bag/{uuid}', 'destroy_bag')->name('berat-produk.destroy_bag');
            Route::delete('/destroy-box/{uuid}', 'destroy_box')->name('berat-produk.destroy_box');
            
        });
        // API Routes untuk Bahan Baku Tumbling
        Route::prefix('api')->group(function () {
            Route::get('/nomor-formula-non-forming/{produkId}', [BahanBakuTumblingController::class, 'getNomorFormulaByProduk']);
            Route::get('/bahan-non-forming/{formulaId}', [BahanBakuTumblingController::class, 'getBahanNonFormingByFormula']);
        });  
        
        Route::get('/keep-alive', function () {
            return response()->json(['status' => 'ok']);
        })->name('keep-alive');
    });

    // =========================
    // SUPER ADMIN ROUTES
    // =========================
    Route::prefix('super-admin')->group(function () {
        Route::resource('plan', PlanController::class, [
            'parameters' => ['plan' => 'uuid']
        ]);
        Route::resource('data-shift', DataShiftController::class, [
            'parameters' => ['data-shift' => 'uuid']
        ]);
        Route::resource('produk', JenisProdukController::class, [
            'parameters' => ['produk' => 'uuid']
        ]);
        Route::resource('nomor-formula', NomorFormulaController::class, [
            'parameters' => ['nomor-formula' => 'uuid']
        ]);
        Route::resource('bahan-forming', BahanFormingController::class, [
            'parameters' => ['bahan-forming' => 'uuid']
        ]);
        Route::resource('jenis-emulsi', JenisEmulsiController::class, [
            'parameters' => ['jenis-emulsi' => 'uuid']
        ]);
        Route::resource('total-pemakaian-emulsi', TotalPemakaianEmulsiController::class, [
            'parameters' => ['total-pemakaian-emulsi' => 'uuid']
        ]);
        Route::resource('nomor-emulsi', NomorEmulsiController::class, [
            'parameters' => ['nomor-emulsi' => 'uuid']
        ]);
        Route::resource('bahan-emulsi', BahanEmulsiController::class, [
            'parameters' => ['bahan-emulsi' => 'uuid']
        ]);
        Route::resource('jenis-better', JenisBetterController::class, [
            'parameters' => ['jenis-better' => 'uuid']
        ]);
        Route::resource('std-salinitas-viskositas', StdSalinitasViskositasController::class, [
            'parameters' => ['std-salinitas-viskositas' => 'uuid']
        ]);
        Route::resource('suhu-adonan', SuhuAdonanController::class, [
            'parameters' => ['suhu-adonan' => 'uuid']
        ]);
        Route::resource('jenis-breader', JenisBreaderController::class, [
            'parameters' => ['jenis-breader' => 'uuid']
        ]);
        Route::resource('data-tumbling', DataTumblingController::class, [
            'parameters' => ['data-tumbling' => 'uuid']
        ]);
        Route::resource('suhu-frayer-1', SuhuFrayer1Controller::class, [
            'parameters' => ['suhu-frayer-1' => 'uuid']
        ]);
        Route::resource('waktu-penggorengan', WaktuPenggorenganController::class, [
            'parameters' => ['waktu-penggorengan' => 'uuid']
        ]);
        Route::resource('jenis-marinade', JenisMarinadeController::class, [
            'parameters' => ['jenis-marinade' => 'uuid']
        ]);
        Route::resource('suhu-blok', SuhuBlokController::class, [
            'parameters' => ['suhu-blok' => 'uuid']
        ]);
        Route::resource('std-fan', StdFanController::class)->parameters([
            'std-fan' => 'uuid'
        ]);
        Route::resource('std-suhu-pusat', StdSuhuPusatController::class)->parameters([
            'std-suhu-pusat' => 'uuid'
        ]);
        Route::resource('std-berat-rheon', StdBeratRheonController::class)->parameters([
            'std-berat-rheon' => 'uuid'
        ]);
        Route::resource('data-gramasi', DataGramasiController::class)->parameters([
            'data-gramasi' => 'uuid'
        ]);
        Route::resource('data-bag', DataBagController::class)->parameters([
            'data-bag' => 'uuid'
        ]);
        Route::resource('data-box', DataBoxController::class)->parameters([
            'data-box' => 'uuid'
        ]);
        Route::resource('input-area', InputAreaController::class)->parameters([
            'input-area' => 'uuid'
        ]);
        Route::resource('input-mesin-peralatan', InputMesinPeralatanController::class)->parameters([
            'input-mesin-peralatan' => 'uuid'
        ]);
        Route::resource('roles', RoleController::class)->parameters([
            'roles' => 'uuid'
        ]);
        Route::resource('jenis-predust', JenisPredustController::class, [
            'parameters' => ['jenis-predust' => 'uuid']
        ]);
        Route::resource('suhu-frayer-2', SuhuFrayer2Controller::class, [
            'parameters' => ['suhu-frayer-2' => 'uuid']
        ]);
        Route::resource('waktu-penggorengan-2', WaktuPenggorengan2Controller::class, [
            'parameters' => ['waktu-penggorengan-2' => 'uuid']
        ]);
        Route::resource('users', UserController::class);
        Route::resource('nama-formula-fla', NamaFormulaFlaController::class)->parameters([
            'nama-formula-fla' => 'uuid'
        ]);
        Route::resource('nomor-step-formula-fla', NomorStepFormulaFlaController::class, [
            'parameters' => ['nomor-step-formula-fla' => 'uuid']
        ]);
        Route::resource('bahan-formula-fla', BahanFormulaFlaController::class, [
            'parameters' => ['bahan-formula-fla' => 'uuid']
        ]);
        Route::resource('data-barang', DataBarangController::class, [
            'parameters' => ['data-barang' => 'uuid']
        ]);
        Route::resource('std-suhu-pusat-roasting', StdSuhuPusatRoastingController::class, [
            'parameters' => ['std-suhu-pusat-roasting' => 'uuid']
        ]);
        Route::resource('data-timbangan', DataTimbanganController::class, [
            'parameters' => ['data-timbangan' => 'uuid']
        ]);
        Route::resource('data-thermo', DataThermoController::class, [
            'parameters' => ['data-thermo' => 'uuid']
        ]);
        Route::resource('data-rm', DataRmController::class, [
            'parameters' => ['data-rm' => 'uuid']
        ]);
        Route::resource('data-seasoning', DataSeasoningController::class, [
            'parameters' => ['data-seasoning' => 'uuid']
        ]);
        Route::resource('data-defect', DataDefectController::class, [
            'parameters' => ['data-defect' => 'uuid']
        ]);

        Route::get('/ajax/area-by-plan/{plan_id}', function($plan_id) {
            $user = auth()->user();
            $effectivePlanId = ($user->role === 'superadmin') ? $plan_id : $user->id_plan;

            return \App\Models\InputArea::where('id_plan', $effectivePlanId)
                ->orderBy('area')
                ->get(['id', 'area']);
        })->name('ajax.area-by-plan');
        //bahan non forming
        // Letakkan di sekitar atau bersama route resource lainnya
        Route::get('bahan-non-forming/download-template', [BahanNonFormingController::class, 'downloadTemplate'])->name('bahan-non-forming.download-template');
        Route::post('bahan-non-forming/import-excel', [BahanNonFormingController::class, 'importExcel'])->name('bahan-non-forming.import-excel');
        Route::resource('bahan-non-forming', BahanNonFormingController::class, [
            'parameters' => ['bahan-non-forming' => 'uuid']
        ]);
        // AJAX untuk filter produk berdasarkan plan dan status_bahan
        Route::get('/ajax/produk-non-forming-by-plan/{plan_id}', function($plan_id) {
            $produks = \App\Models\JenisProduk::where('id_plan', $plan_id)
                ->where('status_bahan', 'non-forming')
                ->get(['id', 'nama_produk']);
            
            return response()->json($produks);
        });
        
        // AJAX khusus super-admin
        Route::get('/get-emulsi-by-plan-produk', [TotalPemakaianEmulsiController::class, 'getEmulsiByPlanProduk'])->name('get-emulsi-by-plan-produk');
        Route::get('/get-total-pemakaian-by-emulsi/{nama_emulsi_id}', [NomorEmulsiController::class, 'getTotalPemakaianByEmulsi']);
        Route::get('/get-nomor-emulsi', [BahanEmulsiController::class, 'getNomorEmulsi'])->name('get-nomor-emulsi');
        Route::get('/ajax/better-by-plan-produk', function (\Illuminate\Http\Request $r) {
            return \App\Models\JenisBetter::where('id_plan', $r->id_plan)
                ->where('id_produk', $r->id_produk)
                ->get(['id', 'nama_better']);
        })->name('ajax.better-by-plan-produk');
        //Ajax data master jenis emulsi
        Route::get('/jenis-emulsi/produk-by-plan/{plan_id}', [JenisEmulsiController::class, 'getProdukByPlan']);
        
        //Ajax Data Master Std Salinitas Viskositas
        Route::get('/std-salinitas-viskositas/better-by-produk/{produk_id}', function($produk_id) {
        return \App\Models\JenisBetter::where('id_produk', $produk_id)
        ->get(['id', 'nama_better']);
        });
       
        //Ajax Nomor Formula Non Forming 
        Route::get('/nomor-formula-non-forming/produk-by-plan/{plan_id}', [NomorFormulaNonFormingController::class, 'getProdukByPlan']);
        //Ajax Nama Formula Non Formingnama-formula-non-forming/nomor-formula-by-produk
        //Ajax Nama Formula Non Forming
        Route::get('/nama-formula-non-forming/create', [NamaFormulaNonFormingController::class, 'create'])->name('nama-formula-non-forming.create');
        Route::get('/nama-formula-non-forming/produk-by-plan/{plan_id}', [NamaFormulaNonFormingController::class, 'getProdukByPlan']);
        Route::get('/nama-formula-non-forming/nomor-formula-by-produk/{produk_id}', [NamaFormulaNonFormingController::class, 'getNomorFormulaByProduk']);
        // Route untuk AJAX
        // AJAX khusus data master fla
        Route::get('/ajax/formula-by-product/{product_id}', [BahanFormulaFlaController::class, 'getFormulaByProduct']);
        Route::get('/ajax/steps-by-formula/{formula_id}', [BahanFormulaFlaController::class, 'getStepsByFormula']);
        Route::get('/ajax/bahan-by-step/{step_id}', [BahanFormulaFlaController::class, 'getBahanByStep']);
        Route::get('/ajax/step-by-formula/{formula_id}', [NomorStepFormulaFlaController::class, 'getStepByFormula']);
        // AJAX route for getting suhu frayer 2 by product data master
        Route::get('/ajax/get-suhu-frayer-2-by-produk/{id_produk}', [WaktuPenggorengan2Controller::class, 'getSuhuFrayer2ByProduk'])->name('ajax.get-suhu-frayer-2-by-produk');
        Route::get('/ajax/bahan-forming-by-formula/{id_formula}', [PersiapanBahanFormingController::class, 'getBahanFormingByFormula']);

        Route::get('/produk/by-plan/{plan_id}', [JenisProdukController::class, 'getProdukByPlan']);
        Route::get('/ajax/suhu-frayer-by-produk/{id_produk}', [\App\Http\Controllers\SuhuFrayer1Controller::class, 'getByProduk']);
        Route::get('/produk/frayer/{produk_id}', [JenisProdukController::class, 'getFrayerByProduk']);
        Route::get('/ajax/get-suhu-blok-by-produk/{produk_id}', function($produk_id) {
            $plan_id = request('plan_id');
            return \App\Models\SuhuBlok::where('id_produk', $produk_id)
                ->when($plan_id, function($q) use ($plan_id) {
                    $q->where('id_plan', $plan_id);
                })
                ->get(['id', 'suhu_blok']);
        });  
        
        Route::get('/keep-alive', function () {
            return response()->json(['status' => 'ok']);
        })->name('keep-alive');
    });
// oke
});
