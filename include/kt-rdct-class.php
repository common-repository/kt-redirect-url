<?php
	if ( ! defined('ABSPATH') ) {
		die();
	}
	class ktrdctClass
	{		
		private $kt_Fields=array("id", "rdct_time", "rdct_title","rdct_dest_link", "rdct_assign_posts", "rdct_menus", "rdct_status");		
		
		function kt_rdct_addNew_redirect($tblname,$rdctinfo)
		{
			if(isset($_POST["submit"]) && $_SERVER['REQUEST_METHOD']=='POST' && (int)$_POST["add_rdct"] == "1" && current_user_can('administrator')) {
				global $wpdb;
				$count = sizeof($rdctinfo);
				if($count>0)
				{
					$id=0;
					$field="";
					$vals="";

					foreach($this->kt_Fields as $key)
					{
						if($field=="")
						{
							$field="`".$key."`";
							$vals="'".$rdctinfo[$key]."'";
						}
						else
						{
							$field=$field.",`".$key."`";
							$vals=$vals.",'".$rdctinfo[$key]."'";
						}
					}

					$sSQL = "INSERT INTO ".$tblname." ($field) values (".sanitize_text_field($vals).")"; // Input values sanitized
					$res = $wpdb->query($sSQL);
					if($res > 0){
						echo "<div class='updated rdct-success' id='message'><p><strong>Redirection Setting Saved</strong>.</p></div>";
					}
				}
				else
				{
					return false;
				}
			}		
		}

		function kt_rdct_updNew_redirect($tblname,$rdctinfo)
		{	
			if(isset($_POST["submit"]) && $_SERVER['REQUEST_METHOD']=='POST' && (int)$_POST["add_rdct"] == 2 && current_user_can('administrator')) {
				global $wpdb;
				$count = sizeof($rdctinfo);
				if($count>0)
				{
					$field="";
					$vals="";
					foreach($this->kt_Fields as $key)
					{
						if($field=="" && $key!="id")
						{
							$field="`".$key."` = '".$rdctinfo[$key]."'";
						}
						else if($key!="id")
						{
							$field=$field.",`".$key."` = '".$rdctinfo[$key]."'";
						}
					}

					$sSQL = "update ".$tblname." set ".sanitize_text_field($field)." where id=".$rdctinfo["id"];  // Input values sanitized
					$res = $wpdb->query($sSQL);
					if($res>0){
						echo "<div class='updated rdct-success' id='message'><p><strong>Redirection Setting Updated</strong>.</p></div>";
					}				
				}
				else
				{
					return false;
				}
			}
		}
		
		function del_Rdct($tblname,$rdctinfo)
		{
			if((int)$_GET["del_act"] == 3 && current_user_can('administrator')) {
				global $wpdb;
				$count = sizeof($rdctinfo);
				if($count>0)
				{
					$sSQL = 'DELETE from '.$tblname;
					$res = $wpdb->query($sSQL);				
					if($res>0){
						echo "<div class='updated' id='message'><p><strong>Redirection Setting Removed.</strong>.</p></div>";
					}
				}
			}
		}
	}
?>