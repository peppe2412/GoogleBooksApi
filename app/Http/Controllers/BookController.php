<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{


    /* Funzione di ricerca libri, tramite l'API di Google */
    public function search(Request $request)
    {
        /* variabile per prendere il valore del parametro inviato */
        $query = $request->input('q');

        /* 
            Controlla se dopo la ricerca la query è vuota.
            Se lo è, ritorna lo status 404  
        */
        if(!$query){
            return response()->json([
                'message'=> 'Libro non trovato'
            ], 404);
        }

        /*
            Chiamata http dell'API,    
        */
        $response = Http::get("https://www.googleapis.com/books/v1/volumes", [
            /* libro cercato */ 
            'q' => $query, 

            /* massimo numero dei risulati */ 
            'maxResults' => 10
        ]);

        return $response->json();
    }


    /*
    Funzione per importare i libri e li salva nel database 
     */
    public function import($id)
    {
        /* richiesta http per prende le informazioni del libro */
        $response = Http::get("https://www.googleapis.com/books/v1/volumes/{$id}");
        $data = $response->json();

        /* inserisce il libro nel database, con dei specifici valori */
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

    public function index(){
        $books = Book::all();
        return response()->json($books);
    }

}
