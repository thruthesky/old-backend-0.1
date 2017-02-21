<?php
namespace model\user;
class Update extends User {
    public function __construct()
    {

        parent::__construct();


        if( empty( in('session_id') ) ) return error( ERROR_SESSION_ID_EMPTY );
        if ( in('id') ) return error( ERROR_CANNOT_CHANGE_USER_ID );
        if ( in('password') ) return error( ERROR_CANNOT_CHANGE_PASSWORD_IN_UPDATE );
        if ( is_string( in('mobile') ) ) return error( ERROR_MOBILE_NOT_NUMERIC );
        $data = [];

        $data['email'] = in('email');
        $data['nickname'] = in('nickname');
        $data['name'] = in('name');
        $data['birth_day'] = in('birthday');
        $data['gender'] = in('gender');
        $data['mobile'] = in('mobile');
        $data['landline'] = in('landline');
        $data['country'] = in('country');
        $data['city'] = in('city');
        $data['zipcode'] = in('zipcode');
        $data['province'] = in('province');
        $data['meta'] = in('meta');
        $data['address'] = in('address');


        //di($data);


        $this->load_by_session_id( in('session_id') );



        if ( $this->isAdmin() ) { // if admin,
            $this->load( in('idx') ); // admin can update user information.
        }


        //di($this->record);


        $this->update( $data );
        success( [ 'session_id' => $this->get_session_id() ] );
    }
}
