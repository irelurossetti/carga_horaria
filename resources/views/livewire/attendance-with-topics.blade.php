<div class="space-y-6">
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Registrar Asistencia</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Grupo</label>
                <select wire:model.live="selectedGroup" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="">Seleccionar grupo...</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }} - {{ $group->subject->name ?? 'Sin materia' }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Horario</label>
                <select wire:model="selectedSchedule" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="">Seleccionar horario...</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}">
                            {{ $schedule->day_of_week }} {{ $schedule->start_time }}-{{ $schedule->end_time }} 
                            ({{ $schedule->teacher->name ?? 'Sin docente' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                <input type="date" wire:model="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hora</label>
                <input type="time" wire:model="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select wire:model="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="present">Presente</option>
                    <option value="absent">Ausente</option>
                    <option value="late">Tardanza</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                <input type="text" wire:model="notes" placeholder="Observaciones..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>
        </div>
    </div>

    @if(count($topics) > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-book-open mr-2 text-brand-primary"></i>
            Temas Vistos en Esta Clase
        </h3>
        <p class="text-sm text-gray-600 mb-4">Selecciona los temas que se cubrieron en esta sesión</p>
        
        <div class="space-y-2 max-h-96 overflow-y-auto">
            @foreach($topics as $topic)
            <label class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                <input type="checkbox" wire:model="selectedTopics" value="{{ $topic->id }}" class="mt-1 mr-3 h-5 w-5 text-brand-primary focus:ring-brand-primary border-gray-300 rounded">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-brand-primary text-white text-xs font-medium rounded">
                            {{ $topic->order_index }}
                        </span>
                        <span class="font-medium text-gray-900">{{ $topic->unit_name }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $topic->topic_description }}</p>
                </div>
            </label>
            @endforeach
        </div>

        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Temas seleccionados:</strong> {{ count($selectedTopics) }} de {{ count($topics) }}
            </p>
        </div>
    </div>
    @endif

    <div class="flex justify-end">
        <button wire:click="saveAttendance" class="px-6 py-3 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
            <i class="fas fa-save mr-2"></i>Guardar Asistencia
        </button>
    </div>
</div>
