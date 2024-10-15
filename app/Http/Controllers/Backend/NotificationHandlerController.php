<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\NotificationHandler;
class NotificationHandlerController extends Controller
{
   
    public function index()
    {
        $handlers = NotificationHandler::all();
        return view('backend.pages.notification_handler.index', compact('handlers'));
    }

    public function create()
    {
        return view('backend.pages.notification_handler.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'notification_type' => 'required',
            'url' => 'required|url',
        ]);

        NotificationHandler::create($request->all());

        return redirect()->route('notification-handler.index')
                         ->with('success', 'Notification Handler created successfully.');
    }

    public function edit(NotificationHandler $notificationHandler)
    {
        return view('backend.pages.notification_handler.edit', compact('notificationHandler'));
    }

    public function update(Request $request, NotificationHandler $notificationHandler)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'notification_type' => 'required|in:webhook,slack',
            'url' => 'required|url',
        ]);

        $notificationHandler->update($request->all());

        return redirect()->route('notification-handler.index')
                         ->with('success', 'Notification Handler updated successfully.');
    }

    public function destroy(NotificationHandler $notificationHandler)
    {
        $notificationHandler->delete();

        return redirect()->route('notification-handler.index')
                         ->with('success', 'Notification Handler deleted successfully.');
    }

    
}
