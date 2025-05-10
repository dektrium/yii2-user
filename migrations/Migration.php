<?php

declare(strict_types=1);

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
    protected ?string $tableOptions = null;

    protected string $restrict = 'RESTRICT';

    protected string $cascade = 'CASCADE';

    protected ?string $dbType = null;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->dbType = match ($this->db->driverName) {
            'mysql' => 'mysql',
            'pgsql' => 'pgsql',
            'dblib', 'mssql', 'sqlsrv' => 'sqlsrv',
            default => throw new \RuntimeException('Your database is not supported!'),
        };

        $this->tableOptions = match ($this->db->driverName) {
            'mysql' => 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB',
            'pgsql', 'dblib', 'mssql', 'sqlsrv' => null,
            default => throw new \RuntimeException('Your database is not supported!'), // Should be caught by dbType match
        };

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
    public function dropColumnConstraints(string $table, string $column): void
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
