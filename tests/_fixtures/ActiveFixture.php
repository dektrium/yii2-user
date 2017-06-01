<?php


namespace tests\_fixtures;

/**
 * This test fixes issues with resetting autoincrement columns.
 * @see https://github.com/yiisoft/yii2/issues/13625
 * @package tests\_fixtures
 *
 */
class ActiveFixture extends \yii\test\ActiveFixture
{
    /**
     * Removes all existing data from the specified table and resets sequence number to 1 (if any).
     * This method is called before populating fixture data into the table associated with this fixture.
     */
    protected function resetTable()
    {
        \Yii::info("Cleaning table");
        $table = $this->getTableSchema();
        $this->db->createCommand()->delete($table->fullName)->execute();
    }
}