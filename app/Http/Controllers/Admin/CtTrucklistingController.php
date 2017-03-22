<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\CtTrucklisting;
use App\Http\Requests\CreateCtTrucklistingRequest;
use App\Http\Requests\UpdateCtTrucklistingRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\FileUploadTrait;
use App\Ctcondition;
use App\CtMake;
use App\CtModels;
use App\Ctregistered;
use App\Ctcolor;
use App\Ctexteriorcolor;
use App\Ctfueltype;
use App\Ctbodystyle;
use App\Cttransmission;
use App\EngineSize;
use App\Bodytype;
use App\Ctsubmodels;
use DB;

class CtTrucklistingController extends Controller {

    /**
     * Display a listing of cttrucklisting
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        /* ->where('id', $id) */

        $query = CtTrucklisting::with("ctcondition")->with("ctmake")->with("ctmodels")->with("ctregistered")->with("ctcolor")->with("ctexteriorcolor")->with("ctfueltype")->with("ctbodystyle")->with("cttransmission")->with("enginesize")->with("bodytype");

        if (isset($_REQUEST['y']) and $_REQUEST['y'] != "") {
            $s = $_REQUEST['y'];
            $query = $query->Where('ctregistered_id', $s);
        }

        if (isset($_REQUEST['make']) and $_REQUEST['make'] != "" and $_REQUEST['make'] != 0) {
            $query = $query->Where('ctmake_id', $_REQUEST['make']);
        }

        if (isset($_REQUEST['mod']) and $_REQUEST['mod'] != "" and $_REQUEST['mod'] != 0) {
            $mod = $_REQUEST['mod'];
            $query = $query->Where('ctmodels_id', $mod);
        }
        if (isset($_REQUEST['con']) and $_REQUEST['con'] != "") {

            $ctcondition_id = $_REQUEST['con'];
            $query = $query->Where('ctcondition_id', $ctcondition_id);
        }

        if (isset($_REQUEST['s']) and $_REQUEST['s'] != "") {

            $s = $_REQUEST['s'];
            $query = $query->Where('status', $s);
        }

        if (isset($_REQUEST['stock']) and $_REQUEST['stock'] != "") {

            $s = $_REQUEST['stock'];
            $query = $query->Where('stock_auto', $s);
        }
        
        if (isset($_REQUEST['featured']) and $_REQUEST['featured'] != "" and $_REQUEST['featured'] != "all") {

            $s = $_REQUEST['featured'];
            $query = $query->Where('featured', $s);
        }

        $query = $query->orderBy('id', 'desc');
        $cttrucklisting = $query->paginate(25);
        $ctcondition = DB::table('ctcondition')->whereNull('deleted_at')->get();
        $ctmake = DB::table('ctmake')->whereNull('deleted_at')->get();
        $ctmodels = DB::table('ctmodels')->whereNull('deleted_at')->get();
        $ctregistered = DB::table('ctregistered')->whereNull('deleted_at')->get();

        $img_arr = '';
        require 'PhotoAlbum/main.php';
        foreach ($cttrucklisting as $row) {

            if ($row->public_id) {
                $img_arr[$row->id] = cl_image_tag($row->public_id, array_merge($thumbs_params, array("width" => 150, "height" => 100, "crop" => "fill")));
            }
        }
        return view('admin.cttrucklisting.index', compact('cttrucklisting', "ctcondition", "ctmake", "ctmodels", "ctregistered", "img_arr"));
    }

    /**
     * Show the form for creating a new cttrucklisting
     *
     * @return \Illuminate\View\View
     */
    public function create() {

        $ctcondition = DB::table('ctcondition')->whereNull('deleted_at')->get();
        /* $ctmake = CtMake::pluck("make", "id")->prepend('Please select', null); */
        $ctmake = DB::table('ctmake')->whereNull('deleted_at')->get();

        /* $ctmodels = CtModels::pluck("model", "id")->prepend('Please select', null); */

        $ctmodels = DB::table('ctmodels')->whereNull('deleted_at')->get();
        $ctregistered = DB::table('ctregistered')->whereNull('deleted_at')->get();
        $ctcolor = DB::table('ctcolor')->whereNull('deleted_at')->get();
        $ctexteriorcolor = DB::table('ctexteriorcolor')->whereNull('deleted_at')->get();
        $ctfueltype = DB::table('ctfueltype')->whereNull('deleted_at')->get();
        $ctbodystyle = DB::table('ctbodystyle')->whereNull('deleted_at')->get();
        $cttransmission = DB::table('cttransmission')->whereNull('deleted_at')->get();

        $enginesize = DB::table('enginesize')->whereNull('deleted_at')->get();
        $horsepower = DB::table('horsepower')->whereNull('deleted_at')->get();
        $torque = DB::table('torque')->whereNull('deleted_at')->get();
        $drivetrain = DB::table('drivetrain')->whereNull('deleted_at')->get();
        $maxseating = DB::table('maxseating')->whereNull('deleted_at')->get();
        $bodytype = DB::table('bodytype')->whereNull('deleted_at')->get();
        return view('admin.cttrucklisting.create', compact("ctcondition", "ctmake", "ctmodels", "ctregistered", "ctcolor", "ctexteriorcolor", "ctfueltype", "ctbodystyle", "cttransmission", "enginesize", "horsepower", "torque", "drivetrain", "maxseating", "bodytype"));
    }

    /**
     * Store a newly created cttrucklisting in storage.
     *
     * @param CreateCtTrucklistingRequest|Request $request
     */
    public function store(CreateCtTrucklistingRequest $request) {

        $now = date("Y-m-d h:i:s");

        /* make other */
        $ctmake_id = $request['ctmake_id'];

        $affected = '';
        $lastid = '';
        if ($ctmake_id == 'other') {

            $ctmake_id_other = $request['ctmake_id_other'];


            $affected = DB::insert("INSERT INTO ctmake ( `make`,`created_at`,`updated_at`)
VALUES ('" . $ctmake_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctmake')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctmake_id'] = $lastid;
            }
        }



        /* make other */



        /* model other */
        $ctmodels_id = $request['ctmodels_id'];


        if ($ctmodels_id == 'other') {

            $ctmodels_id_other = $request['ctmodels_id_other'];
            $make = $request['ctmake_id'];



            $affected = DB::insert("INSERT INTO ctmodels ( `ctmake_id`, `model`,`created_at`,`updated_at`)
VALUES ('" . $make . "', '" . $ctmodels_id_other . "','" . $now . "','" . $now . "') ");
            if ($affected == 1) {

                $lastid = DB::table('ctmodels')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctmodels_id'] = $lastid;
            }
        }



        /* model other */


        /* submodel other */
        $ctsubmodels_id = $request['ctsubmodels_id'];


        if ($ctsubmodels_id == 'other') {

            $ctsubmodels_id_other = $request['ctsubmodels_id_other'];
            $models = $request['ctmodels_id'];


            $make = $request['ctmake_id'];
            $affected = DB::insert("INSERT INTO ctsubmodels ( `submodel_make`,`ctmodel_id`,`submodels`,`created_at`,`updated_at`)
VALUES ('" . $make . "','" . $models . "','" . $ctsubmodels_id_other . "','" . $now . "','" . $now . "') ");
            if ($affected == 1) {

                $lastid = DB::table('ctsubmodels')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctsubmodels_id'] = $lastid;
            }
        }



        /* submodel other */


        /* year other */
        $ctregistered_id = $request['ctregistered_id'];

        $affected = '';
        $lastid = '';
        if ($ctregistered_id == 'other') {

            $ctregistered_id_other = $request['ctregistered_id_other'];


            $affected = DB::insert("INSERT INTO ctregistered ( `year`,`created_at`,`updated_at`)
VALUES ('" . $ctregistered_id_other . "','" . $now . "','" . $now . "') ");
            if ($affected == 1) {
                $lastid = DB::table('ctregistered')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctregistered_id'] = $lastid;
            }
        }



        /* year other */



        /* engine_size_id_other other */
        $engine_size_id = $request['engine_size_id'];

        $affected = '';
        $lastid = '';
        if ($engine_size_id == 'other') {

            $engine_size_id_other = $request['engine_size_id_other'];


            $affected = DB::insert("INSERT INTO enginesize ( `engine_size`,`created_at`,`updated_at`)
VALUES ('" . $engine_size_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('enginesize')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['engine_size_id'] = $lastid;
            }
        }



        /* engine_size_id_other other */



        /* horsepower_id other */
        $horsepower_id = $request['horsepower_id'];

        $affected = '';
        $lastid = '';
        if ($horsepower_id == 'other') {

            $horsepower_id_other = $request['horsepower_id_other'];


            $affected = DB::insert("INSERT INTO horsepower ( `horsepower`,`created_at`,`updated_at`)
VALUES ('" . $horsepower_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('horsepower')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['horsepower_id'] = $lastid;
            }
        }



        /* horsepower_id other */




        /* torque_id other */
        $torque_id = $request['torque_id'];

        $affected = '';
        $lastid = '';
        if ($torque_id == 'other') {

            $torque_id_other = $request['torque_id_other'];


            $affected = DB::insert("INSERT INTO torque ( `torque`,`created_at`,`updated_at`)
VALUES ('" . $torque_id_other . "','" . $now . "','" . $now . "') ");
            if ($affected == 1) {
                $lastid = DB::table('torque')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['torque_id'] = $lastid;
            }
        }



        /* torque_id other */



        /* drivetrain_id other */
        $drivetrain_id = $request['drivetrain_id'];

        $affected = '';
        $lastid = '';
        if ($drivetrain_id == 'other') {

            $drivetrain_id_other = $request['drivetrain_id_other'];


            $affected = DB::insert("INSERT INTO drivetrain ( `drivetrain`,`created_at`,`updated_at`)
VALUES ('" . $drivetrain_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('drivetrain')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['drivetrain_id'] = $lastid;
            }
        }



        /* drivetrain_id other */



        /* Transmission other */
        $cttransmission_id = $request['cttransmission_id'];

        $affected = '';
        $lastid = '';
        if ($cttransmission_id == 'other') {

            $cttransmission_id_other = $request['cttransmission_id_other'];


            $affected = DB::insert("INSERT INTO cttransmission ( `transmission`,`created_at`,`updated_at`)
VALUES ('" . $cttransmission_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('cttransmission')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['cttransmission_id'] = $lastid;
            }
        }



        /* Transmission other */




        /* Max Seating other */
        $seating_id = $request['seating_id'];

        $affected = '';
        $lastid = '';
        if ($seating_id == 'other') {

            $seating_id_other = $request['seating_id_other'];


            $affected = DB::insert("INSERT INTO maxseating ( `seating`,`created_at`,`updated_at`)
VALUES ('" . $seating_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('maxseating')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['seating_id'] = $lastid;
            }
        }



        /* Max Seating other */




        /* ctcondition_id other */
        $ctcondition_id = $request['ctcondition_id'];

        $affected = '';
        $lastid = '';
        if ($ctcondition_id == 'other') {

            $ctcondition_id_other = $request['ctcondition_id_other'];


            $affected = DB::insert("INSERT INTO ctcondition ( `condition`,`created_at`,`updated_at`)
VALUES ('" . $ctcondition_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctcondition')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctcondition_id'] = $lastid;
            }
        }
        /* ctcondition_id other */



        /* ctcolor_id other */
        $ctcolor_id = $request['ctcolor_id'];

        $affected = '';
        $lastid = '';
        if ($ctcolor_id == 'other') {

            $ctcolor_id_other = $request['ctcolor_id_other'];


            $affected = DB::insert("INSERT INTO ctcolor ( `color`,`created_at`,`updated_at`)
VALUES ('" . $ctcolor_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctcolor')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctcolor_id'] = $lastid;
            }
        }
        /* ctcolor_id other */


        /* ctexteriorcolor_id other */
        $ctexteriorcolor_id = $request['ctexteriorcolor_id'];

        $affected = '';
        $lastid = '';
        if ($ctexteriorcolor_id == 'other') {

            $ctexteriorcolor_id_other = $request['ctexteriorcolor_id_other'];


            $affected = DB::insert("INSERT INTO ctexteriorcolor ( `exterior_color`,`created_at`,`updated_at`)
VALUES ('" . $ctexteriorcolor_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctexteriorcolor')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctexteriorcolor_id'] = $lastid;
            }
        }
        /* ctexteriorcolor_id other */


        /* ctbodystyle_id other */
        $ctbodystyle_id = $request['ctbodystyle_id'];

        $affected = '';
        $lastid = '';
        if ($ctbodystyle_id == 'other') {

            $ctbodystyle_id_other = $request['ctbodystyle_id_other'];


            $affected = DB::insert("INSERT INTO ctbodystyle ( `bodystyle`,`created_at`,`updated_at`)
VALUES ('" . $ctbodystyle_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctbodystyle')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctbodystyle_id'] = $lastid;
            }
        }
        /* ctbodystyle_id other */




        /* ctbodystyle_id other */
        $bodytype_id = $request['bodytype_id'];

        $affected = '';
        $lastid = '';
        if ($bodytype_id == 'other') {

            $bodytype_id_other = $request['bodytype_id_other'];


            $affected = DB::insert("INSERT INTO bodytype ( `body_type`,`created_at`,`updated_at`)
VALUES ('" . $bodytype_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('bodytype')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['bodytype_id'] = $lastid;
            }
        }
        /* ctbodystyle_id other */







        $make = $request['ctmake_id'];
        $ctmake = DB::table('ctmake')->where('id', '' . $make . '')->whereNull('deleted_at')->get();
        $vin_make = $ctmake[0]->make;
        $vin_make = $vin_make[0];

        $models = $request['ctmodels_id'];
        $ctmodels = DB::table('ctmodels')->where('id', '' . $models . '')->whereNull('deleted_at')->get();
        $vin_models = $ctmodels[0]->model;
        $vin_models = $vin_models[0];

        $registered = $request['ctregistered_id'];
        $ctregistered = DB::table('ctregistered')->where('id', '' . $registered . '')->whereNull('deleted_at')->get();





        $vin_ctregistered = $ctregistered[0]->year;



        if (!isset($vin_ctregistered[2])) {
            $vin_ctregistered[2] = $vin_ctregistered[0];
        }

        if (!isset($vin_ctregistered[3])) {
            $vin_ctregistered[3] = $vin_ctregistered[2];
        }

        $vin_ctregistered = $vin_ctregistered[2] . "" . $vin_ctregistered[3];





        $engine_size_id = $request['engine_size_id'];
        $engine_size = DB::table('enginesize')->where('id', '' . $engine_size_id . '')->whereNull('deleted_at')->get();
        $engine_size = $engine_size[0]->engine_size;

        preg_match('/([0-9]+\.[0-9]+)/', '' . $engine_size . '', $matches);


        if (empty($matches)) {
            preg_match_all('!\d+!', $engine_size, $matches);

            $engine_size = $matches[0][0];
            $vin_engine_size = $engine_size;
        } else {
            $engine_size = (float) $matches[1];
            $vin_engine_size = $engine_size;
        }



        $request['v_id'] = "CT_" . $vin_make . "" . $vin_models . "" . $vin_ctregistered . "_" . $vin_engine_size;

        if ($request['price']) {
            $str = $request['price'];
            $request['price_int'] = preg_replace('/\D/', '', $str);
        }






        $request = $this->saveFiles($request);
        $CtTrucklisting = CtTrucklisting::create($request->all());




        DB::table('trucklisting_img')->where('truckid', 9999999)->update(['truckid' => $CtTrucklisting->id]);

        DB::table('photo_truck')->where('truck_id', 9999999)->update(['truck_id' => $CtTrucklisting->id]);

        $fetchData = DB::select('select * from photo order by id desc limit 1');

        if ($request['image']) {

            foreach ($fetchData as $data) {

                $public_id = $data->public_id;
                $version = $data->version;
                $signature = $data->signature;
            }

            DB::table('cttrucklisting')
                    ->where('id', $CtTrucklisting->id)
                    ->update(['public_id' => '' . $public_id . '', 'version' => '' . $version . '', 'signature' => '' . $signature . '']);
        }



        $url = 'cttrucklisting/create?insert=1';

        echo '<script type="text/javascript">';
        echo 'window.location.href="' . $url . '";';
        echo '</script>';

        /* 	   
          return redirect()->route(config('quickadmin.route') . '.cttrucklisting.create')->with('success', 1);
         */

        /* return redirect()->route(config('quickadmin.route') . '.cttrucklisting.index'); */
    }

    /**
     * Show the form for editing the specified cttrucklisting.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $cttrucklisting = CtTrucklisting::find($id);
        $ctcondition = DB::table('ctcondition')->whereNull('deleted_at')->get();

        /* $ctmake = CtMake::pluck("make","id")->prepend('Please select', null); */
        /* $ctmodels = CtModels::pluck("model", "id")->prepend('Please select', null); */
        /* $ctregistered = Ctregistered::pluck("year", "id")->prepend('Please select', null); */
        /* $ctcolor = Ctcolor::pluck("color", "id")->prepend('Please select', null); */
        /* $ctexteriorcolor = Ctexteriorcolor::pluck("exterior_color", "id")->prepend('Please select', null); */
        /* $ctfueltype = Ctfueltype::pluck("fuel_type", "id")->prepend('Please select', null); */
        /* $ctbodystyle = Ctbodystyle::pluck("bodystyle", "id")->prepend('Please select', null); */
        /* $cttransmission = Cttransmission::pluck("transmission", "id")->prepend('Please select', null); */

        $ctmake = DB::table('ctmake')->whereNull('deleted_at')->get();
        $ctmodels = DB::table('ctmodels')->whereNull('deleted_at')->get();
        $ctregistered = DB::table('ctregistered')->whereNull('deleted_at')->get();
        $ctcolor = DB::table('ctcolor')->whereNull('deleted_at')->get();
        $ctexteriorcolor = DB::table('ctexteriorcolor')->whereNull('deleted_at')->get();
        $ctfueltype = DB::table('ctfueltype')->whereNull('deleted_at')->get();
        $ctbodystyle = DB::table('ctbodystyle')->whereNull('deleted_at')->get();
        $cttransmission = DB::table('cttransmission')->whereNull('deleted_at')->get();
        $enginesize = DB::table('enginesize')->whereNull('deleted_at')->get();

        $trucklisting_img = DB::table('trucklisting_img')->where('truckid', $id)->whereNull('deleted_at')->get();

        $horsepower = DB::table('horsepower')->whereNull('deleted_at')->get();

        $torque = DB::table('torque')->whereNull('deleted_at')->get();

        $drivetrain = DB::table('drivetrain')->whereNull('deleted_at')->get();

        $maxseating = DB::table('maxseating')->whereNull('deleted_at')->get();

        $bodytype = DB::table('bodytype')->whereNull('deleted_at')->get();

        $ctsubmodels = DB::table('ctsubmodels')->whereNull('deleted_at')->get();


        require 'PhotoAlbum/main.php';

        $all_img_arr = '';

        if ($cttrucklisting->public_id) {
            $main_img_arr = cl_image_tag($cttrucklisting->public_id, array_merge($thumbs_params, array("width" => 150, "height" => 100, "crop" => "fill")));
        }



        $fetchData = DB::select('select * from photo_truck where truck_id="' . $id . '" order by id desc ');

        foreach ($fetchData as $data) {





            $all_img_arr[$data->id] = cl_image_tag($data->public_id, array_merge($thumbs_params, array("width" => 150, "height" => 100, "crop" => "fill")));
        }





        return view('admin.cttrucklisting.edit', compact('cttrucklisting', "ctcondition", "ctmake", "ctmodels", "ctregistered", "ctcolor", "ctexteriorcolor", "ctfueltype", "ctbodystyle", "cttransmission", "trucklisting_img", "enginesize", "horsepower", "torque", "drivetrain", "maxseating", "bodytype", "ctsubmodels", "main_img_arr", "all_img_arr"));
    }

    /**
     * Update the specified cttrucklisting in storage.
     * @param UpdateCtTrucklistingRequest|Request $request
     *
     * @param  int  $id
     */
    public function update($id, UpdateCtTrucklistingRequest $request) {

        $now = date("Y-m-d h:i:s");
        /* make other */
        $ctmake_id = $request['ctmake_id'];

        $affected = '';
        $lastid = '';
        if ($ctmake_id == 'other') {

            $ctmake_id_other = $request['ctmake_id_other'];


            $affected = DB::insert("INSERT INTO ctmake ( `make`,`created_at`,`updated_at`)
VALUES ('" . $ctmake_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctmake')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctmake_id'] = $lastid;
            }
        }



        /* make other */


        /* model other */
        $ctmodels_id = $request['ctmodels_id'];


        if ($ctmodels_id == 'other') {

            $ctmodels_id_other = $request['ctmodels_id_other'];
            $make = $request['ctmake_id'];



            $affected = DB::insert("INSERT INTO ctmodels ( `ctmake_id`, `model`,`created_at`,`updated_at`)
VALUES ('" . $make . "', '" . $ctmodels_id_other . "','" . $now . "','" . $now . "') ");
            if ($affected == 1) {

                $lastid = DB::table('ctmodels')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctmodels_id'] = $lastid;
            }
        }



        /* model other */



        /* submodel other */
        $ctsubmodels_id = $request['ctsubmodels_id'];


        if ($ctsubmodels_id == 'other') {

            $ctsubmodels_id_other = $request['ctsubmodels_id_other'];
            $models = $request['ctmodels_id'];

            $make = $request['ctmake_id'];

            $affected = DB::insert("INSERT INTO ctsubmodels ( `submodel_make`,`ctmodel_id`,`submodels`,`created_at`,`updated_at`)
VALUES ('" . $make . "','" . $models . "','" . $ctsubmodels_id_other . "','" . $now . "','" . $now . "') ");
            if ($affected == 1) {

                $lastid = DB::table('ctsubmodels')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctsubmodels_id'] = $lastid;
            }
        }



        /* submodel other */




        /* year other */
        $ctregistered_id = $request['ctregistered_id'];

        $affected = '';
        $lastid = '';
        if ($ctregistered_id == 'other') {

            $ctregistered_id_other = $request['ctregistered_id_other'];


            $affected = DB::insert("INSERT INTO ctregistered ( `year`,`created_at`,`updated_at`)
VALUES ('" . $ctregistered_id_other . "','" . $now . "','" . $now . "') ");
            if ($affected == 1) {
                $lastid = DB::table('ctregistered')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctregistered_id'] = $lastid;
            }
        }



        /* year other */



        /* engine_size_id_other other */
        $engine_size_id = $request['engine_size_id'];

        $affected = '';
        $lastid = '';
        if ($engine_size_id == 'other') {

            $engine_size_id_other = $request['engine_size_id_other'];


            $affected = DB::insert("INSERT INTO enginesize ( `engine_size`,`created_at`,`updated_at`)
VALUES ('" . $engine_size_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('enginesize')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['engine_size_id'] = $lastid;
            }
        }



        /* engine_size_id_other other */



        /* horsepower_id other */
        $horsepower_id = $request['horsepower_id'];

        $affected = '';
        $lastid = '';
        if ($horsepower_id == 'other') {

            $horsepower_id_other = $request['horsepower_id_other'];


            $affected = DB::insert("INSERT INTO horsepower ( `horsepower`,`created_at`,`updated_at`)
VALUES ('" . $horsepower_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('horsepower')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['horsepower_id'] = $lastid;
            }
        }



        /* horsepower_id other */




        /* torque_id other */
        $torque_id = $request['torque_id'];

        $affected = '';
        $lastid = '';
        if ($torque_id == 'other') {

            $torque_id_other = $request['torque_id_other'];


            $affected = DB::insert("INSERT INTO torque ( `torque`,`created_at`,`updated_at`)
VALUES ('" . $torque_id_other . "','" . $now . "','" . $now . "') ");
            if ($affected == 1) {
                $lastid = DB::table('torque')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['torque_id'] = $lastid;
            }
        }



        /* torque_id other */



        /* drivetrain_id other */
        $drivetrain_id = $request['drivetrain_id'];

        $affected = '';
        $lastid = '';
        if ($drivetrain_id == 'other') {

            $drivetrain_id_other = $request['drivetrain_id_other'];


            $affected = DB::insert("INSERT INTO drivetrain ( `drivetrain`,`created_at`,`updated_at`)
VALUES ('" . $drivetrain_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('drivetrain')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['drivetrain_id'] = $lastid;
            }
        }



        /* drivetrain_id other */



        /* Transmission other */
        $cttransmission_id = $request['cttransmission_id'];

        $affected = '';
        $lastid = '';
        if ($cttransmission_id == 'other') {

            $cttransmission_id_other = $request['cttransmission_id_other'];


            $affected = DB::insert("INSERT INTO cttransmission ( `transmission`,`created_at`,`updated_at`)
VALUES ('" . $cttransmission_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('cttransmission')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['cttransmission_id'] = $lastid;
            }
        }



        /* Transmission other */




        /* Max Seating other */
        $seating_id = $request['seating_id'];

        $affected = '';
        $lastid = '';
        if ($seating_id == 'other') {

            $seating_id_other = $request['seating_id_other'];


            $affected = DB::insert("INSERT INTO maxseating ( `seating`,`created_at`,`updated_at`)
VALUES ('" . $seating_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('maxseating')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['seating_id'] = $lastid;
            }
        }



        /* Max Seating other */



        /* ctcondition_id other */
        $ctcondition_id = $request['ctcondition_id'];

        $affected = '';
        $lastid = '';
        if ($ctcondition_id == 'other') {

            $ctcondition_id_other = $request['ctcondition_id_other'];


            $affected = DB::insert("INSERT INTO ctcondition ( `condition`,`created_at`,`updated_at`)
VALUES ('" . $ctcondition_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctcondition')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctcondition_id'] = $lastid;
            }
        }
        /* ctcondition_id other */



        /* ctcolor_id other */
        $ctcolor_id = $request['ctcolor_id'];

        $affected = '';
        $lastid = '';
        if ($ctcolor_id == 'other') {

            $ctcolor_id_other = $request['ctcolor_id_other'];


            $affected = DB::insert("INSERT INTO ctcolor ( `color`,`created_at`,`updated_at`)
VALUES ('" . $ctcolor_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctcolor')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctcolor_id'] = $lastid;
            }
        }
        /* ctcolor_id other */


        /* ctexteriorcolor_id other */
        $ctexteriorcolor_id = $request['ctexteriorcolor_id'];

        $affected = '';
        $lastid = '';
        if ($ctexteriorcolor_id == 'other') {

            $ctexteriorcolor_id_other = $request['ctexteriorcolor_id_other'];


            $affected = DB::insert("INSERT INTO ctexteriorcolor ( `exterior_color`,`created_at`,`updated_at`)
VALUES ('" . $ctexteriorcolor_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctexteriorcolor')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctexteriorcolor_id'] = $lastid;
            }
        }
        /* ctexteriorcolor_id other */

        /* ctbodystyle_id other */
        $ctbodystyle_id = $request['ctbodystyle_id'];

        $affected = '';
        $lastid = '';
        if ($ctbodystyle_id == 'other') {

            $ctbodystyle_id_other = $request['ctbodystyle_id_other'];


            $affected = DB::insert("INSERT INTO ctbodystyle ( `body_style`,`created_at`,`updated_at`)
VALUES ('" . $ctbodystyle_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('ctbodystyle')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['ctbodystyle_id'] = $lastid;
            }
        }

        /* ctbodystyle_id other */



        /* ctbodystyle_id other */
        $bodytype_id = $request['bodytype_id'];

        $affected = '';
        $lastid = '';
        if ($bodytype_id == 'other') {

            $bodytype_id_other = $request['bodytype_id_other'];


            $affected = DB::insert("INSERT INTO bodytype ( `body_type`,`created_at`,`updated_at`)
VALUES ('" . $bodytype_id_other . "','" . $now . "','" . $now . "') ");

            if ($affected == 1) {
                $lastid = DB::table('bodytype')->orderBy('id', 'desc')->limit(1)->get();
                $lastid = $lastid[0]->id;

                $request['bodytype_id'] = $lastid;
            }
        }
        /* ctbodystyle_id other */


        /**/


        $make = $request['ctmake_id'];
        $ctmake = DB::table('ctmake')->where('id', '' . $make . '')->whereNull('deleted_at')->get();
        $vin_make = $ctmake[0]->make;
        $vin_make = $vin_make[0];

        $models = $request['ctmodels_id'];
        $ctmodels = DB::table('ctmodels')->where('id', '' . $models . '')->whereNull('deleted_at')->get();
        $vin_models = $ctmodels[0]->model;
        $vin_models = $vin_models[0];

        $registered = $request['ctregistered_id'];
        $ctregistered = DB::table('ctregistered')->where('id', '' . $registered . '')->whereNull('deleted_at')->get();
        $vin_ctregistered = $ctregistered[0]->year;
        $vin_ctregistered = $vin_ctregistered[2] . "" . $vin_ctregistered[3];



        $engine_size_id = $request['engine_size_id'];
        $engine_size = DB::table('enginesize')->where('id', '' . $engine_size_id . '')->whereNull('deleted_at')->get();
        $engine_size = $engine_size[0]->engine_size;

        preg_match('/([0-9]+\.[0-9]+)/', '' . $engine_size . '', $matches);

        if (empty($matches)) {
            preg_match_all('!\d+!', $engine_size, $matches);

            $engine_size = $matches[0][0];
            $vin_engine_size = $engine_size;
        } else {
            $engine_size = (float) $matches[1];
            $vin_engine_size = $engine_size;
        }



        $request['v_id'] = "CT_" . $vin_make . "" . $vin_models . "" . $vin_ctregistered . "_" . $vin_engine_size;

        /**/



        if ($request['price']) {
            $str = $request['price'];
            $request['price_int'] = preg_replace('/\D/', '', $str);
        }


        $cttrucklisting = CtTrucklisting::findOrFail($id);

        $request = $this->saveFiles($request);

        $cttrucklisting->update($request->all());


        /* echo $url=config('quickadmin.route') . '/cttrucklisting/'.$id.'/edit'; */

        $url = '' . $id . '/edit?update=1';

        echo '<script type="text/javascript">';
        echo 'window.location.href="' . $url . '";';
        echo '</script>';

        /* return redirect()->route(config('quickadmin.route') . '.cttrucklisting.index'); */
    }

    /**
     * Remove the specified cttrucklisting from storage.
     *
     * @param  int  $id
     */
    public function destroy($id) {
        CtTrucklisting::destroy($id);

        return redirect()->route(config('quickadmin.route') . '.cttrucklisting.index');
    }

    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massDelete(Request $request) {
        if ($request->get('toDelete') != 'mass') {
            $toDelete = json_decode($request->get('toDelete'));
            CtTrucklisting::destroy($toDelete);
        } else {
            CtTrucklisting::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route') . '.cttrucklisting.index');
    }

}
