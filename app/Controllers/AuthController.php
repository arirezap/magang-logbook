<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function index()
    {
        // Jika sudah login, arahkan ke dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        
        return view('auth/login');
    }

    public function process()
    {
        $session = session();
        $userModel = new UserModel();

        $nomor_induk = $this->request->getPost('nomor_induk');
        $password = $this->request->getPost('password');

        $user = $userModel->where('nomor_induk', $nomor_induk)->first();

        if ($user) {
            // Verifikasi password (menggunakan password_verify untuk keamanan)
            // Note: Untuk testing awal jika Anda menginput data manual ke DB via HeidiSQL,
            // baris `$password === $user['password']` mengizinkan login dengan password plain text.
            $verify_pass = password_verify($password, $user['password']);
            
            if ($verify_pass || $password === $user['password']) {
                $ses_data = [
                    'id'            => $user['id'],
                    'nomor_induk'   => $user['nomor_induk'],
                    'nama'          => $user['nama'],
                    'role'          => $user['role'],
                    'prodi_id'      => $user['prodi_id'],
                    'isLoggedIn'    => TRUE
                ];
                $session->set($ses_data);
                return redirect()->to('/dashboard');
            } else {
                $session->setFlashdata('error', 'Password yang Anda masukkan salah.');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('error', 'Nomor Induk tidak ditemukan.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}
