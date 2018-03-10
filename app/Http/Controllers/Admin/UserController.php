<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseControllers;
use Validator;
use App\Department;

class UserController extends BaseControllers
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where('name','!=','Admin')->paginate();
        return view('admin.users.index', ['users' => $users]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::all(['id', 'name']);
        return view('admin.users.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userDetails = $request->only(['name', 'email', 'password', 'active', 'confirmed', 'department_id']);
        $userDetails['password'] = bcrypt($userDetails['password']);
        $role = Role::where('name', $request->only('role'))->first();
        \DB::transaction(function() use ($userDetails, $role) {
            $user = User::create($userDetails);
            $user->roles()->attach($role);
            $user->save();
        });
        return redirect(route('admin.users'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('admin.users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => Role::get()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return mixed
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'active' => 'sometimes|boolean',
            'confirmed' => 'sometimes|boolean',
        ]);

        $validator->sometimes('email', 'unique:users', function ($input) use ($user) {
            return strtolower($input->email) != strtolower($user->email);
        });

        $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            return $input->password;
        });

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->has('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        $user->active = $request->get('active', 0);
        $user->confirmed = $request->get('confirmed', 0);

        $user->save();

        //roles
        if ($request->has('roles')) {
            $user->roles()->detach();

            if ($request->get('roles')) {
                $user->roles()->attach($request->get('roles'));
            }
        }

        if($user->roles->pluck('name')->first() == 'student'){
            return redirect()->intended(route('admin.users.studentindex'));
        }
        return redirect()->intended(route('admin.users'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function exportExcel(Request $request)
    {
        $college_id = $request->input('college_id');
        $group_id = $request->input('group_id');
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'student');})->where('college_id', $college_id)->where('group_id', $group_id)->get();
        foreach ($users as $key => $value) {
            $userDetails[$key]['firstname'] = $value->contact->fname;
            $userDetails[$key]['lastname'] = $value->contact->lname;
            $userDetails[$key]['dateofbirth'] = $value->contact->dob;
            $userDetails[$key]['contactemail'] = $value->contact->cemail;
            $userDetails[$key]['phonenumber'] = $value->contact->phno;
            $userDetails[$key]['address'] = $value->contact->address;
            $userDetails[$key]['emergecy_contact_person'] = $value->contact->emergency_person;
            $userDetails[$key]['emergecy_contact_person_number'] = $value->contact->emergency_contact_no;

            $userDetails[$key]['firstname'] = $value->name;
            $userDetails[$key]['official_email'] = $value->email;
            $userDetails[$key]['password'] = "a123456";
            $userDetails[$key]['active'] = $value->active;
            $userDetails[$key]['confirmed'] = $value->confirmed;
            $userDetails[$key]['college_id'] = $value->college_id;
            $userDetails[$key]['group_id'] = $value->group_id;
        }
        \Excel::create('items', function($excel) use($userDetails) {
            $excel->sheet('ExportFile', function($sheet) use($userDetails) {
                $sheet->fromArray($userDetails);
            });
        })->export('xlsx');
    }

    public function importExcel(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'import_file' => 'required|mimes:xlsx'
        ]);
        
        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());
		if ($request->hasFile('import_file')) {
			$path = $request->file('import_file')->getRealPath();
			$rows = \Excel::load($path, function($reader) {
            })->toArray();
            $role = Role::where('name', 'student')->first();
            $this->deleteGroupUser($rows[0]['group_id']);
			foreach($rows as $row){
                if(!is_null($row['official_email'])){
                    $contactDetails['fname'] = $row['firstname'];
                    $contactDetails['lname'] = $row['lastname'];
                    $contactDetails['dob'] = $row['dateofbirth'];
                    $contactDetails['cemail'] = $row['contactemail'];
                    $contactDetails['phno'] = $row['phonenumber'];
                    $contactDetails['address'] = $row['address'];
                    $contactDetails['emergency_person'] = $row['emergecy_contact_person'];
                    $contactDetails['emergency_contact_no'] = $row['emergecy_contact_person_number'];
    
                    $userDetails['name'] = $row['firstname'];
                    $userDetails['email'] = $row['official_email'];
                    $userDetails['password'] = bcrypt($row['password']);
                    $userDetails['active'] = $row['active'];
                    $userDetails['confirmed'] = $row['confirmed'];
                    $userDetails['college_id'] = $row['college_id'];
                    $userDetails['group_id'] = $row['group_id'];

                    \DB::transaction(function() use ($userDetails, $contactDetails, $role) {
                        $user = User::create($userDetails);
                        $contact = Contact::create($contactDetails);
                        $user->contact()->associate($contact);
                        $user->roles()->attach($role);
                        $user->save();
                    });
                }

            }
		}
		return back()->with('success', 'Users Imported Sussesfully');
    }
    
    private function deleteGroupUser($group_id)
    {
        $users = User::where('group_id', $group_id)->get();
        foreach ($users as $user) {
            $user->contact()->delete();
            $user->roles()->detach();
        }
        \DB::table('users')->where('group_id', $group_id)->delete();
        
    }
}
