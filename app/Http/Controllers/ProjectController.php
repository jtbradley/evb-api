<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Project as ProjectResource;

class ProjectController extends Controller
{

    public function index(Request $request)
    {

    }

    public function show(Request $request)
    {
        return new ProjectResource($request);
    }

    // public function store(Request $request) {
    //     $project = new Project;
    //     $project->id = $request->id;
    //     $project->user()->associate($request->user());

    //     //$topic->posts()->save($post);
    //     return new ProjectResource($project);
    // }

}
