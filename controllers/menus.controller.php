<?php

class MenusController extends Controller{

    public function __construct(){
        parent::__construct();
        $this->model = new Menu();
    }
    public function admin_index(){
        $this->data['menus'] = $this->model->getList('menu');
        if($_POST){
            if(isset($_POST['addMenu'])){
                $result = $this->model->addMenu($_POST['addMenu']);
                if($result){
                    Session::setFlash('Меню добавлено');
                }
                else{
                    Session::setFlash('Error');
                }

            }
            Router::redirect('/admin/menus/');

        }
    }

    public function admin_edit(){
        if ( isset($this->params[0]) ){
            $this->data['pages'] = $this->model->getList('page');
            $this->data['categories'] = $this->model->getList('category');
            $this->data['menu'] = $this->model->getById($this->params[0]);
            $this->data['menu_item'] = $this->model->getMenuPageById($this->params[0]);

        if($_POST){
            if(isset($_POST['page'])){
                $result = $this->model->setMenuItem($this->params[0],$_POST['page']);
                if ( $result ){
                } else {
                    Session::setFlash('Error.');
                }

            }
            elseif(isset($_POST['category'])){
                $result = $this->model->setMenuItem($this->params[0],$_POST['category']);
                if ( $result ){
                } else {
                    Session::setFlash('Error.');
                }

            }
            elseif(isset($_POST['url']) and isset($_POST['text_url'])){
                $custom_link['alias'] = $_POST['url'];
                $custom_link['title'] = $_POST['text_url'];

                $result = $this->model->setMenuCustomLink($this->params[0],$custom_link);
                if ( $result ){
                } else {
                    Session::setFlash('Error.');
                }

            }
            else{
                $this->model->save($_POST,$this->params[0]) ;
            }

            Router::redirect('/admin/menus/edit/'.$this->params[0]);
        }
        }
    }

    public function admin_delete(){
        if ( isset($this->params[0]) ){
            $result = $this->model->delete($this->params[0]);
            if ( $result ){
                Session::setFlash('Меню удалено');
            } else {
                Session::setFlash('Error.');
            }
        }
        Router::redirect('/admin/menus/');
    }
    public function admin_deleteItem(){
        if ( isset($this->params[0]) and isset($this->params[1])){
            $result = $this->model->deleteItem($this->params[0],$this->params[1]);
            if ( $result ){
            } else {
                Session::setFlash('Error.');
            }
        }
        Router::redirect('/admin/menus/edit/'.$this->params[0]);
    }

}