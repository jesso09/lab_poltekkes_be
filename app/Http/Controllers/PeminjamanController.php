<?php

namespace App\Http\Controllers;
use App\Models\AlatLab;
use App\Models\Lab;
use App\Models\PeminjamanAlat;
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
 
        //  $newData = PeminjamanAlat::create([
        //      'id_lab' => $request->id_lab,
        //      'id_alat' => $request->id_alat,
        //      'id_peminjam' => $request->id_peminjam,
        //      'jumlah_alat' => $request->jumlah_alat,
        //      'confirm_time' => $request->confirm_time,
        //      'return_time' => $request->return_time,
        //      'keterangan' => $request->keterangan,
        //      'status' => "Belum Diverifikasi",
        //  ]);

         $lab = Lab::find($request->id_lab);
         $alat = AlatLab::find($request->id_alat);
         $peminjam = User::find($request->id_peminjam);

        //  $detailPeminjaman = [

        //  ];
        //  $table->string('nama_lab');
        //  $table->string('nama_alat');
        //  $table->integer('jumlah_alat');
        //  $table->string('nama_peminjam');
        //  $table->string('role_peminjam');
        //  $table->dateTime('confirm_time')->nullable();
        //  $table->dateTime('return_time')->nullable();
        //  $table->text('keterangan');
        //  $table->string('status');

         return response([
             'message' => 'Data added successfully',
             'data' => $newData,
             'lab data' => $lab,
             'alat data' => $alat,
             'user data' => $peminjam,
         ], status: 201);
     }
}
