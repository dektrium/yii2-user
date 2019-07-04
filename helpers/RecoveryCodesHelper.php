<?php


namespace dektrium\user\helpers;


use dektrium\user\models\Token;

class RecoveryCodesHelper
{
    /** @var int */
    public $columnCount = 2;

    /**
     * @param Token[] $codes
     * @param int $columnCount
     * @return Token[]
     */
    public function prepareDataHtmlView($codes, $columnCount = null)
    {
        if (empty($codes)) {
            return [];
        }

        if (empty($columnCount) || $columnCount < 1) {
            $columnCount = $this->columnCount;
        }

        $result = [];
        for ($i = 0, $length = count($codes); $i < $length; $i++) {
            $row = intval($i / $columnCount);

            if (empty($result[$row])) {
                $result[$row] = [];
            }

            $result[$row][] = $codes[$i];
        }

        $endRow = &$result[count($result) - 1];
        $endRowCount = count($endRow);
        if ($endRowCount < $columnCount) {
            for (; $endRowCount < $columnCount; $endRowCount++) {
                $endRow[] = null;
            }
        }

        return $result;
    }
}