<?php

namespace App\Http\Controllers;

use App\Models\Coiffure;
use Illuminate\Http\Request;

class CoiffureController extends Controller
{
    public function index(Request $request) {
        $category = $request->query('category', '');
        $coiffures = Coiffure::all()->map(function ($coiffure) {
            $coiffure['category'] = strtolower($coiffure['category']);
            return $coiffure;
        })->groupBy('category');
    
        if ($category) {
            $category = strtolower($category);
            $filteredCoiffures = $coiffures->get($category, []);
        } else {
            $filteredCoiffures = $coiffures;
        }
    
        return response()->json(['data' => $filteredCoiffures]);
    }

        public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'category' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $coiffure = new Coiffure();
            $coiffure->name = $request->name;
            $coiffure->description = $request->description;
            $coiffure->category = $request->category;

            if ($request->hasFile('image')) {
                if($request->category=="Homme")
                    {
                        $filename = time() . '.' . $request->image->extension();
                        $path = $request->file('image')->storeAs(
                            'coiffure_store/homme',
                            $filename,
                            'public'
                        );
                        $coiffure->image=$path;
                    }
                if($request->category=="Femme")
                    {
                        $filename = time() . '.' . $request->image->extension();
                        $path = $request->file('image')->storeAs(
                            'coiffure_store/femme',
                            $filename,
                            'public'
                        );
                        $coiffure->image=$path;

                    }
                if($request->category=="Enfant")
                    {
                        $filename = time() . '.' . $request->image->extension();
                        $path = $request->file('image')->storeAs(
                            'coiffure_store/enfant',
                            $filename,
                            'public'
                        );
                        $coiffure->image=$path;
                    }
            }
            if ($request->hasFile('image1')) {
                if($request->category=="Homme")
                {
                    $filename = time() . '.' . $request->image1->extension();
                    $path = $request->file('image1')->storeAs(
                        'coiffure_store/homme',
                        $filename,
                        'public'
                    );
                    $coiffure->image1=$path;
                }
            if($request->category=="Femme")
                {
                    $filename = time() . '.' . $request->image1->extension();
                    $path = $request->file('image1')->storeAs(
                        'coiffure_store/femme',
                        $filename,
                        'public'
                    );
                    $coiffure->image1=$path;

                }
            if($request->category=="Enfant")
                {
                    $filename = time() . '.' . $request->image1->extension();
                    $path = $request->file('image1')->storeAs(
                        'coiffure_store/enfant',
                        $filename,
                        'public'
                    );
                    $coiffure->image1=$path;
                }        }
            if ($request->hasFile('image2')) {
                if($request->category=="Homme")
                {
                    $filename = time() . '.' . $request->image2->extension();
                    $path = $request->file('image2')->storeAs(
                        'coiffure_store/homme',
                        $filename,
                        'public'
                    );
                    $coiffure->image2=$path;
                }
            if($request->category=="Femme")
                {
                    $filename = time() . '.' . $request->image2->extension();
                    $path = $request->file('image2')->storeAs(
                        'coiffure_store/femme',
                        $filename,
                        'public'
                    );
                    $coiffure->image2=$path;

                }
            if($request->category=="Enfant")
                {
                    $filename = time() . '.' . $request->image2->extension();
                    $path = $request->file('image2')->storeAs(
                        'coiffure_store/enfant',
                        $filename,
                        'public'
                    );
                    $coiffure->image2=$path;
                }       
            }

            $coiffure->save();

            return response()->json(['message' => 'Coiffure created successfully', 'data' => $coiffure], 201);
    
        }   

        public function update(Request $request, $id)
        {
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string|max:255',
                'category' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $coiffure = Coiffure::findOrFail($id);

            if ($request->has('name')) {
                $coiffure->name = $request->name;
            }
            if ($request->has('description')) {
                $coiffure->description = $request->description;
            }
            if ($request->has('category')) {
                $coiffure->category = $request->category;
            }

            if ($request->hasFile('image')) {
                $filename = time() . '.' . $request->image->extension();
                if ($request->category == "Homme") {
                    $path = $request->file('image')->storeAs('coiffure_store/homme', $filename, 'public');
                } elseif ($request->category == "Femme") {
                    $path = $request->file('image')->storeAs('coiffure_store/femme', $filename, 'public');
                } elseif ($request->category == "Enfant") {
                    $path = $request->file('image')->storeAs('coiffure_store/enfant', $filename, 'public');
                }
                $coiffure->image = $path;
            }

            if ($request->hasFile('image1')) {
                $filename = time() . '.' . $request->image1->extension();
                if ($request->category == "Homme") {
                    $path = $request->file('image1')->storeAs('coiffure_store/homme', $filename, 'public');
                } elseif ($request->category == "Femme") {
                    $path = $request->file('image1')->storeAs('coiffure_store/femme', $filename, 'public');
                } elseif ($request->category == "Enfant") {
                    $path = $request->file('image1')->storeAs('coiffure_store/enfant', $filename, 'public');
                }
                $coiffure->image1 = $path;
            }

            if ($request->hasFile('image2')) {
                $filename = time() . '.' . $request->image2->extension();
                if ($request->category == "Homme") {
                    $path = $request->file('image2')->storeAs('coiffure_store/homme', $filename, 'public');
                } elseif ($request->category == "Femme") {
                    $path = $request->file('image2')->storeAs('coiffure_store/femme', $filename, 'public');
                } elseif ($request->category == "Enfant") {
                    $path = $request->file('image2')->storeAs('coiffure_store/enfant', $filename, 'public');
                }
                $coiffure->image2 = $path;
            }

            $coiffure->save();

            return response()->json(['message' => 'Coiffure updated successfully', 'data' => $coiffure], 200);
        }


        public function destroy($id)
        {
            $coiffure = Coiffure::findOrFail($id);
            $coiffure->delete();

            return response()->json(['message' => 'Coiffure deleted successfully'], 200);
        }

        public function getAllcoiffures()
        {
            $coiffures=Coiffure::all();
            return response()->json(['data' => $coiffures], 200);

        }
        public function getCoiffureInfo($id)
        {
            $coiffure=Coiffure::findOrfail($id);
            return response()->json(['coiffures'=>$coiffure]);
        }

}
