<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeRequest;
use App\Models\Employe;
use App\Models\Salon;
use App\Models\Type_coiffure;
use App\Notifications\ChangeScheduleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class EmployeController extends Controller
{
    public function index()
    {
        $user=Auth::user()->id_user;
        $salon=Salon::where('user_id' , $user)->first();
        $employes = Employe::where('salon_id' , $salon->id_salon)->get();
        $coiffures=Type_coiffure::where('salon_id' , $salon->id_salon)->get();
        return response()->json(['employees' => $employes  , 'coiffures' => $coiffures]);
    }

    public function salon_employes()
    {
        $user=Auth::user();
        $salon=Salon::where('user_id' , $user->id_user)->first();
        $employees = Employe::where('salon_id', $salon->id_salon)->get();

        return response()->json(['data' => $employees]);
    }

    public function store(EmployeRequest $request)
    {
        $validatedData = $request->validated();
        $path = null;

        if ($request->hasFile('image')) {
            $filename = time().'.'.$request->image->extension();
            $path = $request->file('image')->storeAs(
                'employe_store',
                $filename,
                'public'
            );
            $validatedData['image'] = $path;
        }

        $employe = new Employe();
        $employe->name = $request->name;
        $employe->skills = $request->skills;
        $employe->description = $request->description;
        $employe->image = $path;
        $employe->hiring_date = $request->hiring_date;
        $employe->departure_date = $request->departure_date;
        $employe->work_hours = $request->work_hours;
        $employe->salary = $request->salary;
        $employe->status = "disponible";
        $employe->phone =  $request->phone;
        $employe->email =  $request->email;
        $employe->user_id = Auth::user()->id_user;


        $salon= Salon::where('user_id',  $employe->user_id)->first();
        //dd($employe->user_id);
        //dd($salon);

        $employe->salon_id = $salon->id_salon;

        $employe->save();

        return response()->json(['data' => $employe], 201);
    }

    public function show($id)
    {
        $employe = Employe::findOrFail($id);
        return response()->json(['data' => $employe]);
    }
    public function update(Request $request, $id)
    {

            $request->validate([
                'name' => 'required|string|max:255',
                'skills' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'hiring_date' => 'required|date',
                'departure_date' => 'nullable|date',
                'work_hours' => 'required|string|max:255',
                'salary' => 'required|numeric',
                'phone' => 'required|string|max:20',

            ]);


        $path = null;

        if ($request->hasFile('image')) {
            $filename = time().'.'.$request->image->extension();
            $path = $request->file('image')->storeAs(
                'employe_store',
                $filename,
                'public'
            );
        }

        $employe = Employe::findOrFail($id);
        $employe->name = $request->name;
        $employe->skills = $request->skills;
        $employe->description = $request->description;
        $employe->image = $path;
        $employe->hiring_date = $request->hiring_date;
        $employe->departure_date = $request->departure_date;
        $employe->work_hours = $request->work_hours;
        $employe->salary = $request->salary;
        $employe->status = "disponible";
        $employe->phone =  $request->phone;
        $employe->email =  $request->email;
        $employe->user_id = Auth::user()->id_user;

        $employe->save();

        $newSchedule = $request->work_hours;
        $employe->notify(new ChangeScheduleNotification($newSchedule));

        return response()->json(['data' => $employe]);
    }


    public function destroy($id)
    {
        $employe = Employe::findOrFail($id);
        $employe->delete();
        return response()->json(null, 204);
    }

    public function getEmployeDispo($id_salon)
    {
        $employesActifs = Employe::where('salon_id', $id_salon)
            ->where('status', 'Actif')
            ->pluck('name');

        Log::info('Employés actifs:', ['employes' => $employesActifs]);

        return response()->json(['data' => $employesActifs]);
    }

    public function employehairstyles(Request $request)
    {
        $request->validate([
            'hairstyle_type_id' => 'required|exists:type_coiffures,id_hairstyle_type',
            'employe_id' => 'required|exists:employes,id_employe',
             'duration' => 'required|date_format:H:i'
        ]);
        //dd($request);
        $data = $request->only(['hairstyle_type_id', 'employe_id', 'duration']);
        $data['uuid'] = Uuid::uuid4()->toString();
      DB::table('employe_type_coiffures')->insert($data);
      return response()->json(['message' => 'Create successfully','data'=> $data], 201);
    }

    public function getEmployeInfo($id)
    {
        
        $employe=Employe::where('id_employe' , $id)->first();
        return response()->json(['data' => $employe]);

    }


}
