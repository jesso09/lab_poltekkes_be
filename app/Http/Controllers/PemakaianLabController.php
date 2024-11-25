<?php

namespace App\Http\Controllers;

use App\Models\PemakaianLab;
use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PemakaianLabController extends Controller
{
    public function index()
    {

        $pemakaianData = PemakaianLab::with('lab', 'user')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        if (is_null($pemakaianData)) {
            return response([
                'message' => 'Data not found',
                'data' => $pemakaianData
            ], 404);
        }
        return response([
            'message' => 'Data Jadwal',
            'data' => $pemakaianData,
        ], 200);
    }

    public function indexByLab($idLab)
    {

        $pemakaianData = PemakaianLab::with('lab', 'user')
            ->where('id_lab', $idLab)
            ->orderBy('jam_mulai', 'asc')
            ->get();


        if (is_null($pemakaianData)) {
            return response([
                'message' => 'Data not found',
                'data' => $pemakaianData
            ], 404);
        }
        return response([
            'message' => 'Data Jadwal',
            'data' => $pemakaianData,
        ], 200);
    }

    public function indexByUser()
    {
        $user = Auth::user();
        if (!$user) {
            return response(['message' => "Unauthorized"], 401);
        }

        $pemakaianData = PemakaianLab::with('lab', 'user')
            ->where('id_user', $user->id)
            ->orderBy('jam_mulai', 'asc')
            ->get();


        if (is_null($pemakaianData)) {
            return response([
                'message' => 'Data not found',
                'data' => $pemakaianData
            ], 404);
        }
        return response([
            'message' => 'Data Jadwal',
            'data' => $pemakaianData,
        ], 200);
    }

    public function show($id)
    {
        $data = $pemakaianData = PemakaianLab::with('lab', 'user')->find($id);
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

    /**
     * store
     *
     * @param Request $request
     */

    public function store(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response(['message' => "Unauthorized"], 401);
        }

        $newData = $request->all();
        //Validasi Formulir
        $validator = Validator::make($newData, [
            'id_lab' => 'required',
            'tanggal_pemakaian' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kegiatan' => 'required',

        ], [
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $newData = PemakaianLab::create([
            'id_user' => $user->id,
            'id_lab' => $request->id_lab,
            'tanggal_pemakaian' => $request->tanggal_pemakaian,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kegiatan' => $request->kegiatan,
            'status' => "Belum Dikonfirmasi",
        ]);

        return response([
            'message' => 'Data added successfully',
            'data' => $newData
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = PemakaianLab::find($id);

        if (is_null($data)) {
            return response([
                'message' => 'Data not found',
                'data' => null
            ], 404);
        }

        $update = $request->all();

        // Validasi data
        $validator = Validator::make($update, [
            'id_lab' => 'required',
            'tanggal_pemakaian' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kegiatan' => 'required',
            'status' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        // Update data
        $data->id_lab = $update['id_lab'];
        $data->tanggal_pemakaian = $update['tanggal_pemakaian'];
        $data->jam_mulai = Carbon::parse($update['tanggal_pemakaian'] . ' ' . $update['jam_mulai']);
        $data->jam_selesai = Carbon::parse($update['tanggal_pemakaian'] . ' ' . $update['jam_selesai']);
        $data->kegiatan = $update['kegiatan'];
        $data->status = $update['status'] ?? $data->status; // Tetap gunakan status lama jika tidak ada perubahan

        // Simpan perubahan
        if ($data->save()) {
            return response([
                'message' => 'Data Updated Successfully',
                'data' => $data
            ], 200);
        }

        return response([
            'message' => 'Failed to update data',
            'data' => null
        ], 400);
    }


    public function changeStatus(Request $request, $id)
    {
        $user = Auth::user();
        $data = PemakaianLab::find($id);

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
        }

        if ($data->status == $request->new_status) {
            return response()->json([
                'message' => 'Data Already With This Status',
                'data' => $data,
            ], 400);
        }

        // Perubahan status
        $data->status = $request->new_status;

        if ($request->new_status != "Dibatalkan") {
            if ($request->new_status == "Dikonfirmasi") {
                $data->confirm_time = Carbon::now();

                // Ambil data dengan status "Dikonfirmasi" pada lab yang sama
                $existingData = PemakaianLab::where('id_lab', $data->id_lab)
                    ->where('status', 'Dikonfirmasi')
                    ->where('tanggal_pemakaian', $data->tanggal_pemakaian) // Tanggal sama
                    ->where('id', '!=', $id) // Bukan data yang sedang diperbarui
                    ->get();

                // Cek tabrakan waktu
                foreach ($existingData as $item) {
                    if (
                        ($data->jam_mulai >= $item->jam_mulai && $data->jam_mulai < $item->jam_selesai) || // Mulai bertabrakan
                        ($data->jam_selesai > $item->jam_mulai && $data->jam_selesai <= $item->jam_selesai) || // Selesai bertabrakan
                        ($data->jam_mulai <= $item->jam_mulai && $data->jam_selesai >= $item->jam_selesai) // Meliputi keseluruhan
                    ) {
                        return response()->json([
                            'message' => 'Jadwal Bertabrakan',
                            'data' => $item, // Data yang bertabrakan
                        ], 403);
                    }
                }
            }
        }

        $data->save();

        return response()->json([
            'message' => 'Data Peminjaman',
            'data' => $data,
        ], 200);
    }


    public function destroy($id)
    {
        $targetData = PemakaianLab::find($id);
        $targetData->delete();

        return response()->json([
            'message' => 'Activity deleted successfully',
            'data' => $targetData,
        ]);
    }
}
