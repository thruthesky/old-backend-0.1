<?php
namespace model\test;

function test( $re, $code ) {
    static $count = 0;
    $count ++;
    if ( is_array($re) ) {
        if ( isset($re['code']) && ! isset($re['idx']) ) { // server data.
            if ( $re['code'] ) echo "<div class='error'>$count - ERROR : ( $re[code] ) - $re[message]</div>";
            else echo "<div class='success'>$count - SUCCESS: $code</div>";
        }
        else { // unknown data.
            if ( $re ) echo "<div class='success'>$count - SUCCESS: $code</div>";
            else echo "<div class='error'>$count - ERROR : $code</div>";
        }
    }
    else { //
        if ( $re ) echo "<div class='success'>$count - SUCCESS: $code</div>";
        else echo "<div class='error'>$count - ERROR : $code</div>";
    }
}

function ok( $re ) {
    return $re['code'] == 0;
}

class Forum extends \model\base\Base {


    public function __construct()
    {
        parent::__construct();

    }

    private function randomstring() {
        $random_int1 = rand(25, 50);
        $random_int2 = rand(30, 60);
        return strval (  $random_int2 )."-test" . strval( $random_int1 );
    }

    private function result() {

        $re = json_result();
        $json = json_decode( $re, true );

        // if ( isset($json['code']) ) di( $re );

        if ( json_last_error() ) {
            di( $re );
            return ['code' => 1, 'message' => "JSON ERROR: " . json_last_error_msg() ];
        }
        return $json;


    }

    private function ex( $class_method, $params = [] ) {

        $arr = explode('::', $class_method);
        $class = $arr[0];
        $method = null;
        if ( isset($arr[1]) ) $method = $arr[1];

        $_REQUEST = $params;

        $obj = new $class();
        if ( $method && method_exists($obj, $method) ) $obj->$method();

        return $this->result();
    }

    public function run() {
//        if ( empty(in('forum_id') ) ) return error(-1, "Input forum id");
//        $config = forum_config()->load( in('forum_id') );
//        if( empty($config) ) return error(-2, "No forum config by that ID - " . in('forum_id') );
//
//
//
//        success();
        $data =[];
        $data['session_id'] = $this->createUser(  );
            print_r( ' --- TEST IF ONE CAN POST --- ');

            $this->testEmptySession_ID( $data );
            $this->testPostIfUserNotExist( $data );
            $this->testWithoutForumID( $data );
            $this->testWithoutConfig( $data );
            $this->testEmptyTitle( $data );
            $this->testEmptyContent( $data );
            $this->testPostData( $data );
            print_r( '<br/> --- TEST IF ONE CAN CREATE FORUM CONFIG --- ');
            $this->testForumConfig( $data );

//            print_r( '<br/> --- TEST IF ONE CAN CREATE FORUM CONFIG --- ');



        exit();


    }

    private function createUser( ) {

        $id = 'user-test' . time();
        $name = 'new user';
        $data = [
            'id'=>$id,
            'name'=>$name,
            'meta' =>[
                'age'=>23
            ]
        ];

        $data['password'] = $data['id'];

        $re = $this->ex( "\\model\\user\\Create", $data );

        if ( ok($re) ) return $re['data']['session_id'];
        else return null;




    }



    private function testEmptySession_ID( $params ) {
        //test forum_data post without session_id
        $params['session_id'] = null;
        $re = $this->ex( "\\model\\forum\\Data::create", $params );
        test( $re['code'] == ERROR_SESSION_ID_EMPTY, "FORUM POST TEST WITHOUT SESSION_ID -$re[code]");

    }

    private function testPostIfUserNotExist( $params ) {
        $params['session_id'] = "-test" . rand(1 , 20);
        $re = $this->ex( "\\model\\forum\\Data::create", $params );
        test( $re['code'] == ERROR_WRONG_SESSION_ID, "FORUM POST TEST USER NOT EXIST OR WRONG SESSION ID -$re[code]");
    }

    private function testWithoutForumID( $params ) {
        //test forum_data post with sesssion_id but empty forum_id
        //session_id


        $re = $this->ex("\\model\\forum\\Data::create", $params );
        test( $re['code'] == ERROR_FORUM_ID_EMPTY, "FORUM POST TEST EMPTY FORUM_ID -$re[code]");
    }



    private function testWithoutConfig( $params ) {
        //test forum_data post if forum config not exist
        $random = rand(1,20) . "-test";
        $params['forum_id'] = $random;
        $params['title'] = $random;
        $re = $this->ex("\\model\\forum\\Data::create", $params );
        test( $re['code'] == ERROR_FORUM_CONFIG_NOT_EXIST , "FORUM POST TEST IF CONFIG NOT EXIST -$re[code]");
    }


    private function testEmptyTitle( $params ) {
        //test forum_data post if title is empty
        $data = ['id'=> $this->randomstring() . time() . "2", 'name'=>$this->randomstring(), 'session_id'=>$params['session_id']];
        $config = $this->createConfig( $params );

        $params['forum_id'] = $config['data']['id'];
        $re = $this->ex("\\model\\forum\\Data::create", $params );
        test( $re['code'] == ERROR_FORUM_DATA_TITLE_EMPTY , "FORUM POST TEST IF TITLE IS EMPTY $re[code]");
    }


    private function testEmptyContent( $params ) {
        //test forum_data post if content is empty
        $config = $this->createConfig( $params );
        if( $config )$params['forum_id'] = $config['data']['id'];
        $params['title'] = $this->randomstring() . "test";
        $re = $this->ex("\\model\\forum\\Data::create", $params );
        test( $re['code'] == ERROR_FORUM_DATA_CONTENT_EMPTY , "FORUM POST TEST IF CONTENT IS EMPTY $re[code]");
    }


    private function testPostData( $params ) {
        $config = $this->createConfig( $params );

        if( $config ) $params['forum_id'] = $config['data']['id'];
        $params['title'] = $this->randomstring();
        $params['content'] = $this->randomstring();
        $re = $this->ex("\\model\\forum\\Data::create", $params );
        test( $re['code'] == 0 , "CREATING POST FORUM DATA  - $params[title]");

        $params['idx'] = $re['data']['forum_data'];
        $params['title'] = 'edit'. rand( 1, 20 );

        $re = $this->ex("\\model\\forum\\Data::edit", $params );
        $post_data = forum_data()->load($params['idx']);
        test( $re['data']['forum_data'] == $post_data ,"FORUM POST EDIT TEST -" );


        $re = $this->ex("\\model\\forum\\Data::delete", $params );
        test( $re['code'] == 0 , "DELETING POST FORUM DATA ");

        $re = $this->ex("\\model\\forum\\Data::edit", $params );
        test( $re['code'] == ERROR_FORUM_DATA_NOT_EXIST ,"FORUM POST EDIT TEST POST NOT EXIST -");

        $re = $this->ex("\\model\\forum\\Data::delete", $params );
        test( $re['code'] == ERROR_FORUM_DATA_NOT_EXIST  , "DELETING POST NOT EXIST -$re[code]" );
    }



    ///////////////////////////////////////////////////////////////////////////////////
    private function testForumConfig ( $params ) {
        $params['id'] = $this->randomstring();
        $re = $this->ex( "\\model\\forum\\Config::getConfig", $params );
        test( $re['code'] == ERROR_FORUM_CONFIG_NOT_EXIST, "FORUM CONFIG TEST IF FORUM CONFIG NOT EXIST -$re[code]: $re[message]");

        $re = $this->ex( "\\model\\forum\\Config::create", $params );
        test( $re['code'] == ERROR_PERMISSION_ADMIN , "FORUM CONFIG CREATE TEST WITHOUT ADMIN ACCOUNT -$re[code]: $re[message]");

        $re = $this->createConfig( $params );
        test( $re['code'] == 0, "FORUM CONFIG CREATE TEST -");
        $params['idx'] = $re['data']['idx'];

        $re = $this->ex( "\\model\\forum\\Config::edit", $params );
        test( $re['code'] == ERROR_PERMISSION_ADMIN , "FORUM CONFIG EDIT TEST WITHOUT ADMIN PERMISSION");



        $re = $this->ex( "\\model\\forum\\Config::delete", $params );
        test( $re['code'] == ERROR_PERMISSION_ADMIN, "FORUM CONFIG DELETE TEST WITHOUT ADMIN PERMISSION -$re[code]");

        $params['session_id'] = null;
        $re = $this->ex( "\\model\\forum\\Config::delete", $params );
        test( $re['code'] == ERROR_SESSION_ID_EMPTY, "FORUM CONFIG DELETE TEST EMPTY SESSION ID -$re[code]");


        $admin = $this->loginAdmin();
        $params['session_id'] = $admin['data']['session_id'];

        $re = $this->ex( "\\model\\forum\\Config::edit", $params );
        $edited_config = forum_config()->load($re['data']['forum_data']['idx']);
        test( $re['data']['forum_data'] == $edited_config, "FORUM CONFIG EDIT TEST ");

        $re = $this->ex( "\\model\\forum\\Config::delete", $params );
        test( $re['code'] == 0, "FORUM CONFIG DELETE TEST");

        $re = $this->ex( "\\model\\forum\\Config::getConfig", $params );
        test( $re['code'] == ERROR_FORUM_CONFIG_NOT_EXIST, "FORUM CONFIG TEST GETCONFIG AFTER DELETE -$re[code]: $re[message]");


        $admin = $this->loginAdmin();

        $params['session_id'] = $admin['data']['session_id'];
        $re = $this->ex( "\\model\\forum\\Config::delete", $params );
        test( $re['code'] == ERROR_FORUM_CONFIG_NOT_EXIST, "FORUM CONFIG DELETE TEST IF CONFIT NOT EXIST -$re[code]");




    }

    private function createConfig( $params ) {
        $admin = $this->loginAdmin();
        $params['session_id'] = $admin['data']['session_id'];
        $params['id'] = '-test' . time() . rand(1, 20);
        $re=$this->ex( "\\model\\forum\\Config::create", $params );
        return $re;
    }


    private function loginAdmin() {
        $data =['id'=>'admin', 'password'=>'admin'];
        $admin=$this->ex("\\model\\user\\Login", $data);
        return $admin;
    }




}