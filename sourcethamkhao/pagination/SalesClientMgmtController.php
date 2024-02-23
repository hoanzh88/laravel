<?php

namespace Modules\SalesClientMgmt\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Utility\Sanitize;
use Illuminate\Support\Collection;
use App\Models\Biz\SalesClientMgmtEmployeeDepartment;
use App\Models\Biz\SalesClientMgmtEmployee;

class SalesClientMgmtController extends Controller{
    protected $department_list;
    protected $employee_states;

    public function __construct(){
        $this->department_list  = $this->getDepartmentList();
        $this->states = SalesClientMgmtEmployee::STATE;
    }

    public function listEmployees(Request $request){
        // dd($this->states);
        $input = $request->all();
        $limit = Sanitize::getInt($input, 'limit', 10);
        $page = Sanitize::getInt($input, 'page', 1);
        $employee_name = trim(Sanitize::getVal($input, 'employee_name', ""));
        $employee_department = trim(Sanitize::getVal($input, 'employee_department', ""));
        $employee_start_date = trim(Sanitize::getVal($input, 'employee_start_date', ""));
        $employee_end_date = trim(Sanitize::getVal($input, 'employee_end_date', ""));
        $employee_state = trim(Sanitize::getVal($input, 'employee_state', ""));
        $searchParams['page'] = $page;
        $searchParams['limit'] = $limit;
        $searchParams['employee_name'] = $employee_name;
        $searchParams['employee_department'] = $employee_department;
        $searchParams['employee_start_date'] = $employee_start_date;
        $searchParams['employee_end_date'] = $employee_end_date;
        $searchParams['employee_state'] = $employee_state;
        $SalesClientMgmtEmployeeQuery = SalesClientMgmtEmployee::select('employee_id', 'full_name','email','department_id','start_date','end_date','state','created_by','created_at','updated_at');
    
        if (isset($employee_name) && trim($employee_name) != '') {
            $SalesClientMgmtEmployeeQuery->where('email', 'like', '%' . $employee_name . '%')->orWhere('full_name', 'like', '%' . $employee_name . '%');
        }

        if (isset($employee_department) && trim($employee_department) != '') {
            $SalesClientMgmtEmployeeQuery->where('department_id', '=', $employee_department);
        }
        
        if (isset($employee_state) && trim($employee_state) != '') {
            $SalesClientMgmtEmployeeQuery->where('state', '=', $employee_state);
        }

        if (isset($employee_start_date) && trim($employee_start_date) != '') {
            $employee_start_date_arr = explode('/',$employee_start_date);
            $employee_start_date_ymd = $employee_start_date_arr[2].'-'.$employee_start_date_arr[1].'-'.$employee_start_date_arr[0];
            $SalesClientMgmtEmployeeQuery->where('start_date', '>=', $employee_start_date_ymd);
        }

        if (isset($employee_end_date) && trim($employee_end_date) != '') {
            $employee_end_date_arr = explode('/',$employee_end_date);
            $employee_end_date_ymd = $employee_end_date_arr[2].'-'.$employee_end_date_arr[1].'-'.$employee_end_date_arr[0];
            $SalesClientMgmtEmployeeQuery->where('end_date', '<=', $employee_end_date_ymd);
        }

        $SalesClientMgmtEmployeeQuery = $SalesClientMgmtEmployeeQuery->orderBy('updated_at', 'DESC');
        $nextUrl = '';
        // if($employee_name && $employee_department && $employee_state && $employee_start_date && $employee_end_date){
            // echo "<pre>";
            // print_r($SalesClientMgmtEmployeeQuery->toSql());
            // print_r($SalesClientMgmtEmployeeQuery->getBindings());
            // echo "</pre>";
            $SalesClientMgmtEmployeeList = $SalesClientMgmtEmployeeQuery->paginate($limit);
            if($SalesClientMgmtEmployeeList->currentPage() < $SalesClientMgmtEmployeeList->lastPage()){
                $nextUrl = route('adminweb.sales-client-management.employee_list', [
                    'employee_name' => !empty($searchParams['employee_name']) ? $searchParams['employee_name'] : '',
                    'employee_department' => !empty($searchParams['employee_department']) ? $searchParams['employee_department'] : '',
                    'employee_state' => !empty($searchParams['employee_state']) ? $searchParams['employee_state'] : '',
                    'employee_start_date' => !empty($searchParams['employee_start_date']) ? $searchParams['employee_start_date'] : '',
                    'employee_end_date' => !empty($searchParams['employee_end_date']) ? $searchParams['employee_end_date'] : '',
                    'limit' => $limit,
                    'page' => $page + 1,
                ]);
            }
        // }else{
        //     $SalesClientMgmtEmployeeList = new Collection();
        // }
       
        // dd($SalesClientMgmtEmployeeList);
        // echo "currentPage() :" . $SalesClientMgmtEmployeeList->currentPage();
        // echo "lastPage() :" . $SalesClientMgmtEmployeeList->lastPage();
        // echo "nextUrl() :" . $nextUrl;
        

        // $SalesClientMgmtEmployeeList = $SalesClientMgmtEmployeeList->get();
        $currentUrl = route('adminweb.sales-client-management.employee_list', [
            'employee_name' => !empty($searchParams['employee_name']) ? $searchParams['employee_name'] : '',
            'employee_department' => !empty($searchParams['employee_department']) ? $searchParams['employee_department'] : '',
            'employee_state' => !empty($searchParams['employee_state']) ? $searchParams['employee_state'] : '',
            'employee_start_date' => !empty($searchParams['employee_start_date']) ? $searchParams['employee_start_date'] : '',
            'employee_end_date' => !empty($searchParams['employee_end_date']) ? $searchParams['employee_end_date'] : '',
        ]);

        $previousUrl = route('adminweb.sales-client-management.employee_list', [
            'employee_name' => !empty($searchParams['employee_name']) ? $searchParams['employee_name'] : '',
            'employee_department' => !empty($searchParams['employee_department']) ? $searchParams['employee_department'] : '',
            'employee_state' => !empty($searchParams['employee_state']) ? $searchParams['employee_state'] : '',
            'employee_start_date' => !empty($searchParams['employee_start_date']) ? $searchParams['employee_start_date'] : '',
            'employee_end_date' => !empty($searchParams['employee_end_date']) ? $searchParams['employee_end_date'] : '',
            'limit' => $limit,
            'page' => $page - 1,
        ]);
       
       
        $start_number = $limit * ($page - 1) + 1;
        $response = [
            'currentSearchParams'   => $searchParams,
            'SalesClientMgmtEmployeeList' => $SalesClientMgmtEmployeeList,
            'currentUrl'            => $currentUrl,
            'nextUrl'               => $nextUrl,
            'previousUrl'           => $previousUrl,
            'start_number'          => $start_number,
        ];
        $response['number_search_list'] = [10, 20, 30, 50, 100];
        return view('salesclientmgmt::employee', $response) ;
    }
    
    public function getDepartmentList(){
        $cacheDepartmentListName = 'data_sales_client_mgmt_company_1' . env('APP_ENV', 'production');
        if (Cache::has($cacheDepartmentListName)) {
            $departments = unserialize(Cache::get($cacheDepartmentListName));
        } else {
            $departmentsCollection  = SalesClientMgmtEmployeeDepartment::select('department_id', 'name')
                                                            ->where('state', 1)
                                                            ->get();
            $departments = $departmentsCollection->pluck('name','department_id')->toArray();
            Cache::remember($cacheDepartmentListName, 60*24*30, function () use($departments) {
                return serialize($departments);
            });
        }
        return $departments;
    }
    
    public function listResponsible(){
        return view('salesclientmgmt::index');
    }
}
