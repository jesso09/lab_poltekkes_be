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
        $peminjamanData = PeminjamanAlat::with('lab', 'detailPeminjaman.alat', 'peminjam')->latest()->get();
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

    public function indexByLab($plp)
    {
        $peminjamanData = PeminjamanAlat::whereHas('lab', function ($query) use ($plp) {
            $query->where('plp', $plp);
        })
            ->with('lab', 'detailPeminjaman.alat', 'peminjam')
            ->latest()
            ->get();
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

        $peminjamanData = PeminjamanAlat::where('id_peminjam', $idUser)->with('lab', 'detailPeminjaman.alat', 'peminjam')->latest()->get();
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
            'start_borrow' => 'required',
            'end_borrow' => 'required',
            'keterangan' => 'required',

            'detail_peminjaman' => 'required|array',
            'detail_peminjaman.*.id_alat' => 'required',
            'detail_peminjaman.*.jumlah_alat' => 'required',

        ], [
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $lab = Lab::find($request->id_lab);
        $peminjam = User::find($idUser);

        if ($lab->lokasi == "Dihapus" || $peminjam->status == "Diblokir") {
            return response([
                'message' => 'Tidak dapat melakukan peminjaman',
            ], status: 403);
        }

        $newData = PeminjamanAlat::create([
            'id_peminjam' => $idUser,
            'id_lab' => $request->id_lab,
            'start_borrow' => $request->start_borrow,
            'end_borrow' => $request->end_borrow,
            'keterangan' => $request->keterangan,
        ]);

        foreach ($request->detail_peminjaman as $detail) {
            $newDetailData = $newData->detailPeminjaman()->create([
                'id_alat' => $detail['id_alat'],
                'jumlah_alat' => $detail['jumlah_alat'],
                'status' => "Belum Dikonfirmasi",
            ]);
        }

        return response([
            'message' => 'Data added successfully',
            'data' => $newData,
            'detail data' => $newDetailData,
        ], status: 201);
    }

    public function changeStatus(Request $request, $id)
    {
        $user = Auth::user();
        $data = PeminjamanDetail::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Proses Tidak Dapat Dilanjutkan',
            ], 401);
        }
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

            if ($request->new_status != "Dibatalkan") {
                if ($data->alat->keterangan == "Dihapus" || $user->status == "Diblokir") {
                    return response()->json([
                        'message' => 'Peminjaman Tidak Dapat Dilanjutkan',
                        'data' => $data,
                    ], 403);
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
            }


            $data->save();
            return response()->json([
                'message' => 'Data Peminjaman',
                'data' => $data,
                // 'alat' => $dataAlat,
            ], 200);
        }
    }

    public function show($id)
    {
        $data = PeminjamanAlat::with('lab', 'detailPeminjaman.alat', 'peminjam')->find($id);
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
