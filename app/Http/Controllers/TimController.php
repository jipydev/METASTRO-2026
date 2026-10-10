<?php

namespace App\Http\Controllers;

use App\Models\AnggotaTim;
use App\Models\Divisi;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;

class TimController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $timQuery = Tim::with('guiders')
            ->when($search !== '', fn ($query) => $query->where('nama', 'like', "%{$search}%"));
        $jumlahTim = (clone $timQuery)->count();

        $data = [
            'title' => 'Manajemen Tim & Guider',
            'tims' => $timQuery->paginate(10)->withQueryString(),
            'jumlahTim' => $jumlahTim,
            'search' => $search,
        ];

        return view('tim.index', $data);
    }

    public function peserta(Request $request)
    {
        $teamData = [
            ['nama' => 'Axis', 'members' => [
                'Daniessa Aurelia Pentury', 'Alya Zalfa Khairunnisa', 'Syeirhan Rafsyadha Ghaizan',
                'Abiyyu Rafa Ramadhan', 'Ukhtia Faudzil Iffah Assyifa', 'Muhammad Rifqi Fadhila',
                'Rangga Darma Eka Saputra', 'Doni Rayhan Nugraha', 'Raihan Aprilian',
                'Muhammad Hadin Aditya Utama', 'Noval Ghafa Alfarizi', 'Devlin Muzhaffar',
            ]],
            ['nama' => 'Vector', 'members' => [
                'Mohamad Nur Ramadani', 'Aditya Alamsah', 'Aldo Rafalino', 'Dena Assifa',
                'Maulana Resta Apriliansyah', 'Haura Ziba Karniya', 'Ahmad Zahran Fiqih',
                'Rian Permana', 'Kezia Olivia Siagian', 'Ibrahim Sahl Akbar Jarullah',
                'Firmansah Hijriyah Alimudin', 'Danny Magalih Abielo Wudd',
            ]],
            ['nama' => 'Torque', 'members' => [
                'Ahmad Robeth Jundan Furqoni', 'Hazel Dide Febrano', 'Denis Dwi Fratama',
                'Siti Mutia', 'Maitsa Syams Al Baihaqi', 'Dhowy Harits Attarbanggi',
                'Muhammad Dhafi Alytri', 'Fathir Muhammad Fauzi', "Fa'iq Fadhlillah Saputra",
                'Muhammad Abdullah Assajid', 'Radena Faustine Az-Zuhrah Jusin', 'Wahyu Novianto',
            ]],
            ['nama' => 'Momentum', 'members' => [
                'Faqih Khairy Fathan', 'M Rajwa Sidqi Musali', 'Muhammad Zidan Fawwaz Alkhtabi',
                'Zaskia Bentang Fitri Ramadani', 'Muhammad Alief Dhiaulhaq',
                "Dyrlan Sultan Al'aidin Ardhiyanto", 'Naila Munawaroh',
                'Raka Abdul Rais Al Rezi', 'Muhammad Azhar Fauzan', 'Naisha Camila Shahnaz',
                'Muh Nabil Najhan Masykur', 'Genta Cakrawala Kurniawan',
            ]],
            ['nama' => 'Force', 'members' => [
                'Candra Aditiya', 'Muhammad Hilman Fauzy', 'Aril Alfazri', 'Syifa Amelia Oktafiani',
                'Fachry Andhika Purnomo', 'Ezra Ariq Athallah', 'Syazwina Izzati Aisyah',
                'Ahmad Fakhri Fauzan', "Khoerunnisaa' Shofaa' Dzakiyyah", 'Dimy Awan Wicaksana',
                'Farras Al Bari', 'Raihan Muhafiz Dewanto',
            ]],
            ['nama' => 'Impulse', 'members' => [
                'Zalfaa Zahra Asyifa', 'Alfata Dzaky Ramadhan', 'Muhammad Andhika Pratama' ,
                'Danendra Alif Raditya', 'Diaz Dwi Pamungkas', 'Farel Tristandio Saputro',
                'Anju Alfrido Hutagaol', 'Daffa Dwi Putra', 'Muhammad Fakhri Abiyyu Earlyansyah',
                'Indah Putri Ramadhani', 'Carissa Hananiah Khumaeroh',
            ]],
            ['nama' => 'Inertia', 'members' => [
                'Fathurrohman Sidiq', 'Hafizh Atha Zulyomi', 'Khairu Fakhri Al Fatih',
                'Muhammad Ibnu rizky', 'Muhammad Fahmi Faturrahman', 'Muhammad Faqih Taqiudin',
                'Khanza Aufa Althafunnisa', 'Hanif Huwaidi Maajid', 'Mochammad Rasya Keyzano',
                'Hazimah Fathena', 'Moses Mahardika Nugroho', 'Prinsa Nadifa Alma As Shofi',
            ]],
            ['nama' => 'Flux', 'members' => [
                'Adrian Fatih Nur Muhammad', 'Muhammad Fauzi Arifin', 'Yunik Arika',
                'Muhamad Alkausar Putra Surya',
                'Muhammad Haidar Fadhilah', 'Sulthan Ahmadiningrat',
                'Muhammad Rasya Fahri', 'Muhammad Irsyad', 'Citra Azzahra', 'Alman Fathin',
                'Muhammad Miftah Al-Anshori' ,
            ]],
            ['nama' => 'Accel', 'members' => [
                'Muhammad Rasya Antebing Mame', 'Muhammad Syahri Abdul Rouf', 'Natasya Atalia Labita',
                'Muhammad Nanda Alfaridzi', 'Muhammad Rasheed Muhyiddien', 'NABILA ZIFA ZULKARNAIN',
                'Muhammad Labieb Al Amien', 'Rakha Sulaiman', 'Nayaka Fadhil Prasetyo',
                'Zauja Ummu Aliyya. HP', 'Owen Romega Perwira. S', 'Muhammad Hazmi Al Farizi'
            ]],
            ['nama' => 'Radius', 'members' => [
                'Muhammad Attar Purnama', 'Ahmad Yusuf Salim', 'Raja Maulana',
                'Muhammad Haikal Fadhilah', 'Yuki Detta Sabina', 'Banu Riyadi',
                'Tristan Abdillah Rainaldi', 'Muhammad Jilan Adly Mufid', 'Zahida Zahira',
                'Rafael Alexander Sitompul', 'Belina Zaskia Mulya',
            ]],
        ];

        $membersByTeam = collect($teamData)->keyBy('nama');
        $tims = Tim::with('guiders')->get();

        $tims->each(function (Tim $tim) use ($membersByTeam) {
            $memberNames = $membersByTeam->get($tim->nama)['members'] ?? [];
            $members = collect($memberNames)
                ->map(fn (string $name) => new User(['nama' => $name]));

            $tim->setRelation('members', $members);
        });

        $data = [
            'title' => 'Daftar Tim',
            'tims' => $tims,
        ];

        return view('tim.peserta', $data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:100',
        ], [
            'nama.required' => 'Nama tim harus diisi.',
            'nama.string' => 'Nama tim harus berupa teks.',
            'nama.max' => 'Nama tim tidak boleh lebih dari 100 karakter.',
        ]);

        $validatedData['slug'] = \Str::slug($validatedData['nama'], '-');

        Tim::create($validatedData);

        return redirect()->route('dashboard.tim.index')->with('success', 'Tim baru berhasil dibuat.');
    }

    public function show(string $slug)
    {
        $tim = Tim::where('slug', $slug)->with('guiders', 'members')->firstOrFail();

        $guiders = Divisi::query()->where('nama', 'Guider')->firstOrFail()->users;

        $pesertas = Role::query()
            ->where('name', 'Peserta')
            ->firstOrFail()
            ->users()
            ->whereDoesntHave('tims')
            ->get();

        $data = [
            'title' => 'Detail Tim',
            'tim' => $tim,
            'guiders' => $guiders,
            'pesertas' => $pesertas
        ];

        return view('tim.show', $data);
    }

    public function update(Request $request, string $slug)
    {
        $tim = Tim::where('slug', $slug)->firstOrFail();

        $validatedData = $request->validate([
            'nama' => 'required|string|max:100',
        ], [
            'nama.required' => 'Nama tim harus diisi.',
            'nama.string' => 'Nama tim harus berupa teks.',
            'nama.max' => 'Nama tim tidak boleh lebih dari 100 karakter.',
        ]);

        $validatedData['slug'] = \Str::slug($validatedData['nama'], '-');

        $tim->update($validatedData);

        return redirect()->back()->with('success', 'Data tim berhasil diperbarui.');
    }

    public function destroy(string $slug)
    {
        $tim = Tim::where('slug', $slug)->firstOrFail();
        $tim->delete();

        return redirect()->route('dashboard.tim.index')->with('success', 'Tim berhasil dihapus.');
    }
}
