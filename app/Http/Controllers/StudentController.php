<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /** index page student list */
    public function student()
    {
        $studentList = Student::with('getDepartment')->get();
        return view('student.student',compact('studentList'));
    }

    /** index page student grid */
    public function studentGrid()
    {
        $studentList = Student::all();
        return view('student.student-grid',compact('studentList'));
    }

    /** student add page */
    public function studentAdd()
    {
        $departmentList = Department::all();
        return view('student.add-student',compact('departmentList'));

    }
    
    /** student save record */
    public function studentSave(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'gender'        => 'required|not_in:0',
            'date_of_birth' => 'required|string',
            'department'   => 'required|string',
            'address'      => 'required|string',
            'first_name'    => [
                'required',
                'string',
                Rule::unique('students')->where(function ($query) use ($request) {
                    return $query->where('last_name', $request->last_name);
                })
            ],
        ], [
            'first_name.unique' => 'The combination of first name and last name already exists.',
        ]);
        
        DB::beginTransaction();
        try {
            $student = new Student;
            $student->first_name   = $request->first_name;
            $student->last_name    = $request->last_name;
            $student->gender       = $request->gender;
            $student->date_of_birth= $request->date_of_birth;
            $student->department= $request->department;
            $student->address= $request->address;
            $student->save();

            Toastr::success('Student Has been added successfully :)','Success');
            DB::commit();

            return redirect()->back();
           
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('fail, Add new student  :)','Error');
            return redirect()->back();
        }
    }

    /** view for edit student */
    public function studentEdit($id)
    {
        $studentEdit = Student::where('id',$id)->first();
        $departmentList = Department::all();
        return view('student.edit-student',compact('studentEdit','departmentList'));
    }

    /** update record */
    public function studentUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $updateRecord = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'department' => $request->department,
                'address' => $request->address,
            ];
            Student::where('id',$request->id)->update($updateRecord);
            
            Toastr::success('Student update successfully :)','Success');
            DB::commit();
            return redirect()->back();
           
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('fail, update student  :)','Error');
            return redirect()->back();
        }
    }

    /** student delete */
    public function studentDelete(Request $request)
    {
        DB::beginTransaction();
        try {
           
            if (!empty($request->id)) {
                Student::destroy($request->id);
                DB::commit();
                Toastr::success('Student deleted successfully :)','Success');
                return redirect()->back();
            }
    
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Student deleted fail :)','Error');
            return redirect()->back();
        }
    }

    /** student profile page */
    public function studentProfile($id)
    {
        $studentProfile = Student::where('id',$id)->first();
        return view('student.student-profile',compact('studentProfile'));
    }

    /** get data list */
    public function getDataLists(Request $request)
    {
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column']; // Column index
        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value

        $students =  Student::with('getDepartment');

        $totalRecords = $students->count();

        $totalRecordsWithFilter = $students->where(function ($query) use ($searchValue) {
            $query->where('id', 'like', '%' . $searchValue . '%');
            $query->orWhere('first_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('last_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('gender', 'like', '%' . $searchValue . '%');
            $query->orWhere('date_of_birth', 'like', '%' . $searchValue . '%');
            $query->orWhere('department', 'like', '%' . $searchValue . '%');
            $query->orWhere('address', 'like', '%' . $searchValue . '%');
        })->count();

        $records = $students->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%');
                $query->orWhere('first_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('last_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('gender', 'like', '%' . $searchValue . '%');
                $query->orWhere('date_of_birth', 'like', '%' . $searchValue . '%');
                $query->orWhere('department', 'like', '%' . $searchValue . '%');
                $query->orWhere('address', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowPerPage)
            ->get();
        $data_arr = [];
        
        foreach ($records as $key => $record) {
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a href="'.url('student/edit/'.$record->id).'" class="btn btn-sm bg-danger-light">
                            <i class="far fa-edit me-2"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete student_id" data-bs-toggle="modal" data-student_id="'.$record->id.'" data-bs-target="#delete">
                        <i class="fe fe-trash-2"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr [] = [
                "id"         => $record->id,
                "first_name"       => $record->first_name,
                "last_name"       => $record->last_name,
                "gender"       => $record->gender,
                "date_of_birth"       => $record->date_of_birth,
                "address"    => $record->address,
                "department"    => $record->getDepartment->department_name ?? 'N/A',
                "modify"                => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData"               => $data_arr
        ];
        return response()->json($response);
    }
}
