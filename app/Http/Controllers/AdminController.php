<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/admins",
     *     tags={"Manage Admins"},
     *     summary="Get a paginated list of admins",
     *     description="Retrieves a list of admins, paginated in groups of 10.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of admins retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="List user admin"),
     *             @OA\Property(property="admins", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error has occurred"),
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function getAdmins()
    {
        try {
            $admin = User::where('role_id', '=', 1)->paginate(10);

            return response()->json([
                'message' => 'List user admin',
                'admins' => $admin
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/admins",
     *     tags={"Manage Admins"},
     *     summary="Create a new admin user",
     *     description="Creates a new admin user with the provided information.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe", description="Name of the admin"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com", description="Email address of the admin"),
     *             @OA\Property(property="password", type="string", format="password", example="123456", description="Password of the admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Admin created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin created successfully"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="errors", type="object", description="Validation errors")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error has occurred"),
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function createAdmins(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 1
            ]);

            return response()->json([
                'message' => 'Admin created successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/admins/{id}",
     *     tags={"Manage Admins"},
     *     summary="Update an existing admin user",
     *     description="Updates an existing admin user with the provided information.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the admin to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe", description="Name of the admin"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com", description="Email address of the admin"),
     *             @OA\Property(property="password", type="string", format="password", example="123456", description="Password of the admin (optional)"),
     *             @OA\Property(property="role_id", type="integer", example=1, description="Role ID of the admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin updated successfully"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="errors", type="object", description="Validation errors")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error has occurred"),
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function editAdmins(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role_id' => 'required'
            ]);

            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role_id;
            $user->save();

            return response()->json([
                'message' => 'Admin created successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admins/{id}",
     *     tags={"Manage Admins"},
     *     summary="Delete an admin user",
     *     description="Deletes an admin user with the given ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the admin to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error has occurred"),
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function deleteAdmin($id)
    {
        try {
            $user = User::find($id);

            if ($user->role_id != 1) {
                return response()->json(['message' => 'Admin not found'], 404);
            }

            $user->delete();

            return response()->json([
                'message' => 'Admin delete successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
