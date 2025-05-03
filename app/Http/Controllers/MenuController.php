<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Stan;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function createMenu(Request $request)
    {
        $user = Auth::user();
        $stan = $user->Stan;
        // dd($user);

        if ($user->role !== 'admin_stan') {
            return response()->json(['message' => 'Access denied. Only Stan users can perform this action.'], 403);
        }

        $request->validate([
            'nama_makanan' => 'required',
            'harga' => 'required|numeric',
            'jenis' => 'required|in:makanan,minuman',
            'foto' => 'nullable',
            'deskripsi' => 'required',
        ]);


        if (!$stan) {
            return response()->json(['message' => 'Stan not found for this user'], 404);
        }

        $menu = Menu::create([
            'nama_makanan' => $request->nama_makanan,
            'harga' => $request->harga,
            'harga_asli' => $request->harga,
            'jenis' => $request->jenis,
            'foto' => $request->foto,
            'deskripsi' => $request->deskripsi,
            'id_stan' => $user->Stan->id,
        ]);


        return response()->json(['message' => 'Menu created successfully', 'menu' => $menu], 201);
    }


    public function updateMenu(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_stan') {
            return response()->json(['message' => 'Access denied. Only Stan users can perform this action.'], 403);
        }

        $request->validate([
            'nama_makanan' => 'required',
            'harga' => 'required|numeric',
            'jenis' => 'required|in:makanan,minuman',
            'foto' => 'nullable',
            'deskripsi' => 'required',
        ]);



        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Menu item not found'], 404);
        }

        // Prohibit updating menus that belong to other stans
        if ($menu->id_stan !== $user->stan_id) {
            return response()->json(['message' => 'Unauthorized to update this menu'], 403);
        }

        // Update allowed fields
        $menu->nama_makanan = $request->nama_makanan;
        $menu->harga = $request->harga;
        $menu->harga_asli = $request->harga;
        $menu->jenis = $request->jenis;
        $menu->foto = $request->foto;
        $menu->deskripsi = $request->deskripsi;

        $menu->save();

        return response()->json([
            'message' => 'Menu updated successfully',
            'menu' => $menu
        ], 200);
    }


    public function deleteMenu($id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_stan') {
            return response()->json(['message' => 'Access denied. Only Stan users can perform this action.'], 403);
        }

        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Menu item not found'], 404);
        }

        if ($menu->id_stan !== $user->stan_id) {
            return response()->json(['message' => 'Unauthorized to delete this menu'], 403);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu deleted successfully'], 200);
    }

    public function getmenuStan()
    {
        $user = Auth::user();
        $menus = Menu::where('id_stan', $user->Stan->id_stan)->get();

        if ($menus->isEmpty()) {
            return response()->json(['message' => 'No menus found for this stan'], 404);
        }

        return response()->json($menus);
    }
}
