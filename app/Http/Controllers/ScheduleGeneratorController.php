<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Group;
use App\Models\Room;
use App\Models\TeacherAssignment;
use App\Models\Teacher;
use App\Models\TeacherAvailability;
use Illuminate\Support\Facades\DB;

class ScheduleGeneratorController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/schedules/generate",
     *     summary="CU15 - Generar horarios automáticamente",
     *     description="Genera horarios para grupos sin asignación usando algoritmo greedy. Asigna automáticamente aulas y docentes disponibles.",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="period_id", type="integer", nullable=true, description="ID del periodo académico (opcional)")
     *     )),
     *     @OA\Response(
     *         response=200,
     *         description="Generación completada",
     *         @OA\JsonContent(
     *             @OA\Property(property="created", type="integer", description="Cantidad de horarios creados"),
     *             @OA\Property(property="schedules", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden - Solo administradores")
     * )
     * Generate schedules for a given period (or active period if not provided).
     * This is a simple greedy generator: for each group without schedules, try to
     * assign the first available timeslot/room/teacher that has no conflict.
     */
    public function generate(Request $request)
    {
        $periodId = $request->input('period_id');

        // Define simple time slots and days
        $days = ['monday','tuesday','wednesday','thursday','friday'];
        $slots = [
            ['08:00','10:00'],
            ['10:00','12:00'],
            ['13:00','15:00'],
            ['15:00','17:00'],
        ];

        $groups = Group::when($periodId, fn($q) => $q->where('period_id', $periodId))->get();
        $rooms = Room::all();

        $created = [];

        DB::beginTransaction();
        try {
            foreach ($groups as $group) {
                // skip if group already has a schedule in period
                $exists = Schedule::where('group_id', $group->id)->exists();
                if ($exists) continue;

                // find teacher assignments for this group's subject
                $assignments = TeacherAssignment::where('subject_id', $group->subject_id)
                    ->when($periodId, fn($q) => $q->where('period_id', $periodId))
                    ->get();

                if ($assignments->isEmpty()) continue; // cannot schedule without teacher

                $assigned = false;
                foreach ($days as $day) {
                    foreach ($slots as $slot) {
                        list($start,$end) = $slot;

                        // try each assignment/teacher and room
                        foreach ($assignments as $as) {
                            $teacherId = $as->teacher_id;

                            // ✅ NUEVO: Verificar disponibilidad del docente
                            $isAvailable = TeacherAvailability::isTeacherAvailable($teacherId, $day, $start, $end);
                            if (!$isAvailable) continue; // Saltar si el docente marcó este horario como no disponible

                            // check teacher free (ya asignado en otro horario)
                            $teacherBusy = Schedule::where('teacher_id', $teacherId)
                                ->where('day_of_week', $day)
                                ->where(function($q) use ($start,$end){
                                    $q->whereBetween('start_time', [$start,$end])
                                      ->orWhereBetween('end_time', [$start,$end])
                                      ->orWhere(function($qq) use ($start,$end){
                                          $qq->where('start_time','<=',$start)->where('end_time','>=',$end);
                                      });
                                })->exists();
                            if ($teacherBusy) continue;

                            // find a room free and big enough
                            $roomFound = null;
                            foreach ($rooms as $room) {
                                $roomBusy = Schedule::where('room_id', $room->id)
                                    ->where('day_of_week', $day)
                                    ->where(function($q) use ($start,$end){
                                        $q->whereBetween('start_time', [$start,$end])
                                          ->orWhereBetween('end_time', [$start,$end])
                                          ->orWhere(function($qq) use ($start,$end){
                                              $qq->where('start_time','<=',$start)->where('end_time','>=',$end);
                                          });
                                    })->exists();
                                if ($roomBusy) continue;

                                // capacity check (if group has capacity and room has capacity attribute)
                                $roomCapacity = $room->capacity ?? null;
                                if ($roomCapacity !== null && isset($group->capacity) && $roomCapacity < $group->capacity) continue;

                                $roomFound = $room;
                                break;
                            }

                            if (! $roomFound) continue;

                            // create schedule
                            $s = Schedule::create([
                                'group_id' => $group->id,
                                'room_id' => $roomFound->id,
                                'teacher_id' => $teacherId,
                                'day_of_week' => $day,
                                'start_time' => $start,
                                'end_time' => $end,
                                'assigned_by' => $request->user()->id ?? null,
                            ]);

                            $created[] = $s;
                            $assigned = true;
                            break 3; // move to next group
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message'=>'Generation failed','error'=>$e->getMessage()], 500);
        }

        return response()->json(['created' => count($created), 'schedules' => $created]);
    }
}
