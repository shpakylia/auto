<?php
class Header extends Model{

    public function getMenusPage(){
        $sql = "select terms.title
                from terms
                join term_taxonomy t_t
                on terms.term_id = t_t.term_id
                where t_t.taxonomy like 'menu'";
        $menus = $this->db->query($sql);
//        $title = $this->bd->escape($title);
        foreach($menus as $menu){
            $menu_title = $menu['title'];
            $sql = "select t_r.*, t_t.taxonomy,terms.*
                    from term_relationships t_r join term_taxonomy t_t
                    on t_r.term_page_id = t_t.term_id
                    join terms
                    on t_t.term_id = terms.term_id
                    where t_r.term_taxonomy_id = (select terms.term_id from terms where terms.title like '{$menu_title}')
                    order by t_r.term_order";
            $nav_menus[$menu_title]= $this->db->query($sql);

        }
        return $nav_menus;

    }
}