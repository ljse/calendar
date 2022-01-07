<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use App\Http\Requests\EventsRequest;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app');
    }

    /**
     * Get events saved from database.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEvents()
    {
        $latestEvents = Event::select('id','days','event','from_date','to_date')->get();
        return response()->json($latestEvents);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventsRequest $request)
    {
        $atts = $request->validated();
        $atts['days'] = json_encode($request->days);
        Event::create($atts);
        return response()->json('Success');
    }
}
