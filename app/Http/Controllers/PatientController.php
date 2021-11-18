<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientResource;
use App\Models\Patients;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index() {
        $patients = PatientResource::collection(Patients::all());
   
        if($patients->isEmpty()) {
            $errorMessage = [
                "message" => "Data is empty",
                "success" => false
            ];
            
            return response()->json($errorMessage);
        } 
            $payloads = [
                "message" => "Get All Resource",
                "success" => true,
                "total" => count($patients),
                "data" => $patients
            ];
            
            return response()->json($payloads);
    }

    public function store(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|digits_between:10,12',
            'address' => 'required|string',
            'status_id' => 'required|integer',
            'date_in' => 'date',
            'date_out' => 'date',
        ]);
        $date_in = date('Y-m-d');
        $date_out = date('Y-m-d', strtotime('+14 days'));

        $patient = Patients::create([
            "name" => $fields['name'],
            "phone" => $fields['phone'],
            "address" => $fields['address'],
            "status_id" => $fields['status_id'],
            "date_in" => $request->date_in ? $request->date_in : $date_in,
            "date_out" => $request->date_out ? $request->date_out : $date_out
        ]);

        $payloads = [
            "message" => "Resource is added successfully",
            "success" => true,
            "data" => $patient
        ];

        return response()->json($payloads, 201);
    } 

    public function show($id) {
        $get_patient_by_id = Patients::find($id);
        
        if(!$get_patient_by_id) {
            $errorMessage = [
                "message" => "Resource not found",
                "success" => false
            ];
            
            return response()->json($errorMessage, 404);
        }
        
        $patient = PatientResource::make($get_patient_by_id);
        
        $payloads = [
            "message" => "Get Detail Resource",
            "success" => true,
            "data" => $patient
        ];

        return response()->json($payloads);
    }

    public function update(Request $request, $id) {
        $patient = Patients::find($id);

        if(!$patient) {
            $errorMessage = [
                "message" => "Resource not found",
                "success" => false
            ];
            
            return response()->json($errorMessage, 404);
        }
        
        $fields = $request->validate([
            'name' => 'string',
            'phone' => 'digits_between:10,12',
            'address' => 'string',
            'status_id' => 'integer',
            'date_in' => 'date',
            'date_out' => 'date',
        ]);

        
        $patient->update([
            'name' => ($request->name ? $fields['name'] : $patient->name),
            'phone' => ($request->phone ? $fields['phone'] : $patient->phone),
            'address' => ($request->address ? $fields['address'] : $patient->address),
            'status_id' => ($request->status_id ? $fields['status_id'] : $patient->status_id),
            'date_in' => ($request->date_in ? $fields['date_in'] : $patient->date_in),
            'date_out' => ($request->date_out ? $fields['date_out'] : $patient->date_out),
        ]);

        $payloads = [
            "message" => "Resource is update successfully",
            "success" => true,
            "data" => $patient
        ];

        return response()->json($payloads);
    }

    public function destroy($id) {
        $patient = Patients::find($id);

        if(!$patient) {
            $errorMessage = [
                "message" => "Resource not found",
                "success" => false
            ];

            return response()->json($errorMessage, 404);
        }

        $patient->delete();

        $payloads = [
            "message" => "Resource is delete successfully",
            "success" => true,
        ];

        return response()->json($payloads);
    }

    public function search($name) {
        $patient = Patients::where('name', 'like', "%$name%")->get();

        if($patient->isEmpty()) {
            $errorMessage = [
                "message" => "Resource not found",
                "success" => false
            ];

            return response()->json($errorMessage, 404);
        }
        
        $payloads = [
            'message' => 'Get searched resource',
            'success' => true,
            'data' => $patient
        ];
        
        return response()->json($payloads);
    }

    public function positive() {
        $positive_patients = Patients::where('status_id', '=', 1)->get();
        
        $payloads = [
            'message' => 'Get Positive Resource',
            'success' => true,
            'total' => count($positive_patients),
            'data' => $positive_patients
        ];

        return response()->json($payloads);
    }

    public function recovered() {
        $recovered_patients = Patients::where('status_id', '=', 2)->get();
        
        $payloads = [
            'message' => 'Get Recovered Resource',
            'success' => true,
            'total' => count($recovered_patients),
            'data' => $recovered_patients
        ];

        return response()->json($payloads);
    }

    public function dead() {
        $dead_patients = Patients::where('status_id', '=', 3)->get();
        
        $payloads = [
            'message' => 'Get Dead Resource',
            'success' => true,
            'total' => count($dead_patients),
            'data' => $dead_patients
        ];

        return response()->json($payloads);
    }
    
}