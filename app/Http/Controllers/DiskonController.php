<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diskon;
use Illuminate\Support\Facades\Auth;

class DiskonController extends Controller
{
    // Ensure only stan users can access these routes
    // public function __construct()
    // {
    //     $this->middleware('auth:api'); // Ensure the user is authenticated
    // } 

    // Create a new discount
    public function createDiskon(Request $request)
    {

        if (Auth::user()->role !== 'admin_stan') {
            return response()->json(['message' => 'Access denied. Only Stan users can perform this action.'], 403);
        } else {

            $request->validate([
                'nama_diskon' => 'required',
                'persentase_diskon' => 'required|numeric',
                'tanggal_awal' => 'required|date',
                'tanggal_akhir' => 'required|date|after:tanggal_awal',
                'id_stan' => 'required|numeric',
            ]);

            $diskon = Diskon::create([
                'nama_diskon' => $request->nama_diskon,
                'persentase_diskon' => $request->persentase_diskon,
                'tanggal_awal' => $request->tanggal_awal,
                'tanggal_akhir' => $request->tanggal_akhir,
                'id_stan' => $request->id_stan, // Assign the logged-in stan
            ]);

            if (!$diskon) {
                return response()->json(['message' => 'Failed to create diskon'], 400);
            }

            return response()->json(['message' => 'Diskon created successfully', 'diskon' => $diskon], 201);
        }
    }

    // Retrieve all discounts by the stan
    public function getDiskons()
    {
        $user = Auth::user();
        if ($user->role !== 'admin_stan') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $diskons = Diskon::where('id_stan', $user->id)->with('menus')->get();
        return response()->json($diskons);
    }

    // Update a discount
    public function updateDiskon(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin_stan') {
            return response()->json(['message' => 'Access denied. Only Stan users can perform this action.'], 403);
        } else {
            // Validate input data
            $request->validate([
                'nama_diskon' => 'required',
                'persentase_diskon' => 'required|numeric',
                'tanggal_awal' => 'required|date',
                'tanggal_akhir' => 'required|date|after:tanggal_awal',
            ]);

            // Find the menu item
            $diskon = Diskon::find($id);
            if (!$diskon) {
                return response()->json(['message' => 'Diskon item not found'], 404);
            }

            // Update data
            $diskon->nama_diskon = $request->nama_diskon;
            $diskon->persentase_diskon = $request->persentase_diskon;
            $diskon->tanggal_awal = $request->tanggal_awal;
            $diskon->tanggal_akhir = $request->tanggal_akhir;

            // Save updated dis$diskon
            $diskon->save();

            return response()->json([
                'message' => 'dis$diskon updated successfully',
                'dis$diskon' => $diskon
            ], 200);
        }
    }

    // Delete a discount
    public function deletediskon($id)
    {
        // Find the diskon item
        if (Auth::user()->role !== 'admin_stan') {
            return response()->json(['message' => 'Access denied. Only Stan users can perform this action.'], 403);
        } else {
            $diskon = diskon::find($id);
            if (!$diskon) {
                return response()->json(['message' => 'diskon item not found'], 404);
            }
            // Delete the diskon item
            $diskon->delete();

            return response()->json(['message' => 'Diskon deleted successfully'], 200);
        }
    }
}
