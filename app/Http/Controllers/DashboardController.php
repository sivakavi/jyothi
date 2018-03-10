<?php

namespace App\Http\Controllers;

use App\Models\Auth\User\User;
use Arcanedev\LogViewer\Entities\Log;
use Arcanedev\LogViewer\Entities\LogEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;


class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function testHr()
    {
        return '<h1>HR Page Under Construction</h1>';
    }

    public function testDept()
    {
        return '<h1>Department Page Under Construction</h1>';
    }


}
