<?php

namespace App\Http\Controllers;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketReplyResource;
use App\Http\Requests\TicketReplyStoreRequest;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $requst){
       try {
        $query = Ticket::query();
        $query->orderBy('created_at', 'desc');

        if ($requst->search){
            $query->where('code', 'like', '%' . $requst->search . '%')
            ->orWhere('title', 'like', '%' . $requst->search . '%');
        }
        if ($requst->status){
            $query->where('status', $requst->status);
        }
        if ($requst->priority){
            $query->where('priority', $requst->priority);
        }
        if (auth()->user()->role =='user'){
            $query->where('user_id', auth()->user()->id);
        }
        $ticket = $query->get();

        return response()->json([
            'message'=>'Ticket data has been displayed successfully.',
            'data'=> TicketResource::collection($ticket)
        ], 200);
       } catch (\Exception $e) {
        return response()->json([
            'message'=> 'Ticket Error',
            'data'=>null

         ], 500);
       }
    }
    public function show($code){
        try {
            $ticket = Ticket::where('code',$code)->first();
            if(!$ticket){
                return response()->json([
                    'message'=>'ticket error'
                ], 404);
            }
            if (auth()->user()->role == 'user' && $ticket->user_id != auth()->user()->id){
                return response()->json ([
                    'message'=>'You are not allowed to access this ticket.'
                ], 403);
            }
            return response()->json([
                'message'=>'The ticket has been displayed successfully.',
                'data'=> new TicketResource($ticket)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
              'message'=>'An error occurred.',
              'error'=> $e->getMessage()
            ],500);
        }
    }
    public function store(TicketStoreRequest $requst)
    {
        $data = $requst->validated();
        DB::beginTransaction();
        try {
             $ticket = new Ticket ;
             $ticket->user_id = auth()->user()->id;
             $ticket->code = 'TIC-' . rand(10000, 99999);
             $ticket->title = $data['title'];
             $ticket->description = $data['description'];
             $ticket ->priority = $data['priority'];
             $ticket->save();

             DB::commit();
             return response()->json([
                'message'=> 'Ticket Sucess',
                'data'=>new TicketResource($ticket)

             ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'=> 'Ticket Error',
                'data'=>null

             ], 500);

       
    }
}
    public function storeReply(TicketReplyStoreRequest $requst, $code)
    {
        $data = $requst->validated();
        DB::beginTransaction();
        try {
            $ticket = Ticket::where('code', $code)->first();

            if(!$ticket){
                return response ()->json([
                    'message'=>'Ticket not found.'
                ], 404);
            }
            if (auth()->user()->role == 'user' && $ticket ->user_id != auth()->user()->id){
                return response ()->json ([
                    'message'=>'You are not allowed to access this ticket.'
                ],403);
            }
            $ticketReply =new TicketReply();
            $ticketReply->ticket_id = $ticket->id;
            $ticketReply->user_id = auth()->user()->id;
            $ticketReply->content = $data['content'];
            $ticketReply->save();

            if(auth()->user()->role == 'admin'){
                $ticket->status = $data['status'];
                if ($data['status']=='resolved'){
                    $ticket->completed_at = now();
                }
                $ticket->save();
            }
            DB::commit();

            return response()->json([
                'message'=>'The reply was added successfully.',
                'data'=>new TicketReplyResource($ticketReply)

            ],201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'=> 'Ticket Error',
                'error'=>$e->getMessage()

             ], 500);
    }
   
}
}