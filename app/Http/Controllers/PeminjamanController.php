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

    public function indexDetail()
    {
        $peminjamanData = PeminjamanDetail::latest()->get();
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

    public function historyByUser()
    {
        $idUser = Auth::user()->id;

        if (!$idUser) {
            return response([
                'message' => 'User Not Found',
            ], 404);
        }

        $peminjamanData = PeminjamanDetail::where('id_peminjam', $idUser)->latest()->get();

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
        $idUser = Auth::user()->id;
        $newData = $request->all();
        //Validasi Formulir
        $validator = Validator::make($newData, [
            'id_lab' => 'required',
            'id_alat' => 'required',
            // 'id_peminjam' => 'required',
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
            'id_peminjam' => $idUser,
            'jumlah_alat' => $request->jumlah_alat,
            'confirm_time' => $request->confirm_time,
            'return_time' => $request->return_time,
            'keterangan' => $request->keterangan,
            'status' => "Belum Dikonfirmasi",
        ]);

        $lab = Lab::find($request->id_lab);
        $alat = AlatLab::find($request->id_alat);
        $peminjam = User::find($idUser);

        if ($lab->lokasi == "Dihapus" || $alat->keterangan == "Dihapus" || $peminjam->status == "Diblokir") {
            return response([
                'message' => 'Tidak dapat melakukan peminjaman',
            ], status: 403);
        }

        $detailPeminjaman = [
            'id_peminjam' => $idUser,
            'nama_lab' => $lab->nama_lab,
            'nama_alat' => $alat->nama_alat,
            'jumlah_alat' => $request->jumlah_alat,
            'nama_peminjam' => $peminjam->nama,
            'role_peminjam' => $peminjam->role,
            'confirm_time' => $request->confirm_time,
            'return_time' => $request->return_time,
            'keterangan' => $request->keterangan,
            'status' => "Belum Dikonfirmasi",
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

    public function changeStatus(Request $request, $id)
    {
        $data = PeminjamanAlat::find($id);
        if (!$data) {
            return response()->json([
                'message' => 'Data Not Found',
                'data' => $data,
            ], 404);
        } else {
            if ($data->status == $request->new_status) {
                return response()->json([
                    'message' => 'Data Already With This Status',
                    'data' => $data,
                ], 400);
            }
            $data->status = $request->new_status;
            if ($data->alat->keterangan == "Dihapus") {
                return response()->json([
                    'message' => 'Peminjaman Tidak Dapat Dilanjutkan',
                    'data' => $data,
                ], 400);
            }
            if ($request->new_status == "Dikonfirmasi") {
                $data->confirm_time = $request->confirm_time;
                $dataAlat = AlatLab::find($data->id_alat);

                $dataAlat->jumlah -= $data->jumlah_alat;

                if ($dataAlat->jumlah < 0) {
                    return response()->json([
                        'message' => 'Failed To Confirm Data',
                        'data' => $data,
                    ], 400);
                }

                $dataAlat->save();
            }
            if ($request->new_status == "Dikembalikan") {
                $data->return_time = $request->return_time;
                $dataAlat = AlatLab::find($data->id_alat);

                $dataAlat->jumlah += $data->jumlah_alat;

                $dataAlat->save();
            }


            $data->save();
            return response()->json([
                'message' => 'Data Peminjaman',
                'data' => $data,
                'alat' => $dataAlat,
            ], 200);
        }
    }

    public function show($id)
    {
        $data = PeminjamanAlat::with('lab', 'alat', 'peminjam')->find($id);
        if (!$data) {
            return response()->json([
                'message' => 'data Not Found',
                'data' => $data,
            ], 404);
        } else {
            return response()->json([
                'message' => 'Data Peminjaman',
                'data' => $data,
            ], 200);
        }
    }
}
