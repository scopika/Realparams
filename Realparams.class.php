<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");

class Realparams extends PluginsClassiques{

    /**
     * Les véritables paramètres de l'URL peuvent être occultés par la réécriture.
     * On recompose ici un tableau de paramètres
     */
    public static function recomposeHttpParams() {

        $httpParamsFromDecodedUrl = array();
        $rewrite = new Variable("rewrite");
        if($rewrite->valeur){
            $newParams = array();
            parse_str($_SERVER['QUERY_STRING'], $parsedParams);
            // les URL réécrites cachent des URL de la forme monsite.com?url=monUrlRewritee
            if(!empty($parsedParams['url'])) {
                $sql = 'SELECT param FROM reecriture WHERE url="' . mysql_real_escape_string($parsedParams['url']) . '" LIMIT 0,1';
                $resul = CacheBase::getCache()->mysql_query($sql, null);
                $results = array();
                foreach($resul as $row) {
                    $paramsList = explode('&', substr($row->param,1));
                    foreach($paramsList as $value) {
                        $explodeEgal = explode('=', $value);
                        $httpParamsFromDecodedUrl[$explodeEgal[0]] = $explodeEgal[1];
                    }
                }
            }
        }

        $httpParams = array_merge($_GET, $httpParamsFromDecodedUrl, $_POST);
        return $httpParams;
    }
}