<?php

namespace App\Http\Controllers;

use App\Models\Guider;
use App\Models\Tim;
use Illuminate\Http\Request;

class GuiderController extends Controller
{
    public function update(Request $request, int $id)
    {
        $tim = Tim::with('guiders')->findOrFail($id);

        if ($request->input('action') === 'remove_guider') {
            $request->validate([
                'guider_id' => 'required|integer',
            ]);

            $tim->guiders()->whereKey($request->integer('guider_id'))->delete();
        } else {
            $validatedData = $request->validate([
                'pembimbing_id' => 'required|exists:users,id',
            ], [
                'pembimbing_id.required' => 'Pilih seorang guider.',
                'pembimbing_id.exists' => 'Guider yang dipilih tidak valid.',
            ]);

            if ($tim->guiders()->where('pembimbing_id', $validatedData['pembimbing_id'])->exists()) {
                return back()->withErrors(['pembimbing_id' => 'Guider tersebut sudah ditugaskan pada tim ini.']);
            }

            if ($tim->guiders()->count() >= 2) {
                return back()->withErrors(['pembimbing_id' => 'Tim ini sudah memiliki dua guider.']);
            }

            Guider::create([
                'tim_id' => $tim->id,
                'pembimbing_id' => $validatedData['pembimbing_id'],
            ]);
        }

        return redirect()->route('dashboard.tim.show', $tim->slug)
            ->with('success', 'Data guider berhasil diperbarui.');
    }
}
