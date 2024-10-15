<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServerMonitor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonInterval;
use App\Models\NotificationHandler;
class ServerMonitorController extends Controller
{
    public function index()
    {
        $uptimeInSeconds = 1730154.17;  // Example uptime in seconds

        // Convert to a CarbonInterval instance and format
        $uptime = CarbonInterval::seconds($uptimeInSeconds)->cascade()->forHumans();
        ;

        // dd($uptime);

        // Fetch all data from the ServerMonitor table
        $servers = ServerMonitor::all();
        // Pass the data to the view
        return view('backend.pages.server-monitor.index', compact('servers'));
    }
    public function create()
    {
        $notificationHandlers = NotificationHandler::all();
        // Load the view for creating a new server monitor
        return view('backend.pages.server-monitor.create', compact('notificationHandlers'));
    }
    // public function show($id)
    // {
    //     // Find the server monitor by ID
    //     $serverMonitor = ServerMonitor::findOrFail($id);
    
    //     // Generate dynamic install script
    //     $installScript = 'script_name="nex-server-monitor.sh" '
    //         . '&& wget -O "$PWD/$script_name" "http://127.0.0.1/server-monitor-code/' . $serverMonitor->id . '/' . $serverMonitor->api_key . '" '
    //         . '&& chmod +x "$PWD/$script_name" '
    //         . '&& (crontab -l 2>/dev/null | grep -v "$script_name"; echo "*/' . $serverMonitor->check_interval . ' * * * * $PWD/$script_name") | crontab - '
    //         . '&& echo "The ' . $serverMonitor->server_name . ' monitoring script has been installed."';
    
    //     // Generate dynamic uninstall script
    //     $uninstallScript = 'script_name="nex-server-monitor.sh" '
    //         . '&& (crontab -l 2>/dev/null | grep -v "$script_name") | crontab - '
    //         . '&& echo "The ' . $serverMonitor->server_name . ' monitoring script has been uninstalled."';
    
    //     // Pass both scripts to the view
    //     return view('backend.pages.server-monitor.show', compact('serverMonitor', 'installScript', 'uninstallScript'));
    // }
    


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'server_name' => 'required|string|max:255',
    //         'identifier' => 'required|string|max:255',
    //         'check_interval' => 'required|integer|min:1',
    //     ]);

    //     // Generate a unique API key
    //     $apiKey = Str::random(32); // Generates a random string of 32 characters

    //     // Create a new server monitor record
    //     $serverMonitor = ServerMonitor::create([
    //         'server_name' => $request->input('server_name'),
    //         'identifier' => $request->input('identifier'),
    //         'check_interval' => $request->input('check_interval'),
    //         'api_key' => $apiKey,
    //     ]);

    //     // Define the server ID (which is the same as the newly created server monitor ID)
    //     $serverId = $serverMonitor->id;

    //     // Create directory for the server monitor if it doesn't exist
    //     $directoryPath = storage_path("app/servers/$serverId/$apiKey");
    //     if (!file_exists($directoryPath)) {
    //         mkdir($directoryPath, 0755, true);
    //     }

    //     // Path to the default script
    //     $defaultScriptPath = storage_path('app/scripts/default-nex-server-monitor.sh');        
    //     // Read the default script
    //     $scriptContent = file_get_contents($defaultScriptPath);

    //     // Replace placeholders with actual server ID and API key
    //     $scriptContent = str_replace('{SERVER_ID}', $serverId, $scriptContent);
    //     $scriptContent = str_replace('{API_KEY}', $apiKey, $scriptContent);

    //     // Write the content to the new script file
    //     $scriptFilePath = "$directoryPath/nex-server-monitor.sh";
    //     file_put_contents($scriptFilePath, $scriptContent);

    //     // Make the script executable
    //     chmod($scriptFilePath, 0755);

    //     // Redirect to the show page of the newly created server monitor
    //     return redirect()->route('server-monitor.show', $serverMonitor->id)
    //                     ->with('success', 'Server monitor created successfully.');
    // }



    public function show($id)
{
    // Find the server monitor by ID
    $serverMonitor = ServerMonitor::findOrFail($id);
    
    // Get the base URL from the .env file
    $baseUrl = config('app.url'); // This retrieves the value of APP_URL from your .env file
    
    // Generate dynamic install script
    $installScript = 'script_name="nex-server-monitor.sh" '
        . '&& wget -O "$PWD/$script_name" "' . $baseUrl . '/server-monitor-code/' . $serverMonitor->id . '/' . $serverMonitor->api_key . '" '
        . '&& chmod +x "$PWD/$script_name" '
        . '&& (crontab -l 2>/dev/null | grep -v "$script_name"; echo "*/' . $serverMonitor->check_interval . ' * * * * $PWD/$script_name") | crontab - '
        . '&& echo "The ' . $serverMonitor->server_name . ' monitoring script has been installed."';
    
    // Generate dynamic uninstall script
    $uninstallScript = 'script_name="nex-server-monitor.sh" '
        . '&& (crontab -l 2>/dev/null | grep -v "$script_name") | crontab - '
        . '&& echo "The ' . $serverMonitor->server_name . ' monitoring script has been uninstalled."';
    
    // Pass both scripts to the view
    return view('backend.pages.server-monitor.show', compact('serverMonitor', 'installScript', 'uninstallScript'));
}


public function store(Request $request)
{
    try {
        // Validate the incoming request data
        $request->validate([
            'server_name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255',
            'check_interval' => 'required|integer',
            'metric' => 'nullable|string|max:255',  // Validate alert_metric
            'alert_rule' => 'nullable|string|max:255',    // Validate alert_rule
            'value' => 'nullable|integer',    // Validate alert_value
            'trigger_after_x' => 'nullable|integer', // Validate trigger_after_checks
            'notification' => 'nullable|string|max:255',   // Validate notification
        ]);
        
        // Generate a unique API key and create a new server monitor record
        $apiKey = Str::random(32);
        $serverMonitor = ServerMonitor::create([
            'server_name' => $request->input('server_name'),
            'identifier' => $request->input('identifier'),
            'check_interval' => $request->input('check_interval'),
            'api_key' => $apiKey,
            'alert_metric' => $request->input('metric'),  // Add alert_metric
            'alert_rule' => $request->input('alert_rule'),      // Add alert_rule
            'alert_value' => $request->input('value'),    // Add alert_value
            'trigger_after_checks' => $request->input('trigger_after_x'), // Add trigger_after_checks
            'notification' => $request->input('notification'),   // Add notification
        ]);
        
        // Flash success message and redirect to the server monitor show page
        session()->flash('success', 'Server monitor created successfully.');

        return redirect()->route('server-monitor.show', $serverMonitor->id);
    } catch (\Exception $e) {
        // Flash error message
        session()->flash('error', 'Failed to create server monitor. Please try again.');

        // Redirect back to the create page with input
        return redirect()->route('server-monitor.create')->withInput();
    }
}


    public function edit($id)
    {
        // Find the server monitor by its ID
        $serverMonitor = ServerMonitor::findOrFail($id);
        
        // Load the edit view and pass the server monitor data
        return view('backend.pages.server-monitor.edit', compact('serverMonitor'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'server_name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255',
            'check_interval' => 'required|integer',
            'metric' => 'nullable|string|max:255',  // Validate alert_metric
            'alert_rule' => 'nullable|string|max:255',    // Validate alert_rule
            'value' => 'nullable|integer',    // Validate alert_value
            'trigger_after_x' => 'nullable|integer', // Validate trigger_after_checks
            'notification' => 'nullable|integer',   // Validate notification
        ]);
    
        try {
            // Find the server monitor and update it
            $serverMonitor = ServerMonitor::findOrFail($id);
            $serverMonitor->update([
                'server_name' => $request->input('server_name'),
                'identifier' => $request->input('identifier'),
                'check_interval' => $request->input('check_interval'),
                'alert_metric' => $request->input('metric'),  // Update alert_metric
                'alert_rule' => $request->input('alert_rule'),      // Update alert_rule
                'alert_value' => $request->input('value'),    // Update alert_value
                'trigger_after_checks' => $request->input('trigger_after_x'), // Update trigger_after_checks
                'notification' => $request->input('notification'),   // Update notification
                'project_name' => $request->input('project_name'),    // Update project_name
            ]);
    
            // Flash success message and redirect to the edit page
            session()->flash('success', 'Server monitor updated successfully.');
    
            return redirect()->route('server-monitor.edit', $serverMonitor->id);
        } catch (\Exception $e) {
            // Flash error message and redirect back to the edit page
            session()->flash('error', 'Failed to update the server monitor. Please try again.');
    
            return redirect()->route('server-monitor.edit', $id)->withInput();
        }
    }
    

    public function serveMonitorScript($id, $api_key)
    {
        // Find the server monitor by ID
        $serverMonitor = ServerMonitor::where('id', $id)
                                      ->where('api_key', $api_key)
                                      ->first();
    
        // Check if the server monitor exists
        if (!$serverMonitor) {
            abort(404, 'Server monitor not found.');
        }
    
        // Path to the default script
        $defaultScriptPath = public_path('scripts/nex-server-monitor.sh');
    
        // Read the default script
        $scriptContent = file_get_contents($defaultScriptPath);
    
        // Replace placeholders with actual values
        $baseUrl = config('app.url'); // Get the base URL from the config
        $scriptContent = str_replace(
            ['{SERVER_ID}', '{API_KEY}', '{BASE_URL}'], 
            [$id, $api_key, $baseUrl], 
            $scriptContent
        );
    
        // Return the modified script content with appropriate headers
        return response($scriptContent, 200, [
            'Content-Type' => 'application/x-sh',
            'Content-Disposition' => 'inline; filename="nex-server-monitor.sh"'
        ]);
    }
    
    
    public function saveData(Request $request, $server_id, $api_key)
    {
        // Find the server monitor by ID and API key
        $serverMonitor = ServerMonitor::where('id', $server_id)->where('api_key', $api_key)->firstOrFail();

        // Store the incoming JSON data in the server_data column
        $serverMonitor->server_data = json_encode($request->all()); // Convert array to JSON string
        $serverMonitor->save();

        return response()->json(['message' => 'Data saved successfully!'], 200);
    }

    public function destroy($id)
    {
        // Find the server by ID
        $serverMonitor = ServerMonitor::findOrFail($id);

        // Delete the server record
        $serverMonitor->delete();

        // Redirect back to the server index page with a success message
        return redirect()->route('server-monitor.index')
                        ->with('success', 'Server deleted successfully.');
    }



}
