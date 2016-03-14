<?php

class Post extends Model{
//получаем список для вывода дерева страниц
    public function getList($parent = null){
        $sql = "select * from term_taxonomy tax join terms t on tax.term_id = t.term_id where (tax.taxonomy like 'page' or tax.taxonomy like 'category') ";
        if($parent >=0){
            $sql.=" and tax.parent = {$parent}";
        }
        return $this->db->query($sql);
    }
    public function getListPosts($is_published = null ){
        $sql = "select * from posts where post_type = 'post'";
        if($is_published){
            $sql.=" is_published = 1";
        }
        return $this->db->query($sql);
    }
// создаем пост и присваем ид и перенаправляем страницу на редактирование
    public function newPost(){
        $date = date('Y-m-d');

        $sql = "insert into posts set post_data = '{$date}', post_type = 'post'";
        $this->db->query($sql);
        return $this->db->insert_id();
    }
    public function deletePost($post_id){
        $post_id = (int)$post_id;
        $sql = "delete from postmeta where post_id = {$post_id}";
        $this->db->query($sql);
        $sql = "delete from posts where id = {$post_id}";
        return $this->db->query($sql);
    }


    //получаем миниатюру для поста
    public function getThumbnail($post_id){
        $post_id = (int)$post_id;
        $sql = "select * from posts join postmeta p on posts.id = p.meta_value
                where p.post_id = {$post_id}
                and p.meta_key like '_thumbnail'";
        return $this->db->query($sql);
    }
    //удаляем миниатюру поста
    public function deleteThumbnail($post_id){
        $post_id = (int)$post_id;
        $sql = "delete from postmeta where post_id = {$post_id} and meta_key like '_thumbnail'";
        return $this->db->query($sql);
    }
//получаем данные поста
    public function getDataPost($post_id){
        $id = (int)$post_id;
        $sql = "select * from posts
                where posts.id = {$id}";
        return $this->db->query($sql);
    }

    //получаем страницы где пост опубликован
    public function getPagesPost($post_id){
        $post_id = (int)$post_id;
        $sql = "select postmeta.meta_value from postmeta
                where postmeta.post_id = {$post_id}
                and postmeta.meta_key like '_page'";
        return $this->db->query($sql);
    }






//    public function getById($id){
//        $id = (int)$id;
//        $sql = "select * from term_taxonomy tax join terms t on tax.term_id = t.term_id where tax.taxonomy like 'menu' and t.term_id = '{$id}' limit 1";
//        $result = $this->db->query($sql);
//        return isset($result[0]) ? $result[0] : null;
//    }
//    public function getMenuPageById($id){
//        $id = (int)$id;
//        $sql = "select t_r.*, t_t.taxonomy,terms.*
//                    from term_relationships t_r join term_taxonomy t_t
//                    on t_r.term_page_id = t_t.term_id
//                    join terms
//                    on t_t.term_id = terms.term_id
//                    where t_r.term_taxonomy_id = '{$id}'
//                    order by t_r.term_order";
//        return $this->db->query($sql);
//    }

//сохраняем все данные в посте

    public function save($data, $id = null){
        if ( !isset($data['title']) || !isset($data['contetn']) ){
            return false;
        }
        $is_published = isset($data['is_published']) ? 1 : 0;
        $id = (int)$id;
        $title = $this->db->escape($data['title']);
        $content = $this->db->escape($data['contetn']);
        $data_modify = date('Y-m-d');
        $post_url = 'post='.$id;
        $thumb = isset($data['thumb']) ? $data['thumb'] : null;

        if ( $id ){
            $sql = "update posts
                    set posts.post_content = '{$content}',
                    posts.post_title = '{$title}',
                    posts.is_published = {$is_published},
                    posts.post_modified = '{$data_modify}',
                    posts.guid = '{$post_url}'
                    where posts.id = $id
            ";
            $this->db->query($sql); //сохраняем текстовые данные поста
            if($thumb){ //если присвоена миниатюра
                $sql = "select posts.id from posts where posts.guid like '{$thumb}'";
                $thumb_id = $this->db->query($sql); //проверяем если ли эта миниатюра в дазе, чтоб повторно не добавлять
                $thumb_id = $thumb_id[0]['id']; //если миниатюра есть , то запоминаем ее ид
                if(!$thumb_id){ //если миниатюры нет, то добавляем в таблицу "пост"
                    $data_thumb = date('Y-m-d');
                    $sql = "insert into posts
                    set post_data = '{$data_thumb}',
                    guid = '{$thumb}',
                    post_type = 'image'";

                    $this->db->query($sql);
                    $thumb_id = $this->db->insert_id(); // и запоминаем ид миниатюры

                }
                //ищем, была ли присвоена миниатюра
            $sql="select postmeta.meta_value from postmeta where postmeta.post_id ={$id} and postmeta.meta_key like '_thumbnail' limit 1";
            $thumb_post = $this->db->query($sql);
                if($thumb_post){ //если есть миниатюра у поста, то обновляем, иначе - добавляем
                    $sql = "update postmeta set meta_value = '{$thumb_id}'";
                }
                else{
                    $sql = "insert into postmeta
                    set post_id = {$id},
                    meta_key = '_thumbnail',
                    meta_value = '{$thumb_id}'";
                }
                $this->db->query($sql);

            }
            if($data['items']){ //обновляем страницы, к которому привязан пост
                $sql = "delete from postmeta where postmeta.post_id ={$id} and postmeta.meta_key = '_page'";
                $this->db->query($sql);
                foreach($data['items'] as $page_id){
                    $sql = "insert into postmeta
                    set postmeta.post_id ={$id},
                    postmeta.meta_key = '_page',
                    postmeta.meta_value = '{$page_id}'";
                    $this->db->query($sql);

                }
            }
        }
        return true;

    }


//    public function delete($id){
//        $id = (int)$id;
//        $sql = "delete from term_taxonomy where term_id = {$id}";
//        $this->db->query($sql);
//        $sql = "delete from terms where term_id = {$id}";
//        $this->db->query($sql);
//    }
//
//    public function deleteItem($id_menu, $id_item){
//        $id_menu = (int)$id_menu;
//        $id_item = (int)$id_item;
//        $sql = "delete from term_relationships where term_relationships.term_taxonomy_id = '{$id_menu}' and term_relationships.term_page_id = '{$id_item}'";
//        $this->db->query($sql);
//    }
//    public function setMenuItem($menu_id,$pages_id){
//        $menu_id = (int)$menu_id;
//        foreach($pages_id as $page_id){
//            $page_id =(int)$page_id;
//            $sql = "insert into term_relationships
//                set term_relationships.term_taxonomy_id = {$menu_id},
//                term_relationships.term_page_id = {$page_id}";
//            $this->db->query($sql);
//        }
//    }
//    public function setMenuCustomLink($menu_id,$data){
//        $menu_id = (int)$menu_id;
//        $alias = $this->db->escape($data['alias']);
//        $title = $this->db->escape($data['title']);
//
//        $sql = "
//                insert into terms
//                   set alias = '{$alias}',
//                       title = '{$title}'
//            ";
//        $this->db->query($sql);
//
//        $get_id = $this->db->insert_id();
//        $sql = "
//                insert into term_taxonomy
//                   set term_id = '{$get_id}',
//                       taxonomy = 'custom_link'
//            ";
//        $this->db->query($sql);
//
//        $sql = "insert into term_relationships
//                set term_relationships.term_taxonomy_id = {$menu_id},
//                term_relationships.term_page_id = {$get_id}";
//        return $this->db->query($sql);
//    }


}