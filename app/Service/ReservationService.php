<?php

namespace App\Service;

use App\Models\Reservation;
use Carbon\Carbon;
use Exception;

class ReservationService
{
    const MAX_TIME = '19:00:00';

    /* ===================== CONSULTAS ===================== */
    public function getAll()
    {
        $reservations = Reservation::orderBy('reservation_date')->get();

        if ($reservations->isEmpty()) {
            return ['errors' => 'No existen reservas'];
        }

        return $reservations;
    }

    public function getById(int $id)
    {
        try {
            return $this->findOrFail($id);
        } catch (Exception $e) {
            return ['errors' => $e->getMessage()];
        }
    }

    public function getByDate(string $date)
    {
        $reservations = Reservation::where('reservation_date', $date)
            ->orderBy('start_time')
            ->get();

        if ($reservations->isEmpty()) {
            return ['errors' => 'No existen reservas para esta fecha'];
        }

        return $reservations;
    }

    /* ===================== CREAR ===================== */

    public function create(array $data)
    {
        if ($error = $this->validateTime($data['start_time'], $data['end_time'])) {
            return ['errors' => $error];
        }

        if ($error = $this->validateDate($data['reservation_date'])) {
            return ['errors' => $error];
        }

        if ($error = $this->validateAvailability($data)) {
            return ['errors' => $error];
        }

        $data['duration'] = $this->calculateDuration(
            $data['start_time'],
            $data['end_time']
        );

        $data['status'] = Reservation::STATUS_PENDING;
        $data['reservation_type'] = Reservation::TYPE_NORMAL;

        $reservation = Reservation::create($data);

        if (!$reservation) {
            return ['errors' => 'No se pudo crear la reserva'];
        }

        return $reservation;
    }

    /* ===================== APROBAR ===================== */
    public function approve(int $id)
    {
        $reservation = $this->findOrFail($id);

        if ($reservation->status !== Reservation::STATUS_PENDING) {
            throw new Exception('Solo se pueden aprobar reservas pendientes');
        }

        $reservation->update(['status' => Reservation::STATUS_APPROVED]);
        return $reservation;
    }

    /* ===================== CANCELAR ===================== */
    public function cancel(int $id)
    {
        $reservation = $this->findOrFail($id);

        if ($reservation->status === Reservation::STATUS_CANCELLED) {
            throw new Exception('La reserva ya está cancelada');
        }

        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);
        return $reservation;
    }

    /* ===================== REAGENDAR ===================== */
    public function reschedule(int $id, string $start, string $end)
    {
        $reservation = $this->findOrFail($id);

        if ($reservation->reservation_date !== now()->toDateString()) {
            throw new Exception('Solo se puede reagendar el mismo día');
        }

        $this->validateTime($start, $end);

        $reservation->update([
            'start_time' => $start,
            'end_time' => $end,
            'duration' => $this->calculateDuration($start, $end)
        ]);

        return $reservation;
    }

    /* ===================== BLOQUEAR ===================== */
    public function block(array $data)
    {
        $this->validateTime($data['start_time'], $data['end_time']);

        return Reservation::create([
            'athlete_id' => null,
            'court_id' => $data['court_id'],
            'reservation_date' => $data['reservation_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'duration' => $this->calculateDuration(
                $data['start_time'],
                $data['end_time']
            ),
            'status' => Reservation::STATUS_BLOCKED,
            'reservation_type' => Reservation::TYPE_MAINTENANCE,
            'notes' => $data['notes'] ?? null
        ]);
    }

    /* ===================== VALIDACIONES ===================== */

    private function validateTime($start, $end)
    {
        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime = Carbon::createFromFormat('H:i', $end);

        if ($startTime->gte($endTime)) {
            throw new Exception('La hora de inicio debe ser menor a la hora fin');
        }

        if ($endTime->format('H:i:s') > self::MAX_TIME) {
            throw new Exception('No se puede reservar después de las 19:00');
        }
    }

    private function validateDate($date)
    {
        if (Carbon::parse($date)->isBefore(Carbon::today())) {
            return 'No se permiten reservas en fechas pasadas';
        }

        return null;
    }

    private function validateAvailability($data)
    {
        $exists = Reservation::where('court_id', $data['court_id'])
            ->where('reservation_date', $data['reservation_date'])
            ->where('status', '!=', Reservation::STATUS_CANCELLED)
            ->where(function ($q) use ($data) {
                $q
                    ->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']]);
            })
            ->exists();

        if ($exists) {
            return 'La cancha ya está reservada en ese horario';
        }

        return null;
    }

    private function calculateDuration($start, $end)
    {
        return Carbon::parse($start)->diffInMinutes(Carbon::parse($end));
    }

    private function findOrFail($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return ['errors' => 'Reserva no encontrada'];
        }

        return $reservation;
    }
}
