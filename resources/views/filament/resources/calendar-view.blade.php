<x-filament::page>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-bold mb-4">Appointment Calendar</h2>

        {{-- FullCalendar integration --}}
        <div id="calendar"></div>

        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@5.3.2/main.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.3.2/main.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.2/main.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['interaction', 'dayGrid'],
                    initialView: 'dayGridMonth',
                    events: @json($appointments), // Load events from backend
                    dateClick: function(info) {
                        alert('Date clicked: ' + info.dateStr);
                    },
                    eventClick: function(info) {
                        alert('Appointment: ' + info.event.title + ', Status: ' + info.event.extendedProps
                            .status);
                    }
                });
                calendar.render();
            });
        </script>
    </div>
</x-filament::page>
