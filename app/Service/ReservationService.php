<?php

namespace App\Service;

use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;

class ReservationService
{
    public function getAllReservations()
    {
        $reservations = Reservation::all();
        return $reservations->isEmpty() ? null : $reservations;
    }

    public function getReservationById(int $id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return ['errors' => 'Reservation not found'];
        }
        return $reservation;
    }

    public function createReservation(array $data)
    {
        // Validation
        $validator = Validator::make($data, [
           /*  'athlete_id' => 'required|integer|exists:users,id',
            'court_id' => 'required|integer|exists:courts,id', */
            /* 'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time', */
            'duration' => 'required|integer',
            'status' => 'required|string',
            'reservation_type' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Create reservation
        $reservation = Reservation::create($data);

        if (!$reservation) {
            return ['errors' => 'Reservation creation failed'];
        }

        return $reservation;
    }

    public function updateReservation(int $id, array $data)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return ['errors' => 'Reservation not found'];
        }

        $validator = Validator::make($data, [
            /*  'athlete_id' => 'required|integer|exists:users,id',
            'court_id' => 'required|integer|exists:courts,id', */
            /* 'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time', */
            'duration' => 'required|integer',
            'status' => 'required|string',
            'reservation_type' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Update reservation
        $reservation->update($data);

        return $reservation;
    }

    /*   public function changeStatusReservation($id,  $status)
      {
          $reservation = Reservation::find($id);
          if (!$reservation) {
              return ['errors' => 'Reservation not found'];
          }

          $reservation->status = $status;
          $reservation->save();

          return $reservation;
      } */
}
