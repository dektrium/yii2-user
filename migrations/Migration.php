<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\migrations;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Migration extends \yii\db\Migration
{
    /** @var string|null */
    protected $tableOptions = null;

    /** @var string */
    protected $restrict = 'RESTRICT';

    /** @var string */
    protected $cascade = 'CASCADE';

    /** @var string|null */
    protected $dbType = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        switch ($this->db->driverName) {
            case 'mysql':
                $this->dbType = 'mysql';
                break;
            case 'pgsql':
                $this->dbType = 'pgsql';
                break;
            case 'dblib':
            case 'mssql':
            case 'sqlsrv':
                $this->dbType = 'sqlsrv';
                break;
            default:
                throw new \RuntimeException('Your database is not supported!');
        }

        switch ($this->db->driverName) {
            case 'mysql':
                $this->tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
                break;
            case 'pgsql':
            case 'dblib':
            case 'mssql':
            case 'sqlsrv':
                $this->tableOptions = null;
                break;
            default:
                throw new \RuntimeException('Your database is not supported!');
        }

        if (in_array($this->db->driverName, ['dblib', 'mssql', 'sqlsrv'], true)) {
            $this->restrict = 'NO ACTION';
        }
    }

    /**
     * Drops column constraints (specifically default constraints in SQL Server).
     *
     * @param string $table The table whose column default constraint is to be dropped.
     * @param string $column The name of the column whose default constraint is to be dropped.
     * @throws \yii\db\Exception
     */
    public function dropColumnConstraints($table, $column)
    {
        if ($this->dbType !== 'sqlsrv') {
            return;
        }

        $rawTableName = $this->db->schema->getRawTableName($table);
        $sql = 'SELECT name FROM sys.default_constraints
                WHERE parent_object_id = object_id(:table)
                AND type = \'D\' AND parent_column_id = (
                    SELECT column_id 
                    FROM sys.columns 
                    WHERE object_id = object_id(:table)
                    AND name = :column
                )';

        $constraints = $this->db->createCommand($sql, [':table' => $rawTableName, ':column' => $column])->queryAll();

        foreach ($constraints as $c) {
            if (isset($c['name'])) {
                $this->execute('ALTER TABLE ' . $this->db->quoteTableName($rawTableName) . ' DROP CONSTRAINT ' . $this->db->quoteIdentifier($c['name']));
            }
        }
    }
}
