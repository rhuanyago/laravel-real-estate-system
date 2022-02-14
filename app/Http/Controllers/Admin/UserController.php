<?php

namespace RhDev\Http\Controllers\Admin;

use RhDev\User;
use Illuminate\Http\Request;
use RhDev\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use RhDev\Http\Requests\Admin\User as UserRequest;
use RhDev\Support\Cropper;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', [
            'users' => $users
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function team()
    {
        $users = User::where('admin', 1)->get();
        return view('admin.users.team', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $userCreate = User::create($request->all());

        if (!empty($request->file('cover'))) {
            $userCreate->cover = $request->file('cover')->storeAs('user', str_slug($request->name) . '-' . str_replace('.', '', microtime(true)) . '.' . $request->file('cover')->extension());
            $userCreate->save();
        }

        return redirect()->route('admin.users.edit', [
            'user' => $userCreate->id
        ])->with(['color' => 'green', 'message' => 'Cliente cadastrado com sucesso!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::where('id', $id)->first();

        return view('admin.users.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::where('id', $id)->first();

        $user->setLessorAttribute($request->lessor);
        $user->setLesseeAttribute($request->lessee);

        if (!empty($request->file('cover'))) {
            Storage::delete($user->cover);
            Cropper::flush($user->cover);
            $user->cover = '';
        }

        $user->fill($request->all());

        if (!empty($request->file('cover'))) {
            $user->cover = $request->file('cover')->storeAs('user', str_slug($request->name) . '-' . str_replace('.', '', microtime(true)) . '.' . $request->file('cover')->extension());
        }

        if (!$user->save()) {
            return redirect()->back()->withInput()->withErrors();
        }

        return redirect()->route('admin.users.edit', [
            'user' => $user->id
        ])->with(['color' => 'green', 'message' => 'Cliente atualizado com sucesso!']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
