@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Service Details -->
        <div>
            <h1 class="text-3xl font-bold mb-4">{{ $service->name }}</h1>
            <p class="text-lg text-gray-600 mb-4">{{ $service->description }}</p>
            
            <div class="bg-gray-100 p-4 rounded-lg mb-6">
                <p class="font-semibold">Prix: <span class="text-xl text-blue-600">{{ number_format($service->price, 2, ',', ' ') }} €</span> / heure</p>
                <p class="text-sm text-gray-500 mt-2">Lieu: {{ $service->location }}</p>
                <p class="text-sm text-gray-500">Entreprise: {{ $service->enterprise->name }}</p>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-6">Réserver ce service</h2>
            
            <div id="calendar-container" class="mb-6">
                <!-- Simple FullCalendar or Custom Grid could go here. 
                     For MVP, we use a simple date picker with availability feedback. -->
                <p class="text-sm text-gray-500 mb-2">Vérifiez les disponibilités en sélectionnant une date.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('booking.book', $service->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="start_date">
                        Date
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="start_date" name="start_date" type="date" min="{{ date('Y-m-d') }}" required onchange="checkAvailability()">
                    <div id="availability-feedback" class="text-sm mt-2"></div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="start_time">
                        Heure de début
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="start_time" name="start_time" type="time" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="duration_hours">
                        Durée (heures)
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="duration_hours" name="duration_hours" type="number" min="1" max="8" value="1" required>
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" 
                            type="submit">
                        Ajouter au panier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    async function checkAvailability() {
        const dateInput = document.getElementById('start_date');
        const feedback = document.getElementById('availability-feedback');
        const date = dateInput.value;

        if (!date) return;

        feedback.innerHTML = '<span class="text-gray-500">Vérification...</span>';

        // Fetch availability for the selected day (00:00 to 23:59)
        const start = `${date} 00:00:00`;
        const end = `${date} 23:59:59`;

        try {
            const response = await fetch(`{{ route('booking.availability', $service->id) }}?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`);
            const events = await response.json();

            if (events.length > 0) {
                let html = '<p class="text-orange-600 font-semibold">Créneaux déjà occupés ce jour :</p><ul class="list-disc list-inside text-xs text-gray-600">';
                events.forEach(event => {
                    const startTime = new Date(event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const endTime = new Date(event.end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    html += `<li>${startTime} - ${endTime}</li>`;
                });
                html += '</ul>';
                feedback.innerHTML = html;
            } else {
                feedback.innerHTML = '<span class="text-green-600 font-semibold">Journée libre (sous réserve des heures d\'ouverture).</span>';
            }
        } catch (error) {
            console.error('Error fetching availability:', error);
            feedback.innerHTML = '<span class="text-red-500">Erreur lors de la vérification.</span>';
        }
    }
</script>
@endsection
