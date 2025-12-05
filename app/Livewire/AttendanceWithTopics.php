<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\SyllabusTopic;
use Illuminate\Support\Facades\Auth;

class AttendanceWithTopics extends Component
{
    public $groups = [];
    public $selectedGroup = null;
    public $schedules = [];
    public $selectedSchedule = null;
    public $date;
    public $time;
    public $status = 'present';
    public $notes = '';
    public $topics = [];
    public $selectedTopics = [];

    public function mount()
    {
        $this->date = date('Y-m-d');
        $this->time = date('H:i');
        $this->loadGroups();
    }

    public function loadGroups()
    {
        $this->groups = Group::with('subject')->get();
    }

    public function updatedSelectedGroup($value)
    {
        if ($value) {
            $group = Group::find($value);
            if ($group && $group->subject_id) {
                $this->topics = SyllabusTopic::where('subject_id', $group->subject_id)
                    ->orderBy('order_index')
                    ->get();
                
                $this->schedules = Schedule::where('group_id', $value)->with('teacher')->get();
            } else {
                $this->topics = [];
                $this->schedules = [];
            }
        } else {
            $this->topics = [];
            $this->schedules = [];
        }
        $this->selectedTopics = [];
    }

    public function saveAttendance()
    {
        $this->validate([
            'selectedSchedule' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:present,absent,late',
        ]);

        $schedule = Schedule::find($this->selectedSchedule);
        
        if (!$schedule) {
            session()->flash('error', 'Horario no encontrado');
            return;
        }

        $attendance = Attendance::create([
            'teacher_id' => $schedule->teacher_id,
            'schedule_id' => $this->selectedSchedule,
            'date' => $this->date,
            'time' => $this->time,
            'status' => $this->status,
            'notes' => $this->notes,
            'recorded_by' => Auth::id(),
        ]);

        // Sincronizar temas
        if (!empty($this->selectedTopics)) {
            $attendance->topics()->sync($this->selectedTopics);
        }

        session()->flash('success', 'Asistencia registrada correctamente');
        
        // Limpiar formulario
        $this->reset(['selectedSchedule', 'notes', 'selectedTopics']);
        $this->date = date('Y-m-d');
        $this->time = date('H:i');
    }

    public function render()
    {
        return view('livewire.attendance-with-topics');
    }
}
