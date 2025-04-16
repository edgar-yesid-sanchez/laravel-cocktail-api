@if(count($cocktails) > 0)
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
	@foreach($cocktails as $cocktail)
	<div
		x-data="{ 
        expanded: false,
        loading: false,
        expandCard(el) {
          this.$root.activeId = this.expanded ? null : '{{ $cocktail['idDrink'] }}';
          this.expanded = !this.expanded;

          if (this.expanded) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      }"
		x-ref="card"
		@click="expandCard($refs.card)"
		x-effect="expanded = $root.activeId === '{{ $cocktail['idDrink'] }}'"
		class="bg-white shadow rounded cursor-pointer overflow-hidden transition-all duration-500 ease-in-out hover:shadow-lg"
		:class="expanded ? 'col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-4' : ''">
		<img src="{{ $cocktail['strDrinkThumb'] }}" alt="{{ $cocktail['strDrink'] }}" class="w-full h-48 object-cover">

		<div class="p-4">
			<h2 class="text-lg font-semibold">{{ $cocktail['strDrink'] }}</h2>
			<p class="text-sm text-gray-600">{{ $cocktail['strCategory'] ?? 'Sin categoría' }}</p>

			<!-- CONTENIDO EXPANDIDO -->
			<div x-show="expanded"
				x-transition:enter="transition ease-out duration-500"
				x-transition:enter-start="opacity-0 scale-95"
				x-transition:enter-end="opacity-100 scale-100"
				x-transition:leave="transition ease-in duration-300"
				x-transition:leave-start="opacity-100 scale-100"
				x-transition:leave-end="opacity-0 scale-95"
				class="mt-4 text-sm text-gray-700">
				<template x-if="loading">
					<p class="text-orange-500">Cargando detalles...</p>
				</template>

				<template x-if="expanded">
					<div>
						<p class="mb-2"><strong>Tipo de bebida:</strong> {{$cocktail['strAlcoholic']}} </span></p>
						<p class="mb-2"><strong>Servir en:</strong> {{$cocktail['strGlass']}} </span></p>
						<p class="mb-2"><strong>Instrucciones:</strong> {{$cocktail['strInstructionsES']}} </span></p>
						<p><strong>Ingredientes:</strong></p>
						<ul
							x-data="{ 
        ingredients: {{ json_encode(array_map(function($i) use ($cocktail) {
            return $cocktail['strIngredient' . $i] ?? null;
        }, range(1, 15))) }}
    }"
							class="list-disc ml-6">
							<template x-for="(ingredient, index) in ingredients" :key="index">
								<li x-text="ingredient" x-show="ingredient"></li>
							</template>
						</ul>



						<!-- BOTÓN FAVORITO -->
						<form method="POST" action="{{ route('favorites.store') }}" class="save-fav-form mt-4">
							@csrf
							<input type="hidden" name="api_id" value="{{ $cocktail['idDrink'] }}">
							<input type="hidden" name="name" value="{{ $cocktail['strDrink'] }}">
							<input type="hidden" name="category" value="{{ $cocktail['strCategory'] }}">
							<input type="hidden" name="image" value="{{ $cocktail['strDrinkThumb'] }}">
							<button type="submit" class="mt-2 bg-orange-500 text-white px-4 py-1 rounded hover:bg-orange-600 text-sm">
								Guardar en favoritos
							</button>
						</form>
					</div>
				</template>
			</div>
		</div>
	</div>
	@endforeach
</div>
@else
<p class="text-gray-500">No se encontraron cócteles.</p>
@endif


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	document.addEventListener('DOMContentLoaded', () => {
		document.querySelectorAll('.save-fav-form').forEach(form => {
			form.addEventListener('submit', async function(e) {
				e.preventDefault();

				const formData = new FormData(this);
				const button = this.querySelector('button');

				button.disabled = true;
				button.innerText = 'Guardando...';

				try {
					const response = await fetch(this.action, {
						method: 'POST',
						body: formData,
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'X-CSRF-TOKEN': formData.get('_token')
						}
					});

					const data = await response.json();

					if (data.success) {
						Swal.fire({
							title: '¡Guardado!',
							text: 'El cóctel se guardó en tus favoritos.',
							icon: 'success',
							timer: 2000,
							showConfirmButton: false
						});

						button.innerText = 'Guardado ⭐';
						button.classList.remove('bg-orange-500');
						button.classList.add('bg-green-600', 'hover:bg-green-700');
					} else {
						throw new Error('Error inesperado');
					}
				} catch (err) {
					Swal.fire({
						title: 'Error',
						text: 'No se pudo guardar el cóctel.',
						icon: 'error',
						confirmButtonText: 'Aceptar'
					});

					button.disabled = false;
					button.innerText = 'Guardar en favoritos';
				}
			});
		});
	});
</script>