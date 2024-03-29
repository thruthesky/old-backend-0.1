<?php

namespace model\forum;
use model\test\Test;

class Forum_Config_Test extends Test {




    public function run() {
        $this->testCreate();


    }


    public function create( $record ) {

        return $this->ex( "\\model\\forum\\Config::create", $record );
    }

    public function testCreate() {

        $record = [
            'id' => ''
        ];


        $re = $this->create( $record );
        test( is_error( $re ) == ERROR_FORUM_ID_EMPTY, 'Forum id is empty. To create a forum, you need to give an id' );


        $record['id'] = 'test-' . date('is');
        $re = $this->create( $record );
        test( is_error( $re ), "FAIL: Config Create - $record[id], " . error_string( $re ) );

        $admin_session_id = user()->forceLogin('admin');
        $record['session_id'] = $admin_session_id;
        $re = $this->create( $record );
        test( is_success( $re ), "SUCCESS: Config Create - $record[id], " . error_string( $re ) );

    }

    

}