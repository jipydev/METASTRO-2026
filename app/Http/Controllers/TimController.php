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
