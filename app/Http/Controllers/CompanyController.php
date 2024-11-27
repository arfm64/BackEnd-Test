<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyRequest;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;


class CompanyController extends Controller
{
    /**
     * @OA\Post(
     *     path="/company",
     *     tags={"Companies"},
     *     summary="Buat perusahaan baru beserta manajer",
     *     description="Endpoint ini digunakan untuk membuat perusahaan baru, termasuk informasi perusahaan, manajer, dan data terkait.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "phone", "address"},
     *             @OA\Property(property="name", type="string", example="PT Example"),
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="phone", type="string", example="08123456789"),
     *             @OA\Property(property="address", type="string", example="Jl. Contoh No. 123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Perusahaan berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Company created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", additionalProperties=@OA\Property(type="array", @OA\Items(type="string")))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function createCompany(CreateCompanyRequest $request)
    {
        $data = $request->only('name', 'email', 'phone');
        $data['password'] = bcrypt($request->password);
        $data['phone_number'] = $request->phone;
        $company = Company::create($data);
        $employee = Employee::create([
            'phone_number' => $data['phone_number'],
            'address' => $request->address,
            'company_id' => $company->id,
            'role_id' => 2,
        ]);
        User::create([
            'email' => $request->email,
            'password' => $data['password'],
            'employee_id' => $employee->id,
            'role_id' => 2,

        ]);
        return response()->json([
            'message' => 'Company created successfully',
        ], 201);
    }
}