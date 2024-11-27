<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/manager",
     *     tags={"Managers"},
     *     summary="Menampilkan daftar semua manager",
     *     description="Endpoint ini digunakan untuk mendapatkan daftar manager berdasarkan perusahaan yang dimiliki oleh pengguna saat ini.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Nama manager untuk pencarian",
     *         @OA\Schema(type="string", example="Jane Doe")
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         required=false,
     *         description="Field yang digunakan untuk pengurutan",
     *         @OA\Schema(type="string", example="name")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Urutan pengurutan (asc/desc)",
     *         @OA\Schema(type="string", example="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan daftar manager",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */

    public function allManagers(Request $request)
    {
        $companyID = auth()->user()->employee()->pluck('company_id')->first();

        $query = User::with('employee')
            ->where('role_id', 2)
            ->whereHas('employee', function ($q) use ($companyID) {
                $q->where('company_id', $companyID);
            });

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $sortField = $request->get('sort_field', 'id');
        $sortOrder = $request->get('sort_order', 'asc');

        $managers = $query->orderBy($sortField, $sortOrder)
            ->paginate(10);
        return response()->json($managers);
    }

    /**
     * @OA\Get(
     *     path="/manager/{id}",
     *     tags={"Managers"},
     *     summary="Menampilkan detail manager",
     *     description="Endpoint ini digunakan untuk mendapatkan detail manager berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID manager",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan detail manager",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Manager tidak ditemukan",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Manager not found"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */

    public function detailManager($id)
    {
        $companyID = auth()->user()->employee()->pluck('company_id')->first();

        $manager = User::with('employee')
            ->where('role_id', 2)
            ->whereHas('employee', function ($query) use ($companyID) {
                $query->where('company_id', $companyID);
            })
            ->find($id);

        if (!$manager) {
            return response()->json(['message' => 'Manager not found'], 404);
        }

        return response()->json($manager);
    }

    /**
     * @OA\Get(
     *     path="/profile",
     *     tags={"Managers"},
     *     summary="Menampilkan profil pengguna saat ini",
     *     description="Endpoint ini digunakan untuk mendapatkan data profil pengguna yang sedang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan profil",
     *         @OA\JsonContent(type="object", @OA\Property(property="id", type="integer", example=1))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */

    public function profile()
    {
        $user = auth()->user();

        return response()->json($user);

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'employee_id' => $user->employee_id,
            'role_id' => $user->role_id,
            'role' => $user->role ? $user->role->name : null,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/profile",
     *     tags={"Managers"},
     *     summary="Memperbarui profil pengguna",
     *     description="Endpoint ini digunakan untuk memperbarui profil pengguna yang sedang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone_number", "address"},
     *             @OA\Property(property="phone_number", type="string", example="08123456789"),
     *             @OA\Property(property="address", type="string", example="Jl. Contoh Baru No. 123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profil berhasil diperbarui",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Profile updated successfully"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Validation error"))
     *     )
     * )
     */

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = User::with('employee')->findOrfail(auth()->user()->id);
        $user->employee()->update($request->all());

        return response()->json(['message' => 'Profile updated successfully']);
    }
}