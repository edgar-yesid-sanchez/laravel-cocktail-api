<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cocktail;
use Illuminate\Http\JsonResponse;
class FavoriteCocktailsController extends Controller
{

	public function store(Request $request): JsonResponse
	{
		$data = $request->validate([
			'api_id' => 'required|string',
			'name' => 'required|string',
			'category' => 'nullable|string',
			'image' => 'nullable|string',
		]);

		$exists = Cocktail::where('user_id', auth()->id())
			->where('api_id', $data['api_id'])
			->exists();

		if ($exists) {
			return response()->json([
				'success' => false,
				'code' => 409,
				'type' => 'error',
				'message' => 'Este cóctel ya está guardado en tus favoritos.',
			], 409);
		}

		$data['user_id'] = auth()->id();
		Cocktail::create($data);

		return response()->json([
			'success' => true,
			'code' => 201,
			'type' => 'success',
			'message' => 'Cóctel guardado correctamente.',
		], 201); 
	}

	public function create()
	{
		$cocktailsList = Cocktail::where('user_id', auth()->id())->get();
		return view('favorite-cocktails.index', compact('cocktailsList'));
	}

 	public function destroy(Cocktail $cocktail)
	{
			if ($cocktail->user_id !== auth()->id()) {
					abort(403);
			}

			$cocktail->delete();

			return response()->json(['success' => true]);
	}

	public function update(Request $request, Cocktail $cocktail)
	{

		
			if ($cocktail->user_id !== auth()->id()) {
					abort(403);
			}

			$data = $request->validate([
					'name' => 'required|string',
					'category' => 'nullable|string',
			]);

			$cocktail->update($data);

			return response()->json(['success' => true, 'message' => 'Actualizado correctamente']);
	}

}
