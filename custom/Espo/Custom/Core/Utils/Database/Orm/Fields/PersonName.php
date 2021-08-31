<?php

namespace Espo\Custom\Core\Utils\Database\Orm\Fields;

class PersonName extends \Espo\Core\Utils\Database\Orm\Fields\PersonName
{
    protected function load($fieldName, $entityType)
    {
        $format = $this->config->get('personNameFormat');
        switch ($format) {
            case 'lastFirst':
                $subList = ['last'.ucfirst($fieldName), ' ', 'first'.ucfirst($fieldName)];
                break;

            case 'lastFirstMiddle':
                $subList = [
                    'last'.ucfirst($fieldName), ' ', 'first'.ucfirst($fieldName), ' ', 'middle'.ucfirst($fieldName),
                ];
                break;

            case 'firstMiddleLast':
                $subList = [
                    'first'.ucfirst($fieldName), ' ', 'middle'.ucfirst($fieldName), ' ', 'last'.ucfirst($fieldName),
                ];
                break;

            case 'firstMiddleLastSuffix':
                $subList = [
                    'first'.ucfirst($fieldName), ' ', 'middle'.ucfirst($fieldName), ' ', 'last'.ucfirst($fieldName), ' ', 'suffix'.ucfirst($fieldName),
                ];
                break;
            
            case 'firstMiddleLastMother':
                $subList = [
                    'first'.ucfirst($fieldName), ' ', 'middle'.ucfirst($fieldName), ' ', 'last'.ucfirst($fieldName), ' ', 'mother'.ucfirst($fieldName),
                ];                
                break;

            case 'firstMiddleLastMotherSuffix':
                $subList = [
                    'first'.ucfirst($fieldName), ' ', 'middle'.ucfirst($fieldName), ' ', 'last'.ucfirst($fieldName), ' ', 'mother'.ucfirst($fieldName), ' ','suffix'.ucfirst($fieldName),
                ];                
                break;

            default:
                $subList = ['first'.ucfirst($fieldName), ' ', 'last'.ucfirst($fieldName)];
        }

        if ($format === 'lastFirstMiddle' || $format === 'lastFirst') {
            $orderBy1Field = 'last'.ucfirst($fieldName);
            $orderBy2Field = 'first'.ucfirst($fieldName);
        } else {
            $orderBy1Field = 'first'.ucfirst($fieldName);
            $orderBy2Field = 'last'.ucfirst($fieldName);
        }

        $fullList = [];

        $whereItems = [];

        foreach ($subList as $subFieldName) {
            $fieldNameTrimmed = trim($subFieldName);

            if (empty($fieldNameTrimmed)) {
                $fullList[] = "'".$subFieldName."'";
                continue;
            }
            $fullList[] = $fieldNameTrimmed;

            $whereItems[] = $fieldNameTrimmed;
        }

        $uname = ucfirst($fieldName);

        $firstName = 'first'.$uname;
        $middleName = 'middle'.$uname;
        $lastName = 'last'.$uname;
        $motherName = 'mother'.$uname;
        $suffixName = 'suffix'.$uname;

        $whereItems[] = "CONCAT:({$firstName}, ' ', {$lastName})";
        $whereItems[] = "CONCAT:({$lastName}, ' ', {$firstName})";

        if ($format === 'firstMiddleLast') {
            $whereItems[] = "CONCAT:({$firstName}, ' ', {$middleName}, ' ', {$lastName})";
        } elseif ($format === 'lastFirstMiddle') {
            $whereItems[] = "CONCAT:({$lastName}, ' ', {$firstName}, ' ', {$middleName})";
        } elseif ($format === 'firstMiddleLastSuffix') {
            $whereItems[] = "CONCAT:({$firstName}, ' ', {$middleName}, ' ', {$lastName}, ' ', {$suffixName})";
        } elseif ($format === 'firstMiddleLastMother') {
            $whereItems[] = "CONCAT:({$firstName}, ' ', {$middleName}, ' ', {$lastName}, ' ', {$motherName})";
        } elseif ($format === 'firstMiddleLastMotherSuffix') {
            $whereItems[] = "CONCAT:({$firstName}, ' ', {$middleName}, ' ', {$lastName}, ' ', {$motherName}, ' ', {$suffixName})";
        }

        $selectExpression = $this->getSelect($fullList);
        $selectForeignExpression = $this->getSelect($fullList, '{alias}');

        if ($format === 'firstMiddleLast' || $format === 'lastFirstMiddle') {
            $selectExpression = "REPLACE:({$selectExpression}, '  ', ' ')";
            $selectForeignExpression = "REPLACE:({$selectForeignExpression}, '  ', ' ')";
        }

        $fieldDefs = [
            'type' => 'varchar',
            'select' => [
                'select' => $selectExpression,
            ],
            'selectForeign' => [
                'select' => $selectForeignExpression,
            ],
            'where' => [
                'LIKE' => [
                    'whereClause' => [
                        'OR' => array_fill_keys(
                            array_map(
                                function ($item) {
                                    return $item.'*';
                                },
                                $whereItems
                            ),
                            '{value}'
                        ),
                    ],
                ],
                'NOT LIKE' => [
                    'whereClause' => [
                        'AND' => array_fill_keys(
                            array_map(
                                function ($item) {
                                    return $item.'!*';
                                },
                                $whereItems
                            ),
                            '{value}'
                        ),
                    ],
                ],
                '=' => [
                    'whereClause' => [
                        'OR' => array_fill_keys($whereItems, '{value}'),
                    ],
                ],
            ],
            'order' => [
                'order' => [
                    [$orderBy1Field, '{direction}'],
                    [$orderBy2Field, '{direction}'],
                ],
            ],
        ];

        $dependeeAttributeList = $this->getMetadata()->get(
            ['entityDefs', $entityType, 'fields', $fieldName, 'dependeeAttributeList']
        );

        if ($dependeeAttributeList) {
            $fieldDefs['dependeeAttributeList'] = $dependeeAttributeList;
        }

        return [
            $entityType => [
                'fields' => [
                    $fieldName => $fieldDefs,
                ],
            ],
        ];
    }

    protected function getSelect(array $fullList, ?string $alias = null): string
    {
        foreach ($fullList as &$item) {
            $rowItem = trim($item, " '");

            if (empty($rowItem)) {
                continue;
            }

            if ($alias) {
                $item = $alias.'.'.$item;
            }

            $item = "IFNULL:({$item}, '')";
        }

        $select = 'TRIM:(CONCAT:('.implode(', ', $fullList).'))';

        return $select;
    }
}

