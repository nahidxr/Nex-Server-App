<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServerMonitor;
use App\Models\NotificationHandler;
use Illuminate\Support\Facades\Cache;

class AllServerMonitor extends Command
{
    protected $signature = 'server:monitor';
    protected $description = 'Monitor server metrics and send alerts if thresholds are exceeded';

    public function handle()
    {
        $servers = ServerMonitor::where('status', 1)->get();
    
        foreach ($servers as $server) {
            $data = json_decode($server->server_data, true); // Extract server data
            
            // dd( vars: $data);

            $alerts = json_decode($server->alerts, true); // Extract alert rules

            foreach ($alerts as $alert) {
                $metric = $alert['metric'] ?? '';
                $rule = $alert['rule'] ?? '';
                $threshold = (float) ($alert['value'] ?? 0);
                $triggerAfterChecks = (int) ($alert['trigger_after_checks'] ?? 1);
                $this->info("Checking server: {$server->server_name} with metric {$metric}");

                // Get the current metric value from server data
                $currentValue = $data[$metric] ?? 0;
    
                // Check if the threshold is crossed
                $isThresholdCrossed = $this->checkThreshold($rule, $currentValue, $threshold);
    
                if ($isThresholdCrossed) {
                    // Track consecutive failures using Cache
                    $cacheKey = "server_{$server->id}_{$metric}_checks";
                    $consecutiveFailures = Cache::get($cacheKey, 0) + 1;
    
                    if ($consecutiveFailures >= $triggerAfterChecks) {
                        $this->sendAlert($server, $metric, $currentValue, $threshold);
                        Cache::forget($cacheKey); // Reset the counter after sending alert
                    } else {
                        Cache::put($cacheKey, $consecutiveFailures, now()->addMinutes(5)); // Increment the counter
                    }
                } else {
                    Cache::forget("server_{$server->id}_{$metric}_checks"); // Reset if metric is back to normal
                }
            }
        }
    
        $this->info('Server monitoring completed successfully.');
    }
    
    protected function checkThreshold($rule, $value, $threshold)
    {
        return match ($rule) {
            'higher_than' => $value > $threshold,
            'lower_than' => $value < $threshold,
            default => false,
        };
    }
    
    protected function sendAlert($server, $metric, $currentValue, $threshold)
    {
        $message = "🚨 *Alert!*\n"
            . "Server: {$server->server_name}\n"
            . "Metric: {$metric}\n"
            . "Current Value: {$currentValue}\n"
            . "Threshold: {$threshold}\n";
    
        $notificationHandler = NotificationHandler::find($server->notification);
    
        if ($notificationHandler->notification_type === 'slack') {
            $this->sendSlackNotification($notificationHandler->url, $message);
        } elseif ($notificationHandler->notification_type === 'webhook') {
            $this->sendWebhookNotification($notificationHandler->url, $message);
        }
    }
    
    protected function sendSlackNotification($url, $message)
    {
        $payload = json_encode(['text' => $message]);
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }
    
    protected function sendWebhookNotification($url, $message)
    {
        $payload = json_encode(['message' => $message]);
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }
}
