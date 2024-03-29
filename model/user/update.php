<?php
namespace model\user;
class Update extends User {
    public function __construct()
    {

        parent::__construct();


        if( empty( in('session_id') ) ) return error( ERROR_SESSION_ID_EMPTY );
        if ( in('id') ) return error( ERROR_CANNOT_CHANGE_USER_ID );
        if ( in('password') ) return error( ERROR_CANNOT_CHANGE_PASSWORD_IN_UPDATE );
        $mobile = in('mobile');
        if ( $mobile ) {
            if ( ! is_numeric( $mobile ) ) return error( ERROR_MOBILE_NOT_NUMERIC );
            if( strlen( $mobile ) > 32 ) return error( ERROR_MOBILE_TOO_LONG );
        }
        $data = [];
        $data['email'] = in('email');
        $data['nickname'] = in('nickname');
        $data['name'] = in('name');
        $data['birth_day'] = in('birth_day', 00);
        $data['birth_month'] = in('birth_month', 00);
        $data['birth_year'] = in('birth_year', 0000);
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


        $user = $this->load_by_session_id( in('session_id') );



        if ( $this->isAdmin() ) { // if admin,
            $this->load( in('idx') ); // admin can update user information.
        }


        //di($this->record);


        $this->update( $data );
        success( [ 'session_id' => $this->get_session_id() ] );
    }
}
