<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ServerMonitor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonInterval;

class HomeController extends Controller
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

    public function redirectAdmin()
    {
        return redirect()->route('admin.dashboard');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {

    //     return view('home');
    // }
    public function index()
    {
        // $uptimeInSeconds = 1730154.17;  // Example uptime in seconds

        // // Convert to a CarbonInterval instance and format
        // $uptime = CarbonInterval::seconds($uptimeInSeconds)->cascade()->forHumans();
        // ;

        // dd($uptime);

        // Fetch all data from the ServerMonitor table
        $servers = ServerMonitor::all();
        // Pass the data to the view
        return view('home', compact('servers'));
    }
}
