<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class CocktailController extends Controller
{
    public function create(string $letter = 'a'): View
    {
        $letter = 'A'; // letra por defecto
        $cocktails = $this->fetchCocktailsByLetter('a');
        return view('cocktails.index', compact('cocktails', 'letter' ));
    }

    public function fetchCocktailsByLetter(string $letter): array
    {
        $response = Http::get("https://www.thecocktaildb.com/api/json/v1/1/search.php?f={$letter}");

        return $response->json()['drinks'] ?? [];
    }

    public function fetchAjax(string $letter)
    {
        $cocktails = $this->fetchCocktailsByLetter($letter);

        return view('cocktails.cocktail-list', compact('cocktails','letter'))->render();
    }


    

}