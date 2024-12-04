<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after_or_equal:start_time',
        'description' => 'nullable|string',
    ]);

   $event = Event::create([
        'name' => $request->name,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'description' => $request->description,
    ]);

            return response()->json($event, 201);  
}


//get all
public function index()
{
    $events = Event::all();
    return response()->json($events);
}


//get single event
public function show($id)
{
    
    $event = Event::find($id);
    if (!$event) {
        return response()->json(['message' => 'Event not found'], 404);
    }

    return response()->json($event);
}


//edit
public function update(Request $request, $id)
{
    $event = Event::find($id);

    if (!$event) {
        return response()->json(['message' => 'Event not found'], 404);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after_or_equal:start_time',
        'description' => 'nullable|string',
    ]);

    $event->update([
        'name' => $request->name,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'description' => $request->description,
    ]);

    return response()->json($event);
}


//delete
// EventController.php

public function destroy($id)
{
    $event = Event::find($id);

    if (!$event) {
        return response()->json(['message' => 'Event not found'], 404);
    }

    $event->delete();

    return response()->json(['message' => 'Event deleted successfully']);
}











































}

