<?php


class SmokingController extends ControllerBase
{
    public function deleteAction($args)
    {
        $smokingModel = new Smoking($this->_db);


        $smokingModel->delete([
            'conditions' => 'user_id = :user_id AND id = :id',
            'bind' => [
                'user_id' => $this->user['id'],
                'id' => (int)$args['id']
            ]
        ]);

        $this->_router->goTo(['controller' => 'user', 'action' => 'diary']);
    }
}
