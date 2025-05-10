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

namespace AlexeiKaDev\Yii2User\helpers;

use DateTime;
use DateTimeZone;
use yii\helpers\ArrayHelper;

/**
 * Timezone helper.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Timezone
{
    /**
     * Get all of the time zones with the offsets sorted by their offset
     *
     * @return array<int, array{timezone: string, name: string, offset: int}>
     */
    public static function getAll(): array
    {
        $timeZones = [];
        $timeZoneIdentifiers = DateTimeZone::listIdentifiers();

        foreach ($timeZoneIdentifiers as $timeZone) {
            $date = new DateTime('now', new DateTimeZone($timeZone));
            $offset = $date->getOffset();
            $tz = ($offset >= 0 ? '+' : '-') . gmdate('H:i', abs($offset));
            $timeZones[] = [
                'timezone' => $timeZone,
                'name' => sprintf('%s (UTC %s)', $timeZone, $tz),
                'offset' => $offset
            ];
        }

        ArrayHelper::multisort($timeZones, 'offset', SORT_DESC, SORT_NUMERIC);

        return $timeZones;
    }
}
