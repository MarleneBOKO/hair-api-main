<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Performance;
use App\Models\Type_coiffure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    
public function getPerformanceData()
{
    $employees = Employe::all();
    $performanceData = [];
    $hairstyleData = [];

    foreach ($employees as $employee) {
        // Tableau pour les performances globales de l'employé
        $totalClientsServed = 0;
        $totalRevenueGenerated = 0;

        // Récupérer les performances de l'employé
        $performances = Performance::where('employe_id', $employee->id_employe)->get();

        foreach ($performances as $performance) {
            // Calculer les totaux de clients servis et de revenu généré
            $totalClientsServed += $performance->clients_served;
            $totalRevenueGenerated += $performance->revenue_generated;
        }

        // Ajouter les performances globales de l'employé au tableau
        $performanceData[] = [
            'employee_id' => $employee->id_employe,
            'employee_name' => $employee->name,
            'total_clients_served' => $totalClientsServed,
            'total_revenue_generated' => $totalRevenueGenerated,
        ];
    
    }

    foreach ($employees as $employee) {
        // Tableau pour stocker les durées de chaque hairstyle de l'employé
        $hairstyleDurations = [];

        // Récupérer les performances de l'employé
        $performances = Performance::where('employe_id', $employee->id_employe)->get();

        foreach ($performances as $performance) {
            // Récupérer l'historique du service associé à la performance
            $service = $performance->historiqueService;
        
            // Récupérer le hairstyle associé à l'historique du service
            //$hairstyle = Type_coiffure::find($service->hairstyle_type_id);
        
            // Calculer la durée du service (en secondes)
            //$startTime = strtotime($service->heure_debut);
            //$endTime = strtotime($service->heure_fin);
            //$durationSeconds = $endTime - $startTime;     
            
             // Convertir la durée en heures, minutes et secondes
               // $hours = floor($durationSeconds / 3600);
               // $minutes = floor(($durationSeconds % 3600) / 60);
                //$seconds = $durationSeconds % 60;
            // Ajouter les données au tableau de durées de hairstyle
            $hairstyleData[] = [
                'employee_id' => $employee->id_employe,
                'employee_name' => $employee->name,
                'historique_service_id' => $service->id_service_history, // Ajouter l'ID de l'historique du service
                'hairstyle_id' => $service->hairstyle_name,
               // 'total_duration' => sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds), // Format HH:MM:SS           
             ];
        }
        
    }

    

    // Retourner les données en JSON
    return response()->json([
        'performance_data' => $performanceData
    ], 200);
}

}
