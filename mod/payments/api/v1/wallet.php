<?php
/**
 * Minds Wallet API
 * 
 * @version 1
 * @author Mark Harding
 */
namespace minds\plugin\payments\api\v1;

use Minds\Core;
use minds\interfaces;
use Minds\Api\Factory;

class wallet implements interfaces\api{

    /**
     * Returns the wallet info
     * @param array $pages
     * 
     * API:: /v1/wallet/:slug
     */      
    public function get($pages){
        
        $response = array();
        
        switch($pages[0]){
            
            case "count":
                $count = (int) \Minds\Helpers\Counters::get(Core\session::getLoggedinUser()->guid, 'points', false);
                
                $satoshi_rate = 1;//@todo make this configurable for admins
                $satoshi = $count * $satoshi_rate;
                $btc = ($satoshi / 1000000000);
            
                $response['count'] = $count;
                $response['cap'] = 1000;
                $response['boost_rate'] = 1;
                $response['ex'] = array(
                    'usd' => 0.001
                );
		        $response['satoshi'] = $satoshi;
                $response['btc'] = sprintf('%.9f', $btc);
                $response['usd'] = round($count / 10000, 2);
                break;
                
            case "transactions":
                $entities = Core\entities::get(array('subtype'=>'points_transaction', 'owner_guid'=> Core\session::getLoggedinUser()->guid, 'limit'=>isset($_GET['limit']) ? $_GET['limit'] : 12, 'offset'=>isset($_GET['offset']) ? $_GET['offset'] : ""));
                $response['transactions'] = factory::exportable($entities);
                $response['load-next'] = end($entities)->guid;
                break;
                
        }
    
        return Factory::response($response);
        
    }
    
    public function post($pages){
        $response = array(); 
        switch($pages[0]){
            case "quote":
                $ex_rate = 0.001;
                $points = $_POST['points'];
                $usd = $ex_rate * $points;
                return Factory::response(array('usd'=>$usd));
                break;
            case "charge":
                
                $ex_rate = 0.001;
                $points = $_POST['points'];
                $usd = $ex_rate * $points; 
                
                $card = new \Minds\plugin\payments\entities\card();
                $card_obj = $card->create(array(
                    'type' => $_POST['type'],
                    'number' => (int) str_replace(' ', '', $_POST['number']),
                    'month' => $_POST['month'],
                    'year' => $_POST['year'],
                    'sec' => $_POST['sec'],
                    'name' => $_POST['name'],
                    'name2' => $_POST['name2']
                    ));

                $response['id'] = \Minds\plugin\payments\start::createPayment("$points purchase", $usd, $card->card_id);
                if($response['id']){
                    \Minds\plugin\payments\start::createTransaction(Core\session::getLoggedinUser()->guid, $points, NULL, "purchase");
                }
                break;
            case "paypal":
                switch($pages[1]){
                    case "confirm":
                        $ex_rate = 0.001;
                        $points = $_POST['points'];
                        $usd = $ex_rate * $points;

                        $payment = \Minds\plugin\payments\services\paypal::factory()->capture($_POST['id'], $usd);
                        if($payment->getId()){
                             //ok, now charge!
                             \Minds\plugin\payments\start::createTransaction(Core\session::getLoggedinUser()->guid, $points, NULL, "purchase");
                        } else {
                            $response['status'] = 'error';
                        }
                        
                    break;
                }
                break;
            case "withdraw":
                break;
        }
        

        return Factory::response($response);
    }
    
    public function put($pages){
        
        return Factory::response(array());
        
    }
    
    public function delete($pages){
        
        return Factory::response(array());
        
    }
    
}
        