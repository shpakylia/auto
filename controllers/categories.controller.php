<?php

class CategoriesController extends Controller{

    public function __construct($data = array()){
        parent::__construct($data);
        $this->model = new Category();
    }

//    public function index(){
//        $this->data['categories'] = $this->model->getList();
//    }
//
//    public function view(){
//        $params = App::getRouter()->getParams();
//
//        if ( isset($params[0]) ){
//            $alias = strtolower($params[0]);
//            $this->data['category'] = $this->model->getByAlias($alias);
//        }
//    }

    public function admin_index(){
        $this->data['categories'] = $this->model->getList('category');
    }

    public function admin_add(){
        if ( $_POST ){
            $result = $this->model->save($_POST);
            if ( $result ){
                Session::setFlash('Category was saved.');
            } else {
                Session::setFlash('Error.');
            }
            Router::redirect('/admin/categories/');
        }
        $this->data['categories'] = $this->model->getList('page');
    }

    public function admin_edit(){
        $post_obj = new Post();
        $id_cat = (int)$this->params[0];
        $this->data['thumb'] =$post_obj->getThumbnail($id_cat);
        $this->data['thumbnails'] = self::getThumbnails();

        if ( $_POST ){
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            $result = $this->model->save($_POST, $id);
            if ( $result ){
                Session::setFlash('Page was saved.');
            } else {
                Session::setFlash('Error.');
            }
            Router::redirect('/admin/categories/');
        }

        if ( isset($this->params[0]) ){
            $this->data['category'] = $this->model->getById($this->params[0]);
            $this->data['categories'] = $this->model->getList();

        } else {
            Session::setFlash('Wrong category id.');
            Router::redirect('/admin/categories/');
        }
    }

    public function admin_delete(){
        if ( isset($this->params[0]) ){
            $result = $this->model->delete($this->params[0]);
            if ( $result ){
                Session::setFlash('category was deleted.');
            } else {
                Session::setFlash('Error.');
            }
        }

        Router::redirect('/admin/categories/');
    }

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