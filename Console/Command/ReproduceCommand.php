<?php

namespace SonNguyen\Dev\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReproduceCommand extends Command
{
    private const NAME = 'reproduce:pdo:bug';

    private \Magento\Framework\DB\Adapter\AdapterInterface $connection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->connection = $resourceConnection->getConnection();

        parent::__construct(self::NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $identifierField = 'entity_id';
        $linkField = 'row_id';


        //Copy of Query
        $select = $this->connection
            ->select()
            ->from(
                ['attr' => $this->connection->getTableName('catalog_product_entity_datetime')],
                [
                    $identifierField => 'cat.' . $identifierField,
                ]
            )->joinLeft(
                ['cat' => $this->connection->getTableName('catalog_product_entity')],
                'cat.' . $linkField . '= attr.' . $linkField,
                ''
            )->where(
                'attr.attribute_id = ?',
                79
            )->where(
                'attr.store_id = ?',
                0
            )->where(
                "attr.value = DATE_FORMAT('2022-06-15', '%Y-%m-%d %H:%i:%s')"
            );


        //2nd param is redundant. To fix the issue - just remove it!
        $selectData = $this->connection->fetchCol($select, $identifierField);
    }
}
