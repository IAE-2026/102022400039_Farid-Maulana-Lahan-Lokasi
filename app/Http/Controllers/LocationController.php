<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class LocationController extends Controller {
    
    #[OA\Get(
        path: "/api/v1/locations",
        operationId: "getLocationsList",
        tags: ["Locations"],
        summary: "Melihat daftar lokasi parkir",
        security: [["ApiKeyAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Data retrieved successfully"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object")),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(property: "service_name", type: "string", example: "Lahan-Lokasi-Service"),
                                new OA\Property(property: "api_version", type: "string", example: "v1")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "error"),
                        new OA\Property(property: "message", type: "string", example: "Unauthorized: X-IAE-KEY header is missing")
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "error"),
                        new OA\Property(property: "message", type: "string", example: "Forbidden: Invalid X-IAE-KEY value")
                    ]
                )
            )
        ]
    )]
    public function index() {
        $locations = Location::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $locations,
            'meta' => [
                'service_name' => 'Lahan-Lokasi-Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }

    #[OA\Get(
        path: "/api/v1/locations/{id}",
        operationId: "getLocationById",
        tags: ["Locations"],
        summary: "Melihat detail satu lokasi",
        security: [["ApiKeyAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Data retrieved successfully"),
                        new OA\Property(property: "data", type: "object"),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(property: "service_name", type: "string", example: "Lahan-Lokasi-Service"),
                                new OA\Property(property: "api_version", type: "string", example: "v1")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Resource not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "error"),
                        new OA\Property(property: "message", type: "string", example: "Resource with ID loc_999 not found")
                    ]
                )
            )
        ]
    )]
    public function show($id) {
        $location = Location::find($id);
        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => "Resource with ID $id not found",
                'errors' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $location,
            'meta' => [
                'service_name' => 'Lahan-Lokasi-Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }

    #[OA\Post(
        path: "/api/v1/locations",
        operationId: "storeLocation",
        tags: ["Locations"],
        summary: "Menambahkan master data lahan baru",
        security: [["ApiKeyAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "address", "type", "parking_type", "total_spots", "base_rate"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Gedung Kuliah Umum Parkir"),
                    new OA\Property(property: "address", type: "string", example: "Jl. Telekomunikasi No. 1, Bandung"),
                    new OA\Property(property: "type", type: "string", example: "indoor"),
                    new OA\Property(property: "parking_type", type: "string", example: "regular"),
                    new OA\Property(property: "total_spots", type: "integer", example: 100),
                    new OA\Property(property: "base_rate", type: "integer", example: 3000)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Resource created successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Location master data created successfully"),
                        new OA\Property(property: "data", type: "object"),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(property: "service_name", type: "string", example: "Lahan-Lokasi-Service"),
                                new OA\Property(property: "api_version", type: "string", example: "v1")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation Error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "error"),
                        new OA\Property(property: "message", type: "string", example: "Validation Error"),
                        new OA\Property(
                            property: "errors",
                            type: "array",
                            items: new OA\Items(type: "string"),
                            example: ["The name field is required."]
                        )
                    ]
                )
            )
        ]
    )]
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'type' => 'required|string',
            'parking_type' => 'required|string',
            'total_spots' => 'required|numeric',
            'base_rate' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $latestId = Location::orderBy('id', 'desc')->first();
        $num = $latestId ? ((int) substr($latestId->id, 4)) + 1 : 1;
        $customId = 'loc_' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $location = new Location();
        $location->id = $customId;
        $location->name = $request->name;
        $location->address = $request->address;
        $location->type = $request->type;
        $location->parking_type = $request->parking_type;
        $location->total_spots = $request->total_spots;
        $location->available_spots = $request->total_spots; 
        $location->base_rate = $request->base_rate;
        $location->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Location master data created successfully',
            'data' => $location,
            'meta' => [
                'service_name' => 'Lahan-Lokasi-Service',
                'api_version' => 'v1'
            ]
        ], 201);
    }
}