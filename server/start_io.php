<?php
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;
use Workerman\Protocols\Http\Request;
// composer autoload
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "..", "server", "main.php"));
$io = new SocketIO(2021);

$io->on('workerStart', function()use($io){
    // #### Here listen a http port 3030 #####
    $http = new Worker('http://0.0.0.0:3030');
    $http->onMessage = function($http_connection, Request $response){
        global $io;
        $request = $response->get()['request']; 
        $http_connection->send('');
        $data = $response->post();     
        $data = json_decode($data['postData'], true);
        if($request == 'urp'){          
          $io->to($data['relation_code'])->emit('urp', $data); 
        }        
    };
    $http->listen();
});

$io->on('connection', function($socket){
   
    $socket->on('jr', function ($room)use($socket){//Join A Room
        echo 'Some one in the room ->'.$room;
        $socket->join($room); 
    });

    /*$socket->on('sm', function ($message)use($socket){ // New Message
        $socket->to(45122312)->emit('nm', $message);
        echo "\n ".$message;
    });*/

    $socket->on('mvrc', function ($room)use($socket){// Male Joining Room and Verifying partner has joined or not!
        echo 'Some one in the room ->'.$room;
        $socket->join($room); 
        $partner = Tw_GetPartnerFromCodeAndGender($room, "female");
        if($partner){
            $response = array('status' => 200, 'data' => Tw_UserData($partner['_id']));
            $socket->to($code)->emit('partner_joined', $response);
        }
    });

    $socket->on('vrc', function ($data)use($socket){ // Validate Relation Code
       global $tw;       
       $code = $data['relation_code'];
       $user_id = $data['_id'];
       $tw['logged_id'] = $user_id;   
       $result = Tw_ValidateRelationCode($code, $user_id);     
       if(is_string($result) && substr($result,  0, 1) == '~'){
        $response = array('status' => 400, 'data' => substr($result,  1));
       	$socket->emit('vrc_res', $response);
       }else{
         $socket->join($code); //Set Couple In Same Room

         //For Female Partner         
         $response = array('status' => 200, 'data' => $result);
         $socket->emit('vrc_res', $response);

         //For Male Partner         
         $response = array('status' => 200, 'data' => Tw_UserData($user_id));
         $socket->to($code)->emit('partner_joined', $response);
       }
    });

    $socket->on('urp', function ($room)use($socket){//Join A Room
        echo 'recevied';        
    });

});

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
