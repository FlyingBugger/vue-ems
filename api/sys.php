<?php
  header("Content-type:text/html,charset=utf8");
  $db=new PDO("mysql:host=192.168.20.104;dbname=dati","root","102098hchab");
  $datas=json_decode(file_get_contents("php://input"),true);
  if (isset($datas['action']) ){
    switch ($datas['action']) {
      case 'checkPWD':
            $pwd=$datas['o'];
            $sql="select pwd from admin limit 1";
            $questions=$db->query($sql);
            if($questions->fetch(PDO::FETCH_ASSOC)['pwd']==$pwd){
              echo 1;
            }else{
              echo 0;
            }
        break;
        case 'login':
              $_newCount=$datas['data'];
              $sql="select * from admin  limit 1";
              $questions=$db->query($sql);
              $validateDates=$questions->fetch(PDO::FETCH_ASSOC);
              $res=array(
                "statusCode"=>200
              );
              $validateDates['counter']!=$_newCount['username']?$res['statusCode']=400:null;
              $validateDates['pwd']!=$_newCount['password']?$res['statusCode']=403:null;
              echo json_encode($res);
          break;
      case 'changePWD':
            $pwd=$datas['newpwd'];
            $sql="UPDATE admin set pwd=? where id=1";
            $questions=$db->prepare($sql);
            echo $questions->execute(array($pwd));
        break;
      case 'qUser':
          $sql="select headImg,nickname  from users where openid=? limit 1";
          $tempD=$db->prepare($sql);
          $tempD->execute(array($datas['openid']));
          $userInfo=$tempD->fetch(PDO::FETCH_ASSOC);
          $msg="";
          if(!$userInfo){
            $msg=array("erroCode"=>100);
          }else{
            $time=date("Y-m-d",time());
            $_sql="select times  from dati_record where openid=?";
            $_tempD=$db->prepare($_sql);
            $_tempD->execute(array($datas['openid']));
            $_recording=$_tempD->fetchAll(PDO::FETCH_ASSOC);
            $flag=false;
            foreach ($_recording as $key => $value) {
              if(explode(" ",$value['times'])[0]==$time){
                //存在当前答题时间
                $flag=true;
              }
            }
            if ($flag) {
              echo json_encode(array(
                "errorCode"=>204
              ));
            }else{
              echo json_encode(array(
                "errorCode"=>200,
                "datas"=>$userInfo
              ));
            }

          }
        break;
      case 'EditSysConfig':
          $sql="UPDATE sysconfig set combine=?,conunter=?,perconunter=?,delivery=?,timu=?,editAble=?,model=?,wMoney=?,wLowWinner=?,wMaxMoney=?,scoreR=?,scoreW=?,shareDesc=?,shareLink=?,shareImage=? where id=1";
          $_upup=$db->prepare($sql);
          $datas['data']['delivery']?$_bool="true":$_bool="false";
          $datas['data']['editAble']?$_editAble=1:$_editAble=0;
        echo  $_upup->execute(array($datas['data']['combine'],$datas['data']['conunter'],$datas['data']['perconunter'],$_bool,$datas['data']['timu'],$_editAble,$datas['data']['model'],$datas['data']['wMoney'],$datas['data']['wLowWinner'],$datas['data']['wMaxMoney'],$datas['data']['scoreR'],$datas['data']['scoreW'],$datas['data']['shareDesc'],$datas['data']['shareLink'],$datas['data']['shareImage']));
        break;

      case  'getConfig':
            $sql="select * from sysconfig limit 1";
            $res=$db->query($sql);
            echo json_encode($res->fetch(PDO::FETCH_ASSOC));
        break;
      case  'frontgetConfig':
          $sql="select * from sysconfig limit 1";
          $res=$db->query($sql);
          $dates=$res->fetch(PDO::FETCH_ASSOC);

          echo json_encode($dates);
        break;
      default:
        # code...
        break;
    }

    # code...
  }