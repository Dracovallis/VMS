<?php


class AuthController extends ControllerBase
{
    public function loginAction()
    {
        $form = new LoginForm();

        if ($_POST) {
            $form->validate();
            if ($form->isValid()) {
                $userModel = new User($this->_db);

                $user = $userModel->find([
                    'conditions' => 'email = :email AND password = :password',
                    'bind' => [
                        'email' => $_POST['email'],
                        'password' => md5($_POST['password'])
                    ]
                ]);

                if (!empty($user) && !empty($user[0])) {
                    $this->setToken($user[0]['username'], $user[0]['id']);
                    $this->_router->goTo(['controller' => 'user', 'action' =>  'diary']);
                } else {
                    $this->setVars([
                        'error' => 'Invalid username or password.'
                    ]);
                }
            }
        }

        $this->setVars([
            'form' => $form
        ]);
        $this->render();
    }

    public function registerAction()
    {
        $form = new RegisterForm($this->_db);

        if ($_POST) {
            $form->validate();
            if ($form->isValid()) {
                if ($_POST['password'] !== $_POST['confirm-password']) {
                    $errors['password'] = 'Password and confirm password do not match';
                }

                $data['username'] = $_POST['username'];
                $data['password'] = md5($_POST['password']);
                $data['email'] = $_POST['email'];

                $user = new User($this->_db);
                $userId = $user->save($data);

                $this->setToken($data['username'], $userId);

                $this->_router->goTo(['controller' => 'user', 'action' =>  'diary']);
            }
        }

        $this->setVars([
            'form' => $form
        ]);
        $this->render();
    }

    public function logoutAction()
    {
        $this->unsetToken();
    }
}
