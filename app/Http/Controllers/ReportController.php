<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Item;
use App\Models\Program;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDO;

class ReportController extends Controller
{

    public function top_list_data()
    {
        $most_borrowed_books = DB::table('loaned_items')
            ->join('items', 'loaned_items.barcode', '=', 'items.barcode')
            ->select('items.title', 'items.author', DB::raw('COUNT(loaned_items.barcode) as borrow_count'))
            ->groupBy('items.title', 'items.author')
            ->orderBy('borrow_count', 'desc')
            ->limit(5)
            ->get();

        $top_visitors = DB::table('attendances')
            ->select('card_number', 'name', 'role', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('card_number', 'name', 'role')
            ->orderBy('visit_count', 'desc')
            ->limit(5)
            ->get();

        $recent_reported_items = DB::table('reported_items')
            ->join('items', 'reported_items.barcode', '=', 'items.barcode')
            ->join('users', 'reported_items.reporter_id', '=', 'users.id')
            ->select(
                'items.title',
                'items.author',
                'items.type',
                'reported_items.details',
                'reported_items.created_at',
                'users.name',
            )
            ->orderBy('reported_items.created_at', 'desc')
            ->limit(10)
            ->get();


        return [
            'top_visitors' => $top_visitors,
            'most_borrowed_books' => $most_borrowed_books,
            'recent_reported_items' => $recent_reported_items
        ];
    }

    public function top_list_print(Request $request)
    {
        $user_id = Auth::user()->card_number;
        $person = UserDetail::where('card_number', $user_id)->firstOrFail();
        $data = $this->top_list_data($request);
        $data['prepared_by'] = $person;

        return view('reports.top_list_print', $data);
    }

    public function top_list(Request $request)
    {
        $data = $this->top_list_data($request);

        return view('reports.top_list', $data);
    }

    public function item_list_data(Request $request)
    {
        $user    = UserDetail::where('email', Auth::user()->email)->first();
        $library = $user->library;

        $hasDateFilter = false;
        if($request->input('date_acquired_start') || $request->input('date_acquired_end')) {
            $date_acquired_start = (int) strtotime($request->input('date_acquired_start'));
            $date_acquired_end   = (int) strtotime($request->input('date_acquired_end'));

            if($date_acquired_start <= $date_acquired_end) {
                $hasDateFilter = true;
            }
        }

        $publishers =
            Item::select('publisher')
                ->distinct()
                ->where('library', $library)
                ->get();
        $publishers = $publishers->map(function($item) {
            return $item->publisher;
        });

        $hasYearFilter = false;
        if($request->input('publication_year')) {
            $years = explode('-', $request->input('publication_year'));
            $from = (int) $years[0] ?? 0;
            $to   = (int) $years[1] ?? 0;

            if($from <= $to) {
                $hasYearFilter = true;
            }
        }

        $pdo = DB::connection()->getPdo();
        $sql  = "SELECT * FROM `items` WHERE `library`=:library ";
        $sql .= ($request->input('publisher')==null) ? "" : "AND `publisher`=:publisher ";
        $sql .= ($request->input('type')==null)      ? "" : "AND `type`=:type ";
        $sql .= ($request->input('format')==null)    ? "" : "AND `format`=:format ";
        $sql .= ($request->input('genre')==null)     ? "" : "AND `genre`=:genre ";
        $sql .= ($request->input('status')==null)    ? "" : "AND `status`=:status ";
        $sql .= (!$hasYearFilter)                    ? "" : "AND (`publication_year` BETWEEN :from AND :to) ";
        $sql .= (!$hasDateFilter)                    ? "" : "AND (`date_acquired` BETWEEN :date_acquired_start AND :date_acquired_end) ";
        $sql .= "ORDER BY `date_acquired` DESC ";

        $query = $pdo->prepare($sql);
        $parameters = [];
        if($request->input('publisher')) $parameters['publisher'] = $request->input('publisher');
        if($request->input('type'))      $parameters['type']      = $request->input('type');
        if($request->input('format'))    $parameters['format']    = $request->input('format');
        if($request->input('genre'))     $parameters['genre']     = $request->input('genre');
        if($request->input('status'))    $parameters['status']    = $request->input('status');
        if($hasYearFilter) {
            $parameters['from'] = $from;
            $parameters['to']   = $to;
        }
        if($hasDateFilter) {
            $parameters['date_acquired_start'] = $request->input('date_acquired_start') ?? '0000-00-00';
            $parameters['date_acquired_end']   = $request->input('date_acquired_end') ?? date('Y-m-d');
        }

        $parameters['library'] = $library;

        $query->execute($parameters);
        $items = $query->fetchAll(PDO::FETCH_CLASS, 'stdClass');

        return [
            'items'      => $items,
            'publishers' => $publishers,
        ];
    }

    public function item_list_print(Request $request)
    {
        $user_id = Auth::user()->card_number;
        $person = UserDetail::where('card_number', $user_id)->firstOrFail();
        $data = $this->item_list_data($request);
        $data['prepared_by'] = $person;

        return view('reports.item_list_print', $data);
    }

    public function item_list(Request $request)
    {
        $data = $this->item_list_data($request);

        return view('reports.item_list', $data);
    }

    public function attendance_list_data(Request $request)
    {
        $user    = UserDetail::where('email', Auth::user()->email)->first();
        $library = $user->library;

        $hasDateFilter = false;
        if($request->input('from') || $request->input('to')) {
            $from = (int) strtotime($request->input('from'));
            $to   = (int) strtotime($request->input('to'));

            if($from <= $to) {
                $hasDateFilter = true;
            }
        }

        $pdo  = DB::connection()->getPdo();
        $sql  =
            "SELECT `attendances`.*, `user_details`.`college`, `user_details`.`year`, `user_details`.`section`
            FROM `attendances`
            INNER JOIN `user_details`
            ON `attendances`.`card_number` = `user_details`.`card_number`
            WHERE `library`=:library ";
        $sql .= ($request->input('college')==null) ? "" : "AND `user_details`.`college`=:college ";
        $sql .= ($request->input('program')==null) ? "" : "AND `attendances`.`program`=:program ";
        $sql .= ($request->input('year')==null)    ? "" : "AND `year`=:year ";
        $sql .= ($request->input('role')==null)    ? "" : "AND `user_details`.`role`=:role ";
        $sql .= (!$hasDateFilter)                  ? "" : "AND (`attendances`.`in` BETWEEN :from AND :to) ";
        $sql .= "ORDER BY `in` ASC ";

        $query = $pdo->prepare($sql);
        $parameters = [];
        if($request->input('college')) $parameters['college'] = $request->input('college');
        if($request->input('program')) $parameters['program'] = $request->input('program');
        if($request->input('year'))    $parameters['year']    = $request->input('year');
        if($request->input('role'))    $parameters['role']    = $request->input('role');
        if($hasDateFilter) {
            $parameters['from'] = $request->input('from') ?? '0000-00-00';
            $parameters['to']   = Carbon::parse($request->input('to') ?? date('Y-m-d'))->addDays(1);
        }

        $parameters['library'] = $library;

        $query->execute($parameters);
        $patrons = $query->fetchAll(PDO::FETCH_CLASS, 'stdClass');

        $colleges = College::all();
        $colleges = $colleges->map(function($college) {
            return [
                'key'   => $college->code,
                'value' => $college->name,
            ];
        });

        $programs = Program::all();
        $programs = $programs->map(function($program) {
            return [
                'key'   => $program->code,
                'value' => $program->name,
            ];
        });

        return [
            'patrons'  => $patrons,
            'colleges' => $colleges,
            'programs' => $programs,
        ];
    }

    public function attendance_list_print(Request $request)
    {
        $user_id = Auth::user()->card_number;
        $person  = UserDetail::where('card_number', $user_id)->firstOrFail();
        $data = $this->attendance_list_data($request);
        $data['prepared_by'] = $person;

        return view('reports.attendance_list_print', $data);
    }

    public function attendance_list(Request $request)
    {
        $data = $this->attendance_list_data($request);
        return view('reports.attendance_list', $data);
    }

    public function patron_list_data(Request $request)
    {
        $user    = UserDetail::where('email', Auth::user()->email)->first();
        $library = $user->library;

        $pdo  = DB::connection()->getPdo();
        $sql  = "SELECT * FROM `user_details` WHERE `library`=:library AND role NOT IN ('admin','staff','clerk','librarian') ";
        $sql .= ($request->input('college')==null) ? "" : "AND `college`=:college ";
        $sql .= ($request->input('program')==null) ? "" : "AND `program`=:program ";
        $sql .= ($request->input('year')==null)    ? "" : "AND `year`=:year ";
        $sql .= ($request->input('section')==null) ? "" : "AND `section`=:section ";
        $sql .= ($request->input('role')==null)    ? "" : "AND `role`=:role ";
        $sql .= ($request->input('status')==null)  ? "" : "AND `status`=:status ";

        $query = $pdo->prepare($sql);
        $parameters = [];
        if($request->input('college')) $parameters['college'] = $request->input('college');
        if($request->input('program')) $parameters['program'] = $request->input('program');
        if($request->input('year'))    $parameters['year']    = $request->input('year');
        if($request->input('section')) $parameters['section'] = $request->input('section');
        if($request->input('role'))    $parameters['role']    = $request->input('role');
        if($request->input('status'))  $parameters['status']  = $request->input('status');

        $parameters['library'] = $library;

        $query->execute($parameters);
        $patrons = $query->fetchAll(PDO::FETCH_CLASS, 'stdClass');

        $colleges = College::all();
        $colleges = $colleges->map(function($college) {
            return [
                'key'   => $college->code,
                'value' => $college->name,
            ];
        });

        $programs = Program::all();
        $programs = $programs->map(function($program) {
            return [
                'key'   => $program->code,
                'value' => $program->name,
            ];
        });

        return [
            'patrons'  => $patrons,
            'colleges' => $colleges,
            'programs' => $programs,
        ];
    }

    public function patron_list_print(Request $request)
    {
        $user_id = Auth::user()->card_number;
        $person  = UserDetail::where('card_number', $user_id)->firstOrFail();
        $data = $this->patron_list_data($request);
        $data['prepared_by'] = $person;

        return view('reports.patron_list_print', $data);
    }

    public function patron_list(Request $request)
    {
        $data = $this->patron_list_data($request);

        return view('reports.patron_list', $data);
    }

    public function item_count_list_data(Request $request)
    {
        $user    = UserDetail::where('email', Auth::user()->email)->first();
        $library = $user->library;

        $publishers =
            Item::select('publisher')
                ->distinct()
                ->where('library', $library)
                ->get();
        $publishers = $publishers->map(function($item) {
            return $item->publisher;
        });

        $hasYearFilter = false;
        if($request->input('publication_year')) {
            $years = explode('-', $request->input('publication_year'));
            $from = (int) $years[0] ?? 0;
            $to   = (int) $years[1] ?? 0;

            if($from <= $to) {
                $hasYearFilter = true;
            }
        }

        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET SESSION sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
        $sql = "SELECT *,
                `title` AS `_title`,
                `status` AS `_status`,
                (
                    SELECT COUNT(*) FROM items
                    WHERE `title`=`_title`
                    AND `status`=`_status`
                ) AS `count` ";
        $sql .= "FROM `items` WHERE `library`=:library ";
        $sql .= ($request->input('publisher')==null) ? "" : "AND `publisher`=:publisher ";
        $sql .= ($request->input('type')==null)      ? "" : "AND `type`=:type ";
        $sql .= ($request->input('format')==null)    ? "" : "AND `format`=:format ";
        $sql .= ($request->input('genre')==null)     ? "" : "AND `genre`=:genre ";
        $sql .= ($request->input('status')==null)    ? "" : "AND `status`=:status ";
        $sql .= (!$hasYearFilter)                    ? "" : "AND (`publication_year` BETWEEN :from AND :to) ";

        $sql .= "GROUP BY `title` ";
        $sql .= ($request->input('status')!=null) ? "" : ", `status` ";

        $query = $pdo->prepare($sql);
        $parameters = [];
        if($request->input('publisher')) $parameters['publisher'] = $request->input('publisher');
        if($request->input('type'))      $parameters['type']      = $request->input('type');
        if($request->input('format'))    $parameters['format']    = $request->input('format');
        if($request->input('genre'))     $parameters['genre']     = $request->input('genre');
        if($request->input('status'))    $parameters['status']    = $request->input('status');
        if($hasYearFilter) {
            $parameters['from'] = $from;
            $parameters['to']   = $to;
        }

        $parameters['library'] = $library;

        $query->execute($parameters);
        $items = $query->fetchAll(PDO::FETCH_CLASS, 'stdClass');

        return [
            'items'      => $items,
            'publishers' => $publishers,
        ];
    }

    public function item_count_list_print(Request $request)
    {
        $user_id = Auth::user()->card_number;
        $person  = UserDetail::where('card_number', $user_id)->firstOrFail();
        $data = $this->item_count_list_data($request);
        $data['prepared_by'] = $person;

        return view('reports.item_count_list_print', $data);
    }

    public function item_count_list(Request $request)
    {
        $data = $this->item_count_list_data($request);

        return view('reports.item_count_list', $data);
    }
}
