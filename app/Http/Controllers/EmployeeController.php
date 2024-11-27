<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/employee",
     *     tags={"Employees"},
     *     summary="Menampilkan daftar semua karyawan",
     *     description="Endpoint ini digunakan untuk mendapatkan daftar karyawan berdasarkan perusahaan yang dimiliki oleh pengguna saat ini.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Nama karyawan untuk pencarian",
     *         @OA\Schema(type="string", example="John Doe")
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
     *         description="Berhasil mendapatkan daftar karyawan",
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

    public function allEmployees(Request $request)
    {
        $companyID = auth()->user()->employee()->pluck('company_id')->first();
        $query = Employee::query()->where('company_id', $companyID);

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sortField = $request->get('sort_field', 'id');
        $sortOrder = $request->get('sort_order', 'asc');

        $employees = $query->orderBy($sortField, $sortOrder)
            ->paginate(10);

        return response()->json($employees);
    }

    /**
     * @OA\Get(
     *     path="/employee/{id}",
     *     tags={"Employees"},
     *     summary="Menampilkan detail karyawan",
     *     description="Endpoint ini digunakan untuk mendapatkan detail karyawan berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan detail karyawan",
     *         @OA\JsonContent(type="object", @OA\Property(property="id", type="integer", example=1))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Employee not found"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/employee",
     *     tags={"Employees"},
     *     summary="Membuat karyawan baru",
     *     description="Endpoint ini digunakan untuk membuat karyawan baru di perusahaan pengguna saat ini.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "phone_number", "address"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="phone_number", type="string", example="08123456789"),
     *             @OA\Property(property="address", type="string", example="Jl. Contoh No. 123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Karyawan berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="employee", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Validation error"))
     *     )
     * )
     */

    public function detailEmployee($id)
    {
        $companyID = auth()->user()->employee()->pluck('company_id')->first();

        $employee = Employee::where('id', $id)
            ->where('company_id', $companyID)
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee);
    }

    public function createEmployee(CreateEmployeeRequest $request)
    {
        $companyID = auth()->user()->employee()->pluck('company_id')->first();
        $employee = Employee::create([
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'company_id' => $companyID->company_id,
            'role_id' => 3,

        ]);
        User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'employee_id' => $employee['id'],
            'role_id' => $employee['role_id'],
        ]);

        return response()->json(['message' => 'Success', 'employee' => $employee]);
    }

    /**
     * @OA\Put(
     *     path="/employee/{id}",
     *     tags={"Employees"},
     *     summary="Memperbarui data karyawan",
     *     description="Endpoint ini digunakan untuk memperbarui data karyawan berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone_number", type="string", example="08123456789"),
     *             @OA\Property(property="address", type="string", example="Jl. Baru No. 456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Karyawan berhasil diperbarui",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Employee not found"))
     *     )
     * )
     */

    public function updateEmployee(Request $request, $id)
    {
        $companyID = auth()->user()->employee()->pluck('company_id')->first();
        $employee = Employee::where('id', $id)
            ->where('company_id', $companyID)
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->update($request->all());
        return response()->json($employee);
    }

    /**
     * @OA\Delete(
     *     path="/employee/{id}",
     *     tags={"Employees"},
     *     summary="Menghapus karyawan",
     *     description="Endpoint ini digunakan untuk menghapus karyawan berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Karyawan berhasil dihapus",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Employee deleted successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Employee not found"))
     *     )
     * )
     */

    public function deleteEmployee($id)
    {
        $companyID = auth()->user()->employee()->pluck('company_id')->first();
        $employee = Employee::where('id', $id)
            ->where('company_id', $companyID)
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully']);
    }
}