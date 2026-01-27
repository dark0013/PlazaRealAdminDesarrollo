<?php

namespace App\Service;

use App\Models\Tournament;
use App\Models\TournamentParticipant;
use Illuminate\Support\Facades\Validator;

class TournamentParticipantService
{
    /**
     * Registrar participante
     */
    /*  public function register(
         Tournament $tournament,
         int $sportsmanId,
         ?int $partnerId = null
     ): TournamentParticipant {

         if ($tournament->status !== 'ACTIVE') {
             throw ValidationException::withMessages([
                 'tournament' => 'El torneo no está activo'
             ]);
         }

         return TournamentParticipant::create([
             'tournament_id' => $tournament->id,
             'sportsman_id'  => $sportsmanId,
             'partner_id'    => $partnerId
         ]);
     } */
    public function register(array $data)
    {
       
        // 1️⃣ Obtener torneo
        $tournament = Tournament::find($data['tournament_id']);

        if (!$tournament) {
            return ['errors' => 'Torneo no encontrado'];
        }

        $sportsmanId = $data['sportsman_id'];
        $partnerId = $data['partner_id'] ?? null;

        /* ===================== VALIDACIONES ===================== */

        // 2️⃣ Validar tipo de torneo
        if ($tournament->isTeam == 0 && $partnerId !== null) {
            return ['errors' => 'El torneo es individual, no admite parejas'];
        }

        if ($tournament->isTeam == 1 && $partnerId === null) {
            return ['errors' => 'El torneo es por parejas'];
        }

        if ($partnerId !== null && $partnerId === $sportsmanId) {
            return ['errors' => 'Una persona no puede ser su propia pareja'];
        }

        /* ===================== CAPACIDAD ===================== */

        // 3️⃣ Obtener participantes actuales del torneo
        $participants = TournamentParticipant::where('tournament_id', $tournament->id)->get();

        // 4️⃣ Contar cupos ocupados según tipo de torneo
        $currentCupos = 0;

        if ($tournament->isTeam) {
            // Cada equipo ocupa 1 cupo
            $currentCupos = $participants->count();
            // Si el participante entrante es un equipo, ocupa 1 cupo
            $incomingCupos = 1;
        } else {
            // Torneo individual: cada jugador ocupa 1 cupo
            $currentCupos = 0;
            foreach ($participants as $participant) {
                $currentCupos++;  // sportsman principal
                if (!is_null($participant->partner_id)) {
                    $currentCupos++;  // pareja si existe
                }
            }
            $incomingCupos = $partnerId ? 2 : 1;
        }

        // 5️⃣ Validar contra partitioning_amount
        if (($currentCupos + $incomingCupos) > $tournament->partitioning_amount) {
            return ['errors' => 'No hay cupo disponible en el torneo'];
        }

        /* ===================== DUPLICADOS ===================== */

        // 7️⃣ Validar que no estén ya inscritos
        $exists = TournamentParticipant::where('tournament_id', $tournament->id)
            ->where(function ($q) use ($sportsmanId, $partnerId) {
                $q
                    ->where('sportsman_id', $sportsmanId)
                    ->orWhere('partner_id', $sportsmanId);

                if ($partnerId) {
                    $q
                        ->orWhere('sportsman_id', $partnerId)
                        ->orWhere('partner_id', $partnerId);
                }
            })
            ->exists();

        if ($exists) {
            return ['errors' => 'Uno de los participantes ya está inscrito en el torneo'];
        }

        /* ===================== REGISTRO ===================== */

        $participant = TournamentParticipant::create([
            'tournament_id' => $tournament->id,
            'sportsman_id' => $sportsmanId,
            'partner_id' => $partnerId,
            'teamName' => $data['teamName'],
        ]);

        return $participant;
    }

    /**
     * Quitar participante
     */
    public function remove(TournamentParticipant $participant): void
    {
        $participant->delete();
    }
}
