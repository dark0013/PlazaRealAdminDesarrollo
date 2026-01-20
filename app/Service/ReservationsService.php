<?php
namespace App\Service;

use App\Models\Reservations;
use Carbon\Carbon;
use Exception;

class ReservationsService
{
    const MAX_TIME = '19:00:00';

    /* ===================== CONSULTAS ===================== */

    public function getReservationsByIdDate(int $id, string $date)
    {
        // 1. Traer reservas de la cancha y fecha
        $reservations = Reservations::where('scenario_id', $id)
            ->whereDate('reservation_date', $date)
            ->get()
            ->keyBy('start_hour');  // start_hour = 9, 10, 11...

        // 2. Horario de 09:00 a 19:00
        $hours = range(9, 19);

        // 3. Armar la grilla
        $schedule = collect($hours)->map(function ($hour) use ($reservations) {
            if ($reservations->has($hour)) {
                return [
                    'hour' => sprintf('%02d:00', $hour),
                    'responsable' => $reservations[$hour]->user_name,
                    'disponibilidad' => 'RESERVADO'
                ];
            }

            return [
                'hour' => sprintf('%02d:00', $hour),
                'responsable' => null,
                'disponibilidad' => 'LIBRE'
            ];
        });

        return $schedule;
    }
}
