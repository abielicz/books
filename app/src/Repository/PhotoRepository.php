<?php
/**
 * Created by PhpStorm.
 * User: Ada
 * Date: 01.07.2017
 * Time: 04:47
 */

namespace Repository;


class PhotoRepository
{
    /**
     * Save record.
     *
     * @param array $photo Photo
     *
     * @return boolean Result
     */
    public function save($photo)
    {
        if (isset($photo['photo_id']) && ctype_digit((string) $photo['photo_id'])) {
            // update record
            $id = $photo['photo_id'];
            unset($photo['photo_id']);

            return $this->db->update('photos', $photo, ['photo_id' => $id]);
        } else {
            // add new record
            return $this->db->insert('photos', $photo);
        }
    }

}