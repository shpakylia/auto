<?php

class PostsController extends Controller{
    public function __construct(){
        parent::__construct();
        $this->model = new Post();
    }

    public function admin_index(){
        $this->data['posts'] = $this->model->getListPosts();
//        $this->data['menus'] = $this->model->getList('menu');
//        if($_POST){
//            if(isset($_POST['addMenu'])){
//                $result = $this->model->addMenu($_POST['addMenu']);
//                if($result){
//                    Session::setFlash('Меню добавлено');
//                }
//                else{
//                    Session::setFlash('Error');
//                }
//
//            }
//            Router::redirect('/admin/menus/');
//
//        }
    }

    public function admin_add(){
        if(!$this->params[0]){
            $this->data['id_post'] = $this->model->newPost();
            Router::redirect('/admin/posts/edit/'.$this->data["id_post"].'/');

        }
    }

    public function admin_delete(){
        if($this->params[0]){
            $this->data['id_post'] = $this->model->deletePost($this->params[0]);
            Router::redirect('/admin/posts/');

        }
    }

    public function admin_edit(){

        $this->data['items'] = $this->model->getList(0);
        foreach($this->data['items'] as $k=>$item){
            $result = $this->model->getList($item['term_id']);
            if($result){
                $this->data['items'][$k]['childs'] = $result;
            }
        }

        $id_post = $this->params[0];
        $this->data['post'] = $this->model->getDataPost($id_post);
        $this->data['pages'] = $this->model->getPagesPost($id_post);
        $this->data['thumb'] =$this->model->getThumbnail($id_post);
        $this->data['thumbnails'] = self::getThumbnails();
        if($_POST){
            if(isset($_POST['title']) and isset($_POST['contetn']) and isset($_POST['items']) and isset($this->params[0])){
                $result = $this->model->save($_POST,$this->params[0]);
                if($result){
                    Session::setFlash("Запись сохранена!");
                }
                else{
                    Session::setFlash('Error');
                }
                Router::redirect('/admin/posts/edit/'.$this->params[0]);

            }
            if(!empty($_FILES['userFile'])){
                $result = self::uploadThumb();
            }
            if(isset($_POST['thumb'])){
               $this->data['post_thumb']= $_POST['thumb'];
            }
        }



    }
    public function admin_delete_thumb(){
        if($this->params[0]){
            $this->model->deleteThumbnail($this->params[0]);
        }
        Router::redirect('/admin/posts/edit/'.$this->params[0]);
    }


    //выводим картинки которые есть на сервере
    public function getThumbnails($file_path= "/uploads/"){

        $allowed_types=array('jpg','jpeg','gif','png'); //разрешеные типы изображений
        //пробуем открыть папку
        $dir_handle = @opendir(UPLOAD_PATH) or die("There is an error with your image directory!");
        while ($file = readdir($dir_handle))    //поиск по файлам
        {
            if($file=='.' || $file == '..') continue; //пропустить ссылки на другие папки
                $file_parts = explode('.',$file);  //разделить имя файла и поместить его в массив
                $ext = strtolower(array_pop($file_parts));    //последний элеменет - это расширение
                $title = implode('.',$file_parts);
                $title = htmlspecialchars($title);
            if(in_array($ext,$allowed_types))
            {
                $arr[] = $file_path. $file;
            }
        }
        closedir($dir_handle);  //закрыть папку
        return $arr;

    }
    //загружаем файл - картинку на сервер
    function uploadThumb($uploadDir = UPLOAD_PATH){
            $fileName = $_FILES['userFile']['name'];
            $fileTmpName = $_FILES['userFile']['tmp_name'];
            for ($i=0; $i<count($fileName); $i++) {
                if (file_exists($uploadDir.basename($fileName[$i]))){
                    $fileName[$i] = "copy-".$fileName[$i];
                }
                $uploadFile = $uploadDir.basename($fileName[$i]);
                if($fileTmpName[$i]){

                    if (copy($fileTmpName[$i], $uploadFile))
                    {
                        Session::setFlash("Файл ".$fileName[$i] ." успешно загружен на сервер");
                    }
                    else{
                        Session::setFlash("Ошибка! Не удалось загрузить файл на сервер!");
                    }
                }
                else{
                    Session::setFlash("Файл ".$fileName[$i]." НЕ загружен на сервер. Файл поврежден. ");
                }
            }
        }


}
