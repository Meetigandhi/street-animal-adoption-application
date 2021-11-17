<?php
date_default_timezone_set("Asia/Kolkata");
$con=mysqli_connect("fdb31.biz.nf","3954031_petsdb","petsmeeti05","3954031_petsdb");
if(mysqli_connect_errno()>0)
{
    $arr["msg"]=mysqli_connect_error();
    echo json_encode($arr);
}
if(isset($_GET["country"]) && !isset($_GET["state"]))
{
    try
    {
    $country=$_GET["country"];
    $q="select s.name from states s,countries c where s.country_id=c.id and c.name like ? order by s.name";
    $rs=$con->prepare($q);
    $rs->bind_param("s",$country);
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
       
       $rs->bind_result($state);
       $i=1;
       while($rs->fetch())
       {
       	$arr["state".$i]=$state;
        $i++;
       }

    }
    else{
        $arr["msg"]="States are Not Found";
    }

    }
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}

if(isset($_GET["country"]) && isset($_GET["state"]))
{
    try
    {
    $country=$_GET["country"];
    $state=$_GET["state"];
    $q="select ct.name from states s,countries c,cities ct where s.country_id=c.id and ct.country_id=c.id and ct.state_id=s.id and c.name like ? and s.name like ? order by ct.name";
    $rs=$con->prepare($q);
    $rs->bind_param("ss",$country,$state);
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
       
       $rs->bind_result($city);
       $i=1;
       while($rs->fetch())
       {
       	$arr["city".$i]=$city;
        $i++;
       }

    }
    else{
        $arr["msg"]="Cities are Not Found";
    }

    }
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["signup"]))
{
    try
    {
    	$prefix=$_POST["prefix"];
    	$name=$_POST["name"];
    	$email=$_POST["email"];
    	$contact=$_POST["contact"];
    	$state=$_POST["state"];
    	$city=$_POST["city"];
    	$country=$_POST["country"];
    	$pin=$_POST["pin"];
    $q="insert into users_tbl(title,name,emailid,contactno,country,state,city,pin) values(?,?,?,?,?,?,?,?)";
    $rs=$con->prepare($q);
    $rs->bind_param("ssssssss",$prefix,$name,$email,$contact,$country,$state,$city,$pin);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
  		$arr["msg"]="Registration Successfull";      
    }
    else{
        $arr["msg"]="Registration Unsuccessfull";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}

if(isset($_POST["signin"]))
{
    try
    {
    	$contact=$_POST["contact"];
    	$pin=$_POST["pin"];
    $q="select usertype,status from users_tbl where contactno=? and pin=?";
    $rs=$con->prepare($q);
    $rs->bind_param("ss",$contact,$pin);
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
    	$rs->bind_result($utype,$status);
        $rs->fetch();
        if($status=="Active")
        {
        $arr["msg"]="Valid";
      	$arr["utype"]=$utype;
        }
        else
        {
            $arr["msg"]="Your Account is Inactivated. Contact Administrator";
        }
    }
    else{
        $arr["msg"]="Invalid Contact No or Pin";
    }

}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["forgot_pin"]))
{
    try
    {
    	$contact=$_POST["contact"];
    	
    $q="select emailid,pin from users_tbl where contactno=?";
    $rs=$con->prepare($q);
    $rs->bind_param("s",$contact);
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
    	$rs->bind_result($email,$pin);
    	$rs->fetch();
        $arr["msg"]="Pin sent on your Email Id:";
        $arr["pin"]=$pin;
        $arr["email"]=$email;	
        
    }
    else{
        $arr["msg"]="Invalid Contact No";
    }

    
    }
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["fetchuser"]))
{
    try
    {
    	
    
    	
    $q="select * from users_tbl where usertype='User' order by name ";
    $rs=$con->prepare($q);
   
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
       $rs->bind_result($id,$title,$name,$email,$contact,$country,$state,$city,$pin,$utype,$status);
       $i=1;
       while($rs->fetch())
       {
       	$arr["id".$i]=$id;
       	$arr["title".$i]=$title;
       	$arr["name".$i]=$name;
       	$arr["email".$i]=$email;
       	$arr["city".$i]=$city;
       	$arr["state".$i]=$state;
       	$arr["contact".$i]=$contact;
       	$arr["country".$i]=$country;
       	$arr["status".$i]=$status;
       	$i++;
       }
      
        
    }
    else{
        $arr["msg"]="Users Not Found";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["updateuser"]))
{
    try
    {
    	$contact=$_POST["contact"];
    	$status=$_POST["status"];
    	
    $q="update users_tbl set status=? where contactno=?";
    $rs=$con->prepare($q);
    $rs->bind_param("ss",$status,$contact);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Status Updated";
      
        
    }
    else{
        $arr["msg"]="Status Not Updated";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["addpets"]))
{

    try
    {

        $name=$_POST["name"];
        $type=$_POST["type"];
        $age=$_POST["age"];
        $weight=$_POST["weight"];
        $height=$_POST["height"];
        $bid=$_POST["bid"];
    
    $q1="SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '3954031_petsdb' AND TABLE_NAME = 'pets_tbl'";
    $rs1=$con->prepare($q1);
    $rs1->execute();
    $rs1->store_result();
    $rs1->bind_result($id);
    $rs1->fetch();

    $q="insert into pets_tbl(name,type,age,weight,height,booth_id) values(?,?,?,?,?,?)";
    $rs=$con->prepare($q);
    $rs->bind_param("ssiddi",$name,$type,$age,$weight,$height,$bid);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Pet is Added";
        $arr["id"]=$id;
        
    }
    else{
        $arr["msg"]="Pet is Not Added";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}

if(isset($_POST["uploadimg"]))
{
    $image = $_POST['image'];
    $name = $_POST['name'];
 
    $realImage = base64_decode($image);
 
    file_put_contents($name, $realImage);
 
    echo "Image Uploaded Successfully.";
}


if(isset($_POST["fetchpets"]))
{
    try
    {
        
    
        
    $q="select p.*,b.name from pets_tbl p,booth_tbl b where p.booth_id=b.booth_id order by id";
    $rs=$con->prepare($q);
   
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
       $rs->bind_result($id,$name,$type,$age,$weight,$height,$bid,$bname);
       $i=1;
       while($rs->fetch())
       {
        $arr["id".$i]=$id;
        $arr["name".$i]=$name;
        $arr["type".$i]=$type;
        $arr["age".$i]=$age;
        $arr["weight".$i]=$weight;
        $arr["height".$i]=$height;
        $arr["bname".$i]=$bname;
        
        $i++;
       }
      
        
    }
    else{
        $arr["msg"]="Pets Not Found";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}

if(isset($_POST["deletepet"]))
{
    try
    {
        $id=$_POST["id"];
        
    $q="delete from pets_tbl where id=?";
    $rs=$con->prepare($q);
    $rs->bind_param("i",$id);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Pet Deleted";
      
        
    }
    else{
        $arr["msg"]="Pet Not Deleted";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["addbooth"]))
{

    try
    {

        $name=$_POST["name"];
        $address=$_POST["address"];
        $country=$_POST["country"];
        $state=$_POST["state"];
        $city=$_POST["city"];
        $cno=$_POST["cno"];
        $wday=$_POST["wday"];
        $otime=$_POST["otime"];
        $ctime=$_POST["ctime"];
        
    
    $q="insert into booth_tbl(name,address,country,state,city,contactno,working_day,opening_time,closing_time) values(?,?,?,?,?,?,?,?,?)";
    $rs=$con->prepare($q);
    $rs->bind_param("sssssssss",$name,$address,$country,$state,$city,$cno,$wday,$otime,$ctime);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Booth is Added";
        
    }
    else{
        $arr["msg"]="Booth is Not Added";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["fetchbooths"]))
{
    try
    {
        
    
        
    $q="select * from booth_tbl  order by booth_id";
    $rs=$con->prepare($q);
   
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
       $rs->bind_result($id,$name,$address,$country,$state,$city,$cno,$wday,$otime,$ctime);
       $i=1;
       while($rs->fetch())
       {
        $arr["id".$i]=$id;
        $arr["name".$i]=$name;
        $arr["address".$i]=$address;
        $arr["country".$i]=$country;
        $arr["state".$i]=$state;
        $arr["city".$i]=$city;
        $arr["cno".$i]=$cno;
        $arr["wday".$i]=$wday;
        $arr["otime".$i]=$otime;
        $arr["ctime".$i]=$ctime;
        $i++;
       }
      
        
    }
    else{
        $arr["msg"]="Booth Not Found";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}

if(isset($_POST["deletebooth"]))
{
    try
    {
        $id=$_POST["id"];
        
    $q="delete from booth_tbl where booth_id=?";
    $rs=$con->prepare($q);
    $rs->bind_param("i",$id);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Booth Deleted";
      
        
    }
    else{
        $arr["msg"]="Booth Not Deleted";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["addevent"]))
{

    try
    {

        $name=$_POST["name"];
        $sdate=$_POST["sdate"];
        $stime=$_POST["stime"];
        $edate=$_POST["edate"];
        $etime=$_POST["etime"];
        
        $address=$_POST["address"];
        $country=$_POST["country"];
        $state=$_POST["state"];
        $city=$_POST["city"];
        $details=$_POST["details"];
        
    
    $q="insert into event_tbl(name,sdate,stime,edate,etime,address,country,state,city,details) values(?,?,?,?,?,?,?,?,?,?)";
    $rs=$con->prepare($q);
    $rs->bind_param("ssssssssss",$name,$sdate,$stime,$edate,$etime,$address,$country,$state,$city,$details);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Event is Added";
        
    }
    else{
        $arr["msg"]="Event is Not Added";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}



if(isset($_POST["fetchevents"]))
{
    try
    {
        
    
        
    $q="select * from event_tbl  order by id";
    $rs=$con->prepare($q);
   
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
       $rs->bind_result($id,$name,$sdate,$stime,$edate,$etime,$address,$country,$state,$city,$details);
       $i=1;
       while($rs->fetch())
       {
        $arr["id".$i]=$id;
        $arr["name".$i]=$name;
        $arr["sdate".$i]=$sdate;
        $arr["stime".$i]=$stime;
        $arr["edate".$i]=$edate;
        $arr["etime".$i]=$etime;
        $arr["address".$i]=$address;
        $arr["country".$i]=$country;
        $arr["state".$i]=$state;
        $arr["city".$i]=$city;
        $arr["details".$i]=$details;
       
        $i++;
       }
      
        
    }
    else{
        $arr["msg"]="Event Not Found";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}

if(isset($_POST["deleteevent"]))
{
    try
    {
        $id=$_POST["id"];
        
    $q="delete from event_tbl where id=?";
    $rs=$con->prepare($q);
    $rs->bind_param("i",$id);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Event Deleted";
      
        
    }
    else{
        $arr["msg"]="Event Not Deleted";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["sendnoti"]))
{


    try
    {

        $sub=$_POST["sub"];
        $msg=$_POST["msg"];
        $ndt=date("Y-m-d h:i:s");
    
    $q="insert into notification_tbl(subject,message,ndatetime) values(?,?,?)";
    $rs=$con->prepare($q);
    $rs->bind_param("sss",$sub,$msg,$ndt);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Notification is sent";
        
    }
    else{
        $arr["msg"]="Notification is not sent";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["update_pin"]))
{
    try
    {
        $contactno=$_POST["contactno"];
        $opin=$_POST["opin"];
        $npin=$_POST["npin"];
    
    $q="update users_tbl set pin=? where contactno=? and pin=?";
    $rs=$con->prepare($q);
    $rs->bind_param("sss",$npin,$contactno,$opin);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Pin Updated";
        
    }
    else{
        $arr["msg"]="Pin Not Updated";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["sendquery"]))
{

    try
    {

        $sub=$_POST["sub"];
        $msg=$_POST["msg"];
        $qdt=date("Y-m-d h:i:s");
        $sentby=$_POST["sentby"];
        
    
    $q="insert into query_tbl(subject,message,qdatetime,sent_by) values(?,?,?,?)";
    $rs=$con->prepare($q);
    $rs->bind_param("ssss",$sub,$msg,$qdt,$sentby);
    $rs->execute();
    $rs->store_result();
    if($rs->affected_rows>0)
    {
       $arr["msg"]="Query is sent";
        
    }
    else{
        $arr["msg"]="Query is not sent";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}


if(isset($_POST["fetchquery"]))
{
    try
    {
        
    $q="select q.*,u.name,u.emailid from query_tbl q,users_tbl u where u.contactno=q.sent_by order by q.id desc";
    $rs=$con->prepare($q);
   
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
       $rs->bind_result($id,$sub,$message,$qdt,$cno,$name,$eid);
       $i=1;
       while($rs->fetch())
       {
        $arr["id".$i]=$id;
        $arr["sub".$i]=$sub;
        $arr["message".$i]=$message;
        $arr["qdt".$i]=$qdt;
        $arr["cno".$i]=$cno;
        $arr["name".$i]=$name;
        $arr["eid".$i]=$eid;
       
        $i++;
       }
      
        
    }
    else{
        $arr["msg"]="Query Not Found";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}



if(isset($_POST["fetchcategory"]))
{
    try
    {
        
    $q="select distinct type from pets_tbl order by type";
    $rs=$con->prepare($q);
   
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
       $rs->bind_result($type);
       $i=1;
       while($rs->fetch())
       {
        $arr["type".$i]=$type;
        
        $i++;
       }
      
        
    }
    else{
        $arr["msg"]="Type Not Found";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}



if(isset($_POST["fetchnoti"]))
{
    try
    {
        
    $q="select * from notification_tbl order by id desc";
    $rs=$con->prepare($q);
   
    $rs->execute();
    $rs->store_result();
    if($rs->num_rows>0)
    {
       $rs->bind_result($id,$sub,$message,$ndt);
       $i=1;
       while($rs->fetch())
       {
        $arr["id".$i]=$id;
        $arr["sub".$i]=$sub;
        $arr["message".$i]=$message;
        $arr["ndt".$i]=$ndt;
        
        $i++;
       }
      
        
    }
    else{
        $arr["msg"]="Notification Not Found";
    }
}
catch(Exception $e)
{
    $arr["msg"]=$e->getmessage();
}

    echo json_encode($arr);
}

?>