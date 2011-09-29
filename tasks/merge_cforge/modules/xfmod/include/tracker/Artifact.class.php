<?php
	/**
	*
	* Artifact.class - Main Artifact class
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: Artifact.class,v 1.3 2004/01/15 00:19:14 jcox Exp $
	*
	*/
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/Error.class.php");
	 
	class Artifact extends Error {
		var $db;
		/**
		* Resource ID
		*
		* @var  int  $status_res
		*/
		var $status_res;
		 
		/**
		* Artifact Type object
		*
		* @var  object $ArtifactType
		*/
		var $ArtifactType;
		 
		/**
		* Array of artifact data
		*
		* @var  array $data_array
		*/
		var $data_array;
		 
		/**
		* Array of ArtifactFile objects
		*
		* @var  array $files
		*/
		var $files;
		 
		/**
		*  Artifact() - constructor
		*
		*  Use this constructor if you are modifying an existing artifact
		*
		* @param object The artifact type object
		*  @param integer (primary key from database OR complete assoc array)
		*  ONLY OPTIONAL WHEN YOU PLAN TO IMMEDIATELY CALL->create()
		*  @return true/false
		*/
		function Artifact(&$ArtifactType, $data = false)
		{
			global $icmsDB;
			 
			$this->db = $icmsDB;
			$this->Error();
			 
			$this->ArtifactType = $ArtifactType;
			 
			//was ArtifactType legit?
			if (!$ArtifactType || !is_object($ArtifactType))
			{
				$this->setError('Artifact: No Valid ArtifactType');
				return false;
			}
			//did ArtifactType have an error?
			if ($ArtifactType->isError())
			{
				$this->setError('Artifact: '.$ArtifactType->getErrorMessage());
				return false;
			}
			 
			//
			// make sure this person has permission to view artifacts
			//
			if (!$this->ArtifactType->userCanView())
			{
				$this->setError('Artifact: '._XF_TRK_A_ONLYMEMBERSCANVIEW);
				return false;
			}
			 
			//
			// set up data structures
			//
			if ($data)
			{
				if (is_array($data))
				{
					$this->data_array = $data;
					return true;
				}
				else
				{
					if (!$this->fetchData($data))
					{
						return false;
					}
					else
					{
						return true;
					}
				}
			}
			else
			{
				$this->setError('No ID Present');
			}
		}
		 
		/**
		* create() - construct a new Artifact in the database
		*
		* @param int  The category ID
		* @param int  The artifact group ID
		* @param string The artifact summary
		* @param string Details of the artifact
		* @param int  The ID of the user to which this artifact is to be assigned
		* @param int  The artifacts priority
		*  @return id on success / false on failure
		*/
		function create($category_id, $artifact_group_id, $summary, $details, $assigned_to = 100, $priority = 5, $monitor_email = false)
		{
			global $icmsUser, $ts;
			 
			//
			// make sure this person has permission to add artifacts
			//
			if (!$this->ArtifactType->isPublic())
			{
				//
				// Only admins can post/modify private artifacts
				//
				if (!$this->ArtifactType->userIsAdmin())
				{
					$this->setError('Artifact: '._XF_TRK_A_ONLYADMINSCANMODIFY);
					var_dump('0');
					return false;
				}
			}
			 
			//
			// get the user_id
			//
			if ($icmsUser)
			{
				$user = $icmsUser->getVar("uid");
			}
			else
			{
				if ($this->ArtifactType->allowsAnon())
				{
					$user = 100;
				}
				else
				{
					$this->setError('Artifact: '._XF_TRK_A_ANONSUBMISSIONSNOTALLOWED);
					var_dump('1');
					return false;
				}
			}
			 
			//
			// data validation
			//
			if (!$summary)
			{
				$this->setError('Artifact: '._XF_TRK_A_SUMMARYREQUIRED);
				var_dump('2');
				return false;
			}
			if (!$details)
			{
				$this->setError('Artifact: '._XF_TRK_A_BODYREQUIRED);
				var_dump('2');
				return false;
			}
			if (!$assigned_to)
			{
				if ($category_id == 100)
				{
					$assigned_to = 100;
				}
				else
				{
					//create an ArtifactCategory to determine who to auto-assign to
					$ac = new ArtifactCategory($this->ArtifactType, $category_id);
					if (!$ac || !is_object($ac) || $ac->isError())
					{
						$assigned_to = 100;
					}
					else
					{
						$assigned_to = $ac->getAssignee();
					}
				}
			}
			if ($assigned_to == 100 && $category_id != 100)
			{
				//create an ArtifactCategory to determine who to auto-assign to
				$ac = new ArtifactCategory($this->ArtifactType, $category_id);
				if (!$ac || !is_object($ac) || $ac->isError())
				{
					$assigned_to = 100;
				}
				else
				{
					$assigned_to = $ac->getAssignee();
				}
			}
			if (!$priority)
			{
				$priority = 5;
			}
			if (!$category_id)
			{
				$category_id = 100;
			}
			if (!$artifact_group_id)
			{
				$artifact_group_id = 100;
			}
			if (!$resolution_id)
			{
				$resolution_id = 100;
			}
			 
			//
			// Check to see if this idiot user is trying to double-submit
			//
			$sql = "SELECT * FROM ".$this->db->prefix("xf_artifact")." " ."WHERE summary='".$ts->makeTboxData4Save($summary)."' " ."AND submitted_by='$user' " ."AND open_date>'".(time() - 86400)."'";
			 
			$res = $this->db->query($sql);
			 
			if ($res && $this->db->getRowsNum($res) > 0)
			{
				$this->setError(_XF_TRK_A_ATTEMPTEDDOUBLESUBMIT);
				var_dump('4');
				return false;
			}
			 
			$sql = "INSERT INTO ".$this->db->prefix("xf_artifact")." " ."(group_artifact_id,status_id,category_id,artifact_group_id,priority," ."submitted_by,assigned_to,open_date,summary,details,resolution_id) " ."VALUES(" ."'".$this->ArtifactType->getID()."'," ."'1'," ."'$category_id'," ."'$artifact_group_id'," ."'$priority'," ."'$user'," ."'$assigned_to'," ."'".time()."'," ."'".$ts->makeTboxData4Save($summary)."'," ."'".$ts->makeTareaData4Save($details)."'," ."'$resolution_id')";
			 
			$res = $this->db->queryF($sql);
			 
			$artifact_id = $this->db->getInsertId();
			 
			if (!$res || !$artifact_id)
			{
				$this->setError('Artifact: '.$icmsDB->error());
				var_dump('5');
				return false;
			}
			else
			{
				//
				// Now set up our internal data structures
				//
				if (!$this->fetchData($artifact_id))
				{
					var_dump('6');
					return false;
				}
				 
				//
				// now send an email if appropriate
				//
				$this->mailFollowup(1);
				$this->clearError();
				 
				//
				//  Set up monitoring for the user if requested
				//
				if ($monitor_email)
				{
					$this->setMonitor($monitor_email);
				}
				return $artifact_id;
			}
		}
		 
		/**
		* fetchData() - re-fetch the data for this Artifact from the database
		*
		* @param int  The artifact ID
		* @return true/false
		*/
		function fetchData($artifact_id)
		{
			 
			$SQL_STATEMENT_BIG_ONE = "SELECT a.artifact_id,a.group_artifact_id,a.status_id,a.category_id,a.artifact_group_id," ."a.resolution_id,a.priority,a.submitted_by,a.assigned_to,a.open_date,a.close_date," ."a.summary,a.details," ."u.uname AS assigned_unixname," ."u.name AS assigned_realname," ."u.email AS assigned_email," ."u2.uname AS submitted_unixname," ."u2.name AS submitted_realname," ."u2.email AS submitted_email," ."ast.status_name,ac.category_name,ag.group_name,ar.resolution_name " ."FROM " ."".$this->db->prefix("users")." u," ."".$this->db->prefix("users")." u2," ."".$this->db->prefix("xf_artifact")." a," ."".$this->db->prefix("xf_artifact_status")." ast," ."".$this->db->prefix("xf_artifact_category")." ac," ."".$this->db->prefix("xf_artifact_group")." ag," ."".$this->db->prefix("xf_artifact_resolution")." ar " ."WHERE a.assigned_to=u.uid " ."AND a.submitted_by = u2.uid " ."AND a.status_id = ast.id " ."AND a.category_id = ac.id " ."AND a.artifact_group_id = ag.id " ."AND a.resolution_id = ar.id";
			 
			
			$q_getartifact = $SQL_STATEMENT_BIG_ONE." AND a.artifact_id='$artifact_id' AND a.group_artifact_id='".$this->ArtifactType->getID()."'";
			$res = $this->db->query($q_getartifact);
			 
			if (!$res || $this->db->getRowsNum($res) < 1)
			{
				$this->setError('Artifact class: Invalid ArtifactID <pre>'.$q_getartifact.'</pre>');
				return false;
			}
			 
			$this->data_array = $this->db->fetchArray($res);
			 
			return true;
		}
		/**
		* getArtifactType() - get the ArtifactType Object this Artifact is associated with
		*
		* @return ArtifactType
		*/
		function getArtifactType()
		{
			return $this->ArtifactType;
		}
		 
		/**
		* getID() - get this ArtifactID
		*
		* @return the group_artifact_id #
		*/
		function getID()
		{
			return $this->data_array['artifact_id'];
		}
		 
		/**
		* getStatusID() - get open/closed/deleted flag
		*
		* @return(1) Open,(2) Closed,(3) Deleted
		*/
		function getStatusID()
		{
			return $this->data_array['status_id'];
		}
		/**
		* getStatusName() - get open/closed/deleted text
		*
		* @return text status name
		*/
		function getStatusName()
		{
			return $this->data_array['status_name'];
		}
		 
		/**
		* getResolutionID() - get resolution flag
		*
		* @return int
		*/
		function getResolutionID()
		{
			return $this->data_array['resolution_id'];
		}
		 
		/**
		* getResolutionName() - get resolution name
		*
		* @return text resolution name
		*/
		function getResolutionName()
		{
			return $this->data_array['resolution_name'];
		}
		 
		/**
		* getCategoryID() - get category_id flag
		*
		* @return int category_id
		*/
		function getCategoryID()
		{
			return $this->data_array['category_id'];
		}
		 
		/**
		* getCategoryName() - get category text name
		*
		* @return text category name
		*/
		function getCategoryName()
		{
			return $this->data_array['category_name'];
		}
		 
		/**
		* getArtifactGroupID() - get artifact_group_id flag
		*
		* @return int artifact_group_id
		*/
		function getArtifactGroupID()
		{
			return $this->data_array['artifact_group_id'];
		}
		 
		/**
		* getArtifactGroupName() - get artifact_group_name text
		*
		* @return text artifact_group name
		*/
		function getArtifactGroupName()
		{
			return $this->data_array['group_name'];
		}
		 
		/**
		* getPriority() - get priority flag
		*
		* @return int priority
		*/
		function getPriority()
		{
			return $this->data_array['priority'];
		}
		 
		/**
		* getSubmittedBy() - get ID of submitter
		*
		* @return int user_id of submitter
		*/
		function getSubmittedBy()
		{
			return $this->data_array['submitted_by'];
		}
		/**
		* getSubmittedEmail() - get email of submitter
		*
		* @return text email of submitter
		*/
		function getSubmittedEmail()
		{
			return $this->data_array['submitted_email'];
		}
		 
		/**
		* getSubmittedRealName() - get real name of submitter
		*
		* @return text real name of submitter
		*/
		function getSubmittedRealName()
		{
			return $this->data_array['submitted_realname'];
		}
		 
		/**
		* getSubmittedUnixName() - get login name of submitter
		*
		* @return text unix name of submitter
		*/
		function getSubmittedUnixName()
		{
			return $this->data_array['submitted_unixname'];
		}
		 
		/**
		* getAssignedTo() - get ID of assignee
		*
		* @return int user_id of assignee
		*/
		function getAssignedTo()
		{
			return $this->data_array['assigned_to'];
		}
		 
		/**
		* getAssignedEmail() - get email of assignee
		*
		* @return text email of assignee
		*/
		function getAssignedEmail()
		{
			return $this->data_array['assigned_email'];
		}
		 
		/**
		* getAssignedRealName() - get real name of assignee
		*
		* @return text real name of assignee
		*/
		function getAssignedRealName()
		{
			return $this->data_array['assigned_realname'];
		}
		 
		/**
		* getAssignedUnixName() - get login name of assignee
		*
		* @return text unix name of assignee
		*/
		function getAssignedUnixName()
		{
			return $this->data_array['assigned_unixname'];
		}
		 
		/**
		* getOpenDate() - get unix time of creation
		*
		* @return int unix time
		*/
		function getOpenDate()
		{
			return $this->data_array['open_date'];
		}
		 
		/**
		* getCloseDate() - get unix time of closure
		*
		* @return int unix time
		*/
		function getCloseDate()
		{
			return $this->data_array['close_date'];
		}
		 
		/**
		* getSummary() - get text summary of artifact
		*
		* @return text summary(subject)
		*/
		function getSummary()
		{
			return $this->data_array['summary'];
		}
		 
		/**
		* getDetails() - get text body(message) of artifact
		*
		* @return text body(message)
		*/
		function getDetails()
		{
			return $this->data_array['details'];
		}
		/**
		*  setMonitor() - user can monitor this artifact
		*
		* @param string The email address of the user who is monitoring this artifact
		*  @return false - always false - always use the getErrorMessage() for feedback
		*/
		function setMonitor($email = false)
		{
			global $icmsUser;
			if ($icmsUser)
			{
				 
				$user_id = $icmsUser->getVar("uid");
				$email = ' ';
				 
				//we don't want to include the "And email=" because
				//a logged-in user's email may have changed
				$email_sql = '';
				 
			}
			else
			{
				 
				if (!$email || !checkEmail($email))
				{
					$this->setError('SetMonitor::'._XF_TRK_A_VALIDEMAILREQUIRED);
					return false;
				}
				$user_id = 0;
				 
				$email_sql = "AND email='$email'";
			}
			 
			$email = strtolower($email);
			 
			$res = $this->db->query("SELECT * FROM ".$this->db->prefix("xf_artifact_monitor")." " ."WHERE artifact_id='". $this->getID() ."' " ."AND user_id='$user_id' $email_sql");
			 
			if (!$res || $this->db->getRowsNum($res) < 1)
			{
				//not yet monitoring
				$res = $this->db->queryF("INSERT INTO ".$this->db->prefix("xf_artifact_monitor")."(artifact_id,user_id,email) " ."VALUES('". $this->getID() ."','$user_id','$email')");
				if (!$res)
				{
					$this->setError($this->db->error());
					return false;
				}
				else
				{
					$this->setError(_XF_TRK_A_NOWMONITORING);
					return false;
				}
			}
			else
			{
				//already monitoring - remove their monitor
				$this->db->queryF("DELETE FROM ".$this->db->prefix("xf_artifact_monitor")." " ."WHERE artifact_id='". $this->getID() ."' " ."AND user_id='$user_id' $email_sql");
				 
				$this->setError(_XF_TRK_A_MONITORINGDEACTIVATED);
				return false;
			}
		}
		/**
		*  getMonitorEmails() -
		*
		*  @return array of email addresses monitoring this ArtifactType
		*/
		function getMonitorEmails()
		{
			$res = $this->db->query("SELECT user_id,u.email,am.email as email2 " ."FROM ".$this->db->prefix("users")." u,".$this->db->prefix("xf_artifact_monitor")." am " ."WHERE u.uid=am.user_id " ."AND artifact_id='". $this->getID() ."'");
			 
			$rows = $this->db->getRowsNum($res);
			$email = array();
			 
			for($i = 0; $i < $rows; $i++)
			{
				//
				//  for monitoring by non-logged-in users,
				//  we grab the email they gave us
				//
				//  otherwise we use the confirmed one from the users table
				//
				$email[] = ((unofficial_getDBResult($res, $i, 'user_id') == 0)?unofficial_getDBResult($res, $i, 'email2'):unofficial_getDBResult($res, $i, 'email'));
			}
			return $email;
		}
		 
		/**
		* getHistory() - returns a result set of audit trail for this support request
		*
		* @return result set
		*/
		function getHistory()
		{
			$sql = "SELECT ah.id, ah.artifact_id, ah.field_name, ah.old_value, ah.entrydate, u.uname FROM ".$this->db->prefix("xf_artifact_history")." ah,".$this->db->prefix("users")." u " ."WHERE artifact_id='". $this->getID() ."' " ."AND ah.mod_by=u.uid " ."ORDER BY entrydate DESC";
			 
			return $this->db->query($sql);
		}
		 
		/**
		* getMessages() - get the list of messages attached to this artifact
		*
		* @return database result set
		*/
		function getMessages()
		{
			$sql = "SELECT am.id, am.artifact_id, am.from_email, am.body, am.adddate, u.uid, u.email, u.uname, u.name " ."FROM ".$this->db->prefix("xf_artifact_message")." am,".$this->db->prefix("users")." u " ."WHERE(am.submitted_by=u.uid) " ."AND artifact_id='". $this->getID() ."' " ."ORDER BY adddate DESC";
			 
			return $this->db->query($sql);
		}
		 
		/**
		* getFiles() - get array of ArtifactFile's
		*
		* @return array of ArtifactFile's
		*/
		function getFiles()
		{
			if (!isset($this->files))
			{
				 
				$sql = "SELECT af.id, af.artifact_id, af.description, af.bin_data, af.filename, af.filesize, af.filetype, af.adddate, af.submitted_by, u.uname, u.name " ."FROM ".$this->db->prefix("xf_artifact_file")." af,".$this->db->prefix("users")." u " ."WHERE(af.submitted_by = u.uid) " ."AND artifact_id='". $this->getID() ."'";
				 
				$res = $this->db->query($sql);
				$rows = $this->db->getRowsNum($res);
				 
				if ($rows > 0)
				{
					for($i = 0; $i < $rows; $i++)
					{
						$this->files[$i] = new ArtifactFile($this, $this->db->fetchArray($res));
					}
				}
				else
				{
					$this->files = array();
				}
			}
			return $this->files;
		}
		 
		/**
		*  addMessage() - attach a text message to this Artifact
		*
		* @param string The message being attached
		* @param string Email address of message creator
		* @param bool Whether to email out a followup
		* @access private
		*  @return true/false
		*/
		function addMessage($body, $by = false, $send_followup = false)
		{
			global $icmsUser, $ts;
			if (!$body)
			{
				$this->setError('ERROR - addMessage: '._XF_TRK_A_MISSINGPARAMETERS);
				return false;
			}
			 
			if ($icmsUser)
			{
				if (!$icmsUser || !is_object($icmsUser))
				{
					$this->setError('ERROR - Logged In User Bug Could Not Get User Object');
					return false;
				}
				$user_id = $icmsUser->getVar("uid");
				$body = "Logged In: "._YES."(UID:$user_id)\n\n".$body;
				 
				// we'll store this email even though it will likely never be used -
				// since we have their correct user_id, we can join the USERS table to get email
				$by = $icmsUser->email();
			}
			else
			{
				$body = "Logged In: "._NO." \n\n".$body;
				$user_id = 100;
				if (!$by)
				{
					$this->setError('ERROR - addMessage: '._XF_TRK_A_MISSINGMAILADDRESS);
					return false;
				}
			}
			$sql = "INSERT INTO ".$this->db->prefix("xf_artifact_message")."(artifact_id,submitted_by,from_email,adddate,body) " ."VALUES('". $this->getID() ."','$user_id','$by','". time() ."','". $ts->makeTareaData4Save($body). "')";
			 
			$res = $this->db->queryF($sql);
			 
			if ($send_followup)
			{
				$this->mailFollowup(2, false);
			}
			return $res;
		}
		 
		/**
		*  addHistory() - add an entry to audit trail
		*
		*  @param string The name of the field in the database being modified
		*  @param string The former value of this field
		*  @access private
		*  @return true/false
		*/
		function addHistory($field_name, $old_value)
		{
			global $icmsUser;
			 
			if ($icmsUser)
			{
				$user = $icmsUser->uid();
			}
			else
			{
				$user = 100;
			}
			$sql = "INSERT INTO ".$this->db->prefix("xf_artifact_history")."(artifact_id,field_name,old_value,mod_by,entrydate) " ."VALUES('". $this->getID() ."','$field_name','$old_value','$user','". time() ."')";
			 
			return $this->db->queryF($sql);
		}
		/**
		* update() - update the fields in this artifact
		*
		* @param int  The artifact priority
		* @param int  The artifact status ID
		* @param int  The artifact category ID
		* @param int  The artifact group ID
		* @param int  The artifact resolution ID
		* @param int  The person to which this artifact is to be assigned
		* @param int  The artifact summary
		* @param int  The canned response
		* @param int  Attaching another comment
		* @param int  Allows you to move an artifact to another type
		* @return true/false
		*/
		function update($priority, $status_id, $category_id, $artifact_group_id, $resolution_id,
			$assigned_to, $summary, $canned_response, $details, $new_artifact_type_id)
		{
			global $icmsUser, $ts;
			 
			if (!$this->getID()
			|| !$assigned_to || !$status_id || !$category_id || !$artifact_group_id || !$resolution_id || !$canned_response || !$new_artifact_type_id)
			{
				$this->setError('Artifact: '._XF_TRK_A_MISSINGPARAMETERS.' -->artifact::update()');
				return false;
			}
			 
			// If the current status is Pending then auto-reset it to 'Open'
			// Assumes the status ID for 'Pending' is '4'
			if ($status_id != '2' && $status_id != '3' && $this->getStatusID() == '4')
			{
				$status_id = '1';
			}
			 
			// original submitter can always modify his/her items now
			if (!$this->ArtifactType->userIsAdmin() && ($this->getSubmittedBy() != $icmsUser->uid()))
			{
				$this->setError('Artifact: '._XF_TRK_A_UPDATEPERMISSIONDENIED);
				return false;
			}
			 
			// Array to record which properties were changed
			$changes = array();
			 
			//
			// Get a lock on this row in the database
			//
			$lock = $this->db->query("SELECT * FROM ".$this->db->prefix("xf_artifact")." WHERE artifact_id='".$this->getID()."' FOR UPDATE");
			 
			$artifact_type_id = $this->ArtifactType->getID();
			 
			//
			// Attempt to move this Artifact to a new ArtifactType
			// need to instantiate new ArtifactType obj and test perms
			//
			if ($new_artifact_type_id != $artifact_type_id)
			{
				$newArtifactType = new ArtifactType($this->ArtifactType->getGroup(), $new_artifact_type_id);
				if (!is_object($newArtifactType) || $newArtifactType->isError())
				{
					$this->setError('Artifact: Could not move to new ArtifactType '.$newArtifactType->getErrorMessage());
					return false;
				}
				// do they have perms for new ArtifactType?
				if (!$newArtifactType->userIsAdmin())
				{
					$this->setError('Artifact: Could not move to new ArtifactType: '._XF_G_PERMISSIONDENIED);
					return false;
				}
				//
				// Now set ArtifactGroup, Category, and Assigned to 100 in the new ArtifactType
				//
				$status_id = 1;
				$category_id = '100';
				$artifact_group_id = '100';
				$assigned_to = '100';
				//can't send a canned response when changing ArtifactType
				$canned_response = 100;
				$this->ArtifactType = $newArtifactType;
				$update = true;
			}
			 
			$sqlu = '';
			 
			//
			// handle audit trail & build SQL statement
			//
			if ($this->getStatusID() != $status_id)
			{
				$this->addHistory('status_id', $this->getStatusID());
				$sqlu .= " status_id='$status_id', ";
				$changes['status'] = 1;
				$update = true;
			}
			if (($this->getResolutionID() != $resolution_id) && ($resolution_id != 100))
			{
				$this->addHistory('resolution_id', $this->getResolutionID());
				$sqlu .= " resolution_id='$resolution_id', ";
				$changes['resolution'] = 1;
				$update = true;
			}
			if ($this->getCategoryID() != $category_id)
			{
				$this->addHistory('category_id', $this->getCategoryID());
				$sqlu .= " category_id='$category_id', ";
				$changes['category'] = 1;
				$update = true;
			}
			if ($this->getArtifactGroupID() != $artifact_group_id)
			{
				$this->addHistory('artifact_group_id', $this->getArtifactGroupID());
				$sqlu .= " artifact_group_id='$artifact_group_id', ";
				$changes['artifact_group'] = 1;
				$update = true;
			}
			if ($this->getPriority() != $priority)
			{
				$this->addHistory('priority', $this->getPriority());
				$sqlu .= " priority='$priority', ";
				$changes['priority'] = 1;
				$update = true;
			}
			 
			if ($this->getAssignedTo() != $assigned_to)
			{
				$this->addHistory('assigned_to', $this->getAssignedTo());
				$sqlu .= " assigned_to='$assigned_to', ";
				$changes['assigned_to'] = 1;
				$update = true;
			}
			if ($summary && ($this->getSummary() != $ts->makeTareaData4Save($summary)))
			{
				$this->addHistory('summary', $ts->makeTareaData4Save($this->getSummary()));
				$sqlu .= " summary='". $ts->makeTareaData4Save($summary) ."', ";
				$changes['summary'] = 1;
				$update = true;
			}
			if ($details)
			{
				$this->addMessage($details);
				$changes['details'] = 1;
				$send_message = true;
			}
			 
			//
			// Enter the timestamp if we are changing to closed
			//
			if ($status_id != 1)
			{
				$now = time();
				$sqlu .= " close_date='$now', ";
				$this->addHistory('close_date', $this->getCloseDate());
				$update = true;
			}
			 
			/*
			Finally, update the artifact itself
			*/
			if ($update)
			{
				$sql = "UPDATE ".$this->db->prefix("xf_artifact")."
					SET
					$sqlu
					group_artifact_id='$new_artifact_type_id'
					WHERE
					artifact_id='". $this->getID() ."'
					AND group_artifact_id='$artifact_type_id'";
				 
				$result = $this->db->queryF($sql);
				 
				if (!$result)
				{
					$this->setError('Error - update failed!');
					//echo $this->db->error();
					return false;
				}
				else
				{
					$this->fetchData($this->getID());
					//error check the data fetching??
				}
			}
			 
			/*
			handle canned responses
			 
			Instantiate ArtifactCanned and get the body of the message
			*/
			if ($canned_response != 100)
			{
				//don't care if this response is for this group - could be hacked
				$acr = new ArtifactCanned($this->ArtifactType, $canned_response);
				 
				if (!$acr || !is_object($acr))
				{
					$this->setError('Artifact: Could Not Create Canned Response Object');
				}
				elseif($acr->isError())
				{
					$this->setError('Artifact: '.$acr->getErrorMessage());
				}
				else
				{
					$body = $acr->getBody();
					if ($body)
					{
						if (!$this->addMessage($body, $icmsUser->getVar("uname").'@'.$GLOBALS['sys_users_host']))
						{
							return false;
						}
						else
						{
							$send_message = true;
						}
					}
					else
					{
						$this->setError('Artifact: Unable to Use Canned Response');
						return false;
					}
				}
			}
			 
			if ($update || $send_message)
			{
				/*
				now send the email
				*/
				$this->mailFollowup(2, false, $changes);
				return true;
			}
			else
			{
				//nothing changed, so cancel the transaction
				$this->setError('Nothing Changed - Update Cancelled');
				return false;
			}
		}
		 
		// function which returns proper marker for changed properties
		function marker($prop_name, $changes)
		{
			if ($changes[$prop_name])
			{
				return '->';
			}
			else
			{
				return '  ';
			}
		}
		 
		/**
		* mailFollowup() - send out an email update for this artifact
		*
		* @param int  (1) initial/creation(2) update
		* @param array Array of additional addresses to mail to
		* @param array Array of fields changed in this update
		* @access private
		* @return true/false
		*/
		function mailFollowup($type, $more_addresses = false, $changes = '')
		{
			global $sys_datefmt, $icmsUser, $icmsForge, $ts;
			 
			if (!$changes)
			{
				$changes = array();
			}
			 
			if ($this->ArtifactType->useResolution())
			{
				$resolution_text = $this->marker('resolution', $changes). _XF_TRK_ATHRESOLUTION.": ". $this->getResolutionName() ."\r\n";
			}
			 
			$body = $this->ArtifactType->getName() ." "._XF_TRK_A_ITEMSMALL." #". $this->getID() .", ".sprintf(_XF_TRK_A_WASOPENEDAT, date($sys_datefmt, $this->getOpenDate())). "\r\n"._XF_TRK_A_YOUCANRESPOND." ". "\r\n".ICMS_URL."/modules/xfmod/tracker/?func=detail&atid=". $this->ArtifactType->getID() . "&aid=". $this->getID() . "&group_id=". $this->ArtifactType->Group->getID() . "\r\n". $this->marker('category', $changes). _XF_TRK_ATHCATEGORY.": ". $this->getCategoryName() ."\r\n". $this->marker('artifact_group', $changes). _XF_TRK_ATHGROUP.": ". $this->getArtifactGroupName() ."\r\n". $this->marker('status', $changes). _XF_TRK_ATHSTATUS.": ". $this->getStatusName() ."\r\n". $resolution_text. $this->marker('priority', $changes). _XF_G_PRIORITY.": ". $this->getPriority() ."\r\n". "  "._XF_G_SUBMITTEDBY.": ". $this->getSubmittedRealName() . "(". $this->getSubmittedUnixName(). ")"."\r\n". $this->marker('assigned_to', $changes). _XF_G_ASSIGNEDTO.": ". $this->getAssignedRealName() . "(". $this->getAssignedUnixName(). ")"."\r\n". $this->marker('summary', $changes). _XF_G_SUMMARY.": ". $ts->makeTareaData4Edit($this->getSummary());
			 
			 
			$subject = '[ '. $this->ArtifactType->Group->getUnixName() . '-' . $this->ArtifactType->getName() . '-#' . $this->getID() .' ] '. $ts->makeTareaData4Edit($this->getSummary()) ;
			 
			if ($type > 1)
			{
				// get all the email addresses that are monitoring this request
				$emails = $this->getMonitorEmails();
			}
			 
			if ($more_addresses)
			{
				$emails[] = $more_addresses;
			}
			//we don't email the current user
			if ($this->getAssignedTo() != 100 && $this->getAssignedTo() != $icmsUser->uid())
			{
				$emails[] = $this->getAssignedEmail();
			}
			if ($this->getSubmittedBy() != 100 && $this->getSubmittedBy() != $icmsUser->uid())
			{
				$emails[] = $this->getSubmittedEmail();
			}
			//initial submission
			if ($type == 1)
			{
				//if an email is set for this ArtifactType
				//add that address to the BCC: list
				if ($this->ArtifactType->getEmailAddress())
				{
					$emails[] = $this->ArtifactType->getEmailAddress();
				}
			}
			else
			{
				//update
				if ($this->ArtifactType->emailAll())
				{
					$emails[] = $this->ArtifactType->getEmailAddress();
				}
			}
			 
			$body .= "\r\n" ."\r\n"._XF_TRK_A_INITIALCOMMENT.":" ."\r\n".$this->getDetails()
			."\r\n" ."\r\n----------------------------------------------------------------------";
			 
			if ($type > 1)
			{
				/*
				Now include the followups
				*/
				$result2 = $this->getMessages();
				 
				$rows = $this->db->getRowsNum($result2);
				 
				if ($result2 && $rows > 0)
				{
					for($i = 0; $i < $rows; $i++)
					{
						//
						// for messages posted by non-logged-in users,
						// we grab the email they gave us
						//
						// otherwise we use the confirmed one from the users table
						//
						if (unofficial_getDBResult($result2, $i, 'user_id') == 0)
						{
							$emails[] = unofficial_getDBResult($result2, $i, 'from_email');
						}
						else
						{
							$emails[] = unofficial_getDBResult($result2, $i, 'email');
						}
						 
						$body .= "\r\n";
						if ($i == 0)
						{
							$body .= $this->marker('details', $changes);
						}
						$body .= _XF_TRK_A_COMMENTBY.": ". unofficial_getDBResult($result2, $i, 'name') . "(".unofficial_getDBResult($result2, $i, 'uname').")". "\r\n"._XF_G_DATE.": ". date($sys_datefmt, unofficial_getDBResult($result2, $i, 'adddate')). "\r\n"._XF_G_MESSAGE.":". "\r\n". $ts->makeTareaData4Edit(unofficial_getDBResult($result2, $i, 'body')) . "\r\n----------------------------------------------------------------------";
					}
				}
			}
			 
			$body .= "\r\n"._XF_TRK_A_YOUCANRESPOND." ". "\r\n".ICMS_URL."/modules/xfmod/tracker/?func=detail&atid=". $this->ArtifactType->getID() . "&aid=". $this->getID() . "&group_id=". $this->ArtifactType->Group->getID();
			 
			//only send if some recipients were found
			if (count($emails) < 1)
			{
				return true;
			}
			 
			//now remove all duplicates from the email list
			$BCC_arr = array_unique($emails);
			return xoopsForgeMail($icmsForge['noreply'], $icmsConfig['sitename'], $subject, $body, array($icmsForge['noreply']), $BCC_arr);
		}
	}
?>