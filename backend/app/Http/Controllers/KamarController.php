<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKamarRequest;
use App\Http\Requests\UpdateKamarRequest;
use App\Http\Resources\KamarResource;
use App\Models\Kamar;
use Illuminate\Support\Facades\Storage;

class KamarController extends Controller
{
    /**
     * Menampilkan semua data kamar.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(KamarResource::collection(Kamar::all()), 200);
    }

    /**
     * Menampilkan detail kamar berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $kamar = Kamar::find($id);

        if (!$kamar) {
            return response()->json(['message' => 'Kamar tidak ditemukan'], 404);
        }

        return response()->json(new KamarResource($kamar), 200);
    }

    /**
     * Menyimpan data kamar baru.
     *
     * @param  \App\Http\Requests\StoreKamarRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreKamarRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $kamar = Kamar::create($data);

        return response()->json([
            'message' => 'Kamar berhasil ditambahkan',
            'data' => new KamarResource($kamar),
        ], 201);
    }

    /**
     * Memperbarui data kamar berdasarkan ID.
     *
     * @param  \App\Http\Requests\UpdateKamarRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateKamarRequest $request, $id)
    {
        $kamar = Kamar::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($kamar->image) {
                Storage::disk('public')->delete($kamar->image);
            }
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $kamar->update($data);

        return response()->json([
            'message' => 'Kamar berhasil diperbarui',
            'data' => new KamarResource($kamar),
        ], 200);
    }

    /**
     * Menghapus kamar berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $kamar = Kamar::findOrFail($id);

        if ($kamar->image) {
            Storage::disk('public')->delete($kamar->image);
        }

        $kamar->delete();

        return response()->json(['message' => 'Kamar berhasil dihapus'], 200);
    }

    /**
     * Upload file gambar ke storage.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    private function uploadImage($file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs('uploads/kamars', $filename, 'public');
    }
}
