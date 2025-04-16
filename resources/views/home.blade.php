<x-app-layout>

	<div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="mb-6 text-center">
			<h2 class="text-lg font-semibold mb-2">Filtrar por letra</h2>
			<div class="flex flex-wrap justify-center gap-2">
				@foreach (range('A', 'Z') as $char)
				<a href="{{ route('home', strtolower($char)) }}"
					class="px-3 py-2 rounded-full text-sm font-medium border
						{{ strtolower($char) === $letter ? 'bg-orange-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">
					{{ $char }}
				</a>
				@endforeach
			</div>
		</div>

		<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
			@forelse($cocktails as $cocktail)
			<div class="bg-white shadow rounded overflow-hidden">
				<img src="{{ $cocktail['strDrinkThumb'] }}" alt="{{ $cocktail['strDrink'] }}" class="w-full h-48 object-cover">
				<div class="p-4">
					<h2 class="text-lg font-semibold">{{ $cocktail['strDrink'] }}</h2>
					<p class="text-sm text-gray-600">{{ $cocktail['strCategory'] ?? 'Sin categoría' }}</p>
				</div>
			</div>
			@empty
			<p class="text-gray-500">No se encontraron cócteles.</p>
			@endforelse
		</div>
	</div>

</x-app-layout>