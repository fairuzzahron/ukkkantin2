<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Diskon;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.menu_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $menuIds = collect($request->items)->pluck('menu_id');
            $menus = Menu::whereIn('id', $menuIds)->get();

            $uniqueStanIds = $menus->pluck('stan_id')->unique();
            if ($uniqueStanIds->count() > 1) {
                return response()->json([
                    'error' => 'Siswa hanya bisa memesan dari satu stan dalam satu transaksi.'
                ], 400);
            }

            $stanId = $uniqueStanIds->first();
            $today = now()->toDateString();
            $diskon = Diskon::where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->first();

            $siswaId = auth()->user()->id;

            $transaksi = Transaksi::create([
                'siswa_id' => $siswaId,
                'stan_id' => $stanId,
                'status' => 'belum dikonfirmasi',
            ]);

            foreach ($request->items as $item) {
                $menu = $menus->where('id', $item['menu_id'])->first();
                $harga = $menu->harga;
                $harga_diskon = $diskon ? $harga - ($harga * $diskon->persentase / 100) : $harga;

                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'menu_id' => $item['menu_id'],
                    'jumlah' => $item['quantity'],
                    'harga' => $harga_diskon,
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Transaksi berhasil dibuat.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal membuat transaksi.'], 500);
        }
    }

    public function statusBySiswa($siswa_id)
    {
        $transaksis = Transaksi::where('siswa_id', $siswa_id)
            ->with(['detailTransaksi.menu'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($transaksis);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:belum dikonfirmasi,dimasak,diantar,selesai'
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = $request->status;
        $transaksi->save();

        return response()->json(['message' => 'Status transaksi diperbarui.']);
    }
}
