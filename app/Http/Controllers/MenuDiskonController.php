<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuDiskon;
use App\Models\Diskon;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuDiskonController extends Controller
{
    public function store(Request $request)
    {

        if (Auth::user()->role !== 'admin_stan') {
            return response()->json(['message' => 'Access denied. Only Stan users can perform this action.'], 403);
        } else {
            $request->validate([
                'menu_id' => 'required|exists:menus,id',
                'diskon_id' => 'required|exists:diskons,id',
            ]);

            $menu = Menu::findOrFail($request->menu_id);
            $diskon = Diskon::findOrFail($request->diskon_id);

            // Save original price if not already saved
            if (!$menu->harga_asli) {
                $menu->harga_asli = $menu->harga;
            }

            // Calculate discount
            $discountAmount = $menu->harga_asli * ($diskon->persentase_diskon / 100);
            $menu->harga = $menu->harga_asli - $discountAmount;

            $menu->save();

            // Save the relationship
            // $menu->diskons()->syncWithoutDetaching([$diskon->id]);

            return response()->json([
                'message' => 'Diskon applied to menu successfully',
                'menu' => $menu
            ]);
        }
    }

    public function destroy($menuId, $diskonId)
    {
        $menu = Menu::findOrFail($menuId);
        $menu->diskons()->detach($diskonId);

        // Recalculate harga if needed (e.g. no more diskon)
        if ($menu->diskons()->count() == 0 && $menu->harga_asli) {
            $menu->harga = $menu->harga_asli;
            $menu->save();
        }

        return response()->json(['message' => 'Diskon removed from menu.']);
    }
}
