<?php

namespace MediaWiki\Extension\Adiutor\api;

use ApiBase;
use Wikimedia\ParamValidator\ParamValidator;

class AdiutorApi extends ApiBase {
   
    public function execute() {
        $params = $this->extractRequestParams();
        $action = $params['action'];
        switch ($action) {
            case 'updateLocalConfiguration':
                $result = $this->updateLocalConfiguration($data);
                break;
            default:
                $result = ['error' => 'Invalid action'];
                break;
        }
        $this->getResult()->addValue(null, 'result', $result);
    }
    private function updateLocalConfiguration() {
        return ['echo' => 'test'];
    }

}
