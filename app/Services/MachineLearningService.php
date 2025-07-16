<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MachineLearningService
{
    public function segmentCustomers($customers)
    {
        $response = Http::post('http://localhost:5000/segment-customers', [
            'customers' => $customers
        ]);
        return $response->json('segments');
    }

    public function predictDemand($sales)
    {
        $response = Http::post('http://localhost:5000/predict-demand', [
            'sales' => $sales
        ]);
        return $response->json('forecast');
    }
} 