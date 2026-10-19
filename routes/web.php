<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Expense Tracker API',
        'status' => 'running',
    ]);
});