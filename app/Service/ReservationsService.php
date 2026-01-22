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
    $reservations = Reservations::with('sportsman')
        ->where('scenario_id', $id)
        ->whereDate('reservation_date', $date)
        ->where('availability', 'RESERVADO')
        ->get();

    $reservedHours = [];

    foreach ($reservations as $reservation) {

        // 🔥 parse() evita el error Trailing data
        $startHour = Carbon::parse($reservation->start_time)->hour;

        // Caso: solo hora inicio
        if ($reservation->end_time === null) {
            $reservedHours[$startHour] = $reservation;
            continue;
        }

        $endHour = Carbon::parse($reservation->end_time)->hour;

        // Inclusivo visualmente
        for ($h = $startHour; $h <= $endHour; $h++) {
            $reservedHours[$h] = $reservation;
        }
    }

    $schedule = collect(range(9, 19))->map(function ($hour) use ($reservedHours) {

        if (isset($reservedHours[$hour])) {
            $reservation = $reservedHours[$hour];

            return [
                'hour' => sprintf('%02d:00', $hour),
                'reservation_id' => $reservation->id,
                'responsable' => $reservation->sportsman
                    ? $reservation->sportsman->name . ' ' . $reservation->sportsman->surname
                    : null,
                'disponibilidad' => 'RESERVADO'
            ];
        }

        return [
            'hour' => sprintf('%02d:00', $hour),
            'reservation_id' => null,
            'responsable' => null,
            'disponibilidad' => 'LIBRE'
        ];
    });

    return $schedule;
}

   
public function createReservation(array $data)
{
    /*
    |--------------------------------------------------------------------------
    | 0️⃣ Validar fecha no pasada
    |--------------------------------------------------------------------------
    */
    $reservationDate = Carbon::parse($data['reservation_date'])->startOfDay();
    $today = Carbon::today();

    if ($reservationDate->lt($today)) {
        return ['errors' => 'No se pueden agendar reservas en fechas anteriores al día actual'];
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ Construir hora inicio
    |--------------------------------------------------------------------------
    */
    $startTime = Carbon::createFromFormat('H:i', $data['start_time']);

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ Construir hora fin SOLO si viene
    |--------------------------------------------------------------------------
    */
    $endTime = null;
    if (!empty($data['end_time'])) {
        $endTime = Carbon::createFromFormat('H:i', $data['end_time']);
    }

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ Validar si es hoy, no permitir horas pasadas
    |--------------------------------------------------------------------------
    */
    if ($reservationDate->equalTo($today)) {
        if ($startTime->lt(Carbon::now())) {
            return ['errors' => 'No se pueden agendar reservas en horarios ya pasados'];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ Validar rango horario permitido
    |--------------------------------------------------------------------------
    */
    if (
        $startTime->lt(Carbon::createFromTime(9, 0)) ||
        $startTime->gte(Carbon::createFromTime(19, 0))
    ) {
        return ['errors' => 'Horario fuera del rango permitido (09:00 - 19:00)'];
    }

    if ($endTime) {
        if (
            $endTime->gt(Carbon::createFromTime(19, 0)) ||
            $endTime->lte($startTime)
        ) {
            return ['errors' => 'Rango horario inválido'];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 5️⃣ Validar solapamiento
    |--------------------------------------------------------------------------
    */
    $query = Reservations::where('scenario_id', $data['scenario_id'])
        ->whereDate('reservation_date', $data['reservation_date'])
        ->where('availability', 'RESERVADO');

    if ($endTime) {
        // 🔥 Caso rango
        $query->where(function ($q) use ($startTime, $endTime) {
            $q->where('start_time', '<', $endTime->format('H:i'))
              ->where('end_time', '>', $startTime->format('H:i'));
        });
    } else {
        // 🔥 Caso solo hora
        $query->where('start_time', $startTime->format('H:i'));
    }

    if ($query->exists()) {
        return ['errors' => 'La cancha ya está reservada en ese horario'];
    }

    /*
    |--------------------------------------------------------------------------
    | 6️⃣ Crear reserva
    |--------------------------------------------------------------------------
    */
    $reservation = Reservations::create([
        'scenario_id'        => $data['scenario_id'],
        'id_sportmen'        => $data['id_sportmen'],
        'reservation_date'  => $data['reservation_date'],
        'start_time'        => $startTime->format('H:i'),
        'end_time'          => $endTime?->format('H:i'), // 🔥 NULL si no viene
        'availability'      => 'RESERVADO',
        'responsable_person'=> $data['responsable_person'] ?? null,
    ]);

    return $reservation;
}

    public function releaseReservationsByScenarioAndDate(int $reservationId, string $date)
    {
        $reservations = Reservations::where('id', $reservationId)
            ->whereDate('reservation_date', $date)
            ->get();

        if ($reservations->isEmpty()) {
            return ['errors' => 'No existen reservas para ese escenario y fecha'];
        }

        $updated = false;

        foreach ($reservations as $reservation) {
            // NULL, vacío o LIBRE → ya está libre
            if (empty($reservation->availability) || $reservation->availability === 'LIBRE') {
                continue;
            }

            $reservation->availability = 'LIBRE';
            $reservation->save();

            if ($reservation->wasChanged('availability')) {
                $updated = true;
            }
        }

        if (!$updated) {
            return ['errors' => 'Las reservas ya se encontraban LIBRES'];
        }

        return $reservations;
    }
}
