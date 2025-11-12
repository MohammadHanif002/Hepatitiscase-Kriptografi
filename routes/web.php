<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KasusController;
use App\Http\Controllers\KesehatanController;

Route::get('/', function () {
    return view('home', ['title' => 'Home Page']);
});

Route::get('/petaSebaran', function () {
    return view('petaSebaran', ['title' => 'Cluster Penyebaran Hepatitis A Jember']);
});


Route::get('loginAdmin', function () {
    return view('loginAdmin', ['title' => 'Login Admin']);
})->name('loginAdmin');

Route::get('dataKasus', function () {
    return view('dataKasus', ['title' => 'Data Kasus']);
})->name('dataKasus');


// Proses login manual
Route::post('/loginAdmin', function (Request $request) {
    $email = $request->input('email');
    $password = $request->input('password');

    if ($email === 'admin@example.com' && $password === 'admin123') {
        session(['admin_logged_in' => true]); // Set session manual
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('loginAdmin')->with('error', 'Email atau password salah.');
    }
})->name('admin.login');


// Export ZIP terenkripsi
Route::post('/kasus/export-encrypted-zip', [KasusController::class, 'exportEncryptedZip'])
    ->name('kasus.export.encrypted.zip');


// Middleware manual (tanpa auth Laravel)
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
Route::post('/admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');
Route::delete('/admin/delete/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.create');
Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
Route::get('/admin/decrypt', [AdminController::class, 'showDecrypt'])->name('admin.decrypt');
Route::post('/admin/decrypt', [KasusController::class, 'decryptData'])->name('admin.decrypt.process');

// Route baru untuk menampilkan GeoJSON dengan data kasus hepatitis 
Route::get('/geojson', [MapController::class, 'geojson'])->name('geojson');

Route::post('/petaSebaran', [AdminController::class, 'logout'])->name('logout');

Route::get('/dataKasus', [KasusController::class, 'index'])->name('data.kasus');

Route::get('/grafikKasus', [KasusController::class, 'grafik'])->name('grafikKasus');

Route::get('/export-kasus', [KasusController::class, 'exportCSV'])->name('kasus.export');

Route::get('/searchKasus', [KasusController::class, 'searchKasus']);

Route::get('/lokasi-faskes', [KesehatanController::class, 'lokasiFaskes']);

Route::get('/', [KasusController::class, 'home']);