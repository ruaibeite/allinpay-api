<?php
header("Content-type:text/html;charset=utf-8");
require_once 'AppConfig.php';
require_once 'AppUtil.php';



//当天交易用撤销
    function cancel(){
        $trxamt = $_POST['trxamt'];
        $oldorderid = $_POST['oldorderid'];
        $reqsn = $_POST['reqsn'];
        $params = array();
        $params["cusid"] = AppConfig::CUSID;
        $params["appid"] = AppConfig::APPID;
        $params["version"] = AppConfig::APIVERSION;
        $params["orgid"] = '';
        $params["reqip"] = '';
        $params["reqtime"] = '';
        $params["orderid"] = '' ;
        $params["reqsn"]=$reqsn;
        $params["trxamt"] =  $trxamt;
        $params["oldorderid"] = $oldorderid;//原来订单号
        $params["oldtrxid"] = '';
        $params["randomstr"] = '12';//å
        $params["signtype"] ='RSA';
        $params["sign"] = urlencode(AppUtil::Sign($params));//签名
        $paramsStr = AppUtil::ToUrlParams($params);
        $url = AppConfig::APIURL . "/cancel";
        $rsp = request($url, $paramsStr);
        echo "请求返回:".$rsp;
        echo "<br/>";
        $rspArray = json_decode($rsp, true);
        if(AppUtil::validSign($rspArray)){
            echo "验签正确,进行业务处理";
        }
    }



//发送请求操作仅供参考,不为最佳实践
function request($url,$params){
    $ch = curl_init();
    $this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
    curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//如果不加验证,就设false,商户自行处理
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    $output = curl_exec($ch);
    curl_close($ch);
    return  $output;
}

//验签
function validSign($array){
    if("SUCCESS"==$array["retcode"]){
        $signRsp = strtolower($array["sign"]);
        $array["sign"] = "";
        $sign =  strtolower(AppUtil::Sign($array));
        if($sign==$signRsp){
            return TRUE;
        }
        else {
            echo "验签失败:".$signRsp."--".$sign;
        }
    }
    else{
        echo $array["retmsg"];
    }

    return FALSE;
}


cancel();

?>