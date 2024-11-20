<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;

class EventController extends Controller
{
	use CanLoadRelationships;
	
	private array $relations = ['user', 'attendees', 'attendees.user'];
	
//	public function __construct()
//	{
//		$this->middleware('auth:sanctum')->except(['index', 'show']);
//		$this->authorizeResource(Event::class, 'event');
//	}
	
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        return EventResource::collection(Event::all());
//       return EventResource::collection(Event::with('user')->paginate());
	    
//	    $query = Event::query();
//	    $relations = ['user', 'attendees', 'attendees.user'];
//

//	    foreach ($relations as $relation) {
//		    $query->when(
//			    $this->shouldIncludeRelation($relation),
//			    fn($q) => $q->with($relation)
//		    );
//	    }
	    
	    $query = $this->loadRelationships(Event::query());
	    
	    return EventResource::collection(
            $query->latest()->paginate()
        );
    }
	
	
	// moved to CanLoadRelationships
//	protected function shouldIncludeRelation(string $relation): bool
//	{
//		$include = request()->query('include');
//
//		if (!$include) {
//			return false;
//		}
//
//		$relations = array_map('trim', explode(',', $include));
//
//		return in_array($relation, $relations);
//	}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
	    $event = Event::create([
		    ...$request->validate([
			    'name' => 'required|string|max:255',
			    'description' => 'nullable|string',
			    'start_time' => 'required|date',
			    'end_time' => 'required|date|after:start_time'
		    ]),
		    'user_id' => $request->user()->id
	    ]);
	    
	    return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
	public function show(Event $event)
    {
//	    $event->load('user', 'attendee');
//	    return new EventResource($this->loadRelationships($event));
		return new EventResource(
			$this->loadRelationships($event)
		);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $event->update(
			$request->validate([
				'name' => 'required|string|max:255',
				'description' => 'nullable|string',
				'start_time' => 'required|date',
				'end_time' => 'required|date|after:start_time'
			])
        );
	    
	    return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
		
		return response(status: 204);
    }
}
