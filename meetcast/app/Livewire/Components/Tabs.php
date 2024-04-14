<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\SwipeMatch;

class Tabs extends Component
{

public function createConversation(SwipeMatch $match){
    $receiver=$match->swipe1->user_id==auth()->id()?$match->swipe2->user:$match->swipe1->user;

    Conversation::updateOrCreate(['match_id'=>$match->id],
                                 ['sender_id'=>auth()->id(),'receiver_id'=>$receiver->id]);

    #redirect

}

    public function render()
    {
        $matches=auth()->user()->matches()->get();
        return view('livewire.components.tabs',['matches'=>$matches]);

    }
}
