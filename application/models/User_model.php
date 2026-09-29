<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    /** List users (with search + pagination) and their hobbies as a comma string */
    public function get_all($search = '', $limit = 10, $offset = 0)
    {
        $this->db->select("u.*, GROUP_CONCAT(h.name ORDER BY h.name SEPARATOR ', ') AS hobbies", FALSE);
        $this->db->from('users u');
        $this->db->join('user_hobbies uh', 'uh.user_id = u.id', 'left');
        $this->db->join('hobbies h', 'h.id = uh.hobby_id', 'left');
        if ($search !== '') {
            $this->db->group_start()
                     ->like('u.name', $search)
                     ->or_like('u.email', $search)
                     ->or_like('u.phone', $search)
                     ->group_end();
        }
        $this->db->group_by('u.id');
        $this->db->order_by('u.id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '')
    {
        if ($search !== '') {
            $this->db->group_start()
                     ->like('name', $search)->or_like('email', $search)->or_like('phone', $search)
                     ->group_end();
        }
        return $this->db->count_all_results('users');
    }

    public function get($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function get_by_email($email)
    {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function insert($data, $hobby_ids = [])
    {
        $this->db->trans_start();
        $this->db->insert('users', $data);
        $id = $this->db->insert_id();
        $this->save_hobbies($id, $hobby_ids);
        $this->db->trans_complete();
        return $this->db->trans_status() ? $id : FALSE;
    }

    public function update($id, $data, $hobby_ids = [])
    {
        $this->db->trans_start();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id)->update('users', $data);
        $this->save_hobbies($id, $hobby_ids);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete($id)
    {
        return $this->db->delete('users', ['id' => $id]); // pivot rows removed by FK cascade
    }

    /* ---------- Hobbies ---------- */
    public function all_hobbies()
    {
        return $this->db->order_by('name')->get('hobbies')->result();
    }

    public function user_hobby_ids($user_id)
    {
        $rows = $this->db->select('hobby_id')->get_where('user_hobbies', ['user_id' => $user_id])->result();
        return array_map(function ($r) { return (int) $r->hobby_id; }, $rows);
    }

    private function save_hobbies($user_id, $hobby_ids)
    {
        $this->db->delete('user_hobbies', ['user_id' => $user_id]);
        $batch = [];
        foreach ((array) $hobby_ids as $hid) {
            $batch[] = ['user_id' => $user_id, 'hobby_id' => (int) $hid];
        }
        if ($batch) {
            $this->db->insert_batch('user_hobbies', $batch);
        }
    }
}
