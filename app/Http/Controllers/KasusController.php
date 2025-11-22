<?php

namespace App\Http\Controllers;

use App\Models\Kasus;
use Illuminate\Http\Request;
use App\Entities\KasusEntity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class KasusController extends Controller
{
    public function index()
    {
        // Menampilkan seluruh data kasus dari database 
        $dataKasus = Kasus::all();

        // Mengirim data ke view dataKasus.blade.php
        return view('dataKasus', [
            'title' => 'Data Kasus Hepatitis A Jember',
            'dataKasus' => $dataKasus
        ]);
    }

    public function grafik()
    {
        // Ambil data jumlah kasus per kecamatan
        $data = Kasus::select('kecamatan')
            ->selectRaw('SUM(jumlah_kasus) as total_kasus')
            ->groupBy('kecamatan')
            ->orderBy('kecamatan')
            ->get();

        // Siapkan array label dan data untuk grafik
        $labels = $data->pluck('kecamatan');
        $values = $data->pluck('total_kasus');

        // Kirim ke view untuk ditampilkan dalam bentuk grafik 
        return view('grafikKasus', [
            'labels' => $labels,
            'values' => $values
        ]);
    }

    public function home()
    {
        $kasusRaw = Kasus::all();

        $dataKasus = [];

        foreach ($kasusRaw as $row) {
            $dataKasus[] = new KasusEntity(
                $row->kecamatan ?? 'Tidak diketahui',
                (int) ($row->jumlah_kasus ?? 0),
                (int) ($row->tahun ?? date('Y')) // default ke tahun sekarang kalau null
            );
        }

        // Hitung total kasus
        $totalKasus = array_reduce($dataKasus, fn($carry, $item) => $carry + $item->getJumlahKasus(), 0);

        // Jumlah kecamatan dengan kasus >= 5
        $jumlahKecamatan = count(array_unique(array_map(
            fn($item) => $item->getKecamatan(),
            array_filter($dataKasus, fn($item) => $item->getJumlahKasus() >= 5)
        )));

        // Zona klasifikasi
        $zonaMerah = count(array_filter($dataKasus, fn($k) => $k->getZona() === 'Merah'));
        $zonaKuning = count(array_filter($dataKasus, fn($k) => $k->getZona() === 'Kuning'));
        $zonaHijau = count(array_filter($dataKasus, fn($k) => $k->getZona() === 'Hijau'));

        // Kirim ke view home untuk ditampilkan berdasarkan klasifikasi 
        return view('home', compact(
            'totalKasus',
            'jumlahKecamatan',
            'zonaMerah',
            'zonaKuning',
            'zonaHijau'
        ));
    }

    public function searchKasus(Request $request)
    {
        // Mencari data berdasarkan nama kecamatan 
        $query = $request->input('q');

        // Akses tabel jember langsung dan cari kecamatan yang mengandung kata dari Input (Like %query%)
        $results = DB::table('wilayah')
            ->where('kecamatan', 'LIKE', '%' . $query . '%')
            ->get();

        // Kirm hasil pencraian ke view hasilSearch
        return view('hasilSearch', compact('results', 'query'));
    }

    public function exportCSV()
    {
        $kasus = Kasus::all();

        $filename = 'data_kasus_jember.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($kasus) {
            $file = fopen('php://output', 'w');
            // Baris header
            fputcsv($file, ['ID', 'Kecamatan', 'Jumlah Kasus', 'Tahun']);

            // Baris data
            foreach ($kasus as $row) {
                fputcsv($file, [
                    $row->gid,
                    $row->kecamatan,
                    $row->jumlah_kasus,
                    $row->tahun
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // public function exportEncryptedCSV(Request $request)
    // {
    //     // Validasi input password
    //     $data = $request->validate([
    //         'password' => 'required|string|min:8',
    //     ]);

    //     $password = $data['password'];

    //     if ($request->input('password') !== 'admin123') {
    //         return back()->with('alert', 'Password Salah. Coba lagi ya.')
    //             ->withInput($request->except('password'));
    //     }


    //     // Bangun CSV di Memori
    //     $stream = fopen('php://temp', 'r+');
    //     fputcsv($stream, ['ID', 'Kecamatan', 'Jumlah Kasus', 'Tahun']);
    //     foreach (Kasus::select('gid', 'kecamatan', 'jumlah_kasus', 'tahun')->orderBy('kecamatan')->get() as $row) {
    //         fputcsv($stream, [$row->gid, $row->kecamatan, $row->jumlah_kasus, $row->tahun]);
    //     }

    //     rewind($stream);
    //     $csv = stream_get_contents($stream);
    //     fclose($stream);

    //     // Pastikan ekstensi sodium aktif
    //     if (!function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')) {
    //         abort(500, 'Ekstensi sodium belum aktif di PHP.');
    //     }

    //     // Enkripsi CSV menggunakan Sodium
    //     // 3) Derive key dari password (Argon2id)
    //     // Pastikan ext-sodium aktif
    //     $salt = random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES);
    //     $key = sodium_crypto_pwhash(
    //         SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES,
    //         $password,
    //         $salt,
    //         SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
    //         SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE
    //     );

    //     // 4) Enkripsi AEAD XChaCha20-Poly1305
    //     $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
    //     $aad = 'hepatitiscase_csv_v1'; // Info tambahan untuk integritas
    //     $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($csv, $aad, $nonce, $key);
    //     if (function_exists('sodium_memzero')) {
    //         sodium_memzero($key);
    //     }

    //     // 5) Paketkan salt, nonce, dan ciphertext menjadi satu blob base64
    //     $blob = base64_encode($salt . $nonce . $ciphertext);

    //     $filename = 'data_kasus_jember_encrypted.enc';
    //     return response($blob, 200, [
    //         'Content-Type' => 'text/plain',
    //         'Content-Disposition' => "attachment; filename=$filename",
    //         'X-Content-Type-Option' => 'no-sniff',
    //     ]);
    // }

    public function decryptData(Request $request)
    {
        // 1) Validasi
        $request->validate([
            'file' => ['required', 'file'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // (Opsional) pastikan ekstensi .enc
        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        if ($ext !== 'enc') {
            return back()->withErrors('File bukan .enc. Unggah file hasil Export (Encrypted).')
                ->withInput($request->except('password'));
        }

        // 2) Pastikan sodium aktif
        if (!function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_decrypt')) {
            return back()->withErrors('Ekstensi sodium belum aktif di PHP.')
                ->withInput($request->except('password'));
        }

        // 3) Baca & parse paket: base64(salt | nonce | cipher)
        $raw = file_get_contents($request->file('file')->getRealPath());
        $blob = base64_decode($raw, true); // strict
        if ($blob === false) {
            return back()->withErrors('File tidak valid (bukan base64).')
                ->withInput($request->except('password'));
        }

        $off = 0;
        $salt = substr($blob, $off, \SODIUM_CRYPTO_PWHASH_SALTBYTES);
        $off += \SODIUM_CRYPTO_PWHASH_SALTBYTES;
        $nonce = substr($blob, $off, \SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
        $off += \SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;
        $ciph = substr($blob, $off);

        if ($salt === false || $nonce === false || $ciph === false) {
            return back()->withErrors('Struktur file terenkripsi tidak valid.')
                ->withInput($request->except('password'));
        }

        // 4) Derive key dari password (Argon2id)
        $key = sodium_crypto_pwhash(
            \SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES,
            $request->input('password'),
            $salt,
            \SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
            \SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE
        );

        // 5) Decrypt (AAD harus sama dengan saat enkripsi)
        $aad = 'hepatitiscase_csv_v1';
        $csv = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt($ciph, $aad, $nonce, $key);
        if (function_exists('sodium_memzero'))
            sodium_memzero($key);

        if ($csv === false) {
            return back()->withErrors('Decryption failed. Password salah atau file rusak.')
                ->withInput($request->except('password'));
        }

        // 6) Beri ke user sebagai CSV
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_kasus_jember_decrypted.csv"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function exportEncryptedZip(Request $request)
    {
        // Validasi + password policy
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);
        $password = $data['password'];

        if ($password !== 'admin123') {
            return back()->with('alert', 'Password Salah. Coba lagi ya.')
                ->withInput($request->except('password'));
        }

        // Cek sodium
        if (!\function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')) {
            return back()->withErrors('Ext sodium belum aktif.');
        }

        // 1) Bangun CSV di memori
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, ['ID', 'Kecamatan', 'Jumlah Kasus', 'Tahun']);
        foreach (Kasus::select('gid', 'kecamatan', 'jumlah_kasus', 'tahun')->orderBy('gid')->get() as $row) {
            fputcsv($stream, [$row->gid, $row->kecamatan, $row->jumlah_kasus, $row->tahun]);
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        // 2) Enkripsi -> blob .enc (base64)
        $salt = random_bytes(\SODIUM_CRYPTO_PWHASH_SALTBYTES);
        $key = sodium_crypto_pwhash(
            \SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES,
            $password,
            $salt,
            \SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
            \SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE
        );
        $nonce = random_bytes(\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
        $aad = 'hepatitiscase_csv_v1';
        $cipher = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($csv, $aad, $nonce, $key);
        if (\function_exists('sodium_memzero'))
            \sodium_memzero($key);

        $blob = base64_encode($salt . $nonce . $cipher); // isi file .enc


        // 3) ZIP-kan HANYA .enc + README (TANPA .sig & sign_pk)
        if (!class_exists(\ZipArchive::class)) {
            return back()->withErrors('Ext zip belum aktif.');
        }

        $zipPath = storage_path('app/' . ('enc_' . uniqid() . '.zip'));
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors('Gagal membuat ZIP.');
        }

        $zip->addFromString('data_kasus_jember_encrypted.enc', $blob);
        $zip->addFromString('README.txt', <<<TXT
Cara pakai:
1. Extract "data_kasus_jember_encrypted.zip".
2. Di dalamnya ada:
   - data_kasus_jember_encrypted.enc (ciphertext base64)
   - README.txt (instruksi penggunaan)
3. Buka menu "Decrypt CSV" di website.
4. Pilih file "data_kasus_jember_encrypted.enc".
5. Masukkan password enkripsi yang diberikan oleh admin.
6. Klik "Decrypt" → akan mengunduh "data_kasus_jember_decrypted.csv".
TXT);
        $zip->close();

        return response()->download($zipPath, 'data_kasus_jember_encrypted.zip', [
            'Content-Type' => 'application/zip',
            'X-Content-Type-Options' => 'nosniff',
        ])->deleteFileAfterSend(true);
    }
}
