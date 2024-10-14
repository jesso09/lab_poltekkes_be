<?php

namespace App\Http\Controllers;

use App\Models\AlatLab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlatController extends Controller
{
    public function index($idLab)
    {
        $alatData = AlatLab::where('id_lab', $idLab)->latest()->get();
        if (is_null($alatData)) {
            return response([
                'message' => 'Data not found',
                'data' => $alatData
            ], 404);
        }
        return response([
            'message' => 'Data Lab',
            'data' => $alatData
        ], 200);
    }

    public function getalat($id)
    {
        // $labData = Lab::with()->find($id);
        $labData = AlatLab::find($id);

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
            'id_lab' => 'required',
            // 'foto_alat' => 'required',
            'nama_alat' => 'required',
            'jumlah' => 'required',
            // 'keterangan' => 'required',
        ], [
            'foto_alat.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif.',
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        // Simpan gambar dalam direktori 'storage/app/public/images'
        if ($request->foto_alat != null) {

            $original_name = $request->foto_alat->getClientOriginalName();
            $generated_name = 'alat' . '-' . time() . '.' . $request->foto_alat->extension();

            // menyimpan gambar
            $request->foto_alat->storeAs('public/assets', $generated_name);
        } else {
            $generated_name = null;
        }

        $newData = AlatLab::create([
            'id_lab' => $request->id_lab,
            'foto_alat' => $generated_name,
            'nama_alat' => $request->nama_alat,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
        ]);

        return response([
            'message' => 'Data added successfully',
            'data' => $newData
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = AlatLab::find($id);

        if (is_null($data)) {
            return response([
                'message' => 'Data not found',
                'data' => null
            ], 404);
        }

        $update = $request->all();
        $validator = Validator::make($update, [
            'id_lab' => 'required',
            // 'foto_alat' => 'required',
            'nama_alat' => 'required',
            'jumlah' => 'required',
            // 'keterangan' => 'required',
        ], [
            'foto_alat.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif.',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $data->id_lab = $update['id_lab'];
        // $data->foto_alat = $update['foto_alat'];
        $data->nama_alat = $update['nama_alat'];
        $data->jumlah = $update['jumlah'];
        $data->keterangan = $update['keterangan'];

        if ($request->foto_alat == null) {
            if ($data->save()) {
                return response([
                    'message' => 'Data Updated Success',
                    'data' => $data
                ], 200);
            }
        } else if ($request->foto_alat != null) {
            if ($data->foto_alat == null) {
                $original_name = $request->foto_alat->getClientOriginalName();
                $generated_name = 'alat' . '-' . time() . '.' . $request->foto_alat->extension();

                // menyimpan gambar
                $request->foto_alat->storeAs('public/assets', $generated_name);
                $data->foto_alat = $generated_name;


            } else if ($data->foto_alat != null) {

                // unlink(public_path('storage/public/alat/' . $data->foto_alat));
                unlink(public_path('storage/assets/' . $data->foto_alat));

                $original_name = $request->foto_alat->getClientOriginalName();
                $generated_name = 'alat' . '-' . time() . '.' . $request->foto_alat->extension();
                // menyimpan gambar
                $request->foto_alat->storeAs('public/assets', $generated_name);
                $data->foto_alat = $generated_name;
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
        $targetData = AlatLab::find($id);

        if (!$targetData) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        // Hapus file gambar jika ada
        if ($targetData->foto_alat) {
            unlink(public_path('storage/assets/' . $targetData->foto_alat));
            $targetData->foto_alat = null;
        }

        // Hapus konten dari database
        $targetData->keterangan = "Dihapus";
        $targetData->save();

        return response()->json([
            'message' => 'Data deleted successfully',
            'data' => $targetData,
        ], 200);
    }
}
