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
    // 1. Traer reservas del escenario y fecha
    $reservations = Reservations::where('scenario_id', $id)
        ->whereDate('reservation_date', $date)
        ->get();

    // 2. Crear mapa por hora (9 - 19)
    $reservedHours = [];

    foreach ($reservations as $reservation) {
        $startHour = Carbon::createFromFormat('H:i:s', $reservation->start_time)->hour;
        $endHour   = Carbon::createFromFormat('H:i:s', $reservation->end_time)->hour;

        for ($h = $startHour; $h < $endHour; $h++) {
            $reservedHours[$h] = $reservation;
        }
    }

    // 3. Generar grilla 09:00 - 19:00
    $schedule = collect(range(9, 19))->map(function ($hour) use ($reservedHours) {

        if (isset($reservedHours[$hour])) {
            return [
                'hour' => sprintf('%02d:00', $hour),
                'responsable' => $reservedHours[$hour]->responsable_person,
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


    public function createReservation(array $data)
    {
        // Parseo de horas
        $startTime = Carbon::createFromFormat('H:i', $data['start_time']);

        $endTime = isset($data['end_time']) && $data['end_time']
            ? Carbon::createFromFormat('H:i', $data['end_time'])
            : (clone $startTime)->addHour();

        // 1️⃣ Validar horario permitido
        if (
            $startTime->lt(Carbon::createFromTime(9, 0)) ||
            $endTime->gt(Carbon::createFromTime(19, 0))
        ) {
            return ['errors' => 'Horario fuera del rango permitido (09:00 - 19:00)'];
        }

        // 2️⃣ Validar solapamiento
        $exists = Reservations::where('scenario_id', $data['scenario_id'])
            ->whereDate('reservation_date', $data['reservation_date'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query
                    ->where('start_time', '<', $endTime->format('H:i'))
                    ->where('end_time', '>', $startTime->format('H:i'));
            })
            ->exists();

        if ($exists) {
            return ['errors' => 'La cancha ya está reservada en ese horario'];
        }

        // 3️⃣ Crear reserva
        $reservation = Reservations::create([
            'scenario_id' => $data['scenario_id'],
            'id_sportmen' => $data['id_sportmen'],
            'reservation_date' => $data['reservation_date'],
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'availability' => 'RESERVADO',
            'responsable_person' => $data['responsable_person'],
        ]);

        if (!$reservation) {
            return ['errors' => 'No se pudo crear la reserva'];
        }

        return $reservation;
    }
}
