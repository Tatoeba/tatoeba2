<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
class Export extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => false,
    ];

    protected $_virtual = ['pretty_filename'];

    protected $_hidden = ['filename', 'url', 'queued_job_id', 'user_id'];

    public function getFileExtension()
    {
        $parts = explode('.', basename($this->filename));
        if (count($parts) == 1) {
            $ext = '';
        } else {
            $ext = end($parts);
            if ($ext) {
                $ext2 = prev($parts);
                if ($ext2 == 'tar' && count($parts) > 2) {
                    $ext = ".$ext2.$ext";
                } else {
                    $ext = ".$ext";
                }
            }
        }
        return $ext;
    }

    protected function _getPrettyFilename()
    {
        if ($this->name && $this->filename && $this->generated) {
            $date = $this->generated->format('Y-m-d');
            $ext = $this->getFileExtension();
            return sprintf('%s - %s%s', $this->name, $date, $ext);
        }
    }
}
