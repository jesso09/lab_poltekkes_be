<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LabController extends Controller
{
    public function index()
    {
        $labData = Lab::latest()->get();
        if (is_null($labData)) {
            return response([
                'message' => 'Data not found',
                'data' => $labData
            ], 404);
        }
        return response([
            'message' => 'Data Lab',
            'data' => $labData
        ], 200);
    }

    public function getPet($id)
    {
        // $labData = Lab::with()->find($id);
        $labData = Lab::find($id);

        if (is_null($labData)) {
            return response([
                'message' => 'Data not found',
                'data' => null
            ], 404);
        }

        return response([
            'message' => 'Successfully',
            'data' => $labData
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
            // 'foto_lab' => 'required',
            'nama_lab' => 'required',
            'plp' => 'required',
            // 'status' => 'required',
        ], [
            'foto_lab.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif.',
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        // Simpan gambar dalam direktori 'storage/app/public/images'
        if ($request->foto_lab != null) {

            $original_name = $request->foto_lab->getClientOriginalName();
            $generated_name = 'lab' . '-' . time() . '.' . $request->foto_lab->extension();

            // menyimpan gambar
            $request->foto_lab->storeAs('public/assets', $generated_name);
        } else {
            $generated_name = null;
        }

        $newData = Lab::create([
            'foto_lab' => $generated_name,
            'nama_lab' => $request->nama_lab,
            'plp' => $request->plp,
            'lokasi' => $request->lokasi,
        ]);

        return response([
            'message' => 'Data added successfully',
            'data' => $newData
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = Lab::find($id);

        if (is_null($data)) {
            return response([
                'message' => 'Data not found',
                'data' => null
            ], 404);
        }

        $update = $request->all();
        $validator = Validator::make($update, [
            // 'foto_lab' => 'required',
            'nama_lab' => 'required',
            'plp' => 'required',
        ], [
            'foto_lab.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif.',
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $data->nama_lab = $update['nama_lab'];
        $data->plp = $update['plp'];
        $data->lokasi = $update['lokasi'];

        if ($request->foto_lab == null) {
            if ($data->save()) {
                return response([
                    'message' => 'Data Updated Success',
                    'data' => $data
                ], 200);
            }
        } else if ($request->foto_lab != null) {
            if ($data->foto_lab == null) {
                $original_name = $request->foto_lab->getClientOriginalName();
                $generated_name = 'lab' . '-' . time() . '.' . $request->foto_lab->extension();

                // menyimpan gambar
                $request->foto_lab->storeAs('public/assets', $generated_name);
                $data->foto_lab = $generated_name;


            } else if ($data->foto_lab != null) {

                // unlink(public_path('storage/public/lab/' . $data->foto_lab));
                unlink(public_path('storage/assets/' . $data->foto_lab));

                $original_name = $request->foto_lab->getClientOriginalName();
                $generated_name = 'lab' . '-' . time() . '.' . $request->foto_lab->extension();
                // menyimpan gambar
                $request->foto_lab->storeAs('public/assets', $generated_name);
                $data->foto_lab = $generated_name;
            }
        }

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
        $targetData = Lab::find($id);

        if (!$targetData) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        // Hapus file gambar jika ada
        if ($targetData->foto_lab) {
            unlink(public_path('storage/public/lab/' . $targetData->foto_lab));
        }

        // Hapus konten dari database
        $targetData->delete();

        return response()->json([
            'message' => 'Data deleted successfully',
            'data' => $targetData,
        ], 200);
    }
}
