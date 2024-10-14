<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JadwalController extends Controller
{
    public function index()
    {
        // $currentDate = now()->toDateString();

        // $jadwalData = Jadwal::with('lab')
        //     ->whereDate('mulai', '>=', $currentDate)
        //     ->orderBy('mulai', 'asc')
        //     ->get();

        // Pastikan minggu dimulai dari hari Senin
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        // $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        // $jadwalData = Jadwal::with('lab')
        //     ->whereBetween('mulai', [$startOfWeek, $endOfWeek])
        //     ->orderBy('mulai', 'asc')
        //     ->get();

        $jadwalData = Jadwal::with('lab')
            ->where('mulai', '>=', $startOfWeek)
            ->orderBy('mulai', 'asc')
            ->get();

        // * $jadwalData = Jadwal::with('lab')->latest()->get();
        // * $jadwalData = Jadwal::with('lab')->orderBy('mulai', 'asc')->get();
        if (is_null($jadwalData)) {
            return response([
                'message' => 'Data not found',
                'data' => $jadwalData
            ], 404);
        }
        return response([
            'message' => 'Data Jadwal',
            'data' => $jadwalData,
        ], 200);
    }

    public function indexAll()
    {

        $jadwalData = Jadwal::with('lab')
            ->latest()
            ->get();

        if (is_null($jadwalData)) {
            return response([
                'message' => 'Data not found',
                'data' => $jadwalData
            ], 404);
        }
        return response([
            'message' => 'Data Jadwal',
            'data' => $jadwalData,
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

    public function update(Request $request, $id)
    {
        $data = Jadwal::find($id);

        if (is_null($data)) {
            return response([
                'message' => 'Data not found',
                'data' => null
            ], 404);
        }

        $update = $request->all();
        $validator = Validator::make($update, [
            'id_lab' => 'required',
            'hari' => 'required',
            'mulai' => 'required',
            'selesai' => 'required',
            'praktikan' => 'required',
            'semester' => 'required',
            'mata_kuliah' => 'required',
            'plp' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $data->id_lab = $update['id_lab'];
        $data->hari = $update['hari'];
        $data->mulai = $update['mulai'];
        $data->selesai = $update['selesai'];
        $data->praktikan = $update['praktikan'];
        $data->semester = $update['semester'];
        $data->mata_kuliah = $update['mata_kuliah'];
        $data->plp = $update['plp'];

        if ($data->save()) {
            return response([
                'message' => 'Data Updated Success',
                'data' => $data
            ], 200);
        }

        return response([
            'message' => 'Failed to update data',
            'data' => null
        ], 400);
    }

    public function destroy($id)
    {
        $targetData = Jadwal::find($id);
        $targetData->delete();

        return response()->json([
            'message' => 'Activity deleted successfully',
            'data' => $targetData,
        ]);
    }
}
