<?php
/**
 * call_hatenabookmark config
 */
 
/**
 * cache:storage
 */
define("TEMP_FOLDER",	'./temp/');
/**
 * cache:effective time(second)
 */
define("EFFECTIVE_TIME",86400);
/**
 * User Agent
 */
$AGENT = stream_context_create(
    array(
        'http'=>array(
            'user_agent'=>'//github.com/hiroshikuze/anichecker_call_hatenabookmark/'
        )
    )
);
