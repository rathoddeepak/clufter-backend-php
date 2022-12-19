<?php

class Tookan {

  public $apiKey, $apiTask, $ch, $time, $postData;
  
  function __construct() {
    $this->time = date('m/d/Y H:i:s', time());    
  }

  function getFleetLocation($fleet_id){
    $this->apiTask = 'get_fleet_location';
    $this->ch = curl_init();      
    $this->postData = [
      "api_key" => TOOKAN_API_KEY,
      "fleet_id" => $fleet_id
    ];
    $this->postData = json_encode($this->postData);    
    $this->setHeader();
    return json_decode($this->execute());
  }

  function createMultiTask($data){
    $this->apiTask = 'create_multiple_tasks';
    $this->ch = curl_init();
    $itemTemplate = 'food_delivery';
    $templateData = [
      ['label' => 'item_details', 'data' => []],
      ['label' => 'delivery_cost', 'data' => $data->delivery_fee],
      ['label' => 'total', 'data' => $data->total],
      ['label' => 'customer', 'data' => $data->cust->phone],
      ['label' => 'pay_mode', 'data' => $data->pay_mode],
      ['label' => 'notes', 'data' => $data->cust_note]
    ];
    $loactionData = [
      ['label' => 'item_details', 'data' => []],
      ['label' => 'hotel', 'data' => $data->hotel->name],
      ['label' => 'address', 'data' => $data->cust->cl_address],      
      ['label' => 'landmark', 'data' => $data->cust->landmark],
      ['label' => 'flat', 'data' => $data->cust->flat],
      ['label' => 'pay_mode', 'data' => $data->pay_mode],
      ['label' => 'total', 'data' => $data->total],
      ['label' => 'alternate', 'data' => $data->altnum]
    ];
    foreach($data->foods as $food){
      $templateData[0]['data'][] = [$food->name,'',$food->quantity,$food->price];
      $loactionData[0]['data'][] = [$food->name,'',$food->quantity,$food->price];
    }    
    $this->postData = [
      "api_key" => TOOKAN_API_KEY,
      "timezone" => -330,
      "has_pickup" => 1,
      "has_delivery" => 1,
      "layout_type" => 0,
      "geofence" => 0,
      "team_id" => "",
      "auto_assignment" => 1,
      "tags" => "",
      "pickups" => [
        [
          "address" => $data->hotel->address,
          "latitude" => $data->hotel->lat,
          "longitude" => $data->hotel->lng,
          "time" => $this->time,
          "phone" => $data->hotel->phone,
          "job_description" => "",
          "template_name" => $itemTemplate,
          "template_data" => $templateData,
          "ref_images" => [],
          "name" => $data->hotel->name,
          "email" => "",
          "order_id" => $data->order_id
        ]
      ],
      "deliveries" => [
        [
          "address" => $data->cust->address,
          "latitude" => $data->cust->lat,
          "longitude" => $data->cust->lng,
          "time" => $this->time,
          "phone" => $data->cust->phone,
          "job_description" => "",
          "template_name" => "location",
          "template_data" => $loactionData,
          "ref_images" => [],
          "name" => $data->cust->name,
          "email" => "",
          "order_id" => $data->order_id
        ]
      ]
    ];
    $this->postData = json_encode($this->postData);    
    $this->setHeader();
    return $this->execute();
  }

  function setHeader(){    
    curl_setopt($this->ch, CURLOPT_URL, TOOKAN_API_URL . $this->apiTask);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
    curl_setopt($this->ch, CURLOPT_POST, TRUE);
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postData);
  }

  function execute(){    
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    $response = curl_exec($this->ch);    
    curl_close($this->ch);
    return $response;
  }

}

?>