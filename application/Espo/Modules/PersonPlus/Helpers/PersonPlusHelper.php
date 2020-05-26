<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2020 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 * 
 * Person Plus - Open source plug in module for EspoCRM
 * Copyright (C) 2020 Omar A Gonsenheim
 ************************************************************************/

namespace Espo\Modules\PersonPlus\Helpers;

use Espo\Core\Utils\Config;

use Espo\ORM\Entity;

class PersonPlusHelper extends \Espo\Core\ORM\Helper
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function formatPersonName(Entity $entity, string $field)
    {
        $format = $this->config->get('personNameFormat');

        $first = $entity->get('first' . ucfirst($field));
        $last = $entity->get('last' . ucfirst($field));
        $middle = $entity->get('middle' . ucfirst($field));
        $motherMaiden = $entity->get('motherMaiden' . ucfirst($field));

        switch ($format) {
            case 'lastFirst':
                if (!$first && !$last) {
                    return null;
                }
                if (!$first) {
                    return $last;
                }
                if (!$last) {
                    return $first;
                }
                return $last . ' ' . $first;

            case 'lastFirstMiddle':
                if (!$first && !$last) {
                    return null;
                }

                $arr = [];

                if ($last) $arr[] = $last;
                if ($first) $arr[] = $first;
                if ($middle) $arr[] = $middle;

                return implode(' ', $arr);

            case 'firstMiddleLast':
                if (!$first && !$last && !$middle) {
                    return null;
                }

                $arr = [];

                if ($first) $arr[] = $first;
                if ($middle) $arr[] = $middle;
                if ($last) $arr[] = $last;

                return implode(' ', $arr);

            case 'firstMiddleLastMotherMaiden':
                if (!$first && !$last) {
                    return null;
                }

                $arr = [];

                if ($first) $arr[] = $first;
                if ($middle) $arr[] = $middle;
                if ($last) $arr[] = $last;
                if ($motherMaiden) $arr[] = $motherMaiden;
                
                return implode(' ', $arr);                               
                
            case 'firstLastMotherMaiden':
                if (!$first && !$last) {
                    return null;
                }

                $arr = [];

                if ($first) $arr[] = $first;
                if ($last) $arr[] = $last;
                if ($motherMaiden) $arr[] = $motherMaiden;
                
                return implode(' ', $arr);                               
                
            case 'lastMotherMaidenFirstMiddle':
                if (!$first && !$last) {
                    return null;
                }

                $arr = [];

                if ($last) $arr[] = $last;
                if ($motherMaiden) $arr[] = $motherMaiden;
                if ($first) $arr[] = $first;
                if ($middle) $arr[] = $middle;
                
                return implode(' ', $arr);                               
                
            default:
                if (!$first && !$last) {
                    return null;
                }
                if (!$first) {
                    return $last;
                }
                if (!$last) {
                    return $first;
                }
                return $first . ' ' . $last;
        }

        return null;
    }
}
