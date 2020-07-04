<?php

namespace App\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManager;

class CancelUnpaidSubsCommand extends Command
{
    protected static $defaultName = 'app:cancel-inactive';

    protected function configure()
    {
        $this->setDescription('This command cancel inactive subscriptions after 37 days from the date of the last payment');

    }
    /**
     * Cancel all inactive subscription (both new unpaid subscription and subscriptions which date expired for more then 37 days)
     * Query1 v1 description - Updates subscriptions which was expired (NOTICE! not a new subscription with no payment)
     * UPDATE subscription s SET s.status = "expired" WHERE s.id IN (SELECT t.sub_id FROM (SELECT s.id AS 'sub_id', s.status, sp.id AS 'sub_pay_id', MAX(sp.date) AS 'date' FROM subscription s RIGHT JOIN subscription_payment sp ON s.id = sp.subscription_id GROUP BY s.id) t WHERE t.date + interval 37 day <= NOW())
     * Query2 v1 description - Updates "new" subscription which started_at == NULL and create_date is 37 days old or more and dose not exists in subscription_payment table OR started_at = NULL
     * UPDATE subscription s SET s.status = "expired" WHERE s.id IN (SELECT s.id FROM subscription s WHERE s.status = "new" AND s.created_at + interval 37 day >= NOW() AND (s.id NOT IN(SELECT sp.subscription_id FROM subscription_payment sp GROUP BY sp.subscription_id) OR s.started_at = NULL))
     */
    protected function execute(InputInterface $input, OutputInterface $output, ObjectManager $manager): int
    {

        // Query1
        $result =  $this->createQueryBuilder('
            UPDATE subscription s SET s.status = "expired" WHERE s.id IN 
                (
                SELECT temp.sub_id FROM 
                    (
                    SELECT s.id AS "sub_id", s.status, sp.id AS "sub_pay_id", MAX(sp.date) AS "date" 
                    FROM subscription s RIGHT JOIN subscription_payment sp ON s.id = sp.subscription_id 
                    GROUP BY s.id
                    ) temp 
                WHERE temp.date + interval 37 day <= NOW()
                )
            ')
            ->getQuery()
            ->getResult()
            ;

        // Query2
        $result =  $this->createQueryBuilder('
            UPDATE subscription s SET s.status = "expired" WHERE s.id IN 
                (
                SELECT s.id FROM subscription s 
                WHERE s.status = "new" AND s.created_at + interval 37 day >= NOW() 
                AND (s.id NOT IN(SELECT sp.subscription_id FROM subscription_payment sp GROUP BY sp.subscription_id) 
                OR s.started_at = NULL)
                )
        ')
            ->getQuery()
            ->getResult()
        ;

        $io = new SymfonyStyle($input, $output);
        $io->success('All inactive subscriptions are now stopped --if i will be able to run createQueryBuilder from inside this command... (tested queries are here)');

        return 0;
    }
}

//->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')