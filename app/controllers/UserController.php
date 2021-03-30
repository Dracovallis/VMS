<?php


class UserController extends ControllerBase
{
    public function diaryAction()
    {
        $smokingModel = new Smoking($this->_db);
        if ($_POST) {
            $smokingModel->save([
                'user_id' => $this->user['id'],
                'update_time' => date('Y-m-d H:i:s'),
                'create_time' => date('Y-m-d H:i:s')
            ]);
        }

        $smokingList = $smokingModel->find([
            'conditions' => 'user_id = :user_id',
            'bind' => [
                'user_id' => $this->user['id']
            ],
            'order' => [
                'id' => 'DESC'
            ]
        ]);

        $this->setVars([
            'smokingList' => $smokingList
        ]);
        $this->render();
    }
}
