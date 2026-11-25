<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PrizeController extends Controller
{
    private const MAX_PRIZES = 40;

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:40'],
            'stock' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        if (Prize::count() >= self::MAX_PRIZES) {
            abort(422, 'Maksimal ' . self::MAX_PRIZES . ' hadiah.');
        }

        $prize = Prize::create([
            'name'       => trim($data['name']),
            'stock'      => $data['stock'],
            'sort_order' => (Prize::max('sort_order') ?? -1) + 1,
        ]);

        return response()->json($prize->toWheel(), 201);
    }

    /** Tombol ★ : tandai / lepas hadiah utama. */
    public function update(Request $request, Prize $prize): JsonResponse
    {
        $data = $request->validate(['is_grand' => ['required', 'boolean']]);

        $prize->update(['is_grand' => (bool) $data['is_grand']]);

        return response()->json($prize->toWheel());
    }

    public function destroy(Prize $prize): Response
    {
        $prize->delete();

        return response()->noContent();
    }

    /** Tombol "Kosongkan". Riwayat pemenang tetap aman (prize_id jadi NULL). */
    public function clear(): Response
    {
        Prize::query()->delete();

        return response()->noContent();
    }

    /** Drag & drop: terima daftar id hadiah sesuai urutan baru. */
    public function reorder(Request $request): Response
    {
        $data = $request->validate([
            'order'   => ['required', 'array', 'max:' . self::MAX_PRIZES],
            'order.*' => ['integer', 'distinct', 'exists:prizes,id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['order'] as $position => $id) {
                Prize::whereKey($id)->update(['sort_order' => $position]);
            }
        });

        return response()->noContent();
    }
}
