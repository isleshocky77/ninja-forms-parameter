<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Parameter_Add_On_MergeTags_Parameter
 */
final class NF_Parameter_Add_On_MergeTags_Parameter extends NF_Abstracts_MergeTags
{
    protected $id = 'parameter';

    public function __construct()
    {
        parent::__construct();
        $this->title = __( 'Parameter', 'ninja-forms' );

        $this->merge_tags = array(
            'parameter' => array(
                'tag' => '{parameter:YOUR_KEY}',
                'label' => __( 'Parameter', 'ninja_forms' ),
                'callback' => null,
            ),
        );
    }

    public function __call($name, $arguments)
    {
        return $this->merge_tags[ $name ][ 'value' ];
    }

    public function set_merge_tags( $key, $value )
    {
        $callback = ( is_numeric( $key ) ) ? 'parameter_' . $key : $key;

        $this->merge_tags[ $callback ] = array(
            'id' => $key,
            'tag' => "{parameter:" . $key . "}",
            'callback' => $callback,
            'value' => $value
        );    }

} // END CLASS NF_Parameter_Add_On_MergeTags_Parameter
