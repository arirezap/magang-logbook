<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $role = strtolower(session()->get('role'));
        
        // Hanya Superadmin, Admin Prodi, Kaprodi, Direktur, Wadir, dan Kabag yang bisa mengakses
        if (!in_array($role, ['superadmin', 'admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $prodi_id = session()->get('prodi_id');
        
        // Membaca input filter dari request GET
        $filterNama = $this->request->getGet('nama');
        $filterProdi = $this->request->getGet('prodi');
        $filterRole = $this->request->getGet('role');
        $perPage = $this->request->getGet('per_page') ?? 10;

        $sort = $this->request->getGet('sort') ?? 'nama';
        $order = strtolower($this->request->getGet('order') ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

        $users = $this->userModel->getUsersWithDetails($role, $prodi_id, $filterNama, $filterProdi, $filterRole, $perPage, $sort, $order);
        $pager = $this->userModel->pager;

        // Ambil daftar prodi khusus untuk direktur/wadir/kabag/superadmin
        $prodiList = [];
        if (in_array($role, ['superadmin', 'direktur', 'wadir', 'kabag'])) {
            $prodiModel = new \App\Models\ProdiModel();
            $prodiList = $prodiModel->getOrderedProdi();
        }

        $data = [
            'title'       => 'Data Pengguna',
            'users'       => $users,
            'userRole'    => $role,
            'filterNama'  => $filterNama,
            'filterProdi' => $filterProdi,
            'filterRole'  => $filterRole,
            'prodiList'   => $prodiList,
            'perPage'     => $perPage,
            'pager'       => $pager
        ];

        return view('users/index', $data);
    }

    public function create()
    {
        $role = strtolower(session()->get('role'));
        if (!in_array($role, ['superadmin', 'admin_prodi', 'kaprodi'])) {
            return redirect()->to('/users')->with('error', 'Akses ditolak.');
        }

        // Ambil data prodi dan dosen pembimbing untuk dropdown
        $prodiModel = new \App\Models\ProdiModel();
        $prodiList = $prodiModel->getOrderedProdi();
        
        // Pembimbing yang tersedia
        if ($role == 'admin_prodi') {
            $pembimbingList = $this->userModel->where('role', 'pembimbing')->where('prodi_id', session()->get('prodi_id'))->findAll();
        } else {
            $pembimbingList = $this->userModel->where('role', 'pembimbing')->findAll();
        }

        $data = [
            'title'          => 'Tambah Pengguna Baru',
            'userRole'       => $role,
            'prodiList'      => $prodiList,
            'pembimbingList' => $pembimbingList
        ];

        return view('users/create', $data);
    }

    public function store()
    {
        $role = strtolower(session()->get('role'));
        if (!in_array($role, ['superadmin', 'admin_prodi'])) {
            return redirect()->to('/users');
        }

        $targetRole = $this->request->getPost('role');
        
        // Aturan validasi dasar
        $rules = [
            'nomor_induk' => 'required|is_unique[users.nomor_induk]',
            'nama'        => 'required|min_length[3]',
            'password'    => 'required|min_length[6]',
            'role'        => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator->getErrors());
        }

        // Susun data
        $userData = [
            'nomor_induk' => $this->request->getPost('nomor_induk'),
            'nama'        => $this->request->getPost('nama'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'        => $targetRole,
            'role_kedua'  => ($targetRole == 'pembimbing' && !empty($this->request->getPost('role_kedua'))) ? $this->request->getPost('role_kedua') : null,
        ];

        // Sesuaikan relasi berdasarkan Role yang dibuat
        if ($targetRole == 'taruna') {
            $userData['jenjang']       = $this->request->getPost('jenjang');
            $userData['kelas']         = $this->request->getPost('kelas');
            
            // Admin prodi otomatis prodi-nya sendiri, Superadmin pilih dari form
            $userData['prodi_id'] = ($role == 'admin_prodi') ? session()->get('prodi_id') : $this->request->getPost('prodi_id');
        } 
        elseif (in_array($targetRole, ['pembimbing', 'admin_prodi'])) {
            $userData['prodi_id'] = ($role == 'admin_prodi') ? session()->get('prodi_id') : $this->request->getPost('prodi_id');
        }

        $this->userModel->save($userData);
        return redirect()->to('/users')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $role = strtolower(session()->get('role'));
        if (!in_array($role, ['superadmin', 'admin_prodi'])) {
            return redirect()->to('/users')->with('error', 'Akses ditolak.');
        }

        $userEdit = $this->userModel->find($id);
        if (!$userEdit) {
            return redirect()->to('/users')->with('error', 'Data tidak ditemukan.');
        }

        // Keamanan Admin Prodi: Hanya bisa edit orang dari prodi yang sama
        if (in_array($role, ['admin_prodi']) && $userEdit['prodi_id'] != session()->get('prodi_id')) {
            return redirect()->to('/users')->with('error', 'Data tidak berada di wewenang Anda.');
        }

        $prodiModel = new \App\Models\ProdiModel();
        $prodiList = $prodiModel->getOrderedProdi();
        
        if (in_array($role, ['admin_prodi'])) {
            $pembimbingList = $this->userModel->where('role', 'pembimbing')->where('prodi_id', session()->get('prodi_id'))->findAll();
        } else {
            $pembimbingList = $this->userModel->where('role', 'pembimbing')->findAll();
        }

        $data = [
            'title'          => 'Edit Pengguna',
            'userRole'       => $role,
            'userEdit'       => $userEdit,
            'prodiList'      => $prodiList,
            'pembimbingList' => $pembimbingList
        ];

        return view('users/edit', $data);
    }

    public function update($id)
    {
        $role = strtolower(session()->get('role'));
        if (!in_array($role, ['superadmin', 'admin_prodi'])) {
            return redirect()->to('/users');
        }

        $userEdit = $this->userModel->find($id);
        if (!$userEdit || (in_array($role, ['admin_prodi']) && $userEdit['prodi_id'] != session()->get('prodi_id'))) {
            return redirect()->to('/users')->with('error', 'Data tidak valid.');
        }

        $targetRole = $this->request->getPost('role');
        
        // Aturan validasi
        $rules = [
            'nomor_induk' => "required|is_unique[users.nomor_induk,id,{$id}]",
            'nama'        => 'required|min_length[3]',
            'role'        => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator->getErrors());
        }

        // Susun data
        $userData = [
            'id'          => $id,
            'nomor_induk' => $this->request->getPost('nomor_induk'),
            'nama'        => $this->request->getPost('nama'),
            'role'        => $targetRole,
            'role_kedua'  => ($targetRole == 'pembimbing' && !empty($this->request->getPost('role_kedua'))) ? $this->request->getPost('role_kedua') : null,
        ];

        // Jika password diisi, maka update password
        if (!empty($this->request->getPost('password'))) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // Sesuaikan relasi
        if ($targetRole == 'taruna') {
            $userData['jenjang']       = $this->request->getPost('jenjang');
            $userData['kelas']         = $this->request->getPost('kelas');
            
            $userData['prodi_id'] = (in_array($role, ['admin_prodi'])) ? session()->get('prodi_id') : $this->request->getPost('prodi_id');
        } 
        elseif (in_array($targetRole, ['pembimbing', 'admin_prodi', 'kaprodi'])) {
            $userData['prodi_id'] = (in_array($role, ['admin_prodi'])) ? session()->get('prodi_id') : $this->request->getPost('prodi_id');
            // Bersihkan field lain jika beralih role
            $userData['jenjang'] = null;
            $userData['kelas'] = null;
        } 
        else {
            // Direktur / Wadir / Kabag / Superadmin
            $userData['prodi_id'] = null;
            $userData['jenjang'] = null;
            $userData['kelas'] = null;
        }

        $this->userModel->save($userData);
        return redirect()->to('/users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    // Fungsi Hapus
    public function delete($id)
    {
        $role = strtolower(session()->get('role'));
        if (!in_array($role, ['superadmin', 'admin_prodi'])) {
            return redirect()->to('/users')->with('error', 'Akses ditolak.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'Data tidak ditemukan.');
        }

        if ($role == 'admin_prodi' && $user['prodi_id'] != session()->get('prodi_id')) {
            return redirect()->to('/users')->with('error', 'Tidak berada di bawah wewenang Anda.');
        }

        // Proteksi: Superadmin tidak boleh dihapus oleh siapapun
        if (strtolower($user['role']) === 'superadmin') {
            return redirect()->to('/users')->with('error', 'Keamanan: Akun Superadmin dilarang dihapus!');
        }

        $this->userModel->delete($id);
        return redirect()->to('/users')->with('success', 'Pengguna berhasil dihapus.');
    }
}
