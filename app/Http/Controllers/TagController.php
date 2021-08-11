<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTagRequest;
use Illuminate\Http\Request;

class TagController extends Controller
{
    private $tagRepo;
    public function __construct(\App\Repositories\TagRepository $tagRepo)
    {
        $this->tagRepo = $tagRepo;
    }

    public function index(){
        return $this->tagRepo->getAll();
    }

    public function show($id){
        $tag = $this->tagRepo->find($id);
        return collect($tag->posts)->merge($tag->videos);
    }

    public function store(AddTagRequest $req){
        return $this->tagRepo->create(['name'=>$req->input('name')]);
    }

    public function storeAfter(AddTagRequest $req){
        \App\Jobs\AddTagJob::dispatch($req->input('name'));
        return 1;
    }
}
