<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => 'error', 'message' => 'Email / Password Salah'], );
            }
        } catch (JWTException $e) {
            return response()->json(['status' => 'error', 'message' => 'could_not_create_token']);
        }
        return response()->json(['status' => 'success', 'message' => 'Sukses Login', 'token' => $token]);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required'
        ], [
                'name.required' => 'Nama Pegawai Dibutuhkan !',
                'eamil.required' => 'Email Pegawai Dibutuhkan !',
                'email.string' => 'Email Berupa Huruf / Angka !',
                'email.email' => 'Format Email Harus Sesuai !',
                'email.unique' => 'Email Sudah Digunakan !',
                'password.required' => 'Password Pegawai Dibutuhkan !',
                'password.string' => 'Password Berupa Huruf / Angka !',
                'password.min' => 'Password Minimal 6 Karakter !',
            ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->toJson()]);
        }
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => $request->get('role'),
        ]);
        if ($user) {
            return response()->json(['status' => 'success', 'message' => 'Sukses Tambah Pegawai',]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal Tambah Pegawai',]);
        }
    }



    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return \Response::json(['status' => 'error', 'message' => 'user_not_found']);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return \Response::json(['status' => 'error', 'message' => 'token_expired']);
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return \Response::json(['status' => 'error', 'message' => 'token_invalid']);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return \Response::json(['status' => 'error', 'message' => 'token_absent'], );
        }
        return \Response::json(['status' => 'success', 'user' => $user]);
    }

    public function get(Request $req)
    {
        if ($req->cari) {
            $data = user::where('role', '!=', 'admin')->where('name', 'like', '%' . $req->cari . '%')->count();
            if ($data > 0) {
                $data = user::where('role', '!=', 'admin')->where('name', 'like', '%' . $req->cari . '%')->get();
                return response()->json($data);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Tidak Ditemukan']);
            }
        } else {
            $data = User::where('role', '!=', 'admin')->orderBy('name', 'asc')->paginate(5);
            return response()->json($data);
        }
    }

    public function semua()
    {
        $data = user::where('role', '!=', 'admin')->orderBy('name', 'asc')->get();
        return response()->json($data);
    }
    public function manager()
    {
        $data = user::where('role', 'manager')->orderBy('name', 'asc')->get();
        return response()->json($data);
    }

    public function kasir()
    {
        $data = user::where('role', 'kasir')->orderBy('name', 'asc')->get();
        return response()->json($data);
    }

    public function update(Request $req, $id)
    {
        $data = user::find($id);
        if ($data) {
            if ($req->get('password')) {
                $validator = Validator::make($req->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                    'password' => 'required|string|min:6',
                    'role' => 'required'
                ], [
                        'name.required' => 'Nama Pegawai Dibutuhkan !',
                        'eamil.required' => 'Email Pegawai Dibutuhkan !',
                        'email.string' => 'Email Berupa Huruf / Angka !',
                        'email.email' => 'Format Email Harus Sesuai !',
                        'password.required' => 'Password Pegawai Dibutuhkan !',
                        'password.string' => 'Password Berupa Huruf / Angka !',
                        'password.min' => 'Password Minimal 6 Karakter !',
                    ]);

                if ($validator->fails()) {
                    return response()->json(['status' => 'error', 'message' => $validator->errors()->toJson()]);
                } else {
                    $data = user::where('id', $id)->update([
                        'name' => $req->get('name'),
                        'email' => $req->get('email'),
                        'password' => Hash::make($req->get('password')),
                        'role' => $req->get('role'),
                    ]);
                    if ($data) {
                        return response()->json(['status' => 'success', 'message' => 'Berhasil Update Pegawai']);
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'Gagal Update Pegawai']);
                    }
                }
            } else {
                $validator = Validator::make($req->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                    'role' => 'required'
                ], [
                        'name.required' => 'Nama Pegawai Dibutuhkan !',
                        'email.required' => 'Email Pegawai Dibutuhkan !',
                        'email.string' => 'Email Berupa Huruf / Angka !',
                        'email.email' => 'Format Email Harus Sesuai !',
                    ]);

                if ($validator->fails()) {
                    return response()->json(['status' => 'error', 'message' => $validator->errors()->toJson()]);
                } else {
                    $data = user::where('id', $id)->update([
                        'name' => $req->get('name'),
                        'email' => $req->get('email'),
                        'role' => $req->get('role'),
                    ]);
                    if ($data) {
                        return response()->json(['status' => 'success', 'message' => 'Berhasil Update Pegawai']);
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'Gagal Update Pegawai']);
                    }
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Pegawai Tidak Ditemukan']);
        }
    }
    public function delete($id)
    {
        $data = user::find($id);
        if ($data) {
            $data = user::where('id', $id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Berhasil Hapus Pegawai']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal Hapus Pegawai']);
        }
    }

    public function UbahProfile(Request $req){
            $id = Auth::user()->id;
            $data = user::where('id', $id)->update([
                'name' => $req->get('name'),
            ]);
            if ($data) {
                return response()->json(['status' => 'success', 'message' => 'Sukses Ubah Profile']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Gagal ']);
        }
    }
}