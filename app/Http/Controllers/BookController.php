<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if(!$query){
            return response()->json([
                'message'=> 'Libro non trovato'
            ], 404);
        }

        $response = Http::get("https://www.googleapis.com/books/v1/volumes", [
            'q' => $query, 
            'maxResults' => 10
        ]);

        return $response->json();
    }

    public function import($id)
    {
        $response = Http::get("https://www.googleapis.com/books/v1/volumes/{$id}");
        $data = $response->json();

        $book = Book::updateOrCreate(
            ['google_id'=> $data['id']],
            [
                'title' => $data['volumeInfo']['title'] ?? 'Senza titolo',
                'authors' => isset($data['volumeInfo']['authors']) ? implode(', ', $data['volumeInfo']['authors']) : null,
                "publisher" => $data['volumeInfo']['publisher'] ?? null,
                "publisher_date" => $data['volumeInfo']['publisherDate'] ?? null,
                "description" => $data['volumeInfo']['description'] ?? null,
                'thumbnail' => $data['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                "categories" => $data['volumeInfo']['categories'] ? implode(', ', $data['volumeInfo']
                ['categories']) : null,
                "page_count" => $data['volumeInfo']['page_count'] ?? null
            ]
        );

        return $response->json($book);
    }
}
