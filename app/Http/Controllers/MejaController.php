<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meja;
use Illuminate\Support\Facades\Validator;
class MejaController extends Controller
{
    public function add(){
        $id = Meja::orderBy('id_meja','desc')->first('id_meja');
        if(!$id){
            $data = Meja::create([
                'nama_meja'=>'MEJA 1'
            ]);
            if($data){
                return response()->json(['status'=>'success','message'=>'Sukses Tambah Meja'],200);
            }else{
                return response()->json(['status'=>'success','message'=>'Gagal Tambah Meja'],200);
            }
        }else{
            $data = Meja::create([
                'nama_meja'=>'MEJA '.$id->id_meja+1
            ]);
            if($data){
                return response()->json(['status'=>'success','message'=>'Sukses Tambah Meja'],200);
            }else{
                return response()->json(['status'=>'success','message'=>'Gagal Tambah Meja'],200);
            }
        }
    }
    public function get(Request $req)
    {
        if ($req->cari) {
            $data = Meja::where('nama_meja', 'like', '%' . $req->cari . '%')->count();
            if ($data > 0) {
                $data = Meja::where('nama_meja', 'like', '%' . $req->cari . '%')->paginate(5);
                return response()->json($data);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Tidak Ditemukan']);
            }
        } else {
            $data = Meja::orderBy('id_meja', 'asc')->paginate(5);
            return response()->json($data);
        }
    }

    public function semua()
    {
        $data = meja::orderBy('nama_meja', 'asc')->get();
        return response()->json($data);
    }
    

    public function tersedia(){
        $data = Meja::where('status','tersedia')->orderBy('id_meja','asc')->get();
        return response()->json($data);
    }

    public function terpakai(){
        $data = Meja::where('status','terpakai')->orderBy('id_meja','asc')->get();
        return response()->json($data);
    }

    public function pakai(Request $req, $id_meja){
        $data = Meja::find($id_meja);
        if($data){
            $validator = Validator::make($req->all(),[
                'pelanggan' => 'required',
            ],[
                'pelanggan.required' => 'Nama Pelanggan Dibutuhkan',
            ]);
                if ($validator->fails()){
                    return response()->json(['status'=>'error','message'=>$validator->errors()->toJson()]);
                }else{
                $data = Meja::where('id_meja',$id_meja)->update([
                    'status'=>'terpakai',
                    'pelanggan'=>$req->get('pelanggan')
                ]);
                    if($data){
                        return response()->json(['status'=>'success','message'=>'Sukses Pakai meja']);
                    }else{
                        return response()->json(['status'=>'success','message'=>'Gagal Pakai meja']);
                    }
                }
        }else{
            return response()->json(['status'=>'error','message'=>'Meja Tidak Ditemukan']);
        }
    }

    public function selesai($id_meja){
        $data = Meja::find($id_meja);
        if($data){
                $data = Meja::where('id_meja',$id_meja)->update([
                    'status'=>'tersedia',
                ]);
                    if($data){
                        return response()->json(['status'=>'success','message'=>'Sukses Kembalikan Meja'],200);
                    }else{
                        return response()->json(['status'=>'success','message'=>'Gagal Kembalikan Meja'],400);
                    }
                }return response()->json(['status'=>'error','message'=>'Meja Tidak Ditemukan'],404);
        }
}
