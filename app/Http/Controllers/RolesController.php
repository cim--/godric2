<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\Member;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::with('member')
            ->orderBy('role')
            ->orderBy('restrictfield')
            ->orderBy('restrictvalue')
            ->get();
        return view('roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.form', [
            'role' => new Role(),
            'types' => Role::roleTypes(),
            'fields' => Role::roleFields(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, new Role());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('roles.form', [
            'role' => $role,
            'types' => Role::roleTypes(),
            'fields' => Role::roleFields(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $member = Member::where(
            'membership',
            $request->input('member')
        )->first();
        if (!$member) {
            return back()->with('message', 'Unknown member ID')->withInput();
        }

        $roletype = $request->input('role');
        if (!isset(Role::roleTypes()[$roletype])) {
            return back()->with('message', 'Unknown role type')->withInput();
        }
        $field = $request->input('restrictfield');
        if ($field && !isset(Role::roleFields()[$field])) {
            return back()
                ->with('message', 'Unknown restriction field')
                ->withInput();
        }
        $value = $request->input('restrictvalue');
        if ($field && !$value) {
            return back()
                ->with('message', 'A value must be specified')
                ->withInput();
        }

        $role->member_id = $member->id;
        $role->role = $roletype;
        if ($field) {
            $role->restrictfield = $field;
            $role->restrictvalue = $value;
        } else {
            $role->restrictfield = null;
            $role->restrictvalue = null;
        }
        $role->save();

        return redirect()->route('roles.index')->with('message', 'Role edited');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()
            ->route('roles.index')
            ->with('message', 'Role deleted');
    }
}
