<?php

class Page extends Model{

    public function getList($only_published = false){
        $sql = "select * from term_taxonomy tax join terms t on tax.term_id = t.term_id where tax.taxonomy like 'page'";
        if ( $only_published ){
            $sql .= " and t.is_published = 1";
        }
        return $this->db->query($sql);
    }

    public function getByAlias($alias){
        $alias = $this->db->escape($alias);
        $sql = "select * from term_taxonomy tax join terms t on tax.term_id = t.term_id where tax.taxonomy like 'page' and t.alias = '{$alias}' limit 1";
        $result = $this->db->query($sql);
        return isset($result[0]) ? $result[0] : null;
    }

    public function getById($id){
        $id = (int)$id;
        $sql = "select * from term_taxonomy tax join terms t on tax.term_id = t.term_id where tax.taxonomy like 'page' and t.term_id = '{$id}' limit 1";
        $result = $this->db->query($sql);
        return isset($result[0]) ? $result[0] : null;
    }

    public function save($data, $id = null){
        if ( !isset($data['alias']) || !isset($data['title']) || !isset($data['content']) ){
            return false;
        }

        $id = (int)$id;
        $alias = $this->db->escape($data['alias']);
        $title = $this->db->escape($data['title']);
        $content = $this->db->escape($data['content']);
        $is_published = isset($data['is_published']) ? 1 : 0;

        if ( !$id ){ // Add new record
            $sql = "
                insert into terms
                   set alias = '{$alias}',
                       title = '{$title}',
                       is_published = {$is_published}
            ";
            $this->db->query($sql);
            $get_id = $this->db->insert_id();
            $sql = "
                insert into term_taxonomy
                   set term_id = '{$get_id}',
                       taxonomy = 'page',
                       content = '{$content}'
            ";
            return $this->db->query($sql);

        } else { // Update existing record
            $sql = "
                update terms
                   set alias = '{$alias}',
                       title = '{$title}',
                       is_published = {$is_published}
                   where term_id = {$id}
            ";
            $this->db->query($sql);
            $sql = "
                update term_taxonomy
                   set taxonomy = 'page',
                       content = '{$content}'
                   where term_id = {$id}
            ";
            return $this->db->query($sql);
        }
    }

    public function delete($id){
        $id = (int)$id;
        $sql = "delete from term_taxonomy where term_id = {$id}";
        $this->db->query($sql);
        $sql = "delete from terms where term_id = {$id}";
        $this->db->query($sql);
    }

}