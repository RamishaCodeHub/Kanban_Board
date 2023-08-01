<?php

namespace App\Http\Controllers;
use App\Models\Card;
use App\Models\Card_Status;

use Illuminate\Http\Request;

class KanbanController extends Controller
{
  public function index()
    {
        $cardStatus = Card_Status::with('cards')->get();
        return response()->json($cardStatus);
    }

    public function storeCard(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
        ]);
    
        $title = $validatedData['title'];
    
        $status = Card_Status::where('id',$request->column_id)->first(); // Retrieve the card status

        $card = new Card();
        $card->title = $title;
        $card->status_id = $status->id; // Assign the card status ID
        $card->save();
    
        return response()->json($card);
    }

    public function storeColumn(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string'
        ]);

        $title = $validatedData['title'];

        $status = new Card_Status();
        $status->title = $title;
        $status->save();
        $status->cards()->createMany([]);

        return response()->json($status);
    }

    public function updateCardPosition(Request $request, $cardId)
    {
        $card = Card::findOrFail($cardId);  //check the card Id
        $card->status_id = $request->status_id;
        $card->save();
    
        return response()->json($card);
    }
}
