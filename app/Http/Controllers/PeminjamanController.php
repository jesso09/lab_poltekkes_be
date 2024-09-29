<?php

namespace App\Http\Controllers;
use App\Models\AlatLab;
use App\Models\Lab;
use App\Models\PeminjamanAlat;
use App\Models\PeminjamanDetail;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjamanData = PeminjamanAlat::with('lab', 'alat', 'peminjam')->latest()->get();
        if (is_null($peminjamanData)) {
            return response([
                'message' => 'Data not found',
                'data' => $peminjamanData
            ], 404);
        }
        return response([
            'message' => 'Data Peminjaman',
            'data' => $peminjamanData
        ], 200);
    }

    public function indexByUser()
    {
        $idUser = Auth::user()->id;

        if (!$idUser) {
            return response([
                'message' => 'User Not Found',
            ], 404);
        }

        $peminjamanData = PeminjamanAlat::where('id_peminjam', $idUser)->with('lab', 'alat', 'peminjam')->latest()->get();
        if (is_null($peminjamanData)) {
            return response([
                'message' => 'Data not found',
                'data' => $peminjamanData
            ], 404);
        }
        return response([
            'message' => 'Data Peminjaman',
            'data' => $peminjamanData
        ], 200);
    }

    /**
     * store
     *
     * @param Request $request
     */

    public function store(Request $request)
    {
        $newData = $request->all();
        //Validasi Formulir
        $validator = Validator::make($newData, [
            'id_lab' => 'required',
            'id_alat' => 'required',
            'id_peminjam' => 'required',
            'jumlah_alat' => 'required',
            //  'confirm_time' => 'required',
            //  'return_time' => 'required',
            'keterangan' => 'required',
            //  'status' => 'required',
        ], [
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $newData = PeminjamanAlat::create([
            'id_lab' => $request->id_lab,
            'id_alat' => $request->id_alat,
            'id_peminjam' => $request->id_peminjam,
            'jumlah_alat' => $request->jumlah_alat,
            'confirm_time' => $request->confirm_time,
            'return_time' => $request->return_time,
            'keterangan' => $request->keterangan,
            'status' => "Belum Diverifikasi",
        ]);

        $lab = Lab::find($request->id_lab);
        $alat = AlatLab::find($request->id_alat);
        $peminjam = User::find($request->id_peminjam);

        $detailPeminjaman = [
            'nama_lab' => $lab->nama_lab,
            'nama_alat' => $alat->nama_alat,
            'jumlah_alat' => $request->jumlah_alat,
            'nama_peminjam' => $peminjam->nama,
            'role_peminjam' => $peminjam->role,
            'confirm_time' => $request->confirm_time,
            'return_time' => $request->return_time,
            'keterangan' => $request->keterangan,
            'status' => "Belum Diverifikasi",
        ];

        $newDetailPeminjaman = PeminjamanDetail::create($detailPeminjaman);

        return response([
            'message' => 'Data added successfully',
            'data' => $newData,
            'detail data' => $newDetailPeminjaman,
            // 'lab data' => $lab->nama_lab,
            // 'alat data' => $alat->nama_alat,
            // 'user data' => $peminjam->nama,
            // 'user data 2' => $peminjam->role,
        ], status: 201);
    }
}
