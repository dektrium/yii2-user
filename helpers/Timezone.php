<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\helpers;

/**
 * Password helper.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Timezone
{

    /**
     * Get all of the time zones with the offsets sorted by their offset
     *
     * @return array
     */
    public static function getAll() {
        $timezones = [];
        $time_zones = $timezone_identifiers = \DateTimeZone::listIdentifiers();

        foreach ($time_zones as $time_zone) {
            $date = new \DateTime('now', new \DateTimeZone($time_zone));
            $offset_in_hours = $date->getOffset() / 60 / 60;
            $timezones[] = ['timezone' => $time_zone, 'name' => "{$time_zone} (UTC " . ($offset_in_hours > 0 ? '+' : '') . "{$offset_in_hours})", 'offset' => $offset_in_hours];
        }

        \yii\helpers\ArrayHelper::multisort($timezones, 'offset', SORT_DESC, SORT_NUMERIC);

        return $timezones;
    }
}
