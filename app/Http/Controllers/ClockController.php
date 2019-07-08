<?php
/*
* Timely Sheets: Attendance Management System
* Email: mr.brianluna@gmail.com
* Version: 1.0
* Author: Brian Luna
* Copyright 2019 Brian Luna
* Website: https://github.com/brianluna/timelysheets
*/
namespace App\Http\Controllers;
use DB;
use App\Classes\table;
use App\Classes\permission;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClockController extends Controller
{
    
    public function clock()
    {
        $clock_comment = table::settings()->value('clock_comment');

        return view('clock', compact('clock_comment'));
    }

    public function add(Request $request)
    {

        if ($request->idno == null || $request->type == null ) {
            return response()->json([
                "error" => "Please provide your ID."
            ]);
        }

        $idno = strtoupper($request->idno);
        $type = $request->type;
        $date = date('Y-m-d');
        $time = date('h:i:s A');
        $comment = strtoupper($request->clock_comment);
        $ip = $request->ip();

        // clock-in comment feature
        $clock_comment = table::settings()->value('clock_comment');
        if ($clock_comment == 1) {
            if ($request->clock_comment == null ) {
                return response()->json([
                    "error" => "Please provide your comment!"
                ]);
            }
        }

        // ip resriction
        $iprestriction = table::settings()->value('iprestriction');
        if ($iprestriction != null) {
            $ips = explode(",", $iprestriction);
            if(in_array($ip, $ips) == false) {
                $msge = "Whoops! You are not allowed to Clock In or Out from your IP address ".$ip;
                return response()->json([
                    "error" => $msge,
                ]);
            }
        } 

        $employee_id = table::companydata()->where('idno', $idno)->value('reference');
        $emp = table::people()->where('id', $employee_id)->first();
        $lastname = $emp->lastname;
        $firstname = $emp->firstname;
        $mi = $emp->mi;
        $gender = $emp->gender;
        $civilstatus = $emp->civilstatus;
        $employee = mb_strtoupper($lastname.', '.$firstname.' '.$mi);

        if ($type == 'Time In') {
            $has = table::attendance()->where([['idno', $idno],['date', $date]])->whereNull('timeout')->value('timein');

            if ($has != null) {
                $hti = date('h:i A', strtotime($has));
                return response()->json([
                    "employee" => $employee,
                    "gender" => $gender,
                    "civilstatus" => $civilstatus,
                    "error" => "You already Time In today at ".$hti,
                ]);

            } else {

                $sched_in_time = table::schedules()->where([['idno', $idno], ['archive', 0]])->value('intime');
                if($sched_in_time == null){
                    $status_in = "Ok";
                } else {
                    $sched_clock_in_time_24h = date("H.i", strtotime($sched_in_time));
                    $time_in_24h = date("H.i", strtotime($time));
                    if ($time_in_24h <= $sched_clock_in_time_24h) {
                        $status_in = 'In Time';
                    } else {
                        $status_in = 'Late Arrival';
                    }
                }

                if($clock_comment == 1 && $comment != null) {
                    table::attendance()->insert([
                        [
                            'idno' => $idno,
                            'reference' => $employee_id,
                            'date' => $date,
                            'employee' => $employee,
                            'timein' => $date." ".$time,
                            'status_timein' => $status_in,
                            'comment' => $comment,
                        ],
                    ]);
                } else {
                    table::attendance()->insert([
                        [
                            'idno' => $idno,
                            'reference' => $employee_id,
                            'date' => $date,
                            'employee' => $employee,
                            'timein' => $date." ".$time,
                            'status_timein' => $status_in,
                        ],
                    ]);
                }

                return response()->json([
                    "type" => $type,
                    "time" => $time,
                    "date" => $date,
                    "lastname" => $lastname,
                    "firstname" => $firstname,
                    "mi" => $mi,
                    "gender" => $gender,
                    "civilstatus" => $civilstatus,
                    'totalhours' => null,
                ]);
            }
        }
  
        if ($type == 'Time Out') {
            $timeIN = table::attendance()->where('idno', $idno)->where('date', $date)->whereNull('timeout')->value('timein');
            $hasout = table::attendance()->where([['idno', $idno],['date', $date]])->orderBy('id', 'desc')->value('timeout');
            $timeOUT = date("Y-m-d h:i:s A", strtotime($date." ".$time));
            
            if ($hasout!= null) {
                $hto = date('h:i A', strtotime($hasout));
                return response()->json([
                    "employee" => $employee,
                    "gender" => $gender,
                    "civilstatus" => $civilstatus,
                    "error" => "You already Time Out today at ".$hto,
                ]);

            if($timeIN == null) {
                return response()->json([
                    "error" => "Please Clock In before Clocking Out."
                ]);
            } 



            } else {

                $sched_out_time = table::schedules()->where([['idno', $idno], ['archive', 0]])->value('outime');
                if($sched_out_time == null) {
                    $status_out = "Ok";
                } else {
                    $sched_clock_out_time_24h = date("H.i", strtotime($sched_out_time));
                    $time_out_24h = date("H.i", strtotime($timeOUT));
                    if($time_out_24h >= $sched_clock_out_time_24h) {
                        $status_out = 'On Time';
                    } else {
                        $status_out = 'Early Departure';
                    }
                }

                $time1 = date_create($timeIN); 
                $time2 = date_create($timeOUT);
                $diff = date_diff($time1,$time2);
                $totaltime = $diff->format('%h.%i');


                table::attendance()->where('idno', $idno)->where('date', $date)->whereNull('timeout')->update(array(
                    'timeout' => $timeOUT,
                    'totalhours' => $totaltime,
                    'status_timeout' => $status_out)
                );
                
                return response()->json([
                    "type" => $type,
                    "time" => $time,
                    "date" => $date,
                    "lastname" => $lastname,
                    "firstname" => $firstname,
                    "mi" => $mi,
                    "gender" => $gender,
                    "civilstatus" => $civilstatus
                ]);
            }
        }
    }
}
