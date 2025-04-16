@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    attachFavoriteFormListeners();
  });

  function toggleLoader(show) {
    const loader = document.getElementById('loader');
    loader.style.display = show ? 'block' : 'none';
  }

  function fetchCocktails(letter) {
    toggleLoader(true);
    $.get(`/api/cocktails/${letter}`, function(data) {
      $('#cocktail-container').html(data);
      attachFavoriteFormListeners();
      toggleLoader(false); // solo se oculta después de terminar
    });

    $('#letter-menu button').removeClass('bg-orange-500 text-white').addClass('text-gray-700');
    $(`#letter-menu button[data-letter="${letter}"]`).addClass('bg-orange-500 text-white');
  }

  function attachFavoriteFormListeners() {
    document.addEventListener('submit', async function handler(e) {
      // Desengancha el listener después de ejecutarse
      document.removeEventListener('submit', handler);
      if (!e.target.matches('.save-fav-form')) return; // solo si el form tiene la clase

      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);
      const button = form.querySelector('button');

      button.disabled = true;
      button.innerText = 'Guardando...';

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': formData.get('_token')
          }
        });

        const data = await response.json();
        console.log(data); // para depuración
        if (data.code == 201) {
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
        }else if(data.code === 409) {
          // Si el cóctel ya está guardado en favoritos
          Swal.fire({
            title: '¡Ya guardado!',
            text: 'Este cóctel ya está en tus favoritos.',
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
          });

          button.innerText = 'Ya guardado ⭐';
          button.classList.remove('bg-orange-500');
          button.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }else {
          throw new Error('Error inesperado');
        }
      } catch (err) {
        Swal.fire({
          title: 'Error',
          text: 'No se pudo guardar el cóctel.',
          icon: 'error',
          confirmButtonText: 'Aceptar',
          confirmButtonColor: 'bg-orange-500',
        });

        button.disabled = false;
        button.innerText = 'Guardar en favoritos';
      }
    });
  }
</script>
@endsection

<x-app-layout>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">

          <h2 class="text-lg font-semibold mb-4">Descubre tu próximo cóctel favorito</h2>
          <!-- Loader -->
          <div id="loader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; text-align: center; padding-top: 20%;">
            <div>
              <span class="spinner-border text-primary" role="status" aria-hidden="true"></span>
              <p>Cargando...</p>
            </div>
          </div>


          <!-- Selector de letra -->
          <div class="flex flex-wrap justify-center gap-2 mb-6" id="letter-menu">
            @foreach (range('A', 'Z') as $char)
            <button
              data-letter="{{ strtolower($char) }}"
              onclick="fetchCocktails('{{ strtolower($char) }}')"
              class="px-3 py-1 rounded-full text-sm font-medium border text-gray-700 hover:bg-gray-200">
              {{ $char }}
            </button>
            @endforeach
          </div>
          <!-- Lista de cocteles -->
          <div id="cocktail-container">
            @include('cocktails.cocktail-list')
          </div>

        </div>
      </div>
    </div>
  </div>

</x-app-layout>