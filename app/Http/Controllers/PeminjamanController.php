<?php

namespace App\Http\Controllers;
use App\Models\PeminjamanAlat;
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
             'hari' => 'required',
             'mulai' => 'required',
             'selesai' => 'required',
             'praktikan' => 'required',
             'semester' => 'required',
             'mata_kuliah' => 'required',
             'plp' => 'required',
         ], [
         ]);
         if ($validator->fails()) {
             return response(['message' => $validator->errors()], 400);
         }
 
         $newData = Jadwal::create([
             'id_lab' => $request->id_lab,
             'hari' => $request->hari,
             'mulai' => $request->mulai,
             'selesai' => $request->selesai,
             'praktikan' => $request->praktikan,
             'semester' => $request->semester,
             'mata_kuliah' => $request->mata_kuliah,
             'plp' => $request->plp,
         ]);
 
         return response([
             'message' => 'Data added successfully',
             'data' => $newData
         ], 201);
     }
}
