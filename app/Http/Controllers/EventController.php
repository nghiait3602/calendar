<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function create(Request $request)
    {
        $validate = $request->validate([
            'name_event' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'user_id' => 'required',
            'categoris_id' => 'required'
        ], [
            'name_event.required' => 'Vui lòng nhập sự kiện',
            'start_time.required' => 'Vui lòng nhập thời gian',
            'end_time.required' => 'Vui lòng nhập thời gian',
            'user_id.required' => 'Không được để trống',
            'categoris_id.required' => 'Không được để trống',

        ]);
        $event = Event::create([
            "name_event" => $request['name_event'],
            "start_time" => $request['start_time'],
            "end_time" => $request['end_time'],
            "notes" => $request['notes'],
            "user_id" => $request['user_id'],
            "categoris_id" => $request['categoris_id'],
            "color" =>  $request['color']
        ]);
        return response()->json([
            "Event" => $event,
        ]);
    }
    public function deleteEvent($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        $event->delete();
        return response()->json(['message' => 'success'], 200);
    }
    public function updateEvent(Request $request, $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        $request->validate([
            'name_event' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'categoris_id' => 'required',
        ]);
        $event->name_event = $request->name_event;
        $event->start_time = $request->start_time;
        $event->end_time = $request->end_time;
        $event->categoris_id = $request->categoris_id;
        $event->notes = $request->notes;
        $event->color = $request->color;
        $event->save();

        return response()->json(['message' => 'Event updated successfully'], 200);
    }
}
