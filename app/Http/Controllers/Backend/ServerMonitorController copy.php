<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServerMonitor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class ServerMonitorController extends Controller
{
    public function create()
    {
        // Load the view for creating a new server monitor
        return view('backend.pages.server-monitor.create');
    }
    // public function show($id)
    // {
    //     // Find the server monitor by ID
    //     $serverMonitor = ServerMonitor::findOrFail($id);

    //     // Load the view for showing the specific server monitor
    //     return view('backend.pages.server-monitor.show', compact('serverMonitor'));
    // }
    public function show($id)
    {
        // Find the server monitor by ID
        $serverMonitor = ServerMonitor::findOrFail($id);
    
        // Generate dynamic install script
        $installScript = 'script_name="nex-server-monitor.sh" '
            . '&& wget -O "$PWD/$script_name" "http://127.0.0.1/server-monitor-code/' . $serverMonitor->id . '/' . $serverMonitor->api_key . '" '
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



    public function store(Request $request)
    {
        $request->validate([
            'server_name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255',
            'check_interval' => 'required|integer|min:1',
        ]);

        // Generate a unique API key
        $apiKey = Str::random(32);

        // Create a new server monitor record without the script file path yet
        $serverMonitor = ServerMonitor::create([
            'server_name' => $request->input('server_name'),
            'identifier' => $request->input('identifier'),
            'check_interval' => $request->input('check_interval'),
            'api_key' => $apiKey,
        ]);

        // Define the server ID (which is the same as the newly created server monitor ID)
        $serverId = $serverMonitor->id;

        // Create directory for the server monitor in the public directory if it doesn't exist
        $directoryPath = storage_path("app/servers/$serverId/$apiKey");

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        // Path to the default script
        $defaultScriptPath = storage_path('app/scripts/default-nex-server-monitor.sh');

        // Read the default script
        $scriptContent = file_get_contents($defaultScriptPath);

        // Replace placeholders with actual server ID and API key
        $scriptContent = str_replace('{SERVER_ID}', $serverId, $scriptContent);
        $scriptContent = str_replace('{API_KEY}', $apiKey, $scriptContent);

        // Write the content to the new script file in the public directory
        $scriptFilePath = "$directoryPath/nex-server-monitor.sh";
        file_put_contents($scriptFilePath, $scriptContent);

        // Make the script executable
        chmod($scriptFilePath, 0755);

        // Store the relative path for accessing the script
        $relativePath = "storage/servers/$serverId/$apiKey/nex-server-monitor.sh";

        // Update the server monitor record with the relative file path
        $serverMonitor->update([
            'script_file_path' => $relativePath, // Save the relative path
        ]);

        // Redirect to the show page of the newly created server monitor
        return redirect()->route('server-monitor.show', $serverMonitor->id)
                        ->with('success', 'Server monitor created successfully.');
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

        // Get the relative path of the script
        $scriptFilePath = $serverMonitor->script_file_path; // Assuming this is stored in the database

        // Construct the full path to the script
        $fullPath = public_path($scriptFilePath);
        // dd($fullPath);
        // Check if the script file exists
        if (!file_exists($fullPath)) {
            abort(404, 'Script file not found.');
        }

        // Return the script file with appropriate headers
        return response()->file($fullPath, [
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



}
