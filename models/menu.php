<?php

class Menu extends Model{
    //список страниц по нужному критерию

    public function getList($taxonomy = NULL, $only_published = false){
        $sql = "select * from term_taxonomy tax join terms t on tax.term_id = t.term_id ";
        if($taxonomy){
            $sql .= " where tax.taxonomy like '{$taxonomy}'";
        }
        if ( $only_published ){
            $sql .= " and t.is_published = 1";
        }
        return $this->db->query($sql);
    }


    public function addMenu($title= null){
        $title = $this->db->escape($title);
        $sql = "
                insert into terms
                   set title = '{$title}',
                       is_published = 1
            ";
        $this->db->query($sql);
        $get_id = $this->db->insert_id();
        $sql = "
                insert into term_taxonomy
                   set term_id = '{$get_id}',
                       taxonomy = 'menu'
            ";
        return $this->db->query($sql);

    }

    public function getById($id){
        $id = (int)$id;
        $sql = "select * from term_taxonomy tax join terms t on tax.term_id = t.term_id where tax.taxonomy like 'menu' and t.term_id = '{$id}' limit 1";
        $result = $this->db->query($sql);
        return isset($result[0]) ? $result[0] : null;
    }
    public function getMenuPageById($id){
        $id = (int)$id;
        $sql = "select t_r.*, t_t.taxonomy,terms.*
                    from term_relationships t_r join term_taxonomy t_t
                    on t_r.term_page_id = t_t.term_id
                    join terms
                    on t_t.term_id = terms.term_id
                    where t_r.term_taxonomy_id = '{$id}'
                    order by t_r.term_order";
        return $this->db->query($sql);
    }


    public function save($data, $id = null){
        if ( !isset($data['menu_title']) || !isset($data['menu_order']) ){
            return false;
        }

        $id = (int)$id;
        $title = $this->db->escape($data['menu_title']);

        if ( $id ){
            $sql = "update terms
                    set title = '{$title}'
                    where term_id = {$id}
            ";
            $this->db->query($sql);
            for($i=0; $i<count($data['menu_order']); $i++){
                foreach($data['menu_order'][$i] as $key_order=>$val_order){
                    $key_order = (int)$key_order;
                    $val_order = (int)$val_order;
                    $sql = "update term_relationships
                               set term_order = {$val_order}
                               where term_taxonomy_id = {$id} and
                               term_page_id = {$key_order}
                            ";
                    $this->db->query($sql);

                }
            }

        }
    }

    public function delete($id){
        $id = (int)$id;
        $sql = "delete from term_taxonomy where term_id = {$id}";
        $this->db->query($sql);
        $sql = "delete from terms where term_id = {$id}";
        $this->db->query($sql);
    }

    public function deleteItem($id_menu, $id_item){
        $id_menu = (int)$id_menu;
        $id_item = (int)$id_item;
        $sql = "delete from term_relationships where term_relationships.term_taxonomy_id = '{$id_menu}' and term_relationships.term_page_id = '{$id_item}'";
        $this->db->query($sql);
    }
    public function setMenuItem($menu_id,$pages_id){
        $menu_id = (int)$menu_id;
        foreach($pages_id as $page_id){
            $page_id =(int)$page_id;
            $sql = "insert into term_relationships
                set term_relationships.term_taxonomy_id = {$menu_id},
                term_relationships.term_page_id = {$page_id}";
            $this->db->query($sql);
        }
    }
    public function setMenuCustomLink($menu_id,$data){
        $menu_id = (int)$menu_id;
        $alias = $this->db->escape($data['alias']);
        $title = $this->db->escape($data['title']);

        $sql = "
                insert into terms
                   set alias = '{$alias}',
                       title = '{$title}'
            ";
        $this->db->query($sql);

        $get_id = $this->db->insert_id();
        $sql = "
                insert into term_taxonomy
                   set term_id = '{$get_id}',
                       taxonomy = 'custom_link'
            ";
        $this->db->query($sql);

        $sql = "insert into term_relationships
                set term_relationships.term_taxonomy_id = {$menu_id},
                term_relationships.term_page_id = {$get_id}";
        return $this->db->query($sql);
    }


}