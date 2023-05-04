<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Menu;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DaateTime;

class DashboardController extends Controller
{
    public function CountMejaTersedia()
    {
        $MejaTersedia = Meja::where('status', 'tersedia')->count();
        return $MejaTersedia;
    }

    public function CountMejaTerpakai()
    {
        $MejaTerpakai = Meja::where('status', 'terpakai')->count();
        return $MejaTerpakai;
    }

    public function CountKasir()
    {
        $Kasir = User::where('role','!=', 'admin')->count();
        return $Kasir;
    }

    public function CountPendapatan()
    {
        $bulan_ini = date('Y-m');
        $Pendapatan = DB::table('transaksi')
            ->select(DB::raw("SUM(total) as total_pendapatan"))
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'")
            ->first();
        return $Pendapatan;
    }

    public function CountMenu()
    {
        $Menu = Menu::count();
        return $Menu;
    }

    public function CountAll()
    {
        return response()->json(['MejaTersedia' => $this->CountMejaTersedia(), 'MejaTerpakai' => $this->CountMejaTerpakai(), 'Kasir' => $this->CountKasir(), 'Pendapatan' => $this->CountPendapatan(), 'Menu' => $this->CountMenu()]);
    }

    public function MenuTerlaris()
    {
        $data = DetailTransaksi::join('menu', 'detail_transaksi.id_menu', '=', 'menu.id_menu')
            ->select('menu.nama_menu', DB::raw('SUM(qty) as total_jumlah'))
            ->groupBy('nama_menu')
            ->orderBy('total_jumlah', 'desc')
            ->limit(5)
            ->get();

        return response()->json($data);
    }

    public function getTotalPendapatanPerBulan()
    {
        $transaksi = DB::table('transaksi')
            ->select(DB::raw("DATE_FORMAT(tanggal, '%Y-%M') as bulan"), DB::raw('SUM(total) as total_pendapatan'))
            ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y-%M')"))
            ->orderBy(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"))
            ->get();

        foreach ($transaksi as $item) {
            $bulan_tahun = date('F Y', strtotime($item->bulan));
            $data[] = [
                'bulan_tahun' => $bulan_tahun,
                'total_pendapatan' => $item->total_pendapatan
            ];
        }
        return response()->json($data);
    }
}