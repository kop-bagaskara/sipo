<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\EbookPKBController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Ebook PKB Tracking API Routes (using auth:sanctum or auth:api)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ebook-pkb/tracking/update-progress', [EbookPKBController::class, 'trackProgress']);
    Route::post('/ebook-pkb/tracking/mark-complete', [EbookPKBController::class, 'markSessionComplete']);
    Route::get('/ebook-pkb/tracking/current-session', [EbookPKBController::class, 'getCurrentSession']);
    Route::get('/ebook-pkb/tracking/user-statistics', [EbookPKBController::class, 'getUserStatistics']);
});




// FullCalendar API Routes
Route::get('/machines/resources', function () {
    // Sample data for machines grouped by department
    return response()->json([
        'resources' => [
            [
                'id' => 'dept1',
                'title' => 'Department 1',
                'children' => [
                    ['id' => 'machine1', 'title' => 'Machine 1 - Cutting', 'department' => 'dept1'],
                    ['id' => 'machine2', 'title' => 'Machine 2 - Printing', 'department' => 'dept1']
                ]
            ],
            [
                'id' => 'dept2',
                'title' => 'Department 2',
                'children' => [
                    ['id' => 'machine3', 'title' => 'Machine 3 - Finishing', 'department' => 'dept2'],
                    ['id' => 'machine4', 'title' => 'Machine 4 - Packaging', 'department' => 'dept2']
                ]
            ]
        ]
    ]);
});

Route::get('/plans/events', function () {
    // Sample data for production plans
    return response()->json([
        'events' => [
            [
                'id' => 'event1',
                'title' => 'Job Order 001 - Cutting',
                'start' => '2024-01-15T08:00:00',
                'end' => '2024-01-15T12:00:00',
                'resourceId' => 'machine1',
                'extendedProps' => [
                    'status' => 'progress',
                    'so' => 'SO-001',
                    'wo' => 'WO-001',
                    'item' => 'Product A',
                    'qty' => 100
                ]
            ],
            [
                'id' => 'event2',
                'title' => 'Job Order 002 - Printing',
                'start' => '2024-01-15T13:00:00',
                'end' => '2024-01-15T17:00:00',
                'resourceId' => 'machine2',
                'extendedProps' => [
                    'status' => 'pending',
                    'so' => 'SO-002',
                    'wo' => 'WO-002',
                    'item' => 'Product B',
                    'qty' => 50
                ]
            ],
            [
                'id' => 'event3',
                'title' => 'Job Order 003 - Finishing',
                'start' => '2024-01-16T08:00:00',
                'end' => '2024-01-16T16:00:00',
                'resourceId' => 'machine3',
                'extendedProps' => [
                    'status' => 'completed',
                    'so' => 'SO-003',
                    'wo' => 'WO-003',
                    'item' => 'Product C',
                    'qty' => 75
                ]
            ]
        ]
    ]);
});
