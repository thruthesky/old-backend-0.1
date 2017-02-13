<?php
/**
 * @see README.md
 */
namespace model\forum;
class config extends Forum {
    public function __construct()
    {

        parent::__construct();

        $this->setTable('forum_config');


    }


    public function create() {

        $data = [];
        $data['id'] = in('id');
        $data['name'] = in('name');
        $data['description'] = in('description');

        $forum = $this->insert( $data );

        if ( $forum <= 0 ) return error( $forum );
        success( ['forum_config'=>$forum] );
    }

    public function edit() {
        $data = [];
        $data['idx'] = in('idx');
        $data['id'] = in('id');
        $data['name'] = in('name');
        $data['description'] = in('description');

        $datas = $this->load($data['idx']);
        if( ! $datas ) error( ERROR_POST_NOT_EXIST );
        $forum = $this->update($data);
        success( ['forum_data'=>$forum] );
    }

    public function remove() {
        $idx=in('idx');
        $data = $this->load( $idx );
        if( !$data ) error( ERROR_POST_NOT_EXIST );
        $condition = "idx = $idx";
        $this->delete( $condition );

        success( ['deleted' => $idx] );
    }
}
