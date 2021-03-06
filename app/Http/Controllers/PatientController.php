<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientResource;
use App\Models\Patients;
use Illuminate\Http\Request;

class PatientController extends Controller
{   
    public function index() {
        $patients = PatientResource::collection(Patients::all());
        
        // Check if variable patients is empty
        if($patients->isEmpty()) {
            return $this->errorMessage('Data is empty', 200);
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

        // Validate each field with method validate
        $fields = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|digits_between:10,12',
            'address' => 'required|string',
            'status_id' => 'required|integer',
            'date_in' => 'date',
            'date_out' => 'date',
        ]);
        // Create automatically date 
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
        // Find patient by id
        $get_patient_by_id = Patients::find($id);

        // Check if patient not found
        if(!$get_patient_by_id) {
           return $this->errorMessage();
        }
        
        // Create collection using format in PatientResource 
        $patient = PatientResource::make($get_patient_by_id);
        
        $payloads = [
            "message" => "Get Detail Resource",
            "success" => true,
            "data" => $patient
        ];

        return response()->json($payloads);
    }

    public function update(Request $request, $id) {
        // Find patient by id
        $patient = Patients::find($id);

        // Check if patient not found
        if(!$patient) {
            return $this->errorMessage();
        }
        
        // Validate each field on request body
        $fields = $request->validate([
            'name' => 'string',
            'phone' => 'digits_between:10,12',
            'address' => 'string',
            'status_id' => 'integer',
            'date_in' => 'date',
            'date_out' => 'date',
        ]);

        // Update patient if patient is available
        // Update partial data, if not declare on body request, using old data
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
        // Find patient by id
        $patient = Patients::find($id);

        // Check if patient not found
        if(!$patient) {
            return $this->errorMessage();
        }

        // If patient is available so delete a data by id
        $patient->delete();

        $payloads = [
            "message" => "Resource is delete successfully",
            "success" => true,
        ];

        return response()->json($payloads);
    }

    public function search($name) {
        // Get data using 'where' and 'like'
        $patient = Patients::where('name', 'like', "%$name%")->get();

        // Check if variable patient is empty
        if($patient->isEmpty()) {
            return $this->errorMessage();
        }
        
        $payloads = [
            'message' => 'Get searched resource',
            'success' => true,
            'data' => $patient
        ];
        
        return response()->json($payloads);
    }

    public function positive() {
        // Get data using 'where' which is status_id is '1' (positive)
        $positive_patients = Patients::where('status_id', '=', 1)->get();
        
        // Check if variable positive_patients is empty
        if($positive_patients->isEmpty()) {
            return $this->errorMessage('Data positive patient is empty', 200);
        }

        $payloads = [
            'message' => 'Get Positive Resource',
            'success' => true,
            'total' => count($positive_patients),
            'data' => $positive_patients
        ];

        return response()->json($payloads);
    }

    public function recovered() {
         // Get data using 'where' which is status_id is '2' (recovery)
        $recovered_patients = Patients::where('status_id', '=', 2)->get();
        
        // Check if variable recovered_patients is empty
        if($recovered_patients->isEmpty()) {
            return $this->errorMessage('Data recovered patient is empty', 200);
        }

        $payloads = [
            'message' => 'Get Recovered Resource',
            'success' => true,
            'total' => count($recovered_patients),
            'data' => $recovered_patients
        ];

        return response()->json($payloads);
    }

    public function dead() {
        // Get data using 'where' which is status_id is '3' (dead)
        $dead_patients = Patients::where('status_id', '=', 3)->get();
        
        // Check if variable dead_patients is empty
        if($dead_patients->isEmpty()) {
            return $this->errorMessage('Data dead patient is empty', 200);
        }

        $payloads = [
            'message' => 'Get Dead Resource',
            'success' => true,
            'total' => count($dead_patients),
            'data' => $dead_patients
        ];

        return response()->json($payloads);
    }

    // Method for calling errorMessage if there is no data or data not found
    public function errorMessage($message = 'Resource not found', $statusCode = 404) {
        return response()->json([
            "message" => $message,
            "success" => false
        ], $statusCode);
    }
}