@section('scripts')
<script>
  document.addEventListener('submit', async function(e) {
    const form = e.target;

    if (form.classList.contains('delete-form') || form.classList.contains('update-form')) {
      e.preventDefault();

      const method = form.classList.contains('delete-form') ? 'DELETE' : 'PUT';
      const formData = new FormData(form);
      const url = form.action;
      console.log(formData); // para depuración
      try {
        const response = await fetch(url, {
          method: method,
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': formData.get('_token')
          }
        });
        if (!response.ok) {
          const errorData = await response.json();
          console.error('Errores de validación:', errorData);
          throw new Error('Validación fallida');
        }

        const data = await response.json();



        if (data.success) {
          Swal.fire({
            title: method === 'DELETE' ? 'Eliminado' : 'Actualizado',
            text: data.message || 'Operación completada',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
          });

          // Si fue eliminado, quitar la tarjeta del DOM
          if (method === 'DELETE') {
            form.closest('[x-data]').remove();
          }
        } else {
          throw new Error('Operación fallida');
        }
      } catch (error) {
        Swal.fire('Error', 'No se pudo completar la acción', 'error');
      }
    }
  });
</script>
@endsection
<x-app-layout>
  <div class="py-12 max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Mis Cócteles Favoritos</h1>


    @if(count($cocktailsList) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      @foreach($cocktailsList as $cocktail)
      <div
        x-data="{ 
          expanded: false, 
          details: null, 
          loading: false,
          editing: false,
          newName: '{{ $cocktail['name'] }}',
          newCategory: '{{ $cocktail['category'] }}',
          expandCard(el) {
            // no expandir/cerrar si estás editando
            if (this.editing) return;
            this.expanded = !this.expanded;
            if (this.expanded && !this.details) {
              this.loading = true;
              fetch(`https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i={{ $cocktail['api_id'] }}`)
                .then(res => res.json())
                .then(data => {
                  this.details = data.drinks[0];
                  this.loading = false;
                });
            }
            // Scroll al inicio de la card
            if (this.expanded) {
              el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          },
          updateCocktail() {
            fetch(`{{ route('favorites.update', $cocktail['id']) }}`, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
              },
              body: JSON.stringify({
                name: this.newName,
                category: this.newCategory,
              })
            })
            .then(res => {
              if (!res.ok) throw new Error('Error en la solicitud');
              return res.json();
            })
            .then(data => {
              if (data.success) {
                this.editing = false;
                Swal.fire('Actualizado', data.message, 'success');
              }
            })
            .catch(() => {
              Swal.fire('Error', 'No se pudo actualizar el cóctel.', 'error');
            });
          }
        }"
        x-ref="card"
        @click="expandCard($refs.card)"
        class="bg-white shadow rounded cursor-pointer overflow-hidden transition-all duration-500 ease-in-out hover:shadow-lg"
        :class="expanded ? 'col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-4' : ''">
        <img src="{{ $cocktail['image'] }}" alt="{{ $cocktail['name'] }}" class="w-full h-48 object-cover">

        <div class="p-4">
          <h2 class="text-lg font-semibold">{{ $cocktail['name'] }}</h2>
          <p class="text-sm text-gray-600">{{ $cocktail['category'] ?? 'Sin categoría' }}</p>

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

            <template x-if="details">
              <div>
                <p class="mb-2"><strong>Tipo de bebida:</strong> <span x-text="details.strAlcoholic"></span></p>
                <p class="mb-2"><strong>Servir en:</strong> <span x-text="details.strGlass"></span></p>
                <p class="mb-2"><strong>Instrucciones:</strong> <span x-text="details.strInstructionsES"></span></p>
                <p><strong>Ingredientes:</strong></p>
                <ul class="list-disc ml-6">
                  <template x-for="i in 15">
                    <li x-show="details[`strIngredient${i}`]" x-text="details[`strIngredient${i}`]"></li>
                  </template>
                </ul>

                <!-- BOTÓN FAVORITO -->
                <!-- Botón Eliminar -->
                <form method="POST" action="{{ route('favorites.destroy', $cocktail['id']) }}" class="mt-2 delete-form">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="bg-red-500 text-white px-4 py-1 rounded hover:bg-red-600 text-sm">
                    Eliminar
                  </button>
                </form>
                <button
                  @click="editing = true"
                  x-show="!editing"
                  class="mt-2 bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600 text-sm">
                  Editar
                </button>

                <!-- Editar -->
                <div x-show="editing" class="mt-2 space-y-2">
                <form @submit.prevent="updateCocktail" class="flex flex-col gap-2">
                    <input type="text" x-model="newName" class="w-full border border-gray-300 rounded p-1 text-sm">
                    <input type="text" x-model="newCategory" class="w-full border border-gray-300 rounded p-1 text-sm">
                    <div class="flex gap-2">
                      <button type="submit" class="bg-orange-600 text-white px-4 py-1 rounded hover:bg-orange-700 text-sm">Guardar</button>
                      <button type="button" @click="editing = false" class="text-gray-500 text-sm">Cancelar</button>
                    </div>
                  </form>
                </div>
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

  </div>
</x-app-layout>