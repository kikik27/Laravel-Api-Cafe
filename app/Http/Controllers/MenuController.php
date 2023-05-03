<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function get(Request $req)
    {
        if ($req->cari) {
            $data = Menu::where('nama_menu', 'like', '%' . $req->cari . '%')->count();
            if ($data > 0) {
                $data = Menu::where('nama_menu', 'like', '%' . $req->cari . '%')->orderBy('id_menu', 'asc')->paginate(5);
                return response()->json($data);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Tidak Ditemukan']);
            }
        } else {
            $data = Menu::orderBy('id_menu', 'asc')->paginate(5);
            return response()->json($data);
        }
    }

    public function semua()
    {
        $data = Menu::orderBy('id_menu', 'asc')->paginate(5);
        return response()->json($data);
    }

    public function id($id_menu)
    {
        $data = Menu::where('id_menu', $id_menu)->paginate(5);
        if ($data) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data Tidak Ditemukan']);
        }
    }

    public function makanan()
    {
        $data = Menu::where('jenis', 'makanan')->orderBy('id_menu', 'asc')->paginate(5);
        return response()->json($data);
    }

    public function minuman()
    {
        $data = Menu::where('jenis', 'minuman')->orderBy('id_menu', 'asc')->paginate(5);
        return response()->json($data);
    }

    public function add(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'nama_menu' => 'required',
            'jenis' => 'required',
            'desc' => 'required',
            'harga' => 'required|numeric',
            'gambar' => 'required|mimes:png,jpg||max:2048',
        ], [
                'id_menu.required' => 'ID Menu Diperlukan',
                'id_menu.unique' => 'ID Menu Tidak Boleh Sama',
                'nama_menu.required' => 'Nama Menu Diperlukan',
                'jenis.required' => 'Jenis Menu Diperlukan',
                'desc.required' => 'Deskripsi Menu Diperlukan',
                'harga.required' => 'Harga Diperlukan',
                'harga.numeric' => 'Masukkan Harga Yang Sesuai',
                'gambar.required' => 'Gambar Menu Diperlukan',
                'gambar.mimes' => 'Hanya Menerima JPG\PNG',
                'gambar.max' => 'File Tidak Boleh Melebihi 2MB',
            ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->toJson()]);
        } else {
            $file = $req->file('gambar');
            $nama = time() . '.' . $file->getClientOriginalExtension();
            $gambarPath = $file->storeAs('public/gambar/menu', $nama);
            $gambarName = basename($gambarPath);
            $save = Menu::create([
                'id_menu' => $this->idMenu(),
                'nama_menu' => $req->get('nama_menu'),
                'jenis' => $req->get('jenis'),
                'desc' => $req->get('desc'),
                'harga' => $req->get('harga'),
                'gambar' => $gambarName
            ]);

            if ($save) {
                return response()->json(['status' => 'success', 'message' => 'Berhasil Tambah Menu'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Gagal Tambah Menu'], 400);
            }
        }
    }

    public function delete($id_menu)
    {
        $data = Menu::find($id_menu);
        Storage::delete('public/gambar/menu/' . $data->gambar);
        $data = Menu::where('id_menu', $id_menu)->delete();
        if ($data) {
            return response()->json(['status' => 'success', 'message' => 'Berhasil Hapus Menu']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal Hapus Menu']);
        }
    }

    public function update(Request $req, $id_menu)
    {
        $data = Menu::find($id_menu);
        if ($data) {
            $validator = Validator::make($req->all(), [
                'nama_menu' => 'required',
                'jenis' => 'required',
                'desc' => 'required',
                'harga' => 'required|numeric',
            ], [
                    'nama_menu.required' => 'Nama Menu Diperlukan',
                    'jenis.required' => 'Jenis Menu Diperlukan',
                    'desc.required' => 'Deskripsi Menu Diperlukan',
                    'harga.required' => 'Harga Diperlukan',
                    'harga.numeric' => 'Masukkan Harga Yang Sesuai',
                ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->toJson()]);
            } else {
                if ($req->hasFile('gambar')) {
                    $del = Storage::delete('public/gambar/menu/' . $data->gambar);
                    if ($del) {
                        $file = $req->file('gambar');
                        $nama = time() . '.' . $file->getClientOriginalExtension();
                        $gambarPath = $file->storeAs('public/gambar/menu', $nama);
                        $gambarName = basename($gambarPath);

                        $data = Menu::where('id_menu', $id_menu)->update([
                            'nama_menu' => $req->get('nama_menu'),
                            'jenis' => $req->get('jenis'),
                            'desc' => $req->get('desc'),
                            'harga' => $req->get('harga'),
                            'gambar' => $gambarName,
                        ]);

                        if ($data) {
                            return response()->json(['status' => 'success', 'message' => 'Berhasil Update Menu']);
                        } else {
                            return response()->json(['status' => 'error', 'message' => 'Gagal Update Menu']);
                        }
                    }
                    return response()->json(['status' => 'error', 'message' => 'Hapus Gagal',]);
                } else {

                    $data = Menu::where('id_menu', $id_menu)->update([
                        'nama_menu' => $req->get('nama_menu'),
                        'jenis' => $req->get('jenis'),
                        'desc' => $req->get('desc'),
                        'harga' => $req->get('harga'),


                    ]);
                    if ($data) {
                        return response()->json(['status' => 'success', 'message' => 'Berhasil Update Menu']);
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'Gagal Update Menu']);
                    }

                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data Menu Tidak Ditemukan']);
        }
    }

    public function Upload(Request $req, $id_menu)
    {
        $data = Menu::find($id_menu);
        if ($data) {
            $validator = Validator::make($req->all(), [
                'gambar' => 'required|mimes:png,jpg||max:2048',
            ], [
                    'gambar.required' => 'Gambar Menu Diperlukan',
                    'gambar.mimes' => 'Hanya Menerima JPG\PNG',
                    'gambar.max' => 'File Tidak Boleh Melebihi 2MB',
                ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->toJson()], 400);
            } else {
                if ($data->gambar) {
                    $del = Storage::delete('public/gambar/menu/' . $data->gambar);
                    if ($del) {
                        $file = $req->file('gambar');
                        $nama = time() . '.' . $file->getClientOriginalExtension();
                        $gambarPath = $file->storeAs('public/gambar/menu', $nama);
                        $gambarName = basename($gambarPath);

                        $upload = Menu::where('id_menu', $id_menu)->update([
                            'gambar' => $gambarName,
                        ]);

                        if ($upload) {
                            return response()->json(['status' => 'success', 'message' => 'Upload Berhasil 1', $data], 200);
                        } else {
                            return response()->json(['status' => 'error', 'message' => 'Upload Gagal 1', $upload], 400);
                        }
                    }
                    return response()->json(['status' => 'error', 'message' => 'Hapus Gagal',], 400);
                } else {
                    $file = $req->file('gambar');
                    $nama = time() . '.' . $file->getClientOriginalExtension();
                    $gambarPath = $file->storeAs('public/gambar/menu', $nama);
                    $gambarName = basename($gambarPath);

                    $upload = Menu::where('id_menu', $id_menu)->update([
                        'gambar' => $gambarName,
                    ]);

                    if ($upload) {
                        return response()->json(['status' => 'success', 'message' => 'Upload Berhasil 2', $data], 200);
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'Upload Gagal', $data], 400);
                    }
                }
            }
        } else {

        }
    }


    public function idMenu()
    {
        $lastMenu = Menu::orderBy('id_menu', 'desc')->first();
        $lastId = $lastMenu ? substr($lastMenu->id_menu, 7) : 0;
        $newId = sprintf("%04d", $lastId + 1);
        $newMenuId = 'MENU' . $newId;
        return $newMenuId;
    }

}