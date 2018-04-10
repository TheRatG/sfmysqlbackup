<?php
namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class BackupCollection extends ArrayCollection
{
    /*
     * return Backup
     */
    public function getByDatetime(\DateTime $datetime)
    {
        $result = null;
        foreach ($this as $key => $value) {
            if ($datetime == $value->getDatetime()) {
                $result = $value;
            }
        }

        return $result;
    }

    public function sort()
    {
        $criteria = Criteria::create()
            ->orderBy([
                "datetime" => Criteria::DESC,
                'typeOrderValue' => Criteria::ASC,
            ]);

        return $this->matching($criteria);
    }
}
