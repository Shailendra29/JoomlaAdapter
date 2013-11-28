<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Registration controller class for Users.
 *
 * @package     Joomla.Site
 * @subpackage  com_users
 * @since       1.6
 */
class UsersControllerAutoreg extends UsersController
{
	/**
	 * Method to register a user.
	 *
	 * @return  boolean  True on success, false on failure.
	 * @since   1.6
	 */
	public function register()
	{
		// Check for request forgeries.
		JSession::checkToken() or AutoregHelper::error(JText::_('JINVALID_TOKEN'));

		// If registration is disabled - Redirect to login page.
		if (JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0)
		{
			AutoregHelper::error("Registration is disabled");
			return false;
		}

		$app	= JFactory::getApplication();
		$model	= $this->getModel('Registration', 'UsersModel');

		// Get the user data.
		$requestData = $this->input->post->get('jform', array(), 'array');
		$requestData['password1'] = JUserHelper::genRandomPassword();
		// $requestData['password1'] = 'TibcoOpenAPI';
		$requestData['password2'] = $requestData['password1'];
		
		if($groupName = $requestData['user_group_name']){
			$requestData['groups'] = $this->_getUserGroupId($groupName);
            array_push($requestData['groups'], 2);
		} else {
		    $requestData['groups'] = array(2);
		}
		// Validate the posted data.
		$form	= $model->getForm();
		if (!$form)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();
			$msgv = array();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$msgv[] = $errors[$i]->getMessage();
				} else {
					$msgv[]=$errors[$i];
				}
			}
			AutoregHelper::error($msgv ? $msgv : 500);
			return false;
		}
		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();
			$msg = array();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$msg[] = $errors[$i]->getMessage();
				} else {
					$msg[]=$errors[$i];
				}
			}
			AutoregHelper::error($msg);
			return false;
		}

		// Attempt to save the data.
		$return	= $model->register($data);
		// Check for errors.
		// if ($return === false)
		// {
		// 	// Redirect back to the edit screen.
		// 	AutoregHelper::error(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()));
		// 	return false;
		// }
		
		// Flush the data from the session.
		$app->setUserState('com_users.registration.data', null);
		
		$uname = $requestData;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
		->from($db->quoteName('#__users'))
		->where($db->quoteName('username') . ' = "' . $requestData['username'] . '"')
		->where($db->quoteName('name') . ' = "' . $requestData['name'] . '"')
		->where($db->quoteName('email') . ' = "' . $requestData['email1'] . '"');
		$db->setQuery($query);
		
		$newuser =  $db->loadColumn(0);
		AutoregHelper::send($newuser,'userid');
		// Redirect to the profile screen.
		if ($return === 'adminactivate'){
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
		} elseif ($return === 'useractivate')
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
		}
		else
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
	}

	protected function _getUserGroupId($groupName='')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
		->from($db->quoteName('#__usergroups'))
		->where($db->quoteName('title') . ' = "' . $groupName . '"');
		$db->setQuery($query);
		$groupid = $db->loadColumn();
		return $groupid;
	}
}


// here was ItemsStore class

class AutoregHelper
{

	public static function error($msg)
	{
		$out = array(
				'success' => 0,
				'error' => $msg
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}

	public static function send($result, $key = 'result')
	{
		$out = array(
				'success' => 1,
				$key => $result
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}
}