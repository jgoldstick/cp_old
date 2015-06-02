<?php
/*
A MIME Mailer Class
Contributed by Chris Root
2006-01-04
from http://www.devshed.com/c/a/PHP/A-MIME-Mailer-Class/

Sending plain text email is pretty easy in most web application environments. Within a PHP application you can use the "mail()" function. Where things get more interesting is when your requirements extend to other content such as images, HTML, PDF, RTF, CSV or other document formats

The mime_mailer class has three methods (including the constructor) that can be considered "public" and is all you need to reliably send email with attachments from a PHP application. You could easily extend this class with other classes that produce files of various types, provide shopping, checkout and payment services, process input from forms, work with the local file system and a long list of other useful functions.

The first order of business is to initialize some variables and provide a constructor for the class.
*/

class clsMimeMailer
{
 var $from;
 var $to;
 var $subject;
 var $body = "";
 var $headers;
 var $files = array();
 var $header_set = false;
 var $body_set = false;

 
//////////////////////////////////////////////
  function clsMimeMailer($to,$from,$subject,$body)
  {
   if($to != "" && $from != "" && $subject != "")
   {
    $this->to = $to;
    $this->from = $from;
    $this->subject = $subject;
    if($body != "")
    {
     $this->body = $this->set_body($body);
    }
    $this->set_headers();
   }
   else
   {
    echo("Some constructor arguments are blank: clsMimeMailer");
    exit();
   }
  }

//////////////////////////////////////////////////////////////////
  function set_body($body)
  {
   $body_str = "--MIME_BOUNDRY\n";
             $body_str .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
   $body_str .= "Content-Transfer-Encoding: quoted-printable\n";
   $body_str .= "\n\n";
   $body_str .= "$body";
   $body_str .= "\n\n";
   $this->body_set = true;
   return $body_str;
  }

//////////////////////////////////////////////////////////////////////
  function set_headers(){
	$this->headers = "From: ".$this->from."  \r\n";
	$this->headers .= "Reply-To: ".$this->from." \r\n";
	$this->headers .= "MIME-Version: 1.0\r\n";
	$this->headers .= "Content-Type: multipart/mixed;boundary=\"MIME_BOUNDRY\"\n";
	$this->headers .= "X-Sender: ".$this->from."\n";
	$this->headers .= "X-Mailer: PHP4\n";
	$this->headers .= "X-Priority: 3\n";
	$this->headers .= "Return-Path: <".$this->from.">\n";
	$this->headers .= "This is a multi-part message in MIME format.\n";
	$this->header_set = true;
}
//////////////////////////////////////////////////////////////////
  function add_attachment($file_path)
  {
   if($file_path != "")
   {
    $this->files[] = $this->set_file_section($file_path);
   }
  }

////////////////////////////////////////////////////////////////////
function set_file_section($file_path)
   {
   if($str = file_get_contents($file_path))
   {
    $str = file_get_contents($file_path);
    $bname = basename($file_path);
         $str = chunk_split(base64_encode($str));
    $section .= "--MIME_BOUNDRY\n";
    $section .= "Content-Type: ".$this->determine_mime($file_path)."; name=\"$bname\"\n";
    $section .= "Content-disposition: attachment\n";
    $section .= "Content-Transfer-Encoding: base64";
    $section .= "\n\n";
    $section .= "$str";
    $section .= "\n\n";
    return $section;
   }
   else
   {
    $problem = "";
    if(!file_exists($file_path))
    {
     $problem = "File could not be found";
    }
    echo("<strong>unable to load specified file $file_path in set_file_section method</strong> <br>$problem");
    exit();
   }
   }

///////////////////////////////////////////////////////////////////
   function determine_mime($name)
   {
    $str = basename($name);
   $name_arr = explode(".",$str);
   $len = count($name_arr) - 1;
   $name_arr[$len] = strtolower($name_arr[$len]);
   switch($name_arr[$len])
   {
    case "jpg":
     $type = "image/jpeg";
     break;
    case "jpeg":
     $type = "image/jpeg";
     break;
    case "gif":
     $type = "image/gif";
     break;
    case "png":
     $type = "image/png";
     break;     
    case "txt":
     $type = "text/plain";
     break;
    case "pdf":
     $type = "application/pdf";
     break;
    case "csv";
     $type = "text/csv";
     break;
    case "html":
     $type = "text/html";
     break;
    case "htm":
     $type = "text/html";
     break;
    case "xml":
     $type = "text/xml";
     break;
   }
   return $type;
   }

//////////////////////////////////////////////////////////////
  function send()
  {
   if($this->header_set && $this->body_set)
   {
    $message = $this->assemble();
    $headers = $this->headers;
    $to_address = $this->to;
    $mail_subject = $this->subject;
    if(mail($to_address,$mail_subject,$message,$headers))
    {
     return true;
    }
    else
    {
     return false;
    }
   }
  }
  
  function assemble()
  {
   $str = $this->body;
   if(count($this->files) > 0)
   {
    $str .= implode($this->files);
    $str .= "--MIME_BOUNDRY--\n";
   }
   return $str;
  }
}

/* ********************************************************************
The Code in Use

Using the clsMimeMailer class is a simple matter. The first line you will need is to include the file in your application. You then initialize an instance of the class, add your attachments and send the mail. Use as many instances as you need. The to, from, subject and body information can come from any source in your application you need it to.

include("class/clsMimeMailer.php");
$to = "recipeint@someserver.com";
$from = "mesilly@thisserver.com";
$subject = "Season's Greetings";
$body = "Here's some xmas cards just for you!";
$mailer = new clsMimeMailer($to,$from,$subject,$body);
$mailer->add_attachment("cards/xmascard.pdf");
$mailer->add_attachment("cards/xmascard2.gif");
$mailer->send();

*************************************************************************
*/
?>