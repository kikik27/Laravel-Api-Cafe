<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaksi;
use App\Models\Meja;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{

    public function transaksi(Request $req)
    {
        $user = Auth::user();
        $validator = Validator::make($req->all(), [
            'id_meja' => 'required',
            'pelanggan' => 'required',
        ], [
                'pelanggan.required' => 'Nama Pelanggan Dibutuhkan',
                'id_meja.required' => 'Mohon Pilih Meja',
            ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()]);
        } else {
            $save = Transaksi::create([
                'tanggal' => date('Y-m-d'),
                'kasir' => $user->id,
                'id_meja' => $req->get('id_meja'),
                'pelanggan' => $req->get('pelanggan'),
                'total' => $req->get('total'),
            ]);

            if ($save) {
                $data = Meja::find($req->get('id_meja'));
                if ($data) {
                    $validator = Validator::make($req->all(), [
                        'pelanggan' => 'required',
                    ], [
                            'pelanggan.required' => 'Nama Pelanggan Dibutuhkan',
                        ]);
                    if ($validator->fails()) {
                        return response()->json(['status' => 'error', 'message' => $validator->errors()->toJson()]);
                    } else {
                        $data = Meja::where('id_meja', $req->get('id_meja'))->update([
                            'status' => 'terpakai',
                            'pelanggan' => $req->get('pelanggan')
                        ]);
                        if ($data) {
                            return response()->json(['status' => 'success', 'message' => 'Transaksi Berhasil']);
                        } else {
                            return response()->json(['status' => 'success', 'message' => 'Gagal Pakai meja']);
                        }
                    }
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Meja Tidak Ditemukan'], 404);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Transaksi Gagal']);
            }
        }
    }

    public function get(Request $req)
    {
        if ($req->cari) {
            $data = Transaksi::where('id_transaksi',$req->cari)->join('users','Transaksi.kasir','=','users.id')->get();
            if ($data) {
                return response()->json($data);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Tidak Ditemukan']);
            }
        }elseif($req->kasir){
            $data = Transaksi::where('kasir',$req->kasir)->orderBy('id_transaksi', 'asc')->join('users','Transaksi.kasir','=','users.id')->get();
            if ($data) {
                return response()->json($data);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Tidak Ditemukan']);
            }        
        } elseif($req->start && $req->end){
            $start = $req->get('start');
            $end = $req->get('end');
    
            $data = Transaksi::whereBetween('tanggal', [$start, $end])->paginate(10);
            return response()->json($data);
        }
        else{
            $data = Transaksi::orderBy('id_transaksi', 'asc')->join('users','Transaksi.kasir','=','users.id')->paginate(10);
            return response()->json($data);
        }
    }

    public function pakaiMeja(Request $req)
    {
        $data = Meja::find($req->get('id_meja'));
        if ($data) {
            $validator = Validator::make($req->all(), [
                'pelanggan' => 'required',
            ], [
                    'pelanggan.required' => 'Nama Pelanggan Dibutuhkan',
                ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->toJson()], 400);
            } else {
                $data = Meja::where('id_meja', $req->get('id_meja'))->update([
                    'status' => 'terpakai',
                    'pelanggan' => $req->get('pelanggan')
                ]);
                if ($data) {
                    return response()->json(['status' => 'success', 'message' => 'Sukses Pakai meja']);
                } else {
                    return response()->json(['status' => 'success', 'message' => 'Gagal Pakai meja']);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Meja Tidak Ditemukan'], 404);
        }
    }


    public function detail(Request $req)
    {
        $dataPembelian = $req->input('dataPembelian');
        $id_transaksi = Transaksi::latest('id_transaksi')->first('id_transaksi')->id_transaksi;
        $count = count($dataPembelian);
        for ($i = 0; $i < $count; $i++) {
            DetailTransaksi::create([
                'id_transaksi' => $id_transaksi,
                'id_menu' => $dataPembelian[$i]['id_menu'],
                'qty' => $dataPembelian[$i]['qty'],
                'harga' => $dataPembelian[$i]['harga'],
                'total' => $dataPembelian[$i]['total'],
            ]);
        }
        return response()->json(['status' => 'success', 'message' => 'Transaksi Berhasil']);
    }

    public function bayar($id)
    {
        $data = Transaksi::find($id);
        if ($data) {
            $cek = Transaksi::where('id_transaksi', $data->id_transaksi)->update([
                'status' => 'lunas'
            ]);
            if ($cek) {
                $cek = Meja::where('id_meja', $data->id_meja)->update([
                    'status' => 'tersedia',
                    'pelanggan' => null,
                ]);
                if ($cek) {
                    return response()->json(['status' => 'success', 'message' => 'Pembayaran Berhasil']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Pembayaran Gagal Meja']);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Pembayaran Gagal']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Transaksi Not Found']);
        }
    }

    public function bill()
    {
        $user = Auth::user()->id;
        $data = Transaksi::where('status', 'belum_bayar')->where('kasir', $user)->join('users','Transaksi.kasir','=','users.id')->paginate(15);
        return response()->json($data);
    }

    public function getDetail($id)
    {
        $data = DetailTransaksi::where('id_transaksi', $id)->join('menu', 'detail_transaksi.id_menu', '=', 'menu.id_menu')->get();
        if ($data) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Transaksi Not Found']);
        }
    }

    public function GetByKasir(request $req)
    {   
        $id = $req->id;
        $data = Transaksi::where('status', 'belum_bayar')->where('kasir', $id)->join('users','Transaksi.kasir','=','users.id')->paginate(5);
        return response()->json($data);
    }

    public function GetbyDate(request $req){
        $start = $req->get('start');
        $end = $req->get('end');

        $data = Transaksi::whereBetween('date', [$start, $end])->get();
        return response()->json($data);
    }
}