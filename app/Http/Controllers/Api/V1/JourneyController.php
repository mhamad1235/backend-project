<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Journey;
use App\Models\Hotel;
use Illuminate\Validation\Rule;
class JourneyController extends Controller
{
       public function index()
    {
        $account = auth('account')->user();

     $journeys = $account->journeys()->with('images')->get();


        return response()->json($journeys);
    }
public function show($id)
{
    $journey = Journey::with(['images', 'translations'])->findOrFail($id);
    $this->authorize('view', $journey); 
    return response()->json($journey);
}

 public function store(Request $request)
{
    $account = auth('account')->user();

    $validated = $request->validate([
        'name' => 'required|string',
        'description' => 'required|string',
        'destination' => 'required|string',
        'duration' => 'required|integer',
        'price' => 'required|integer',
        'locale' => 'required|string|size:2',
        'images.*' => 'nullable|image|max:2048',
        'locations' => 'required|array|min:1',
        'locations.*.latitude' => 'required|numeric|between:-90,90',
        'locations.*.longitude' => 'required|numeric|between:-180,180',
    ]);

    $journey = $account->journeys()->create([
        'destination' => $validated['destination'],
        'duration' => $validated['duration'],
        'price' => $validated['price'],
    ]);

    // Translations
    $journey->translateOrNew($validated['locale'])->name = $validated['name'];
    $journey->translateOrNew($validated['locale'])->description = $validated['description'];
    $journey->save();

    // Save images
    if ($request->hasFile('images')) {
        $images = is_array($request->file('images')) ? $request->file('images') : [$request->file('images')];
        foreach ($images as $image) {
            $path = $image->store('uploads', 's3');
            $journey->images()->create(['path' => $path]);
        }
    }

    // Save multiple coordinates
    foreach ($validated['locations'] as $location) {
        $journey->locations()->create([
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
        ]);
    }

    return response()->json([
        'message' => 'Journey created successfully',
        'data' => $journey->load('images', 'locations'),
    ], 201);
}


public function update(Request $request, $id)
{   
   
    $account = auth('account')->user();
    $journey = Journey::where('id', $id)->firstOrFail();
    $this->authorize('update', $journey); 
    $validated = $request->validate([
        'name' => 'sometimes|required|string',
        'description' => 'sometimes|required|string',
        'destination' => 'sometimes|required|string',
        'duration' => 'sometimes|required|integer',
        'price' => 'sometimes|required|integer',
        'images.*' => 'nullable|image|max:2048',
        'locale' => 'sometimes|required|string|size:2', 
    ]);

    
    $journey->update($request->only(['destination', 'duration', 'price']));

    
   if ($request->has('locale') && $request->has('name') && $request->has('description')) {
    foreach (['en', 'ar', 'ckb'] as $locale) {
        $journey->translateOrNew($locale)->fill([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);
    }

    $journey->save(); 
}


 
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('journeys', 's3');
            $journey->images()->create(['path' => $path]);
        }
    }

    $journey->load('translations', 'images');
    return response()->json([
        'message' => 'Journey updated successfully',
        'data' => $journey
    ]);
}


    public function destroy($id)
    {
         $account = auth('account')->user();
         $journey = Journey::where('id', $id)->firstOrFail();
         $this->authorize('delete', $journey); 
         $journey->delete();

        return response()->json(['message' => 'Journey deleted']);
    }

  public function listOfJoined($id)
{
    $journey = Journey::with('registrationGroups.contactUser')->findOrFail($id);

    $grouped = $journey->registrationGroups
        ->map(function ($group) {
            $data = [
                'id' => $group->id,
                'journey_id' => $group->journey_id,
                'type' => $group->type,
                'paid' => $group->paid==1 ? true : false,
                'status' => $group->status,
                'total_people' =>$group->total_people,
                'contact_user' => $group->contactUser ? [
                    'id' => $group->contactUser->id,
                    'name' => $group->contactUser->name,
                    'phone' => $group->contactUser->phone,
                ] : null,
            ];
            if ($group->type === 'family') {
                $data['adults_count'] = $group->adults_count;
                $data['children_count'] = $group->children_count;
            }

            return $data;
        })
        ->groupBy('type'); 

    $filtered = [
        'id' => $journey->id,
        'tourist_id' => $journey->tourist_id,
        'destination' => $journey->destination,
        'duration' => $journey->duration,
        'price' => $journey->price,
        'is_favorite' => $journey->is_favorite,
        'name' => $journey->name,
        'description' => $journey->description,
        'registration_groups' => $grouped,
    ];

    return response()->json(['data' => $filtered]);
}

 public function available(Request $request)
    {
        $validated = $request->validate([
            'city_id'               => ['nullable','integer','exists:cities,id'],
            'limit'                 => ['nullable','integer','min:1','max:100'],
            'requests'              => ['required','array','min:1','max:50'],
            'requests.*.people'     => ['required','integer','min:1','max:20'],
            'requests.*.type'       => ['nullable', Rule::in(['family','group'])],
        ]);

        // Sort requests by people desc → makes greedy fit stronger
        $requests = collect($validated['requests'])
            ->map(fn($r) => ['people' => (int)$r['people'], 'type' => $r['type'] ?? null])
            ->sortByDesc('people')
            ->values();

        $minPeople = $requests->max('people');
        $cityId    = $validated['city_id'] ?? null;
        $limit     = $validated['limit']   ?? 20;

        // Pull hotels that have at least some rooms capable of the largest request
        $hotels = Hotel::query()
            ->when($cityId, fn($q) => $q->where('city_id', $cityId))
            ->whereHas('rooms', function ($q) use ($minPeople) {
                $q->where('is_active', true)
                  ->where('quantity', '>', 0)
                  ->where('guest', '>=', $minPeople);
            })
            ->with(['city:id', 'rooms' => function ($q) use ($minPeople) {
                $q->where('is_active', true)
                  ->where('quantity', '>', 0)
                  ->where('guest', '>=', $minPeople)
                  ->with('roomType:id'); // if you added is_family
            }])
            ->limit(200) // internal cap for processing
            ->get();

        $results = [];
        foreach ($hotels as $hotel) {
            $simulation = $this->tryAssignAll($requests, $hotel->rooms);
            if ($simulation !== false) {
                $results[] = [
                    'hotel_id'   => $hotel->id,
                    'hotel_name' => $hotel->name ?? null,
                    'city'       => $hotel->city?->name,
                    'latitude'   => $hotel->latitude ?? null,
                    'longitude'  => $hotel->longitude ?? null,
                    'assignments'=> $simulation['assignments'],
                    'rooms_used' => $simulation['rooms_used'],
                    'estimated_total_price' => $simulation['total_price'],
                ];
                if (count($results) >= $limit) break;
            }
        }

        return response()->json([
            'count' => count($results),
            'data'  => $results,
        ]);
    }

    /**
     * Try to assign each request to one room in the hotel's stock.
     * Greedy: for each request pick the least-overcapacity (then lowest price) room,
     * decrement its quantity; fail if any request can’t be placed.
     *
     * @param \Illuminate\Support\Collection $requests  [ ['people'=>int, 'type'=>?string] ... ]
     * @param \Illuminate\Support\Collection $rooms     HotelRoom[] with guest, quantity, price, roomType
     * @return array|false
     */
    protected function tryAssignAll($requests, $rooms)
    {
        // Build stock snapshot
        $stock = $rooms->map(function ($r) {
            return [
                'id'        => $r->id,
                'name'      => $r->name,
                'guest'     => (int) $r->guest,     // capacity
                'qty'       => (int) $r->quantity,  // how many identical units
                'price'     => $r->price !== null ? (float)$r->price : null,
                'is_family' => optional($r->roomType)->is_family ?? null, // null if not using the flag
            ];
        })->values()->all();

        $assignments = [];
        $roomsUsed   = [];
        $totalPrice  = 0.0;

        foreach ($requests as $idx => $req) {
            $people = (int) $req['people'];
            $needFamily = ($req['type'] ?? null) === 'family';

            // Filter candidates
            $candidates = array_values(array_filter($stock, function ($s) use ($people, $needFamily) {
                if ($s['qty'] <= 0) return false;
                if ($s['guest'] < $people) return false;
                if ($needFamily && $s['is_family'] === false) return false; // if flag exists & false
                return true;
            }));

            if (!$candidates) {
                return false; // cannot satisfy this hotel
            }

            // Choose least waste; tie by price asc (nulls last)
            usort($candidates, function ($a, $b) use ($people) {
                $wasteA = $a['guest'] - $people;
                $wasteB = $b['guest'] - $people;
                if ($wasteA === $wasteB) {
                    $pa = $a['price'] ?? INF;
                    $pb = $b['price'] ?? INF;
                    return $pa <=> $pb;
                }
                return $wasteA <=> $wasteB;
            });

            $chosen = $candidates[0];

            // Decrement from stock
            foreach ($stock as &$s) {
                if ($s['id'] === $chosen['id']) { $s['qty']--; break; }
            }

            $assignments[] = [
                'request_index' => $idx,
                'people'        => $people,
                'type'          => $req['type'] ?? null,
                'room_id'       => $chosen['id'],
                'room_name'     => $chosen['name'],
                'room_capacity' => $chosen['guest'],
                'unit_price'    => $chosen['price'],
            ];

            $roomsUsed[$chosen['id']] = ($roomsUsed[$chosen['id']] ?? 0) + 1;
            if ($chosen['price'] !== null) {
                $totalPrice += (float) $chosen['price'];
            }
        }

        return [
            'assignments'       => $assignments,
            'rooms_used'        => $roomsUsed,
            'total_price'       => round($totalPrice, 2),
        ];
    }
}
