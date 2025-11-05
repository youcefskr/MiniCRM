<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    /**
     * Display a listing of the information records.
     */
    public function index()
    {
        $informations = Information::latest()->get();
        return response()->json($informations);
    }

    /**
     * Store a newly created information record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $information = Information::create($validated);

        return response()->json([
            'message' => 'Information created successfully',
            'data' => $information,
        ], 201);
    }

    /**
     * Display the specified information.
     */
    public function show(Information $information)
    {
        return response()->json($information);
    }

    /**
     * Update the specified information record.
     */
    public function update(Request $request, Information $information)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $information->update($validated);

        return response()->json([
            'message' => 'Information updated successfully',
            'data' => $information,
        ]);
    }

    /**
     * Remove the specified information record.
     */
    public function destroy(Information $information)
    {
        $information->delete();

        return response()->json([
            'message' => 'Information deleted successfully',
        ]);
    }
}
