<?php
/**
 * Created by PhpStorm.
 * User: nicu
 * Date: 02.07.2018
 * Time: 16:50
 */

namespace Sinmax\AuditBundle\Service;



class AuditViewerService
{
    /**
     * @param array $data
     * @param object $entity
     * @return array
     */
    public function parseDataForTable(array $data, object $entity): array
    {
        foreach ($data as $akey => $avalue) {
            $tableData[$avalue['field']][$avalue['date']] =
                [
                    'value' => $avalue['value'],
                    'user' => $avalue['user']
                ];
            $dates[$avalue['date']] = $avalue['date'];
        }

        $this->completeForEmpty($tableData, $dates);

        $tableData = $this->getComplete($tableData, $entity);

        return [$dates, $tableData];
    }

    /**
     * @param array $tableData
     * @param array $dates
     *
     * We need to complete data for dates that has no data.
     */
    private function completeForEmpty(array &$tableData, array $dates)
    {
        foreach ($tableData as $tdkey => $tdvalue) {
            foreach ($dates as $dkey => $dvalue) {
                if(!isset($tableData[$tdkey][$dvalue])){
                    $tableData[$tdkey][$dvalue]=[
                        'value'=>'-',
                        'user'=>''
                    ];
                }
            }
        }
    }

    /**
     * @param array $tableData
     * @param object $entity
     * @return array
     *
     * We need to complete the data
     */
    private function getComplete(array $tableData, object $entity): array
    {
        foreach ($tableData as $tdkey => $tdvalue) {
            foreach ($tdvalue as $tdvk => $tdvd) {
                $nextdate = next($tdvalue);
                if ($nextdate) {
                    $newData[$tdkey][$tdvk] = $nextdate;
                } else {
                    $getter = 'get'.str_replace(' ', '', ucwords(str_replace('_', ' ', $tdkey)));
                    $entityval = $entity->$getter();
                    $newData[$tdkey][$tdvk] =
                        [
                            'value'=>is_a($entityval, 'DateTime')?$entityval->format('Y-m-d H:i:s'):$entityval,
                            'user'=>'-'
                        ];
                }
            }
        }

        return $newData;
    }
}
