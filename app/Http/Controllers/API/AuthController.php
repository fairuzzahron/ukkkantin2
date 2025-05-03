<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Stan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class authController extends Controller
{

    public function registerSiswa(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:siswa',
            'nama_siswa' => 'required',
            'alamat' => 'required',
            'telp' => 'required',
            'foto' => 'nullable',
        ]);

        // Check if user already exists
        $existingUser = User::where('email', $request->email)
            ->orWhere('username', $request->username)
            ->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'Email already exists'
            ], 409);
        }

        // Hash the password before creating user
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $siswa = Siswa::create([
            'nama_siswa' => $request->nama_siswa,
            'alamat' => $request->alamat,
            'telp' => $request->telp,
            'id_user' => $user->id,
            'foto' => $request->foto,
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user, 'siswa' => $siswa], 201);
    }

    public function registerStan(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'nama_stan' => 'required',
            'role' => 'required|in:admin_stan',
            'nama_pemilik' => 'required',
            'telp' => 'required',
        ]);

        $existingUser = User::where('email', $request->email)
            ->orWhere('username', $request->username)
            ->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'Email already exists'
            ], 409);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        $stan = Stan::create([
            'nama_stan' => $request->nama_stan,
            'nama_pemilik' => $request->nama_pemilik,
            'telp' => $request->telp,
            'id_user' => $user->id,
        ]);



        return response()->json(['message' => 'User registered successfully', 'user' => $user, 'stan' => $stan], 201);
    }

    public function loginSiswa(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!$token = Auth::attempt(array_merge($credentials, ['role' => 'siswa']))) {
            return response()->json(['message' => 'Siswa not found'], 401);
        }

        $user = Auth::user();
        $siswa = Siswa::where('id_user', $user->id)->first();

        if (!$siswa) {
            return response()->json(['message' => 'Siswa not found'], 404);
        }

        return response()->json([
            'user' => $user,
            'siswa' => $siswa,
            'token' => $token
        ]);
    }

    public function loginStan(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!$token = Auth::attempt(array_merge($credentials, ['role' => 'admin_stan']))) {
            return response()->json(['message' => 'Stan not found'], 401);
        }

        $user = Auth::user();
        $admin_stan = Stan::where('id_user', $user->id)->first();

        if (!$admin_stan) {
            return response()->json(['message' => 'Email or password is incorrect'], 404);
        }

        return response()->json([
            'user' => $user,
            'stan' => $admin_stan,
            'token' => $token
        ]);
    }

    public function resetPassword(Request $request,$id_user)
    {
        $user = User::findOrFail($id_user);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }   
        $newPassword = $request->new_password; // choose a new password
        $user->password = Hash::make($newPassword);
        $user->save();

        return response()->json([
            'message' => 'Password has been reset.',
            'new_password' => $newPassword, // ⚠️ Don't return this in production!
        ]);
    }

    public function updateStan(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin_stan') {
            return response()->json(['message' => 'Access denied. Must login to perform this action.'], 403);
        } else {
            // Find the user by ID
            // $user = User::findOrFail($id);
            $stan = Stan::where('id', $id)->firstOrFail();

            // Validate the incoming request
            $request->validate([
                // 'username' => 'nullable',
                // 'email' => 'nullable|email',
                // 'password' => 'nullable',
                // 'role' => 'nullable|in:admin_stan',
                'nama_stan' => 'nullable',
                'nama_pemilik' => 'nullable',
                'telp' => 'nullable|numeric',
            ]);

            // Update the stan data
            $stan->update([
                'nama_stan' => $request->nama_stan ?? $stan->nama_stan,
                'nama_pemilik' => $request->nama_pemilik ?? $stan->nama_pemilik,
                'telp' => $request->telp ?? $stan->telp,
            ]);

            $stan->save();

            return response()->json(['message' => 'Stan updated successfully', 'stan' => $stan], 200);
        }
    }

    public function deleteSiswa($id)
    {
        // Find the menu item
        $siswa = Siswa::find($id);
        if (!$siswa) {
            return response()->json(['message' => 'Siswa not found'], 404);
        }

        // Get the associated user
        $user = User::find($siswa->id_user);
        if (!$user) {
            return response()->json(['message' => 'Associated user not found'], 404);
        }

        try {
            // Start transaction
            DB::beginTransaction();

            // Delete the siswa first (due to foreign key constraint)
            $siswa->delete();

            // Delete the associated user
            $user->delete();

            // Commit transaction
            DB::commit();

            return response()->json(['message' => 'siswa and associated user account deleted successfully'], 200);
        } catch (\Exception $e) {
            // Rollback transaction if any error occurs
            DB::rollback();
            return response()->json(['message' => 'Error deleting siswa and user account'], 500);
        }
        // Delete the associated image file if exists
        // if ($siswa->foto && file_exists(public_path($siswa->foto))) {
        //     unlink(public_path($siswa->foto));
        // }

        // Delete the menu item
        $siswa->delete();

        return response()->json(['message' => 'Siswa account deleted successfully'], 200);
    }

    public function deleteStan($id)
    {
        // Find the stan
        $stan = Stan::find($id);
        if (!$stan) {
            return response()->json(['message' => 'Stan not found'], 404);
        }

        // Get the associated user
        $user = User::find($stan->id_user);
        if (!$user) {
            return response()->json(['message' => 'Associated user not found'], 404);
        }

        try {
            // Start transaction
            DB::beginTransaction();

            // Delete the stan first (due to foreign key constraint)
            $stan->delete();

            // Delete the associated user
            $user->delete();

            // Commit transaction
            DB::commit();

            return response()->json(['message' => 'Stan and associated user account deleted successfully'], 200);
        } catch (\Exception $e) {
            // Rollback transaction if any error occurs
            DB::rollback();
            return response()->json(['message' => 'Error deleting stan and user account'], 500);
        }
    }

    public function logout()
    {
        try {
            Auth::logout(); // Invalidate the token

            return response()->json(['message' => 'User logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to logout'], 500);
        }
    }
}
