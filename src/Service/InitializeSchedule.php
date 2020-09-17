<?php
/**
 * Created by IntelliJ IDEA.
 * User: Benharrat Khaled
 * Date: 24/08/2020
 * Time: 18:37
 */

namespace App\Service;

use App\Entity\Schedule;
use Doctrine\ORM\EntityManagerInterface;
use \DateTime;

/**
 * Class InitializeSchedule
 * @package App\Service
 */
class InitializeSchedule
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function InitializeSchedule(): void
    {
        $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        foreach ($days as $day) {
            $schedule = new Schedule();
            $schedule->setDay($day);
            $schedule->setStartMorning(new DateTime('07:30'));
            $schedule->setEndMorning(new DateTime('12:00'));
            $schedule->setStartAfternoon(new DateTime('14:30'));
            $schedule->setEndAfternoon(new DateTime('18:30'));
            $schedule->setOpening('Open');
            $schedule->setMeet('Not meet');

            $this->entityManager->persist($schedule);
        }

        $this->entityManager->flush();
    }
}
