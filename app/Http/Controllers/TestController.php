<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function dummyUsers(Request $request)
    {
        $limit = $request->query('limit', 10);
        $roleFilter = $request->query('role'); // optional filter example

        $dummyUsers = [
            [
                'id' => 1,
                'name' => 'Anil Kumar',
                'email' => 'anil.kumar@example.com',
                'phone' => '+91-9876543210',
                'role' => 'admin',
                'city' => 'Meerut',
                'state' => 'Uttar Pradesh',
                'created_at' => '2025-11-15 10:30:00',
                'status' => 'active',
                'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg',
            ],
            [
                'id' => 2,
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@example.com',
                'phone' => '+91-9123456789',
                'role' => 'staff',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'created_at' => '2025-12-03 14:15:00',
                'status' => 'active',
                'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg',
            ],
            [
                'id' => 3,
                'name' => 'Rahul Verma',
                'email' => 'rahul.verma@example.com',
                'phone' => '+91-9988776655',
                'role' => 'customer',
                'city' => 'Ghaziabad',
                'state' => 'Uttar Pradesh',
                'created_at' => '2026-01-20 09:45:00',
                'status' => 'inactive',
                'avatar' => 'https://randomuser.me/api/portraits/men/65.jpg',
            ],
            [
                'id' => 4,
                'name' => 'Sneha Gupta',
                'email' => 'sneha.gupta@example.com',
                'phone' => '+91-7894561230',
                'role' => 'staff',
                'city' => 'Noida',
                'state' => 'Uttar Pradesh',
                'created_at' => '2026-02-10 16:20:00',
                'status' => 'active',
                'avatar' => 'https://randomuser.me/api/portraits/women/28.jpg',
            ],
            [
                'id' => 5,
                'name' => 'Vikram Singh',
                'email' => 'vikram.singh@example.com',
                'phone' => '+91-8765432109',
                'role' => 'customer',
                'city' => 'Meerut',
                'state' => 'Uttar Pradesh',
                'created_at' => '2026-02-18 11:05:00',
                'status' => 'active',
                'avatar' => 'https://randomuser.me/api/portraits/men/91.jpg',
            ],
        ];

        // Apply optional limit
        $dummyUsers = array_slice($dummyUsers, 0, (int)$limit);

        // Optional: filter by role
        if ($roleFilter) {
            $dummyUsers = array_filter($dummyUsers, function ($user) use ($roleFilter) {
                return strtolower($user['role']) === strtolower($roleFilter);
            });
            $dummyUsers = array_values($dummyUsers); // re-index array
        }

        return response()->json([
            'success' => true,
            'users' => array_values($dummyUsers),
            'total' => count($dummyUsers),
            'message' => 'Dummy users list returned',
        ]);
    }
}
