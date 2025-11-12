<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function checkLogin()
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('loginAdmin')->with('error', 'Silakan login terlebih dahulu.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkLogin())
            return $redirect;

        $data = DB::table('wilayah')->get();
        return view('adminDashboard', compact('data'));
    }

    public function edit($id)
    {
        if ($redirect = $this->checkLogin())
            return $redirect;

        $row = DB::table('wilayah')->where('gid', $id)->first();
        return view('adminEdit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->checkLogin())
            return $redirect;

        DB::table('wilayah')->where('gid', $id)->update([
            'kecamatan' => $request->kecamatan,
            'jumlah_kasus' => $request->jumlah_kasus,
            'tahun' => $request->tahun
        ]);
        return redirect()->route('admin.dashboard');
    }

    public function destroy($id)
    {
        if ($redirect = $this->checkLogin())
            return $redirect;

        DB::table('wilayah')->where('gid', $id)->delete();
        return redirect()->route('admin.dashboard');
    }

    public function create()
    {
        if ($redirect = $this->checkLogin())
            return $redirect;

        return view('adminCreate');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkLogin())
            return $redirect;

        DB::table('wilayah')->insert([
            'kecamatan' => $request->kecamatan,
            'jumlah_kasus' => $request->jumlah_kasus,
            'tahun' => $request->tahun
        ]);
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Logout user
        $request->session()->invalidate(); // Kosongkan session
        $request->session()->regenerateToken(); // Cegah CSRF
        return redirect('/'); // Arahkan ke halaman login
    }

    public function showDecrypt()
    {
        return view('decrypt');
    }
}
