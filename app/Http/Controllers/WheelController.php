<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use App\Models\Winner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WheelController extends Controller
{
    /** Halaman utama: kirim data awal (hadiah + riwayat) langsung ke view. */
    public function index(): View
    {
        return view('wheel', [
            'boot' => [
                'prizes'       => Prize::forWheel()->map(fn (Prize $p) => $p->toWheel())->values(),
                'winners'      => Winner::latest('id')->limit(30)->get()
                    ->map(fn (Winner $w) => ['name' => $w->prize_name, 'is_grand' => $w->is_grand])
                    ->values(),
                'winnersTotal' => Winner::count(),
                'urls'         => [
                    'prizes'  => url('/prizes'),
                    'reorder' => url('/prizes/reorder'),
                    'spin'    => url('/spin'),
                    'winners' => url('/winners'),
                ],
            ],
        ]);
    }

    /**
     * Undi pemenang di SERVER (random_int, aman dari utak-atik browser),
     * simpan ke riwayat, lalu kurangi stok. JS hanya menganimasikan hasilnya.
     */
    public function spin(): JsonResponse
    {
         $result = DB::transaction(function () {
            // lockForUpdate: dua orang spin bersamaan tidak akan memakai stok yang sama
            $prizes = Prize::where('stock', '>', 0)
                ->orderBy('sort_order')->orderBy('id')
                ->lockForUpdate()->get();

            if ($prizes->isEmpty()) {
                abort(422, 'Belum ada hadiah di roda.');
            }

            $snapshot = $prizes->map(fn (Prize $p) => $p->toWheel())->values();
            $index    = random_int(0, $prizes->count() - 1);
            $winner   = $prizes[$index];
            $payload  = $winner->toWheel();

            Winner::create([
                'prize_id'   => $winner->id,
                'prize_name' => $winner->name,
                'is_grand'   => $winner->is_grand,
            ]);

            if ($winner->stock <= 1) {
                $winner->delete();
            } else {
                $winner->decrement('stock');
            }

            return ['index' => $index, 'winner' => $payload, 'snapshot' => $snapshot];
        });

        return response()->json($result + [
            'prizes'        => Prize::forWheel()->map(fn (Prize $p) => $p->toWheel())->values(),
            'winners_total' => Winner::count(),
        ]);
    }

    public function clearWinners(): Response
    {
        Winner::query()->delete();

        return response()->noContent();
    }
}
