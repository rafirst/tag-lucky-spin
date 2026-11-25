<?php

namespace Database\Seeders;

use App\Models\Prize;
use Illuminate\Database\Seeder;

class PrizeSeeder extends Seeder
{
    /** Hadiah contoh (stok hanya placeholder, ubah lewat halaman). */
    public function run(): void
    {
        if (Prize::exists()) {
            return;
        }

        $rows = [
            ['Grand Prize', 1, true],
            ['Voucher Servis Berkala', 5, false],
            ['Gratis Oli TMO', 5, false],
            ['Merchandise Gazoo Racing', 3, false],
            ['E-Money', 5, false],
            ['Diskon Aksesoris', 10, false],
            ['Gratis Cuci Mobil', 10, false],
            ['Coba Lagi', 20, false],
        ];

        foreach ($rows as $i => [$name, $stock, $grand]) {
            Prize::create(['name' => $name, 'stock' => $stock, 'is_grand' => $grand, 'sort_order' => $i]);
        }
    }
}
