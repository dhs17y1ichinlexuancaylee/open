<?if($OP->lg){header("Location:home.php");}?>
<!DOCTYPE html>
<html><head>
 <?$OP->head("Sign Up", "pc");?>
</head><body>
 <?$OP->inc("inc/header.php");?>
 <div class="content" style="width:320px;">
  <div style="margin-bottom:10px;text-align: center;">
   <a class="button" style="padding: 5px 25px;" onclick="$('#social').show();$('#classic').hide();">Social Sign Up</a>
   <a class="button" style="padding: 5px 25px;" onclick="$('#social').hide();$('#classic').show();">Form Sign Up</a>
  </div>
  <div id="social" style="<?if(isset($_POST['submit']) || isset($_POST['verify'])){?>display:none;<?}?>">
   <center>
    <a href="oauth/login_with_facebook" style="display: inline-block;height: 43px;margin: 0px;padding: 0px 20px 0px 52px;font-family: 'Ubuntu', sans-serif;font-size: 18px;font-weight: 400;color: #fff;line-height: 41px;background: #3b579d url(<?echo HOST;?>/source/cdn/img/fb_icon.png) no-repeat 14px 8px scroll;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;text-decoration: none;cursor:pointer;margin-right:5px;">Login With Facebook</a><cl/>
    <a href="oauth/login_with_google" style="display: inline-block;height: 43px;margin: 0px;padding: 0px 20px 0px 52px;font-family: 'Ubuntu', sans-serif;font-size: 18px;font-weight: 400;color: #fff;line-height: 41px;background:rgb(231, 38, 54) url(<?echo HOST;?>/source/cdn/img/g+_icon.png) no-repeat 14px 8px scroll;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;text-decoration: none;cursor:pointer;">Login With Google +</a>
   </center>
  </div>
  <div style="<?if(!isset($_POST['submit']) && !isset($_POST['verify'])){?>display:none;<?}?>" id="classic">
   <?
   $verified=0;
   $cde=isset($_POST['code']) ? $_POST['code']:"";
   if(isset($_POST['verify']) && $cde!=""){
    $dcde=$OP->decrypter($cde);
    $vfr=$_POST['user']."private_value";
    if($vfr==$dcde){
     $verified=1;
    }else{
     $verified=0;
    }
   }
   if($verified==1){
   ?>
   <form action="register" method="POST" style="padding-left:15px;padding-top:1px;">
    <input name="user" value="<?echo$cde;?>" type="hidden"/>
    <input name="nuser" value="<?echo$_POST['user'];?>" type="hidden"/>
    <h2>E-Mail</h2>
    <input type="text" style="width:290px;" value="<?echo$_POST['user'];?>" disabled="disabled"/><br/>
    <h2>Password</h2>
    <input name="pass" style="width:290px;" id="pass" placeholder="Make It Great" autocomplete="off" type="password"/><br/>
    <h2>Retype Password</h2>
    <input name="pass2" style="width:290px;" id="pass2" placeholder="Is It that great ?" autocomplete="off" type="password"/><br/>
    <div id="ppbar" title="Strength"><div id="pbar"></div></div>
    <div id="ppbartxt"></div>
    <h2>Name</h2>
    <input name="name" style="width:290px;" id="user" placeholder="You Must Have a Name" type="text"/><br/><cl/>
    <input name="submit" type="submit" value="Sign Up"/><cl/>
    Already Have An Account ?
    <a href="<?echo HOST;?>/login"><input type="button" value="Sign In"/></a>
   </form>
   <?
   }elseif(!isset($_POST['verify']) && !isset($_POST['submit'])){
   ?>
   <form action="register" method="POST">
    <p>Type In Your E-Mail to Continue Signup Process.</p>
    <input name="mail" style="width:290px;" placeholder="Your E-Mail Please" type="text"/><cl/>
    <input name="verify" type="submit" value="Verify E-Mail"/><cl/>
    <p>You can only sign up if you verify your email.</p>
   </form>
   <?
   }elseif($verified==0 && isset($_POST['verify']) && $cde!=""){
    $OP->ser("Wrong Verification Code", "The Code you entered is wrong.");
   }
   if(isset($_POST['verify']) && !isset($_POST['code'])){
    $u=$_POST['mail'];
    if(!preg_match('/^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/', $u)){
     $OP->ser("E-Mail Is Not Valid", "The E-Mail you submitted is not a valid E-Mail");
    }
    $sql=$OP->dbh->prepare("SELECT * FROM users WHERE username=?");
    $sql->execute(array($u));
    if($sql->rowCount()!=0){
     $OP->ser("You Already Have An Account!", "There is already an account registered with the E-Mail you have given. <a href='http://open.subinsb.com/me/ResetPassword'>Forgot Password ?</a>");
    }
    $OP->sendEMail($u, "Verify Your E-Mail", "You requested for registering on Open. For signing up, you need to verify your E-Mail address. Paste the code below in the input field of the page where you requested for signing up.<blockquote>".$OP->encrypter($_POST['mail']."private_value")."</blockquote>");
   ?>
   An E-Mail containing a code have been sent to the E-Mail address you gave us. Check Your Inbox for that mail. The mail might have went to the SPAM folder. Hence you have to check that folder too.<cl/>
   <form action="register" method="POST">
    Paste The Code you received via E-Mail below<br/><cl/>
    <input name="user" value="<?echo$u;?>" type="hidden"/>
    <input name="code" style="width:290px;" autocomplete="off" placeholder="Paste The Code Here" type="text"/><br/><cl/>
    <input name="verify" type="submit" value="Complete Verification"/><cl/>
   </form>
   <?
   }
   if(isset($_POST['submit'])){
    $u=strtolower($OP->format($_POST['nuser']));
    $p=$OP->format($_POST['pass']);
    $p2=$OP->format($_POST['pass2']);
    $n=str_replace("@", "",
     str_replace("*", "",
      str_replace("(", "",
       str_replace(")", "",
        $OP->format($_POST['name'])
       )
      )
     )
    );
    if($u=="" || $p=='' || $p2=='' || $n==''){
     $OP->ser("Fields Left Blank", "Some Fields were left blank. Please fill up all fields. You now have to start over the signup process.");
    }
    if($p!=$p2){
     $OP->ser("Passwords Don't Match", "The Passwords you entered didn't match");
    }
    $dcde=$OP->decrypter($_POST['user']);
    $vfr=$_POST['nuser']."private_value";
    if($vfr!=$dcde){
     $OP->ser("User Not Verified.", "The user in which this form was sent have not verified his/her E-Mail.");
    }
    function ras($length){$str="";$chars='q!f@g#h#n$m%b^v&h*j(k)q_-=jn+sw47894swwfv1h36y8re879d5d2sd2sdf55sf4rwejeq093q732u4j4320238o/.Qkqu93q324nerwf78ew9q823';$size=strlen($chars);for($i=0;$i<$length;$i++){$str.=$chars[rand(0, $size-1)];}return$str;}
    $r_salt=ras(25);
    $site_salt="private_value";
    $salted_hash=hash('sha256', $p.$site_salt.$r_salt);
    $json='{"joined":"'.date("Y-m-d H:i:s").'"}';
    $sql=$OP->dbh->prepare("INSERT INTO users (username,password,psalt,name,udata) VALUES(?,?,?,?,?)");
    $sql->execute(array($u, $salted_hash, $r_salt, $n, $json));
    $OP->sss("Registration Success", "Your account has been created. Sign In <a href='login'>here</a>");
    header('Location:home.php');
   }
   ?>
  </div>
  <p style="margin-top:10px;">
   By using Open, you are agreeing to our <br/><a href="open.pdf">Terms & Conditions</a>.
  </p>
 </div>
 <style>#ppbar{background:#CCC;width:300px;height:15px;margin:5px;}#pbar{margin:0px;width:0px;background:lightgreen;height: 100%;}#ppbartxt{text-align:right;margin:2px;}</style>
</body></html>