<?php
namespace SuiteCRM\Api\V8\Controller;

use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

use SuiteCRM\Api\Core\Api;
use SuiteCRM\Api\V8\Library\ModuleLib;

class ModuleController extends Api{

    public function getModuleRecords(Request $req, Response $res, $args){


        $module = \BeanFactory::getBean($args['module_name']);
        if(is_object($module)){
            $records = $module->get_full_list();

            if(count($records)){
                foreach($records as $record){
                    $return_records[] = $record->toArray();
                }
            }

        }else{
            return $this->generateResponse($res, 400,NULL,'Module Not Found');

        }

        return $this->generateResponse($res,200,$return_records,'Success');

    }

    public function getAvailableModules(Request $req, Response $res, $args){
        global $errorList, $container;

        $lib = new ModuleLib();

        $filter = 'all';
        if(!empty($args['filter']))
            $filter = $args['filter'];

        if ($container["jwt"] !== null && $container["jwt"]->userId !== null) {
            $user = \BeanFactory::getBean('Users', $container["jwt"]->userId);
            if ($user === false) {
                return $this->generateResponse($res,401,'No user id','Failure');
            }
            else
            {
                return $this->generateResponse($res,200,$lib->getAvailableModules($filter,$user),'Success');
            }

        } else {
            $GLOBALS['log']->warn(__FILE__.': ' . __FUNCTION__ . ' called but user not found');
            return $this->generateResponse($res,401,'No user id','Failure');
        }
    }

}