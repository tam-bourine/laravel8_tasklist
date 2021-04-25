<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tasks',function(){
    $keyword = request()->get("keyword");
    $tasks = \DB::table("tasks")->where("name","like","%$keyword%")->get();
    return view('task.list',[
        "tasks" => $tasks
    ]);
});

Route::get('/tasks/new',function(){
    return view('task.new');
});

Route::post('/tasks/new',function(){
    $payload = [
        "name" => request()->get("name"),
        "date_on" => request()->get("date_on"),
        "body" => request()->get("body"),
    ];
    $rules = [
        "name" => ["required"],
        "date_on" => ["required"],
        "body" => ["required"],
    ];

    $val = validator($payload,$rules);
    if($val->fails()){
        session()->flash("old_form",$payload);
        session()->flash("errors",$val->errors()->toArray());
        return redirect("/tasks/new");
    }
    \DB::table("tasks")->insert($payload);
    return redirect("/tasks");
});

Route::get('/task/{id}',function($id){
    $task = \DB::table("tasks")->where("id",$id)->first();
    return view('task.detail',[
        "task" => $task
    ]);
});

Route::get('/task/{id}/edit',function($id){
    $task = \DB::table("tasks")->where("id",$id)->first();
    if(!session()->has("old_form")){
        session()->flash("old_form",[
            "name" => $task->name,
            "date_on" => substr($task->date_on,0,10),
            "body" => $task->body,
        ]);
    }
    return view('task.edit',[
        "task" => $task
    ]);
});

Route::post('/task/{id}/edit',function($id){
    $task = \DB::table("tasks")->where("id",$id)->first();
    $payload = [
        "name" => request()->get("name"),
        "date_on" => request()->get("date_on"),
        "body" => request()->get("body"),
    ];
    $rules = [
        "name" => ["required"],
        "date_on" => ["required"],
        "body" => ["required"],
    ];

    $val = validator($payload,$rules);
    if($val->fails()){
        session()->flash("old_form",$payload);
        session()->flash("errors",$val->errors()->toArray());
        return redirect("/task/$id/edit");
    }
    \DB::table("tasks")->where("id",$id)->update($payload);
    return redirect("/tasks");
});


Route::get('/task/{id}/done',function($id){
    $task = \DB::table("tasks")->where("id",$id)->first();
    return view('task.done',[
        "task" => $task
    ]);
});

Route::post('/task/{id}/done',function($id){
    \DB::table("tasks")->where("id",$id)->delete();
    return redirect("/tasks");
});

