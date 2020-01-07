<?php

class DbErrorHandler{

    // 0 off, 1 on
    protected $enable_err = 0;


    public function db_error($custom){

        if( $this->enable_err == 0 ){
            print json_encode(array('status'=>'error'));
        }
        else{
            trigger_error('Something wrong of query: '. $custom);
        }
        exit;
    }


}